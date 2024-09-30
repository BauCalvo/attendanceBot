-- Mi id: 7501

-- agarramos la clase 41016
select * from pruebasmoodle.mdl_zoom_meeting_details mzmd 
where id = 41016;

-- Agarramos su zoomId
select * from pruebasmoodle.mdl_zoom mz 
where id = 5998;

-- agarramos los participantes
select * from pruebasmoodle.mdl_zoom_meeting_participants mzmp 
where detailsid = 12299;

-- nos concentramos en mi (523437) dea (user id 7501)
select *  from pruebasmoodle.mdl_zoom_meeting_participants mzmp 
where id = 523437;

-- agarramos la mdl_attendance de la clase 1337
select * from pruebasmoodle.mdl_attendance ma 
where course = 1337;

-- agarramos la mdl_attendance_session que entre dentro de los horarios de la clase
select * from pruebasmoodle.mdl_attendance_sessions mas 
where attendanceid = 1573;

/*  
 Reunion:
 - Start: 1665526455 19:14
 - End: 1665540795   23:13
 
 Sessions
 - Sessdate: 1665525600 19:00
 - Sessdate + duration (14.400) = 1665540000 23:00
 */

SELECT *
FROM pruebasmoodle.mdl_attendance_sessions mas 
WHERE mas.sessdate <= 1665540795 -- Start_Time de la meeting
AND (mas.sessdate + mas.duration) >= 1665526455  -- End_Time de la meeting
and mas.groupid = 8960; -- GroupId del muchacho

-- filtramos
SELECT *
FROM pruebasmoodle.mdl_attendance_sessions mas 
WHERE sessdate between 1665526455 - 3600 and 1665540795 + 3600
and attendanceid = 1573;

-- vemos los participantes
select * from pruebasmoodle.mdl_attendance_log mal 
where 
mal.sessionid in (
  551012,
  552283,
  554151,
  554530
) and mal.studentid = 7501;

-- convert to a single query

SELECT mas.*
FROM pruebasmoodle.mdl_attendance_sessions mas 
WHERE mas.sessdate BETWEEN (
    SELECT mzmd2.start_time - 3600
    FROM pruebasmoodle.mdl_zoom_meeting_details mzmd2
    WHERE mzmd2.id = 41016
) AND (
    SELECT mzmd3.end_time + 3600
    FROM pruebasmoodle.mdl_zoom_meeting_details mzmd3
    WHERE mzmd3.id = 41016
)
AND mas.attendanceid = (
    SELECT ma.id 
    FROM pruebasmoodle.mdl_attendance ma 
    WHERE ma.course = (
        SELECT mz.course 
        FROM pruebasmoodle.mdl_zoom mz 
        WHERE mz.id = (
            SELECT mzmd.zoomid 
            FROM pruebasmoodle.mdl_zoom_meeting_details mzmd 
            WHERE mzmd.id = 41016
        )
    )
);

-- parametrizado

SELECT mas.*
FROM pruebasmoodle.mdl_attendance_sessions mas 
WHERE mas.sessdate BETWEEN (
    SELECT mzmd2.start_time - 3600
    FROM pruebasmoodle.mdl_zoom_meeting_details mzmd2
    WHERE mzmd2.id = :mzmd_id
) AND (
    SELECT mzmd3.end_time + 3600
    FROM pruebasmoodle.mdl_zoom_meeting_details mzmd3
    WHERE mzmd3.id = :mzmd_id
)
AND mas.attendanceid = (
    SELECT ma.id 
    FROM pruebasmoodle.mdl_attendance ma 
    WHERE ma.course = (
        SELECT mz.course 
        FROM pruebasmoodle.mdl_zoom mz 
        WHERE mz.id = (
            SELECT mzmd.zoomid 
            FROM pruebasmoodle.mdl_zoom_meeting_details mzmd 
            WHERE mzmd.id = :mzmd_id
        )
    )
);

-- lo inverso

