<?php
chdir(dirname(__FILE__));
require_once 'config.php';
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDepartamentos.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Departamento.php';
chdir(dirname(__FILE__));

if(session_status()==PHP_SESSION_NONE){
    ManejadorSesion::sec_session_start();
}

if($_GET){   
    if(isset($_GET['codigo'])){        
        try{
            $codigo = $_GET['codigo'];
            $departamento = new Departamento($codigo,"");
            ManejadorDepartamentos::eliminarDepartamento($departamento);            
            echo json_encode("ok");
        }catch(Exception $e){
            echo json_encode($e->getMessage());
        } 
    }    
}