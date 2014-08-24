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
        $facultad="";
        if(isset($_SESSION['reservaciones_facultad'])){
            $facultad = $_SESSION['reservaciones_facultad'];
        }else{
            $facultad = asignarInfo($año, $ciclo);
        }    
        ManejadorReservaciones::limpiarReservaciones($facultad->getAulas());
        $reservaciones = ManejadorReservaciones::getTodasReservaciones($año,$ciclo);
        ManejadorReservaciones::asignarRerservaciones($reservaciones, $facultad->getAulas());
    } catch (Exception $ex) {
        echo json_encode($ex->getMessage());
    }        
}

function asignarInfo($año,$ciclo) {
    $facultad = new Facultad(ManejadorDepartamentos::getDepartamentos(),  ManejadorCargos::obtenerTodosCargos(), ManejadorReservaciones::getTodasReservaciones($año,$ciclo),$año,$ciclo);
    $facultad->setAgrupaciones(ManejadorAgrupaciones::getAgrupaciones($año, $ciclo, $facultad->getAulas()));
    $facultad->setDocentes(ManejadorDocentes::obtenerTodosDocentes($facultad->getCargos(),$año,$ciclo,$facultad->getDepartamentos()));
    $facultad->setCarreras(ManejadorCarreras::getTodasCarreras($facultad->getDepartamentos()));
    $facultad->setGrupos(ManejadorGrupos::obtenerGrupos($año, $ciclo, $facultad->getAgrupaciones(), $facultad->getDocentes()));
    $facultad->setMaterias(ManejadorMaterias::getTodasMaterias($ciclo,$año,$facultad->getAgrupaciones(),$facultad->getCarreras(),$facultad->getAulas()));
    ManejadorReservaciones::asignarRerservaciones($facultad->getReservaciones(),$facultad->getAulas());
    return $facultad;
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
