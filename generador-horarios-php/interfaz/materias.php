<?php

require_once '../reglas_negocio/ManejadorMaterias.php';
require_once '../reglas_negocio/ManejadorCarreras.php';
require_once '../reglas_negocio/Facultad.php';

session_start();

$facultad = $_SESSION['facultad'];

if(isset($_GET['carrera'])  && isset($_GET['departamento'])){
    $carrera = $_GET['carrera'];
    $departamento = $_GET['departamento'];
    if($carrera=='todos'){
        $idCarrera = ManejadorCarreras::getCodigoCarrera($carrera);
        $materias = ManejadorMaterias::obtenerMateriasDeDepartamento($facultad->getMaterias(),$departamento );        
        foreach ($materias as $materia) {
            echo "<option value='".$materia->getCodigo()."'>".$materia->getNombre()."</option>";
        }
    }else{
        $idCarrera = ManejadorCarreras::getCodigoCarrera($carrera);
        $materias = ManejadorMaterias::getMateriasDeCarrera($facultad->getMaterias(), $idCarrera);        
        foreach ($materias as $materia) {
            echo "<option value='".$materia->getCodigo()."'>".$materia->getNombre()."</option>";
        }
    }
    
}
