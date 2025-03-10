<?php
require_once(__DIR__ . '/../../config.php');
global $DB, $COURSE;


load_template([
    "path" => __DIR__,
    "template" => "layout",
    "components" => [
        "page"
    ],
    "data" => []
  ]);