<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorMaterias.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Materia.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Departamento.php';
chdir(dirname(__FILE__));
include 'paginacionConfig.php';
chdir(dirname(__FILE__));

if(session_status()==PHP_SESSION_NONE){
    ManejadorSesion::sec_session_start();
}

$materias;

if($_GET){    
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
    }    
    if(isset($_GET['materia'])){
        $materia_a_buscar = $_GET['materia'];
        $materias = ManejadorMaterias::getMateriasParaAgrupar($materia_a_buscar,"todos",$_SESSION['id_departamento']);               
    }else{
        if(isset($_SESSION['datos_tabla_materias'])){
            $materias = $_SESSION['datos_tabla_materias'];
        }else{
            $materias = ManejadorMaterias::getTodasMateriasSinPaginacion();
        }
    }    
}else{
    $materias = ManejadorMaterias::getTodasMateriasSinPaginacion();
}

$_SESSION['datos_tabla_materias'] = $materias;
$_SESSION['numero_filas'] = count($materias); 

$pagina = ($pagina-1)*$numeroResultados;    
$inicio = $pagina;
$fin = $inicio + $numeroResultados;
if($fin > count($materias)){
    $fin = count($materias);
}
for ($i = $inicio; $i < $fin; $i++) {
    $materia = $materias[$i];
    echo    "<tr id='materia".$materia->getCodigo().$materia->getPlan_estudio().$materia->getCarrera()->getCodigo()."' nombre='".$materia->getNombre()."' codigo='".$materia->getCodigo()."' carrera='".$materia->getCarrera()->getCodigo()."' nombre_carrera='".$materia->getCarrera()->getNombre()."' id_depar='".$materia->getDepartamento()->getId()."' plan='".$materia->getPlan_estudio()."' class='arrastrable'>".
            "<td id='nombre".$materia->getNombre()."' class='text-left'>".$materia->getNombre()."</td>".
            "<td class='text-left'>".$materia->getCodigo()."</td>".
             "<td class='text-left'>".$materia->getPlan_estudio()."</td>".
             "<td class='text-left'>".$materia->getCiclo()."</td>".
             "<td class='text-left'>".$materia->getCarrera()->getNombre()."</td>".  
             "<td class='text-center'><a id='".$materia->getCodigo().$materia->getPlan_estudio().$materia->getCarrera()->getCodigo()."' nombre='".$materia->getNombre()."' codigo='".$materia->getCodigo()."' plan='".$materia->getPlan_estudio()."' carrera='".$materia->getCarrera()->getCodigo()."' class='row-delete'><span class='glyphicon glyphicon-remove'></span></a></td>".              
            "</tr>";
}