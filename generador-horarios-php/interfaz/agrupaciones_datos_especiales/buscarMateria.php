<?php
chdir(dirname(__FILE__));
include 'config.php';
require_once '../../reglas_negocio/ManejadorAgrupaciones.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorSesion.php';

if (isset($_GET)){
    ManejadorSesion::sec_session_start();
    if(isset($_GET['term'])){
        $buscarComo = $_GET['term'];        
        if($ciclo==1){
              echo json_encode(ManejadorAgrupaciones::buscarAgrupacionSoloNombre($buscarComo,$_SESSION['id_departamento'],$año,"impar"));                  
//            echo json_encode(ManejadorAgrupaciones::buscarAgrupacion($buscarComo,$_SESSION['id_departamento'],$año,"impar"));                
        }else{
              echo json_encode(ManejadorAgrupaciones::buscarAgrupacionSoloNombre($buscarComo,$_SESSION['id_departamento'],$año,"par"));                  
//            echo json_encode(ManejadorAgrupaciones::buscarAgrupacion($buscarComo,$_SESSION['id_departamento'],$año,"par"));                
        }        
    }       
}


