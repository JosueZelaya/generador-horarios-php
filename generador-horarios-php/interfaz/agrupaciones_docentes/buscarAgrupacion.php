<?php

chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorAgrupaciones.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

ManejadorSesion::sec_session_start();

if (isset($_GET)){
    if(isset($_GET['term'])){
        $buscarComo = $_GET['term'];     
        if($ciclo==1){
            echo json_encode(ManejadorAgrupaciones::buscarAgrupacion($buscarComo,$_SESSION['id_departamento'],$año,"impar"));                
        }else{
            echo json_encode(ManejadorAgrupaciones::buscarAgrupacion($buscarComo,$_SESSION['id_departamento'],$año,"par"));                
        }
        
    }       
}


