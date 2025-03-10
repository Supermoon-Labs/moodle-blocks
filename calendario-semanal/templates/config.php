<?php

function load_template($config) {
  $mustache = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_ArrayLoader($loader),
  ));
    
  // Define la plantilla que se cargará en el layout
  $template = $config["template"]; // Cambia esto dinámicamente
  
  // Renderiza el layout con los datos
  echo $mustache->render($template, $config["data"]);
}
