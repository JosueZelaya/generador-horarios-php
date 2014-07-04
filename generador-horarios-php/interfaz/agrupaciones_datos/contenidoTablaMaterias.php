<?php
require_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
include 'config.php';
include_once 'paginacionConfig.php';
require_once '../../reglas_negocio/ManejadorAgrupaciones.php';

if($_GET){
    
    ManejadorSesion::sec_session_start();
    
    if(isset($_GET['agrupacion'])){
        $agrupacion_a_buscar = $_GET['agrupacion'];               
        $arrayMaterias = ManejadorAgrupaciones::getAgrupacionesPorNombre($agrupacion_a_buscar,$_SESSION['id_departamento'],$año,$ciclo);            
        $idAgrupaciones=array();
        $arrayMateriasFiltradas = array();
        foreach ($arrayMaterias as $materia) {           
            if(yaSeContoAgrupacion($materia->getIdAgrupacion(),$idAgrupaciones)){
                agregar_materia_a_la_agrupacion($materia->getIdAgrupacion(),$arrayMaterias,$materia->getNombre()." carrera: ".$materia->getCarrera());
            }else{
                $arrayMateriasFiltradas[] = $materia;
                $idAgrupaciones[]=$materia->getIdAgrupacion();
            }
        }
        $_SESSION['contenido_tabla'] = $arrayMateriasFiltradas;        
        $_SESSION['num_filas'] = count($arrayMateriasFiltradas);                      
    }else{
        $arrayMaterias = ManejadorAgrupaciones::getAgrupacionesDepartamento($_SESSION['id_departamento'],$año,$ciclo);
        $idAgrupaciones=array();
        $arrayMateriasFiltradas = array();
        foreach ($arrayMaterias as $materia) {           
            if(yaSeContoAgrupacion($materia->getIdAgrupacion(),$idAgrupaciones)){
                agregar_materia_a_la_agrupacion($materia->getIdAgrupacion(),$arrayMaterias,$materia->getNombre()." carrera: ".$materia->getCarrera());
            }else{
                $arrayMateriasFiltradas[] = $materia;
                $idAgrupaciones[]=$materia->getIdAgrupacion();
            }
        }
        $_SESSION['contenido_tabla'] = $arrayMateriasFiltradas;        
        $_SESSION['num_filas'] = count($arrayMateriasFiltradas); 
    }
    
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
        
    } 
    
}else{
    $arrayMaterias = ManejadorAgrupaciones::getAgrupacionesDepartamento($_SESSION['id_departamento'],$año,$ciclo);
    $idAgrupaciones=array();
        $arrayMateriasFiltradas = array();
        foreach ($arrayMaterias as $materia) {           
            if(yaSeContoAgrupacion($materia->getIdAgrupacion(),$idAgrupaciones)){
//                error_log("Se agrega la materia: ".$materia->getNombre()." a la agrupacion: ".$materia->getIdAgrupacion(),0);
                agregar_materia_a_la_agrupacion($materia->getIdAgrupacion(),$arrayMaterias,$materia->getNombre()." carrera: ".$materia->getCarrera());
            }else{
                $arrayMateriasFiltradas[] = $materia;
                $idAgrupaciones[]=$materia->getIdAgrupacion();
            }
        }
        $_SESSION['contenido_tabla'] = $arrayMateriasFiltradas;        
        $_SESSION['num_filas'] = count($arrayMateriasFiltradas); 
}
       
$inicio = ($pagina-1)*$numeroResultados;    
$fin = $inicio + $numeroResultados;
if($fin > count($arrayMateriasFiltradas)){
    $fin = count($arrayMateriasFiltradas);
}

