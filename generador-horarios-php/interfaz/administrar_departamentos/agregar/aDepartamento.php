<?php
chdir(dirname(__FILE__));
require_once 'config.php';
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDepartamentos.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Departamento.php';        
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();

if (ManejadorSesion::comprobar_sesion() == true){
    if($_POST){ 
        if(is_numeric($_POST['nombre']) || $_POST['nombre']==""){
            echo json_encode("Error: el nombre del departamento es inválido.");
        }else{
            $nombre = strtoupper($_POST['nombre']);
            $departamento = new Departamento("",$nombre);                    
            try{                
                ManejadorDepartamentos::agregarDepartamento($departamento);
                $respuesta = "¡Departamento Agregado!";
                echo json_encode($respuesta);
            }catch(Exception $e){
                echo json_encode($e->getMessage());
            }            
        }      
    }
    
}else{
    echo json_encode("No está autorizado para realizar esta acción");
}
?>