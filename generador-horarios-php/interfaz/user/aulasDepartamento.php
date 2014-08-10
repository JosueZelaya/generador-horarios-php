<?php

chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorAulas.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
ManejadorSesion::sec_session_start();

$facultad = $_SESSION['facultad'];

if(isset($_GET['carrera']) && isset($_GET['departamento'])){
    
    $carrera = $_GET['carrera'];
    if($carrera=='todos'){
        $idDepartamento = $_GET['departamento'];    
        if($idDepartamento=='todos'){
            $aulas = $facultad->getAulas();
            for ($index = 0; $index < count($aulas); $index++) {    
                echo "<option value='".$aulas[$index]->getNombre()."'>".$aulas[$index]->getNombre()."</option>";    
            }
        }else{
            $aulas = ManejadorAulas::getAulasDepartamento($idDepartamento,$facultad);                    
            for ($index = 0; $index < count($aulas); $index++) {
                echo "<option value='".$aulas[$index]."'>".$aulas[$index]."</option>";
            }       
        }
    }else{
        $ids_agrupaciones = ManejadorAgrupaciones::obtenerAgrupacionesDeCarrera($carrera);
        $aulas = ManejadorAulas::getAulasCarrera($ids_agrupaciones, $facultad);                            
        for ($index = 0; $index < count($aulas); $index++) {
            echo "<option value='".$aulas[$index]."'>".$aulas[$index]."</option>";
        }       
    }
}else if(isset($_GET['departamento'])){
    $idDepartamento = $_GET['departamento'];    
    if($idDepartamento=='todos'){
        $aulas = $facultad->getAulas();
        for ($index = 0; $index < count($aulas); $index++) {    
            echo "<option value='".$aulas[$index]->getNombre()."'>".$aulas[$index]->getNombre()."</option>";    
        }        
    }else{
        $aulas = ManejadorAulas::getAulasDepartamento($idDepartamento,$facultad);                    
        for ($index = 0; $index < count($aulas); $index++) {
            echo "<option value='".$aulas[$index]."'>".$aulas[$index]."</option>";
        }       
    }
}
