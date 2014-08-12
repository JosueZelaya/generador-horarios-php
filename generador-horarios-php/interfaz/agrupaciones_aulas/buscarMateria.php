<?php
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorAgrupaciones.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

if (isset($_GET)){
    ManejadorSesion::sec_session_start();
    if(isset($_GET['term'])){
        $buscarComo = $_GET['term'];  
        echo json_encode(ManejadorAgrupaciones::buscarAgrupacionSoloNombre($buscarComo,$_SESSION['id_departamento'],$año,$ciclo));        
    }else if(isset($_GET['materia'])){
        $materia_a_buscar = $_GET['materia'];               
        $arrayMaterias = ManejadorAgrupaciones::getAgrupacionesPorNombre($materia_a_buscar,$_SESSION['id_departamento'],$año,$ciclo);            
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
        
        for ($i = 0; $i < count($arrayMateriasFiltradas); $i++) {
            $ciclos = implode(",",$arrayMateriasFiltradas[$i]->getCiclos());         
            $totalAlumnos = $arrayMateriasFiltradas[$i]->getAlumnosNuevos()+$arrayMateriasFiltradas[$i]->getOtrosAlumnos();
            $num = $i + 1;

            echo "<tr class='arrastrable' id-agrupacion='".$arrayMateriasFiltradas[$i]->getIdAgrupacion()."'>".                               
            "<td data-toggle='true'>".$arrayMateriasFiltradas[$i]->getNombre()."</td>".
            "<td data-hide='all'>".getStringMateriasCarreras($arrayMateriasFiltradas[$i]->getMaterias())."</td>".         
            "<td>".$arrayMateriasFiltradas[$i]->getDepartamento()->getNombre()."</td>".                                
            "</tr>";
        }        
        
    }      
    
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
