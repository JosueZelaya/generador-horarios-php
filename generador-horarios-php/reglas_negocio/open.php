<?php

chdir(dirname(__FILE__));
include_once 'Facultad.php';
session_start();

$u = file_get_contents("../horarios_guardados/facultad");
if(!$u){
    $respuesta = "fallo";
    echo json_encode($respuesta);
}else{
    $facultad = unserialize($u);
    $_SESSION['facultad'] = $facultad;
    $respuesta = "exito";
    echo json_encode($respuesta);
}