SELECT mzmd.id AS detailsId
FROM pruebasmoodle.mdl_zoom_meeting_details mzmd
WHERE mzmd.zoomid IN (
    SELECT mz.id 
    FROM pruebasmoodle.mdl_zoom mz 
    WHERE mz.course IN (
        SELECT ma.course 
        FROM pruebasmoodle.mdl_attendance ma 
        WHERE ma.id IN (
            SELECT mas.attendanceid 
            FROM pruebasmoodle.mdl_attendance_sessions mas 
            WHERE mas.id = :session_id
        )
    )
)
AND mzmd.start_time - 3600 <= (
    SELECT mas.sessdate 
    FROM pruebasmoodle.mdl_attendance_sessions mas 
    WHERE mas.id = :session_id
)
AND mzmd.end_time + 3600 >= (
    SELECT mas.sessdate 
    FROM pruebasmoodle.mdl_attendance_sessions mas 
    WHERE mas.id = :session_id
);

select * from pruebasmoodle.mdl_zoom_meeting_details mzmd where mzmd.id in (
41014,
40993,
41004,
41016,
41066,
40941,
40951,
40973,
40977,
40944,
41076,
40934,
40990,
40946
)

-- bring all participants with their attendance percentage
select mzmp.uuid, mzmp.duration, (mzmp.leave_time - mzmp.join_time) as total_duration, mzmp.userid, mzmp.name, mzmp.join_time, mzmp.leave_time  from pruebasmoodle.mdl_zoom_meeting_participants mzmp 
where mzmp.detailsid = 12299;

-- Dado una reunion traer cada participante con su duracion total
SELECT SUM(mzmp.duration) AS total_duration, mzmp.userid, mzmp.name
FROM pruebasmoodle.mdl_zoom_meeting_participants mzmp
WHERE mzmp.detailsid = 12299
GROUP BY mzmp.userid, mzmp.name; -- 11438

-- bring the zoom duration
select (mzmd.end_time - mzmd.start_time) AS time_difference from pruebasmoodle.mdl_zoom_meeting_details mzmd 
where id = 12299;

select * from pruebasmoodle.mdl_zoom_meeting_details mzmd
inner join pruebasmoodle.mdl_zoom mz on mzmd.zoomid = mz.id
where mzmd.id = 12299;

select * from pruebasmoodle.mdl_zoom_meeting_details mzmd 
inner join pruebasmoodle.mdl_zoom mz on mzmd.zoomid = mz.id
where mzmd.start_time > :startTime and mzmd.end_time < :endTime and mz.course = :course and mzmd.participants_count > 1;


-- Bring the zoom duration and include it in the list of people and a percentage of attendance in said meeting
SELECT 
    mzmp.userid, 
    mzmp.name, 
    SUM(mzmp.duration) AS total_duration, 
--    (mzmd.end_time - mzmd.start_time) AS meeting_duration, 
--    mzmd.start_time as start_time_meeting,
--    mzmd.end_time as end_time_meeting,
    (SUM(mzmp.duration) * 100.0 / (mzmd.end_time - mzmd.start_time)) AS attendance_percentage,
--    MIN(mzmp.join_time) as min_join_time,
--    MAX(mzmp.leave_time) as max_leave_time,
	CASE WHEN mzmp.join_time > mzmd.start_time + (:minutesOfTolerance * 60) THEN 1 ELSE 0 END AS is_late
FROM 
    pruebasmoodle.mdl_zoom_meeting_participants mzmp
JOIN 
    pruebasmoodle.mdl_zoom_meeting_details mzmd ON mzmp.detailsid = mzmd.id
WHERE 
    mzmp.detailsid = :detailsId
GROUP BY 
    mzmp.userid, mzmp.name, mzmd.start_time, mzmd.end_time;
   
select * from pruebasmoodle.mdl_zoom_meeting_details mzmd where mzmd.id = 12299;
   
SELECT mzmp.userid, mzmp.name, MIN(mzmp.join_time) as min_join_time, MAX(mzmp.leave_time) as max_leave_time, (MAX(mzmp.leave_time) - MIN(mzmp.join_time)) as total_duration
FROM pruebasmoodle.mdl_zoom_meeting_participants mzmp 
WHERE mzmp.detailsid = 12299 
and userid = 2764
GROUP BY mzmp.userid, mzmp.name;