for ($i = $inicio; $i < $fin; $i++) {
    $ciclos = implode(",",$arrayMateriasFiltradas[$i]->getCiclos());         
    $totalAlumnos = $arrayMateriasFiltradas[$i]->getAlumnosNuevos()+$arrayMateriasFiltradas[$i]->getOtrosAlumnos();
    $num = $i + 1;
    
    echo "<tr>".
    "<td>".$num."</td>".        
    "<td>".$arrayMateriasFiltradas[$i]->getCodigo()."</td>".        
    "<td>".$arrayMateriasFiltradas[$i]->getNombre()."</td>".
    "<td>".getStringMateriasCarreras($arrayMateriasFiltradas[$i]->getMaterias())."</td>".         
    "<td>".$arrayMateriasFiltradas[$i]->getDepartamento()->getNombre()."</td>".        
    "<td>".$ciclos."</td>".
    "<td><div style='cursor: pointer;' href='#' id='alumnos_nuevos' class='campoModificable n".$arrayMateriasFiltradas[$i]->getIdAgrupacion()."' data-type='text' data-placement='bottom' data-pk=" . $arrayMateriasFiltradas[$i]->getIdAgrupacion() . " data-url='modificarAgrupacion.php' data-title='Alumnos Nuevos'>".$arrayMateriasFiltradas[$i]->getAlumnosNuevos()."</div></td>".        
    "<td><div style='cursor: pointer;' href='#' id='otros_alumnos' class='campoModificable o".$arrayMateriasFiltradas[$i]->getIdAgrupacion()."' data-type='text' data-placement='bottom' data-pk=" . $arrayMateriasFiltradas[$i]->getIdAgrupacion() . " data-url='modificarAgrupacion.php' data-title='Otros Alumnos'>".$arrayMateriasFiltradas[$i]->getOtrosAlumnos()."</div></td>".
    "<td><div id='".$arrayMateriasFiltradas[$i]->getIdAgrupacion()."'>".$totalAlumnos."</div></td>".
    "<td><div style='cursor: pointer;' href='#' id='num_grupos' class='campoModificable ng".$arrayMateriasFiltradas[$i]->getIdAgrupacion()."' data-type='text' data-placement='bottom' data-pk=" . $arrayMateriasFiltradas[$i]->getIdAgrupacion() . " data-url='modificarAgrupacion.php' data-title='Numero de Grupos'>".$arrayMateriasFiltradas[$i]->getNumeroGrupos()."</div></td>".   
    "<td><div style='cursor: pointer;' href='#' id='num_grupos_l' class='campoModificable ngl".$arrayMateriasFiltradas[$i]->getIdAgrupacion()."' data-type='text' data-placement='bottom' data-pk=" . $arrayMateriasFiltradas[$i]->getIdAgrupacion() . " data-url='modificarAgrupacion.php' data-title='Numero de Grupos'>".$arrayMateriasFiltradas[$i]->getNumeroGruposLaboratorio()."</div></td>".           
    "<td><div style='cursor: pointer;' href='#' id='num_grupos_d' class='campoModificable ngd".$arrayMateriasFiltradas[$i]->getIdAgrupacion()."' data-type='text' data-placement='bottom' data-pk=" . $arrayMateriasFiltradas[$i]->getIdAgrupacion() . " data-url='modificarAgrupacion.php' data-title='Numero de Grupos'>".$arrayMateriasFiltradas[$i]->getNumeroGruposDiscusion()."</div></td>".           
    "<td><div style='cursor: pointer;' href='#' id='alumnos_grupo' class='campoModificable na".$arrayMateriasFiltradas[$i]->getIdAgrupacion()."' data-type='text' data-placement='bottom' data-pk=" . $arrayMateriasFiltradas[$i]->getIdAgrupacion() . " data-url='modificarAgrupacion.php' data-title='Alumnos por grupo'>".$arrayMateriasFiltradas[$i]->getAlumnosGrupo()."</div></td>".        
    "</tr>";
}

function yaSeContoAgrupacion($idAgrupacion,$idAgrupaciones){
    foreach ($idAgrupaciones as $id) {
        if($idAgrupacion==$id){            
            return TRUE;
        }
    }
    return FALSE;
}

function agregar_materia_a_la_agrupacion($id,$agrupaciones,$materia){
    foreach ($agrupaciones as $agrupacion) {
        if($agrupacion->getIdAgrupacion()==$id){
//            $agrupacion->setMaterias($agrupacion->getMaterias().", ".$materia);
            $agrupacion->addMateria($materia);
            break;
        }
    }    
}

function getStringMateriasCarreras($materias){
    $resultado="";
    $count=0;
    foreach ($materias as $materia) {
        if($count==0){
            $resultado = $materia;
        }else{
            $resultado = $resultado."<br/> ".$materia;
        }            
        $count++;
    }
    return $resultado;
}
