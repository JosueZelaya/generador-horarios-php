<?php
chdir(dirname(__FILE__));
require_once "../../../reglas_negocio/ManejadorMaterias.php";
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';

if (isset($_GET)){
    ManejadorSesion::sec_session_start();
    if(isset($_GET['term'])){        
        $buscarComo = $_GET['term'];        
            echo json_encode(ManejadorMaterias::buscarMateriaParaAgrupar($buscarComo,"todos",$_SESSION['id_departamento']));                
    }       
}