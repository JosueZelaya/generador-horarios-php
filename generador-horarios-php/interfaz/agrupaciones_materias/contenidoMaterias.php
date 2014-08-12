<?php
chdir(dirname(__FILE__));
include_once 'paginacionConfig.php';
require_once '../../reglas_negocio/ManejadorMaterias.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

if($ciclo==1){
    $ciclo = "impar";
}else{
    $ciclo="par";
}
$arrayMaterias;
if(isset($_GET)){    
    ManejadorSesion::sec_session_start();
    if(isset($_GET['materia'])){
        $agrupacion_a_buscar = $_GET['materia'];               
        if(!isset($_SESSION['materia_buscada'])){
            $arrayMaterias = ManejadorMaterias::getMateriasParaAgrupar($agrupacion_a_buscar,$ciclo,$_SESSION['id_departamento']);        
        }else{
            if($_SESSION['materia_buscada']!=$agrupacion_a_buscar){
                $arrayMaterias = ManejadorMaterias::getMateriasParaAgrupar($agrupacion_a_buscar,$ciclo,$_SESSION['id_departamento']);
            }else{
                $arrayMaterias = $_SESSION['datos_tabla'];
            }
        }
        $_SESSION['datos_tabla'] = $arrayMaterias;        
        $_SESSION['numero_filas'] = count($arrayMaterias);        
        $_SESSION['materia_buscada'] = $agrupacion_a_buscar;        
    }
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
    }
}

$arrayMaterias = $_SESSION['datos_tabla'];
$pagina = ($pagina-1)*$numeroResultados;    
$inicio = $pagina;
$fin = $inicio + $numeroResultados;
if($fin > count($arrayMaterias)){
    $fin = count($arrayMaterias);
}

for ($i = $inicio; $i < $fin; $i++) {
    $materia = $arrayMaterias[$i];
    $carrera = $materia->getCarrera();
    $departamento = $carrera->getDepartamento();
    echo    "<tr cod_materia='".$materia->getCodigo()."' cod_carrera='".$carrera->getCodigo()."' id_depar='".$departamento->getId()."' plan_estudio='".$carrera->getPlanEstudio()."' class='arrastrable'>".
            "<td>".$materia->getCodigo()."</td>".
            "<td>".$materia->getNombre()."</td>".
            "<td>".$carrera->getNombre()."</td>".
            "<td>".$carrera->getPlanEstudio()."</td>".
            "<td>".$departamento->getNombre()."</td>".            
            "</tr>";
}
