<?php
chdir(dirname(__FILE__));
require_once 'config.php';
require_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorAgrupaciones.php';
chdir(dirname(__FILE__));

if(session_status()==PHP_SESSION_NONE){
    ManejadorSesion::sec_session_start();
}

if($_GET){   
    if(isset($_GET['agrupacion'])){        
        try{
            $id = $_GET['agrupacion'];
            ManejadorAgrupaciones::eliminarAgrupacionPorId($id, $año, $ciclo);
            echo json_encode("ok");
        }catch(Exception $e){
            echo json_encode($e->getMessage());
        } 
    }    
}