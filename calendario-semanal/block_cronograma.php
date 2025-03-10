<?php
class block_calendario_semanal extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_calendario_semanal');
    }

    public function get_content() {
        global $COURSE, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $calendario_semanal_url = new moodle_url('/blocks/calendario_semanal/view.php', ['id' => $COURSE->id]);
        $button = html_writer::tag('button', get_string('open_schedule', 'block_calendario_semanal'), [
            'onclick' => "window.open('$calendario_semanal_url', '_blank')",
            'style' => 'padding:10px 20px; font-size:16px; cursor:pointer; background-color:#0073e6; color:white; border:none; border-radius:5px;'
        ]);

        $this->content = new stdClass();
        $this->content->text = $button;
        $this->content->footer = '';

        return $this->content;
    }
}
