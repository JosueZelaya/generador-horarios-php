<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Docente.php';
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();

if($_GET){   
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $usuario = ManejadorPersonal::getDocente($id);        
        try{
            ManejadorPersonal::desactivarDocente($usuario);
            echo json_encode("ok");
        }catch(Exception $e){
            echo json_encode($e->getMessage());
        } 
    }    
}