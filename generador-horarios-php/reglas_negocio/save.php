<?php
session_start();
include_once 'Facultad.php';
$facultad = $_SESSION['facultad'];
$s = serialize($facultad);
$u = file_put_contents("../horarios_guardados/facultad", $s);

if(!$u){
    $respuesta = "fallo";
    echo json_encode($respuesta);
}else{
    $respuesta = "exito";
    echo json_encode($respuesta);
}
