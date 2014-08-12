<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorMaterias.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Materia.php';        
chdir(dirname(__FILE__));

ManejadorSesion::sec_session_start();

$cicloinfo = parse_ini_file('../../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

if (ManejadorSesion::comprobar_sesion() == true){
    if($_POST){       
        if(!is_numeric($_POST['ciclo']) || !is_numeric($_POST['uv'])){
            echo json_encode("Error: Verifique que las unidades valorativas y el ciclo sean valores numéricos.");
        }else if(!is_numeric($_POST['plan'])){
            echo json_encode("Error: Plan de estudio no válido.");
        }else{
            $materia = new Materia("","","","","","","");
            $carrera = new Carrera($_POST['carrera'],$_POST['plan'],"","");
            $materia->setCarrera($carrera);            
            $materia->setCodigo($_POST['codigo']);
            $materia->setNombre(strtoupper($_POST['nombre']));        
            $materia->setCiclo($_POST['ciclo']);        
            $materia->setUnidadesValorativas($_POST['uv']);
            $materia->setTipo($_POST['tipo']);
            try{
                if(fmod ($materia->getCiclo(),2)==0){
                    $ciclo = 2;
                }else{
                    $ciclo = 1;
                }
                ManejadorMaterias::agregarMateria($materia,$año,$ciclo);
                $respuesta = "¡Materia Agregada!";
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
