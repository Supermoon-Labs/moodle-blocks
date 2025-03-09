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
        $can_upload_cv = true;

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
        $user = core_user::get_user($teacher->id);
        $user_picture = new user_picture($user);
        $user_picture->size = 100; // Optional: Set the picture size (100px)
        // Generate the URL for the user picture
        $teacher_picture_div = $user_picture->get_url($PAGE)->out();

        $teacher_picture_div = html_writer::tag('img', '', [
            'src' => $teacher_picture_div,
            'class' => 'userpicture',
            'width' => 100,
            'height' => 100
        ]);

        if ($can_upload_cv) {
            if (isset($_FILES['cv'])) {
                $fs = get_file_storage();
                $existing_files = $fs->get_area_files($context->id, 'block_teacher_info', 'cv', $teacher->id, 'itemid, filepath, filename', false);
                foreach ($existing_files as $existing_file) {
                    if ($existing_file->get_filename() === $_FILES['cv']['name']) {
                        $existing_file->delete();
                    }
                }
                $fileinfo = array(
                    'contextid' => $context->id,
                    'component' => 'block_teacher_info',
                    'filearea' => 'cv',
                    'itemid' => $teacher->id,
                    'filepath' => '/',
                    'filename' => $_FILES['cv']['name']
                );
                $file = $fs->create_file_from_pathname($fileinfo, $_FILES['cv']['tmp_name']);
                $this->content->text .= html_writer::div(get_string('cvchanged', 'block_teacher_info'), 'alert alert-success');
            }
        }

        // Obtener el archivo CV del docente
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'block_teacher_info', 'cv', $teacher->id, 'itemid, filepath, filename', false);
        $cv_file = reset($files);

        $cv_link = '';
        if ($cv_file != false) {
            $cv_url = moodle_url::make_pluginfile_url($cv_file->get_contextid(), $cv_file->get_component(), $cv_file->get_filearea(), $cv_file->get_itemid(), $cv_file->get_filepath(), $cv_file->get_filename());
            $cv_link .= html_writer::link('#', get_string('downloadcv', 'block_teacher_info'), [
                'class' => 'btn btn-secondary',
                'onclick' => "downloadFileInBackground('$cv_url'); return false;"
            ]);
        }

        if ($can_upload_cv) {
            $cv_link .= html_writer::tag('form', 
                html_writer::tag('input', '', ['type' => 'file', 'name' => 'cv', 'id' => 'cv-input', 'style' => 'display:none;', 'onchange' => 'this.form.submit();']) .
                html_writer::tag('button', get_string('uploadcv', 'block_teacher_info'), ['type' => 'button', 'class' => 'btn btn-primary', 'onclick' => "document.getElementById('cv-input').click();"]),
                ['method' => 'post', 'enctype' => 'multipart/form-data']
            );
        }

        // Construir contenido del bloque
        $this->content->text = html_writer::div(
            html_writer::div($teacher_picture_div, 'teacher-picture') .
            html_writer::tag('p', $teacher->firstname . ' ' . $teacher->lastname) .
            $cv_link .
            html_writer::end_div()
        );

        // Add JavaScript for background download
        $this->content->text .= html_writer::script("
            function downloadFileInBackground(url) {
                var iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = url;
                document.body.appendChild(iframe);
            }
        ");

        return $this->content;
    }
}