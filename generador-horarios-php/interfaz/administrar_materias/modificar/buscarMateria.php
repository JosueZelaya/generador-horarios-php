<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once "../../../reglas_negocio/ManejadorMaterias.php";
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();
if (ManejadorSesion::comprobar_sesion() == true){
    if (isset($_GET)){        
        if(isset($_GET['term'])){        
            $buscarComo = $_GET['term'];        
                echo json_encode(ManejadorMaterias::buscarMateriaParaAgrupar($buscarComo,"todos",$_SESSION['id_departamento']));                
        }       
    }
}else{
    echo "No está autorizado para ver esta página";
}