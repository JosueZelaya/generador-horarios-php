<?php
chdir(dirname(__FILE__));
require_once "../../reglas_negocio/ManejadorMaterias.php";
include 'config.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorSesion.php';

if (isset($_GET)){
    ManejadorSesion::sec_session_start();
    if(isset($_GET['term'])){        
        $buscarComo = $_GET['term'];        
        if($ciclo==1){
            echo json_encode(ManejadorMaterias::buscarMateriaParaAgrupar($buscarComo,"impar",$_SESSION['id_departamento']));
        }else{
            echo json_encode(ManejadorMaterias::buscarMateriaParaAgrupar($buscarComo,"par",$_SESSION['id_departamento']));
        }        
    }       
}