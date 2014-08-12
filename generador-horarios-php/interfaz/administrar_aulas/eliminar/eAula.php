<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorAulas.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Aula.php';
chdir(dirname(__FILE__));

if(session_status()==PHP_SESSION_NONE){
    ManejadorSesion::sec_session_start();
}

if($_GET){   
    if(isset($_GET['codigo'])){        
        try{
            $codigo = $_GET['codigo'];
            $aula = new Aula();
            $aula->setNombre($_GET['codigo']);
            ManejadorAulas::eliminarAula($aula);            
            echo json_encode("ok");
        }catch(Exception $e){
            echo json_encode($e->getMessage());
        } 
    }    
}
