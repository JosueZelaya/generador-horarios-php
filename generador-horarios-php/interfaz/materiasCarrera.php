<?php

require_once '../reglas_negocio/ManejadorMaterias.php';
require_once '../reglas_negocio/ManejadorCarreras.php';
require_once '../reglas_negocio/Facultad.php';

session_start();

$facultad = $_SESSION['facultad'];

if(isset($_GET['carrera'])  && isset($_GET['departamento'])){
    $carrera = $_GET['carrera'];
    
    if($carrera=='todos'){
            echo "<option value='todos'></option>";
//        $idDepartamento = $_GET['departamento'];    
//        if($idDepartamento=='todos'){
//            echo "<option value='todos'></option>";
//        }else{
//            $aulas = ManejadorAulas::getAulasDepartamento($idDepartamento,$facultad);                    
//            for ($index = 0; $index < count($aulas); $index++) {
//                echo "<option value='".$aulas[$index]."'>".$aulas[$index]."</option>";
//            }       
//        }
    }else{
        $idCarrera = ManejadorCarreras::getCodigoCarrera($carrera);
        $materias = ManejadorMaterias::getMateriasDeCarrera($facultad->getMaterias(), $idCarrera);        
        foreach ($materias as $materia) {
            echo "<option value='".$materia->getCodigo()."'>".$materia->getNombre()."</option>";
        }
    }
    
}
