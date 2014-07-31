<?php
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
ManejadorSesion::sec_session_start();

$u = file_get_contents("../../horarios_guardados/facultad");
if(!$u){
    $respuesta = "fallo";
    echo json_encode($respuesta);
}else{
    $facultad = unserialize($u);
    $_SESSION['facultad'] = $facultad;
    $respuesta = "exito";
    echo json_encode($respuesta);
}
