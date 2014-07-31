<?php

chdir(dirname(__FILE__));
include 'config.php';
require_once '../../reglas_negocio/ManejadorMaterias.php';

if (isset($_GET)){
    if(isset($_GET['agrupacion'])){
        $agrupacion = $_GET['agrupacion'];  
        $materias = ManejadorMaterias::getMateriasDeAgrupacion($agrupacion, $año, $ciclo);
        $cont=0;
        $arrayMaterias=array();           
        foreach ($materias as $materia){                     
            $arrayMaterias[$cont]["agrupacion"] = $agrupacion;
            $arrayMaterias[$cont]["codigo"] = $materia->getCodigo();
            $arrayMaterias[$cont]["nombre"] = $materia->getNombre();
            $arrayMaterias[$cont]["carrera"] = $materia->getCarrera()->getNombre();   
            $arrayMaterias[$cont]["id_carrera"] = $materia->getCarrera()->getCodigo();
            $arrayMaterias[$cont]["plan_estudio"] = $materia->getPlan_estudio();
            $arrayMaterias[$cont]["departamento"] = $materia->getDepartamento()->getNombre();            
            $cont++;
        }
        echo json_encode($arrayMaterias);
    }
}