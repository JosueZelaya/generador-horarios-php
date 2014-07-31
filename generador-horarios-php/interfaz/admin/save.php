<?php
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
ManejadorSesion::sec_session_start();
$facultad = $_SESSION['facultad'];
$s = serialize($facultad);

$u = file_put_contents("../../horarios_guardados/facultad", $s);

if(!$u){
    $respuesta = "fallo";
    echo json_encode($respuesta);
}else{
    $respuesta = "exito";
    echo json_encode($respuesta);
}
