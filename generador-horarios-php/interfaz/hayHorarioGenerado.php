<?php

session_start();

if(isset($_SESSION['facultad'])){
    $respuesta = "si";
    echo json_encode($respuesta);
}else{
    $respuesta = "no";
    echo json_encode($respuesta);
}
