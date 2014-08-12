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
    $reservaciones;
    for ($i = $hi; $i <= $hf; $i++) {
        $reservaciones[] = new Reservacion(getIdDia($dia),$i,$aula);
    }
    try{
        ManejadorReservaciones::nuevaReserva($reservaciones,$año, $ciclo);
        echo json_encode("ok");
    } catch (Exception $ex) {
        echo json_encode($ex->getMessage());
    }    
    $facultad = $_SESSION['facultad'];
    $reservaciones = ManejadorReservaciones::getTodasReservaciones($año,$ciclo);
    ManejadorReservaciones::asignarRerservaciones($reservaciones, $facultad->getAulas());
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