select * from pruebasmoodle.mdl_zoom_meeting_participants mzmp 
where mzmp.userid = 2764
and
mzmp.detailsid = 12299; 
-- 1977

-- Sumar solo duraciones, hacemos un chabon que entro 5 min y entro 5 al final, esto lo soluciona para que no rellene el medio
SELECT mzmp.userid, mzmp.name, SUM(mzmp.leave_time - mzmp.join_time) as total_duration
FROM pruebasmoodle.mdl_zoom_meeting_participants mzmp 
WHERE mzmp.detailsid = 12299 
and mzmp.userid = 2764
GROUP BY mzmp.userid, mzmp.name; 
-- 1977

-- Pero volvemos al problema inicial, que si tengo 2 dispositivos en paralelo, me va a contar doble
select * from pruebasmoodle.mdl_zoom_meeting_participants mzmp 
where mzmp.userid = 2948
and
mzmp.detailsid = 12299; 

SELECT mzmp.userid, mzmp.name, SUM(mzmp.leave_time - mzmp.join_time) as total_duration
FROM pruebasmoodle.mdl_zoom_meeting_participants mzmp 
WHERE mzmp.detailsid = 12299 
and mzmp.userid = 2948
GROUP BY mzmp.userid, mzmp.name; 

-- da como resultado



--- Agarrar algun attendance que tenga 2 por curso
select ma.id, count(*), ma.course  from pruebasmoodle.mdl_attendance ma 
group by ma.course 
having count(*) >= 2;

select * from pruebasmoodle.mdl_attendance ma where ma.course  = 1983;
select * from pruebasmoodle.mdl_attendance_sessions mas where mas.attendanceid in (select ma.id from pruebasmoodle.mdl_attendance ma where ma.course  = 1983)

select mz.name from pruebasmoodle.mdl_zoom mz where mz.course = 1983;
-- grab only the text in []
SELECT 
    SUBSTRING(mz.name, LOCATE('[', mz.name) + 1, LOCATE(']', mz.name) - LOCATE('[', mz.name) - 1) 
FROM 
    pruebasmoodle.mdl_zoom mz 
WHERE 
    mz.course = 1983;


-- Agarrar las instalaciones de attendance dado un curso
select count(*) as cant_attendance from pruebasmoodle.mdl_attendance ma where ma.course = 1983; 

-- Solo los usuarios
SELECT 
    mzmp.userid,
    (SUM(mzmp.duration) * 100.0 / (mzmd.end_time - mzmd.start_time)) AS attendance_percentage,
    CASE
        WHEN (SUM(mzmp.duration) * 100.0 / (mzmd.end_time - mzmd.start_time)) >= 75 THEN 1
        ELSE 0
    END AS pass_status
FROM 
    pruebasmoodle.mdl_zoom_meeting_participants mzmp
JOIN 
    pruebasmoodle.mdl_zoom_meeting_details mzmd ON mzmp.detailsid = mzmd.id
WHERE 
    mzmp.detailsid = 12299
GROUP BY 
    mzmp.userid;

   -- Dada la session 46720, meterle los alumnos [att: 202]
 select * from pruebasmoodle.mdl_attendance ma where ma.id = 202;
 select * from pruebasmoodle.mdl_attendance_sessions mas where mas.id = 46720;
 select * from pruebasmoodle.mdl_attendance_log mal where mal.sessionid = 46720;
 
-- agarrar id, acronym y description
select mas.id, mas.acronym, mas.description  from mysql.mdl_attendance_statuses mas where mas.attendanceid = 0;

INSERT INTO pruebasmoodle.mdl_attendance_log (sessionid, studentid, statusid, statusset, timetaken, takenby, remarks, ipaddress)
VALUES (46720, 2665, 809, '809,811,812,810', 1606255889, 2948, 'Tomado por Presenty', '127.0.0.1');


INSERT INTO `pruebasmoodle`.`mdl_attendance_log` (sessionid, studentid, statusid, statusset, timetaken, takenby, remarks, ipaddress)
VALUES (46720, 2665, 809, '809,811,812,810', 1606255889, 2948, 'Tomado por Presenty', '127.0.0.1');

