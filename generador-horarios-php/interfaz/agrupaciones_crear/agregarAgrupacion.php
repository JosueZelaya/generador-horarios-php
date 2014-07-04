<?php

chdir(dirname(__FILE__));
include 'config.php';
require_once '../../reglas_negocio/MateriaAgrupacion.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorAgrupaciones.php';


if(isset($_GET)){
    if(isset($_GET['materias'])){        
        $arrayMaterias = utf8_encode($_GET['materias']);                
        $arrayMaterias = json_decode($arrayMaterias,true);                        
        if($arrayMaterias!=NULL){
            if(count($arrayMaterias[0])<=0){
                echo json_encode("Debe añadir materias a la agrupacion");
            }else{
                $arrayMaterias = $arrayMaterias[0];
                $materias = array();
                for ($i = 0; $i < count($arrayMaterias); $i++) {
                    $materia = new MateriaAgrupacion();
                    $materia->setCodigo($arrayMaterias[$i]["codigo"]);
                    $materia->setCarrera($arrayMaterias[$i]["carrera"]);
                    $materia->setDepartamento($arrayMaterias[$i]["departamento"]);
                    $materia->setPlan_estudio($arrayMaterias[$i]["plan_estudio"]);
                    $materias[]=$materia;                    
                }
                try{
                    ManejadorAgrupaciones::agregarAgrupacion($materias,$año,$ciclo);                    
                    echo json_encode("exito");
                }catch(Exception $e){
                    echo json_encode($e->getMessage());
                }
            }            
        }else{
            echo json_encode("No se pudieron agregar las materias");
        }
    }
}