<?php
include_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
ManejadorSesion::sec_session_start();

if(isset($_SESSION['facultad'])){
    $respuesta = "si";
    echo json_encode($respuesta);
}else{
    $respuesta = "no";
    echo json_encode($respuesta);
}
