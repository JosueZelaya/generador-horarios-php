<?php

chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();

if($_POST){    
    if(isset($_POST['passwordActual']) && isset($_POST['passwordNuevo']) && isset($_POST['passwordNuevo2'])){        
        $actual = $_POST['passwordActual'];
        $nuevo = $_POST['passwordNuevo'];
        $nuevo2 = $_POST['passwordNuevo2'];
        if($nuevo == $nuevo2){
            $login = $_SESSION['usuario_login'];
            $usuario = ManejadorPersonal::getUsuario($login);
            try{
                ManejadorPersonal::cambiarPassword($usuario, $actual, $nuevo);
                echo json_encode("ok");
            } catch (Exception $ex) {
                echo json_encode($ex->getMessage());
            }            
        }else{
            echo json_encode("Su nuevo password está mal escrito, escribalo correctamente en ambos campos");
        }
    }else{
        echo json_encode("No se han enviado los datos necesarios para cambiar su password");
    }
}
    