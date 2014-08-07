<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDepartamentos.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorCargos.php';
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();
if (ManejadorSesion::comprobar_sesion() == true){
    if (isset($_GET)){
        if(isset($_GET['term'])){
            $buscarComo = $_GET['term'];
            $usuarios = ManejadorPersonal::buscarDocente($buscarComo);
            
            $departamentos_string="[";
            $cont=1;
            $departamentos = ManejadorDepartamentos::quitarDepartamentosEspeciales(ManejadorDepartamentos::getDepartamentos());
            foreach ($departamentos as $departamento) {
                if(count($departamentos)==$cont){
                    $departamentos_string = $departamentos_string."{value: '".$departamento->getId()."', text: '".$departamento->getNombre()."'}";
                }else{
                    $departamentos_string = $departamentos_string."{value: '".$departamento->getId()."', text: '".$departamento->getNombre()."'},";
                }
            }
            $departamentos_string= $departamentos_string."]";
            
            $cargos_string="[{value: 'ninguno', text: 'ninguno'},";
            $cont=1;
            $cargos = ManejadorCargos::obtenerTodosCargos();
            foreach ($cargos as $cargo) {
                if(count($departamentos)==$cont){
                    $cargos_string = $cargos_string."{value: '".$cargo->getId_cargo()."', text: '".$cargo->getNombre()."'}";
                }else{
                    $cargos_string = $cargos_string."{value: '".$cargo->getId_cargo()."', text: '".$cargo->getNombre()."'},";
                }
            }
            $cargos_string=$cargos_string."]";
            
            $datos = array();
            foreach ($usuarios as $usuario) {
                $datos[] = array("value"=> $usuario->getNombre_completo(),
                                "id" => $usuario->getIdDocente(),
                                "nombres" => $usuario->getNombres(),
                                "apellidos" => $usuario->getApellidos(),
                                "contratacion" => $usuario->getContratacion(),
                                "depar" => $usuario->getDepar(),    
                                "cargo" => $usuario->getCargo(),
                                "depars" => $departamentos_string,
                                "cargos" => $cargos_string
                                );
            }
            echo json_encode($datos);
        }
    }
}else{
    echo "No está autorizado para ver esta página";
}

