<?php
chdir(dirname(__FILE__));
require_once 'config.php';
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorMaterias.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Materia.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Carrera.php';
chdir(dirname(__FILE__));

if(session_status()==PHP_SESSION_NONE){
    ManejadorSesion::sec_session_start();
}

if($_GET){   
    if(isset($_GET['codigo']) && isset($_GET['plan'])&& isset($_GET['carrera'])){        
        try{
            $codigo = $_GET['codigo'];
            $plan = $_GET['plan'];
            $id_carrera = $_GET['carrera']; 
            $carrera = new Carrera($id_carrera,$plan,"","");
            $materia = new Materia($codigo,"","","",$carrera,"","");
            $materia->setPlan_estudio($plan);            
            ManejadorMaterias::eliminarMateria($materia, $año, $ciclo);
            if(isset($_SESSION['datos_tabla_materias'])){
                $materias = $_SESSION['datos_tabla_materias'];
                quitarDeArray($materias, $materia);
            }            
            echo json_encode("ok");
        }catch(Exception $e){
            echo json_encode($e->getMessage());
        } 
    }    
}

function quitarDeArray($materias,$materia){
    $materiasN = array();
    foreach ($materias as $materiaX) {
        if($materia->getCodigo()!=$materiaX->getCodigo() && $materia->getPlan_estudio()!=$materiaX->getPlan_estudio() && $materia->getCarrera()->getCodigo()!=$materiaX->getCarrera()->getCodigo()){
            $materiasN[] = $materiaX;
        }
    }
    $_SESSION['datos_tabla_materias'] = $materiasN;
}