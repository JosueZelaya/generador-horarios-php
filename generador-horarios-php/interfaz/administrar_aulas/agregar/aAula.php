<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorAulas.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Aula.php';        
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();

if (ManejadorSesion::comprobar_sesion() == true){
    if($_POST){ 
        if(is_numeric($_POST['codigo']) || $_POST['codigo']==""){
            echo json_encode("Error: el codigo de aula no es válido.");
        }else if(!is_numeric($_POST['capacidad'])){
            echo json_encode("Error: la capacidad del aula debe ser un valor numérico.");
        }else{
            $aula = new Aula();
            $aula->setNombre(strtoupper($_POST['codigo']));
            $aula->setCapacidad($_POST['capacidad']);
            if($_POST['exclusiva']=="true"){
                $aula->setExclusiva("t");
            }else{
                $aula->setExclusiva("f");
            }            
            try{                
                ManejadorAulas::agregarAula($aula);
                $respuesta = "¡Aula Agregada!";
                echo json_encode($respuesta);
            }catch(Exception $e){
                echo json_encode($e->getMessage());
            }            
        }      
    }
    
}else{
    echo json_encode("No está autorizado para realizar esta acción");
}