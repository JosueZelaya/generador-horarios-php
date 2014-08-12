<?php

chdir(dirname(__FILE__));
require_once '../../reglas_negocio/MateriaAgrupacion.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorAgrupaciones.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

if(isset($_GET)){
    if(isset($_GET['materias']) && isset($_GET['agrupacion'])){       
        $arrayMaterias = utf8_encode($_GET['materias']);                
        $arrayMaterias = json_decode($arrayMaterias,true);                        
        $agrupacion = $_GET['agrupacion'];
        if($arrayMaterias!=NULL && $agrupacion!=NULL && $agrupacion!="" && $agrupacion!="undefined" && is_numeric($agrupacion)){            
            if(count($arrayMaterias)<=0){
                echo json_encode("Debe añadir materias a la agrupacion");
            }else{                
                $materias = array();
                for ($i = 0; $i < count($arrayMaterias); $i++) {
                    $materia = new Materia("","","","","","","");
                    $materia->setCodigo($arrayMaterias[$i]["codigo"]);                    
                    $carrera = new Carrera($arrayMaterias[$i]["id_carrera"],$arrayMaterias[$i]["plan_estudio"],"","");
                    $materia->setCarrera($carrera);                    
                    $materias[]=$materia;                    
                }
                try{
                    ManejadorAgrupaciones::actualizarMateriasAgrupacion($materias, $año, $ciclo, $agrupacion);
                    echo json_encode("exito");
                }catch(Exception $e){
                    echo json_encode($e->getMessage());
                }
            }            
        }else{
            if($arrayMaterias==NULL){
                echo json_encode("Error: No ha arrastrado ninguna materia.");
            }else{
                echo json_encode("Error: Debe indicar a cual agrupacion desea agregar las materias.");                
            }            
        }
    }
}
