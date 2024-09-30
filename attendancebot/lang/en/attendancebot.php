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
 * Plugin strings are defined here.
 *
 * @package     mod_attendancebot
 * @category    string
 * @copyright   2024 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'ORT Attendance Bot';
$string['modulename'] = 'ORT Attendance Bot Module';
$string['modulenameplural'] = 'ORT Attendance Bot Modules';
$string['pluginadministration'] = 'ORT Attendance Bot Administration';
$string['attendancebotname'] = 'Nombre';
$string['attendancebotsettings'] = 'Configuración';
$string['attendancebotfieldset'] = 'Otras configuraciones';
$string['meeting_processer_task'] = 'Tarea de Procesamiento de Reuniones';
$string['scheduler_task_name'] = 'Programadora de las Tareas de Procesamiento de asistencia automatica de clases';
$string['attendancebot_settings'] = 'Configuración de Attendance Automático';
//STRINGS DEL FORM CONFIGURATION
$string['form_enable_settings'] = 'Plugin Habilitado';
$string['form_enabledescription_settings'] = 'Marca la casilla para habilitarlo';
$string['form_percentage_settings'] = 'Seleccione el porcentaje minimo para estar presente';
$string['form_recolection_platform'] = 'Seleccione la plataforma donde se va a recolar la informacion';
$string['form_saving_platform'] = 'Seleccione la plataforma donde se va a guardar la informacion';
$string['form_late_tolerance'] = 'Seleccione la tolerancia de llegada tarde';
$string['form_clases_start_date'] = 'Fecha de Comienzo de clases';
$string['form_clases_finish_date'] = 'Fecha de Finalizacion de clases';
$string['form_clases_start'] = 'Hora y Minuto del comienzo de la reunión';
$string['form_clases_finish'] = 'Hora y Minuto de la finalizacion de la reunión';
$string['form_late_tolerance_text'] = ' minutos (al seleccionar 0, no habrá tolerancia de llegada tarde)';
$string['form_min_percentage_text'] = ' %';
//HELPERS DE FORM
$string['enabled'] = 'plugin habilitado';
$string['enabled_help'] = 'Si la casilla esta marcada, el plugin está habilitado, si esta desmarcada, está deshabilitado';
$string['min_percentage'] = 'porcentaje minimo para estar presente';
$string['min_percentage_help'] = 'Elija el porcentaje minimo de un estudiante para que se lo considere presente: valor de 0 a 100';
$string['late_tolerance'] = 'tolerancia para no llegar tarde';
$string['late_tolerance_help'] = 'Elija tolerancia en minutos para el cual a un estudiante se lo considera presente';
$string['recolection_platform'] = 'plataforma de obtención de datos';
$string['recolection_platform_help'] = 'Plataforma de donde se obtendrán los datos. Por defecto: zoom';
$string['saving_platform'] = 'plataforma donde se guardará el presente';
$string['saving_platform_help'] = 'Plataforma donde se guardará el presente. Por defecto: attendance';
$string['clases_start_date'] = 'fecha de inicio de clases';
$string['clases_start_date_help'] = 'Fecha en el que plugin empezará a poner los presentes';
$string['clases_finish_date'] = 'fecha de finalizacion de clases';
$string['clases_finish_date_help'] = 'Fecha en el que plugin terminará de poner los presentes';
$string['clases_start'] = 'tiempo de comienzo de la clase';
$string['clases_start_help'] = 'Hora y minutos en que comienza la clase';
$string['clases_finish'] = 'tiempo de finalizacion de la clase';
$string['clases_finish_help'] = 'Hora y minutos en que termina la clase';
//STRINGS DE INFORMACION EN VIEW
$string['text_title'] = 'Instrucciones de utilización de AttendanceBot';
$string['text_descripcion_1'] = 'Attendancebot es un plugin que se instala en un curso, y funcionará de forma automática en segundo plano a través de un cron job. El cron job se implementó de forma que funcione a la 1am, y levanta un scheduler.';
$string['text_descripcion_2'] = 'El scheduler se ejecuta cada 24hs, y por cada curso, donde esta instalado el plugin, levanta una ad-hoc task, el cual es el encargado de calcular el presentismo a las personas que pertenecen a un curso, para todos los grupos.';
$string['text_instrucciones'] = '
    <p>Para la correcta utilización de este, se deberá programar, a través de un formulario, las configuraciones necesarias. Para ello, se deberá ir a la pestaña ‘Settings’ o ‘Configuración’, en la sección ‘Configuración de Attendance Automático’:</p>
    <ul>
        <li><strong>Plugin habilitado:</strong> Si la opción esta tildada, el plugin no funcionará para el curso donde esta instalado.</li>
        <li><strong>Porcentaje mínimo de presente:</strong> Este valor, porcentual de 0 a 100%, indica el porcentaje para que una persona se considere presente. Este porcentaje es en base a la duración de la reunión.</li>
        <li><strong>Tolerancia de llegada tarde:</strong> Este valor, que va de 0 a 60 minutos, indica los minutos que la persona tiene para que no se le considere tarde. Si elige 0 minutos, entones se desactivará esta opción, y no habrá llegadas tarde.</li>
        <li><strong>Plataforma de recolección de información:</strong> Plugin/Plataforma de donde se obtiene la información para la asistencia. En este caso, será Zoom, pero en algún futuro se puede llevar a Meet y Teams.</li>
        <li><strong>Plataforma de persistencia de información:</strong> Plugin/Plataforma de donde se persiste la información para la asistencia. En este caso, será Attendance.</li>
        <li><strong>Fecha de comienzo de clases:</strong> Fecha de inicio de clases (el plugin dejará de tomar asistencia, previo a este valor).</li>
        <li><strong>Fecha de finalización de clases:</strong> Fecha de finalización de clases (el plugin dejará de tomar asistencia pasado este valor).</li>
        <li><strong>Hora y minutos del comienzo de la reunión:</strong> Horas y minutos del comienzo de la reunión (utilizado para crear una sesión de attendance).</li>
        <li><strong>Hora y minutos de la finalización de la reunión:</strong> Horas y minutos de la finalización de la reunión (utilizado para crear una sesión de attendance).</li>
    </ul>
