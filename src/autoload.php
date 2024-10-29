<?php

spl_autoload_register(function($class){
    
    $namespace = 'WCAT\\';
    if(strpos($class, $namespace) !== 0) return;
    
    $class = str_replace($namespace, '', $class);
    $class = str_replace('\\', '/', $class);
    
    $class_path = WCAT_ROOT . '/src/classes/' . $class . '.php';
    
    if(file_exists($class_path)) require_once $class_path;
});
