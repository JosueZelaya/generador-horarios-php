<?php
chdir(dirname(__FILE__));
require_once "../../../reglas_negocio/ManejadorDepartamentos.php";
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';

if (isset($_GET)){
    ManejadorSesion::sec_session_start();
    if(isset($_GET['term'])){        
        $buscarComo = $_GET['term'];   
        $departamentos = ManejadorDepartamentos::buscarDepartamento($buscarComo,"activos");
        $array_departamentos=array();
        foreach ($departamentos as $departamento) {            
            $array_departamentos[] = array("value"=>$departamento->getNombre(),"id"=>$departamento->getId());
        }        
        echo json_encode($array_departamentos);
    }       
}