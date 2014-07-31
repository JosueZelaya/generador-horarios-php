<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();
if (ManejadorSesion::comprobar_sesion() == true){
    if (isset($_GET)){
        if(isset($_GET['term'])){
            $buscarComo = $_GET['term'];
            $usuarios = ManejadorPersonal::buscarDocente($buscarComo);
            $datos = array();
            foreach ($usuarios as $usuario) {
                $datos[] = array("value"=> $usuario->getNombre_completo(),
                                "id" => $usuario->getIdDocente(),
                                "nombres" => $usuario->getNombres(),
                                "apellidos" => $usuario->getApellidos(),
                                "contratacion" => $usuario->getContratacion(),
                                "depar" => $usuario->getDepar(),    
                                "cargo" => $usuario->getCargo()
                                );
            }
            echo json_encode($datos);
        }
    }
}else{
    echo "No está autorizado para ver esta página";
}