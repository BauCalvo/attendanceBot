<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The main mod_attendancebot configuration form.
 *
 * @package     mod_attendancebot
 * @copyright   2024 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once(__DIR__ . '/utilities.php');

//DEFINO VALORES POR DEFECTO PARA EL FORMULARIO
define('FECHA_INICIO_PRIMERO', mktime(0, 0, 0, 3, 1, date('Y')));
define('FECHA_FIN_PRIMERO', mktime(0, 0, 0, 7, 1, date('Y')));
define('FECHA_INICIO_SEGUNDO', mktime(0, 0, 0, 8, 8, date('Y')));
define('FECHA_FIN_SEGUNDO', mktime(0, 0, 0, 12, 22, date('Y')));
define('PORCENTAJE_MINIMO',75);
define('PLUGIN_HABILITADO_DEFAULT',1);
define('HORA_COMIENZO',19);
define('HORA_FINALIZACION',23);
define('MINUTOS_DEFAULT',0);
define('TOLERANCIA_MINIMA',15);

/**
 * Module instance settings form.
 *
 * @package     mod_attendancebot
 * @copyright   2024 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendancebot_mod_form extends moodleform_mod {

    //DEFINO EL FORMULARIO
    public function definition() {
      global $CFG;

      $mform = $this->_form;

      // Adding the "general" fieldset, where all the common settings are shown.
      $mform->addElement('header', 'general', get_string('general', 'form'));

      // Adding the standard "name" field.
      $mform->addElement('text', 'name', get_string('attendancebotname', 'mod_attendancebot'), array('size' => '64'));

      if (!empty($CFG->formatstringstriptags)) {
          $mform->setType('name', PARAM_TEXT);
      } else {
          $mform->setType('name', PARAM_CLEANHTML);
      }

      $mform->addRule('name', null, 'required', null, 'client');
      $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

      // Adding the standard "intro" and "introformat" fields.
      if ($CFG->branch >= 29) {
          $this->standard_intro_elements();
      } else {
          $this->add_intro_editor();
      }

      //CONFIGURACION DE PLUGIN
      $mform->addElement('header', 'attendancebot_settings', get_string('attendancebot_settings', 'mod_attendancebot'));
      
      //<FIELD NAME="enabled" TYPE="int" LENGTH="1" DEFAULT="1" NOTNULL="false"  SEQUENCE="false" COMMENT="Si el plugin esta prendido (true) o apagado(false)"/>
      $mform->addElement('advcheckbox', 'enabled', get_string('form_enable_settings', 'mod_attendancebot'), get_string('form_enabledescription_settings', 'mod_attendancebot'), null, array(0,1));
      $mform->addRule('enabled', null, 'required', null, 'client');
      $mform->addHelpButton('enabled', 'enabled', 'mod_attendancebot');

      //<FIELD NAME="min_percentage" TYPE="int" LENGTH="3" NOTNULL="false" DEFAULT="75" SEQUENCE="false" COMMENT="Porcentaje minimo para estar presente"/>
      for ($i = 0; $i <= 100; $i++) {
        $porcentaje[$i] =  sprintf("%02d", $i) ;
      }
      $min_percentage = array();
      $min_percentage[]=& $mform->createElement('select','min_percentage','',$porcentaje);
      $min_percentage[]=& $mform->createElement('static', 'min_percentage_text', '', get_string('form_min_percentage_text', 'mod_attendancebot'));
      $mform->addGroup($min_percentage, 'min_percentage_group', get_string('form_percentage_settings', 'mod_attendancebot'), array(' '), false);

      $mform->setType('min_percentage', PARAM_INT);
      $mform->addRule('min_percentage_group', null, 'required', null, 'client');
      $mform->addHelpButton('min_percentage_group', 'min_percentage', 'mod_attendancebot');
    
      //<FIELD NAME="late_tolerance" TYPE="int" LENGTH="10" NOTNULL="false" DEFAULT="0" SEQUENCE="false" COMMENT="Tolerancia (en minutos) para saber si alguien esta tarde"/>
      for ($i = 0; $i <= 60; $i++) {
        $tolerancia[$i] ="   " .  sprintf("%02d", $i);
      }
      $tolerance = array();
      $tolerance[]=& $mform->createElement('select','late_tolerance','',$tolerancia);
      $tolerance[]=& $mform->createElement('static', 'late_tolerance_text', '',get_string('form_late_tolerance_text', 'mod_attendancebot'));
      $mform->addGroup($tolerance, 'late_tolerance_group', get_string('form_late_tolerance', 'mod_attendancebot'), array(' '), false);

      $mform->setType('late_tolerance', PARAM_INT);
      $mform->addRule('late_tolerance_group', null, 'required', null, 'client');
      $mform->addHelpButton('late_tolerance_group', 'late_tolerance', 'mod_attendancebot');

      //<FIELD NAME="recolection_platform" TYPE="text" NOTNULL="false" DEFAULT="zoom" SEQUENCE="false" COMMENT="Plataforma en el que se va a recolectar la informacion"/>
      $recolection_platform = array(
        'zoom' => 'Zoom'
      );
      $mform->addElement('select', 'recolection_platform', get_string('form_recolection_platform', 'mod_attendancebot'), $recolection_platform);
      $mform->setType('recolection_platform', PARAM_TEXT);
      $mform->addRule('recolection_platform', null, 'required', null, 'client');
      $mform->addHelpButton('recolection_platform', 'recolection_platform', 'mod_attendancebot');

      //<FIELD NAME="saving_platform" TYPE="text" NOTNULL="false" DEFAULT="attendance" SEQUENCE="false" COMMENT="Plataforma en donde se va a pasar la asistencia"/>
      $saving_platform = array(
        'attendance' => 'Attendance'
      );
      $mform->addElement('select', 'saving_platform', get_string('form_saving_platform', 'mod_attendancebot'), $saving_platform);
      $mform->setType('saving_platform', PARAM_TEXT);
      $mform->addRule('saving_platform', null, 'required', null, 'client');
      $mform->addHelpButton('saving_platform', 'saving_platform', 'mod_attendancebot');

      //<FIELD NAME="clases_start_date" TYPE="int" LENGTH="10" NOTNULL="false" DEFAULT="0" SEQUENCE="false" COMMENT="Timestamp del comienzo de clases"/>
      $mform->addElement('date_selector', 'clases_start_date',get_string('form_clases_start_date', 'mod_attendancebot'),array(
        'startyear' => 2000, 
        'stopyear'  => 2100,
        'timezone'  => -3,
        'optional'  => false
      ));
      $mform->addRule('clases_start_date', null, 'required', null, 'client');
      $mform->addHelpButton('clases_start_date', 'clases_start_date', 'mod_attendancebot');
    
      //<FIELD NAME="clases_finish_date" TYPE="int" LENGTH="10" NOTNULL="false" DEFAULT="0" SEQUENCE="false" COMMENT="Timestamp de la finalizacion de clases"/>
      $mform->addElement('date_selector', 'clases_finish_date',get_string('form_clases_finish_date', 'mod_attendancebot'),array(
        'startyear' => 2000, 
        'stopyear'  => 2100,
        'timezone'  => -3,
        'optional'  => false
      ));
      $mform->addRule('clases_finish_date', null, 'required', null, 'client');
      $mform->addHelpButton('clases_finish_date', 'clases_finish_date', 'mod_attendancebot');      
      
      //<FIELD NAME="clases_start_time" TYPE="int" LENGTH="10" NOTNULL="true" DEFAULT="0" SEQUENCE="false" COMMENT="Timestamp del tiempo (horas y minutos desde el comienzo del dia) del comienzo del meeting"/>
      for ($i = 0; $i <= 23; $i++) {
        $start_hours[$i] =  sprintf("%02d", $i);
      }
      for ($i = 0; $i < 60; $i++) {
        $start_minutes[$i] ="   " .  sprintf("%02d", $i);
      }
      
      $clases_start=array();
      $clases_start[]=& $mform->createElement('select', 'clases_start_hour', ' ', $start_hours);
      $clases_start[]=& $mform->createElement('select', 'clases_start_minutes', ' ', $start_minutes);
      $mform->addGroup($clases_start, 'clases_start',get_string('form_clases_start', 'mod_attendancebot'), array(' '), false);
      //SETEO REGLAS Y TIPOS
      $mform->addRule('clases_start', null, 'required', null, 'client');
      $mform->setType('clases_start_hour', PARAM_INT); 
      $mform->setType('clases_start_minutes', PARAM_INT);
      $mform->addHelpButton('clases_start', 'clases_start', 'mod_attendancebot');
      
      //<FIELD NAME="clases_finish_time" TYPE="int" LENGTH="10" NOTNULL="true" DEFAULT="0" SEQUENCE="false" COMMENT="Timestamp del tiempo (horas y minutos desde el comienzo del dia) del final del meeting"/>
      for ($i = 0; $i <= 23; $i++) {
        $finish_hours[$i] =  sprintf("%02d", $i);
      }
      for ($i = 0; $i < 60; $i++) {
        $finish_minutes[$i] ="   " .  sprintf("%02d", $i);
      }
      
      $clases_finish=array();
      $clases_finish[]=& $mform->createElement('select', 'clases_finish_hour', ' ', $finish_hours);
      $clases_finish[]=& $mform->createElement('select', 'clases_finish_minutes', ' ', $finish_minutes);
      $mform->addGroup($clases_finish, 'clases_finish', get_string('form_clases_finish', 'mod_attendancebot'), array(' '), false);
      //SETEO REGLAS Y TIPOS
      $mform->addRule('clases_finish', null, 'required', null, 'client');
      $mform->setType('clases_finish_hour', PARAM_INT); 
      $mform->setType('clases_finish_minutes', PARAM_INT);
      $mform->addHelpButton('clases_finish', 'clases_finish', 'mod_attendancebot');

      //Creo los elementos hidden para enviar el start_time y finishtime
      $mform->addElement('hidden', 'clases_start_time');
      $mform->addElement('hidden', 'clases_finish_time');
      $mform->setType('clases_start_time', PARAM_INT);
      $mform->setType('clases_finish_time', PARAM_INT);

      //TEXTO DE OTRAS CONFIGURACIONES DEL MODULO
      $mform->addElement('header', 'attendancebotfieldset', get_string('attendancebotfieldset', 'mod_attendancebot'));
      // Add standard elements.
      $this->standard_coursemodule_elements();
      
      // Add standard buttons.
      $this->add_action_buttons();
  }

  //FUNCION QUE SETEA LOS DATOS ANTES DE MOSTRAR EL FORM
  function data_preprocessing(&$default_values) {
          
    //SI OBTENGO INFO LO SETEO, SINO VALORES DEFECTO
    if(isset($default_values['clases_finish_time']) && isset($default_values['clases_start_time'])){

      $clases_finish_time = $default_values['clases_finish_time'];
      $clases_start_time = $default_values['clases_start_time'];
 
      $hora_start = timestamp_to_hour($clases_start_time);
      $minutos_start = timestamp_to_minutes($clases_start_time);
      $hora_finish = timestamp_to_hour($clases_finish_time);
      $minutos_finish = timestamp_to_minutes($clases_finish_time);
    }else{
      $hora_start = HORA_COMIENZO;
      $minutos_start = MINUTOS_DEFAULT;
      $hora_finish = HORA_FINALIZACION;
      $minutos_finish = MINUTOS_DEFAULT;
    }

    // SETEO VALORES DEFECTO PARA LAS FECHAS
    $fecha_actual = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    if ($fecha_actual >= FECHA_FIN_PRIMERO){
      $fecha_default_inicio = FECHA_INICIO_SEGUNDO;
      $fecha_default_fin = FECHA_FIN_SEGUNDO;
     }else{
      $fecha_default_inicio = FECHA_INICIO_PRIMERO;
      $fecha_default_fin = FECHA_FIN_PRIMERO;
    }
    //SETEO VALORES DEFECTO SI NO ESTAN SETEADOS
    if (!isset($default_values['enabled'])){
      $default_values['enabled'] = PLUGIN_HABILITADO_DEFAULT;
    }
    if(!isset($default_values['min_percentage'])){
      $default_values['min_percentage'] = PORCENTAJE_MINIMO;
    }
    if(!isset($default_values['late_tolerance'])){
      $default_values['late_tolerance'] = TOLERANCIA_MINIMA;
    }
    //SETEO HORAS Y MINUTOS DEFAULT SI NO ESTAN SETEADOS
    $default_values['clases_finish_hour'] = $hora_finish;
    $default_values['clases_start_hour'] = $hora_start;
    $default_values['clases_finish_minutes'] = $minutos_finish;
    $default_values['clases_start_minutes'] = $minutos_start;
    //SETEO FECHAS DEFAULT SI NO ESTAN SETEADOS
    if(!isset($default_values['clases_start_date'])){
      $default_values['clases_start_date'] = $fecha_default_inicio;
    }
    if(!isset($default_values['clases_finish_date'])){
      $default_values['clases_finish_date'] = $fecha_default_fin;         
    }         
  }
  
  //FUNCION QUE VALIDA LOS DATOS INGRESADOS EN EL FORM
  function validation($data, $files) {
    $errors = parent::validation($data, $files);

    //VALIDACION QUE LOS DATOS NO SEAN NULL
    if(!isset($data['enabled'])){
      $errors['enabled'] = get_string('error_enabled', 'mod_attendancebot');
    }
    if(!isset($data['min_percentage'])){
      $errors['min_percentage'] = get_string('error_min_percentage', 'mod_attendancebot');
    }
    if(!isset($data['late_tolerance'])){
      $errors['late_tolerance'] = get_string('error_late_tolerance', 'mod_attendancebot');
    }
    if(!isset($data['recolection_platform'])){
      $errors['recolection_platform'] = get_string('error_recolection_platform', 'mod_attendancebot');
    }
    if(!isset($data['saving_platform'])){
      $errors['saving_platform'] = get_string('error_saving_platform', 'mod_attendancebot');
    }
    if(!isset($data['clases_start_hour']) || !isset($data['clases_start_minutes'])){
      $errors['clases_start'] = get_string('error_clases_start', 'mod_attendancebot');
    }
    if(!isset($data['clases_finish_hour']) || !isset($data['clases_finish_minutes'])){
      $errors['clases_finish'] = get_string('error_clases_finish', 'mod_attendancebot');
    }
    if(!isset($data['clases_start_date'])){
      $errors['clases_start_date'] = get_string('error_clases_start_date', 'mod_attendancebot');
    }
    if(!isset($data['clases_finish_date'])){
      $errors['clases_finish_date'] = get_string('error_clases_finish_date', 'mod_attendancebot');
    }

    // Validar que la fecha no esta mal ordenada
    if ($data['clases_finish_date'] < $data['clases_start_date']) {
      $errors['clases_finish_date'] = get_string('error_fechafinalizacion', 'mod_attendancebot');
      $errors['clases_start_date'] = get_string('error_fechainicio', 'mod_attendancebot');
    }elseif($data['clases_finish_date'] == $data['clases_start_date']){
      $errors['clases_finish_date'] = get_string('error_fechafinalizacion_igual', 'mod_attendancebot');
      $errors['clases_start_date'] = get_string('error_fechainicio_igual', 'mod_attendancebot');
    }
    
    // Validar que la las horas y minutos esten bien ordenadas
    $attendance_meet_start_time = hour_minutes_to_timestamp($data['clases_start_hour'],$data['clases_start_minutes']);
    $attendance_meet_finish_time = hour_minutes_to_timestamp($data['clases_finish_hour'],$data['clases_finish_minutes']);
    if($attendance_meet_finish_time < $attendance_meet_start_time){
      $errors['clases_finish'] = get_string('error_horaminutos_menor', 'mod_attendancebot');
      $errors['clases_start'] = get_string('error_horaminutos_mayor', 'mod_attendancebot');
    }else if($attendance_meet_start_time == $attendance_meet_finish_time){
      $errors['clases_finish'] = get_string('error_horaminutos_final_igual', 'mod_attendancebot');
      $errors['clases_start'] = get_string('error_horaminutos_comienzo_igual', 'mod_attendancebot');
    }

    // WARNINGS si desactiva la tolerancia, o desactiva el plugin
    if($data['late_tolerance'] == 0){
      \core\notification::warning(get_string('warning_late_tolerance', 'mod_attendancebot'));
    }
    if($data['enabled'] == 0){
      \core\notification::warning(get_string('warning_enabled', 'mod_attendancebot'));
    }

    return $errors;
  }

  //FUNCION QUE SETEA LOS DATOS DESPUES DE ENVIAR EL FROM
  public function data_postprocessing($data) {

    if($data){
      $attendance_meet_start_time = hour_minutes_to_timestamp($data->clases_start_hour,$data->clases_start_minutes);
      $attendance_meet_finish_time = hour_minutes_to_timestamp($data->clases_finish_hour,$data->clases_finish_minutes);

      $data->clases_start_time = $attendance_meet_start_time;
      $data->clases_finish_time = $attendance_meet_finish_time;
    }
    return $data;
  }
}
