<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorMaterias.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Materia.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Carrera.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Departamento.php';
chdir(dirname(__FILE__));
include 'paginacionConfig.php';
chdir(dirname(__FILE__));

if(session_status()==PHP_SESSION_NONE){
    ManejadorSesion::sec_session_start();
}

$materias;
$id_departamento = $_SESSION['id_departamento'];

if($_GET){    
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
    }    
    if(isset($_GET['materia'])){
        $materia_a_buscar = $_GET['materia'];
        $materias = ManejadorMaterias::getMateriasParaAgrupar($materia_a_buscar,"todos",$id_departamento);
    }else{
        if(isset($_SESSION['datos_tabla_materias'])){
            $materias = $_SESSION['datos_tabla_materias'];
        }else{
            $materias = ManejadorMaterias::getTodasMateriasSinPaginacion();
            if($id_departamento!="todos"){
                $materias = ManejadorMaterias::obtenerMateriasDeDepartamento($materias,$id_departamento,"todos");
            }
        }
    }    
}else{
    $materias = ManejadorMaterias::getTodasMateriasSinPaginacion();
    if($id_departamento!="todos"){
        $materias = ManejadorMaterias::obtenerMateriasDeDepartamento($materias,$id_departamento,"todos");
    }
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
    $carrera = $materia->getCarrera();
    $departamento = $carrera->getDepartamento();
    echo    "<tr id='materia".$materia->getCodigo().$carrera->getPlanEstudio().$carrera->getCodigo()."' nombre='".$materia->getNombre()."' codigo='".$materia->getCodigo()."' carrera='".$carrera->getCodigo()."' nombre_carrera='".$carrera->getNombre()."' id_depar='".$departamento->getId()."' plan='".$carrera->getPlanEstudio()."' class='arrastrable'>".
            "<td id='nombre".$materia->getNombre()."' class='text-left'>".$materia->getNombre()."</td>".
            "<td class='text-left'>".$materia->getCodigo()."</td>".
             "<td class='text-left'>".$carrera->getPlanEstudio()."</td>".
             "<td class='text-left'>".$materia->getCiclo()."</td>".
             "<td class='text-left'>".$carrera->getNombre()."</td>".  
             "<td class='text-center'><a id='".$materia->getCodigo().$carrera->getPlanEstudio().$carrera->getCodigo()."' nombre='".$materia->getNombre()."' codigo='".$materia->getCodigo()."' plan='".$carrera->getPlanEstudio()."' carrera='".$carrera->getCodigo()."' class='row-delete'><span class='glyphicon glyphicon-remove'></span></a></td>".              
            "</tr>";
}