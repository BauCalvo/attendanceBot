<?php


require_once($CFG->dirroot . '/mod/attendancebot/classes/persistence/BasePersistance.php');
require_once($CFG->dirroot . '/mod/attendancebot/classes/models/AttendanceLog.php');
require_once($CFG->dirroot . '/mod/attendancebot/classes/utils/StudentAttendance.php');
require_once($CFG->dirroot . '/mod/attendancebot/classes/utils/StudentMap.php');
require_once($CFG->dirroot . '/mod/attendancebot/classes/utils/Statuses.php');
require_once($CFG->dirroot . '/mod/attendance/externallib.php');
require_once($CFG->dirroot . '/mod/attendancebot/utilities.php');


class AttendancePersistance extends BasePersistance
{
    private $courseId;
    private $descriptionBot = "taken by attendancebot";

    public function __construct($courseId)
    {
      $this->courseId = $courseId;
    }

    public function persistStudents($students)
    {
        $map = new StudentMap($students);
        $sortedStudentMap = $map->getMap();
        $attendanceId = getInstanceByModuleName('attendance',$this->courseId);

        $sessionsNgroups= $this->insertStudents($sortedStudentMap,$attendanceId);

        $absentStudents = $this->markAbsentStudents($sessionsNgroups);

        $this->markSessions($sessionsNgroups["sessionid"]);

        return $absentStudents;
    }


    /**
     * crea una session nueva y inserta a todos los alumnos dentro de ella con sus respectivos estados
     * @param $studentMap  StudentMap
     * @param $attendanceId string
     * @return array de sessionesId
     */
    private function insertStudents($studentMap, $attendanceId){
        global $DB;
        $inserts = [];
        $sessionsNgroups = array( "sessionid" => array(),
                            "groupid" => array());
        $statuses = $this->getStatuses($attendanceId);

        foreach ($studentMap as $group) {
            $sessionId = $this->getNewSession($group[0],$attendanceId);
            if ($sessionId == null){
                continue;
            }

            $sessionsNgroups ["sessionid"][] = $sessionId;
            $sessionsNgroups ["groupid"][] = $group[0]->getGroupId();

            $idBot = getInstanceByModuleName('attendancebot',$this->courseId);
            $attendancePercentage = (int) $DB->get_record('attendancebot', array('id' => $idBot ),
                'late_tolerance,min_percentage', MUST_EXIST)->min_percentage;

            foreach ($group as $student) {
                $statusId = $statuses->getAbscent();
                if ($student->getAttendancePercentage()>= $attendancePercentage and $student->getIsLate() == 0){
                    $statusId = $statuses->getPresent();
                }elseif ($student->getAttendancePercentage()>= $attendancePercentage and $student->getIsLate() == 1){
                    $statusId = $statuses->getLate();
                }
                
                $attendanceLog = new AttendanceLog($student->getUserId(),$sessionId,$statuses->getAll(),$statusId);
                
                $inserts[] = $attendanceLog;
                mtrace('Agregando estudiante');
            }
        }

        $DB->insert_records('attendance_log', $inserts);
        return $sessionsNgroups;
    }


    //marcar las sesiones como tomadas
    private function markSessions( $sessions)
    {
        global $DB;
        foreach ($sessions as $session){
            $result = $DB->get_record('attendance_sessions', array('id' => $session));
            if ($result == null){
                continue;
            }
            $result->lasttaken = time();
            $DB->update_record('attendance_sessions', $result);
        }

    }

    private function getStatuses($attendanceId){
        global $DB;
        $statusesRaw = array_values($DB->get_records('attendance_statuses', array('attendanceid' => $attendanceId),'id','id'));
        $statuses = new Statuses($statusesRaw);
        return $statuses;
    }


    private function getNewSession($student , $attendanceId){
        global $DB;
        $attendanceExternal = new mod_attendance_external();
        $idBot = getInstanceByModuleName('attendancebot',$this->courseId);
        $claseInfo = $DB->get_record('attendancebot', array('id' => $idBot ),
            'clases_start_time,clases_finish_time', MUST_EXIST);


        $startTime = getTime($student->getStartTime(),(int)$claseInfo->clases_start_time);
        $endTime = getTime($student->getStartTime(),(int)$claseInfo->clases_finish_time);
        $duration = $endTime - $startTime;

        $gruopid = $student->getGroupId();

        $description = ($DB->get_record('course', array('id' => $this->courseId))->fullname)." " . $this->descriptionBot;
        return array_values($attendanceExternal->add_session($attendanceId ,$description,$startTime,$duration,$gruopid,false))[0];
    }

    private function markAbsentStudents($sessionsNgroups){
        global $DB;
        $sql = "SELECT gm.userid 
                FROM mdl_groups_members gm
                LEFT JOIN mdl_attendance_log al ON gm.userid = al.studentid AND al.sessionid = :sessionid
                WHERE gm.groupid = :groupid
                AND al.studentid IS NULL;";

        $absentStudents = array();

        for ($i = 0; $i < count($sessionsNgroups["sessionid"]); $i++) {
            $params = array('sessionid' => $sessionsNgroups["sessionid"][$i], 'groupid' => $sessionsNgroups["groupid"][$i]);
            $records = $DB->get_records_sql($sql,$params);
            if ($records == null || count($records) == 0){
                continue;
            }
            $this->insertAbsentStudents($records,$sessionsNgroups["sessionid"][$i]);
            $absentStudents[] = [$sessionsNgroups["sessionid"][$i],$records];
        }

        return $absentStudents;
    }

    private function insertAbsentStudents($records, $sessionid)
    {
        global $DB;
        $statuses = $this->getStatuses(getInstanceByModuleName('attendance',$this->courseId));
        $inserts = [];
        foreach ($records as $record){
            $attendanceLog = new AttendanceLog($record->userid,$sessionid,$statuses->getAll(),$statuses->getAbscent());
            $inserts[] = $attendanceLog;
            mtrace('Agregando estudiante ausente'. $attendanceLog->studentid);
        }
        $DB->insert_records('attendance_log', $inserts);
    }

}
