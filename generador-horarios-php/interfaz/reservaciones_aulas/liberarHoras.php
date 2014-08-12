<?php
chdir(dirname(__FILE__));
include '../../reglas_negocio/ManejadorReservaciones.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

if($_GET){    
    ManejadorSesion::sec_session_start();
    $dia = $_GET['dia'];
    $hi = $_GET['hora_inicio'];
    $hf = $_GET['hora_fin'];
    $aula = $_GET['aula'];    
    try{
        ManejadorReservaciones::liberarHoras(getIdDia($dia), $aula, $hi, $hf, $año, $ciclo);
        echo json_encode("ok");
        $facultad = $_SESSION['facultad'];
        ManejadorReservaciones::limpiarReservaciones($facultad->getAulas());
        $reservaciones = ManejadorReservaciones::getTodasReservaciones($año,$ciclo);
        ManejadorReservaciones::asignarRerservaciones($reservaciones, $facultad->getAulas());
    } catch (Exception $ex) {
        echo json_encode($ex->getMessage());
    }        
}

function getIdDia($dia){
    switch ($dia) {
        case "lunes":
            return 1;
        case "martes":
            return 2;
        case "miercoles":
            return 3;
        case "jueves":
            return 4;
        case "viernes":
            return 5;
        case "sabado":
            return 6;
        case "domingo":
            return 7;        
        default:
            break;
    }
}
