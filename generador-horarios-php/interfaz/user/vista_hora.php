<?php
include_once 'funciones.php';
chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/ManejadorHoras.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/Facultad.php';
    chdir(dirname(__FILE__));
ManejadorSesion::sec_session_start();
$facultad = $_SESSION['facultad'];

if(isset($_GET['dia']) && isset($_GET['hora']) && isset($_GET['depar']) ){
    $idDia = $_GET['dia'];
    $idHora = $_GET['hora'];
    $idDepar = $_GET['depar'];    
    if($idDepar=="todos"){
        $horario = ManejadorHoras::getHorarioHora_depar($idDia, $idHora, $idDepar, $facultad);
        echo imprimirMallaHora($horario,TRUE);
    }else{
        $horario = ManejadorHoras::getHorarioHora_depar($idDia, $idHora, $idDepar, $facultad);
        echo imprimirMallaHora($horario,FALSE);
    }
}