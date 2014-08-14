<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Docente.php';        
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();
$id_departamento = $_SESSION['id_departamento'];

if (ManejadorSesion::comprobar_sesion() == true){
    if($_POST){        
        if($id_departamento=='todos' || ($id_departamento!='todos' && $_POST['departamento'] == $id_departamento)){
            $usuario = new Docente("","","","","");
            $usuario->setNombres($_POST['nombres']);
            $usuario->setApellidos($_POST['apellidos']);    
            $usuario->setContratacion($_POST['contratacion']);
            $usuario->setDepar($id_departamento);
            $usuario->setCargo($_POST['cargo']);      
            try{
                ManejadorPersonal::agregarDocente($usuario);
                $respuesta = "¡Docente Agregado!";
                echo json_encode($respuesta);
            }catch(Exception $e){
                echo json_encode($e->getMessage());
            }
        }else{
            echo json_encode("Error: Departamento incorrecto, solo puede agregar docentes a su departamento.");
        }        
    }
    
}else{
    echo json_encode("No está autorizado para realizar esta acción");
}
?>