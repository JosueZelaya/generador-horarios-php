<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDocentes.php';
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();
if (ManejadorSesion::comprobar_sesion() == true){
    if (isset($_GET)){
        if(isset($_GET['term'])){
            $buscarComo = $_GET['term'];
            $usuarios = ManejadorPersonal::buscarUsuario($buscarComo);
            
            $docentes_string="[";
            $cont=1;
            $docentes = ManejadorPersonal::getDocentes();

            foreach ($docentes as $docente) {
                if(count($docentes)==$cont){
                    $docentes_string = $docentes_string."{value: '".$docente->getIdDocente()."', text: '".$docente->getNombre_completo()."'}";
                }else{
                    $docentes_string = $docentes_string."{value: '".$docente->getIdDocente()."', text: '".$docente->getNombre_completo()."'},";        
                }
            }
            $docentes_string= $docentes_string."]";
            
            $datos = array();
            foreach ($usuarios as $usuario) {
                $datos[] = array("value"=> $usuario->getLogin(),
                                "id" => $usuario->getId(),
                                "login" => $usuario->getLogin(),
                                "docente" => $usuario->getDocente()->getNombre_completo(),
                                "docentes" => $docentes_string
                                );
            }
            echo json_encode($datos);
        }
    }
}else{
    echo "No está autorizado para ver esta página";
}
