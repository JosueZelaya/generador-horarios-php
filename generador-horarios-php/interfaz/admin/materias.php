<?php
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorMaterias.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorCarreras.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));

session_start();

$facultad = $_SESSION['facultad'];

if(isset($_GET['carrera'])  && isset($_GET['departamento'])){
    $carrera = $_GET['carrera'];
    $departamento = $_GET['departamento'];
    if($carrera=='todos'){
        $materias = ManejadorMaterias::obtenerMateriasDeDepartamento($facultad->getMaterias(),$departamento);        
        foreach ($materias as $materia) {
            echo "<option value='".$materia->getCodigo()."'>".$materia->getNombre()."</option>";
        }
    }else{
        $materias = ManejadorMaterias::getMateriasDeCarrera($facultad->getMaterias(), $carrera);
        foreach ($materias as $materia) {
            echo "<option value='".$materia->getCodigo()."'>".$materia->getNombre()."</option>";
        }
    }
    
}