';
$string['text_mensaje_warning'] = 'AttendanceBot depende de Attendance para persistir la información, ya que el mismo crea una sessión para guardar la asistencia, por lo que si se desinstala Attendance en el curso, se mostrará un mensaje de warning, ya que el plugin no funcionará correctamente';

//STRINGS DE ERROR
$string['pluginmissingfromcourse'] = 'El plugin "{$a}" no está instalado en este curso.';
$string['pluginalredyoncourse'] = 'El plugin "{$a}" ya está instalado en este curso.';
$string['errornotifacationattadance'] = 'CUIDADO, no exista el plugin de attendance instalado, y sin eso, attedancebot no funcionará correctamente';
//STRINGS DE ERRORES EN EL FORM
$string['error_fechainicio'] = 'ERROR: La fecha de comienzo no puede ser mayor que la de finalizacion';
$string['error_fechainicio_igual'] = 'ERROR: La fecha de comienzo no puede ser igual que la de finalizacion';
$string['error_fechafinalizacion'] = 'ERROR: La fecha de finalizacion no puede ser menor que la de comienzo';
$string['error_fechafinalizacion_igual'] = 'ERROR: La fecha de finalizacion no puede ser igual que la de comienzo';
$string['error_horaminutos_final_igual'] = 'ERROR: Las horas y minutos de finalizacion no puede ser igual que las de comienzo';
$string['error_horaminutos_comienzo_igual'] = 'ERROR: Las horas y minutos de comienzo no puede ser igual que las de finalizacion';
$string['error_horaminutos_mayor'] = 'ERROR: Las horas y minutos no puede ser mayor que las de finalizacion';
$string['error_horaminutos_menor'] = 'ERROR: Las horas y minutos no puede ser menor que las de comienzo';
//STRINGS DE ERROES POR NULL TYPE EN EL FORM
$string['error_enabled'] = 'ERROR: El valor no puede ser null';
$string['error_min_percentage'] = 'ERROR: El porcentaje de presente no puede ser null';
$string['error_late_tolerance'] = 'ERROR: La tolerancia de llegada tarde no puede ser null';
$string['error_recolection_platform'] = 'ERROR: La platafroma de donde se recoleca la información no puede ser null';
$string['error_saving_platform'] = 'ERROR: La platafroma de donde se guarda la información no puede ser null';
$string['error_clases_start'] = 'ERROR: Las horas y minutos del comienzo de la reunión no puede ser null';
$string['error_clases_finish'] = 'ERROR: Las horas y minutos de la finalización de la reunión no puede ser null';
$string['error_clases_start_date'] = 'ERROR: La fecha de comienzo de clases no puede ser null';
$string['error_clases_finish_date'] = 'ERROR: La fecha de finalización de clases no puede ser null';
//STRINGS DE WARNINGS EN EL FORM
$string['warning_late_tolerance'] = 'CUIDADO: Está desactivada la tolerancia de llegada tarde';
$string['warning_enabled'] = 'CUIDADO: Está desactivado el plugin de asistencia automática';


