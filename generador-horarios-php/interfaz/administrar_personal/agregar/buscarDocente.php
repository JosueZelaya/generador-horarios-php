<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDocentes.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';

ManejadorSesion::sec_session_start();

if (isset($_GET)){
    if(isset($_GET['term'])){
        $buscarComo = $_GET['term'];        
        echo json_encode(ManejadorDocentes::buscarDocentes($buscarComo,$_SESSION['id_departamento']));                
    }       
}
