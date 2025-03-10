<?php
require_once('../../config.php');
global $DB, $COURSE;

$courseid = required_param('id', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseid], 'startdate');
if (!$course) {
    print_error('nocourse', 'block_calendario_semanal');
}

$startdate = $course->startdate;
$weeks = 40;

// Generar calendario_semanal en HTML
echo "<html><head><title>Calendario semanal del Curso</title></head><body>";
echo "<h2>calendario_semanal del Curso</h2><table border='1'>";
echo "<tr><th>Semana</th><th>Inicio</th><th>Fin</th></tr>";
for ($i = 1; $i <= $weeks; $i++) {
    $week_start = strtotime("+" . ($i - 1) . " weeks", $startdate);
    $week_end = strtotime("+6 days", $week_start);
    echo "<tr><td>Semana $i</td><td>" . date('d-m-Y', $week_start) . "</td><td>" . date('d-m-Y', $week_end) . "</td></tr>";
}
echo "</table></body></html>";
