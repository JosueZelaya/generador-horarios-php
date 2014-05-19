<?php

require_once '../reglas_negocio/ManejadorAulas.php';
require_once '../reglas_negocio/Facultad.php';

session_start();

$facultad = $_SESSION['facultad'];

if(isset($_GET['carrera']) && isset($_GET['departamento'])){
    
    $carrera = $_GET['carrera'];
    if($carrera=='todos'){
        $idDepartamento = $_GET['departamento'];    
        if($idDepartamento=='todos'){
            echo "<option value='todos'></option>";
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
        echo "<option value='todos'></option>";
    }else{
        $aulas = ManejadorAulas::getAulasDepartamento($idDepartamento,$facultad);                    
        for ($index = 0; $index < count($aulas); $index++) {
            echo "<option value='".$aulas[$index]."'>".$aulas[$index]."</option>";
        }       
    }
}
