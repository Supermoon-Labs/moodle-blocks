<?php
class block_teacher_info extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_teacher_info');
    }

    public function get_content() {
        global $COURSE, $DB, $USER, $OUTPUT, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;

        // Obtener el docente del curso
        $context = context_course::instance($COURSE->id);
        $teachers = get_enrolled_users($context, 'mod/assign:grade', 0, 'u.id, u.firstname, u.lastname, u.email, u.picture, u.imagealt');

        if (empty($teachers)) {
            $this->content->text = get_string('noteacherfound', 'block_teacher_info');
            return $this->content;
        }

        $teacher = reset($teachers); // Tomamos el primer docente

        // Renderizar imagen del docente
        //$user_picture = new user_picture($teacher);
        //$user_picture->size = 100; // Optional: Set the picture size (100px)
        // Generate the URL for the user picture
        //$teacher_picture = $user_picture->get_url($PAGE)->out();

        // Construir contenido del bloque
        $this->content->text = html_writer::div(
            html_writer::tag('h3', get_string('teacher', 'block_teacher_info')) .
            //html_writer::div($teacher_picture) .
            html_writer::tag('p', $teacher->firstname . ' ' . $teacher->lastname) .
            html_writer::start_div('teacher-links') .
            html_writer::link(new moodle_url('/user/profile.php', ['id' => $teacher->id]), get_string('cv', 'block_teacher_info')) . ' | ' .
            html_writer::link(new moodle_url('/calendar/view.php', ['id' => $teacher->id]), get_string('calendar', 'block_teacher_info')) . ' | ' .
            html_writer::link('mailto:' . $teacher->email, get_string('email', 'block_teacher_info')) .
            html_writer::end_div(),
            'teacher-info-block'
        );

        return $this->content;
    }
}