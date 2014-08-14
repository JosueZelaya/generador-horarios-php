<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();

$id_departamento = $_SESSION['id_departamento'];
$nombre_departamento = $_SESSION['nombre_departamento'];

if (ManejadorSesion::comprobar_sesion() == true){
    if (isset($_GET)){
        if(isset($_GET['term'])){
            $buscarComo = $_GET['term'];
            $usuarios = array();
            if($id_departamento=="todos"){
                $usuarios = ManejadorPersonal::buscarDocente($buscarComo);
            }else{
                $usuarios = ManejadorPersonal::buscarDocente($buscarComo,$id_departamento);
            }
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