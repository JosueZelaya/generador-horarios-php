<?php
chdir(dirname(__FILE__));
require_once 'paginacionConfig.php';
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorAulas.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Aula.php';
chdir(dirname(__FILE__));

if(session_status()==PHP_SESSION_NONE){
    ManejadorSesion::sec_session_start();
}

if($_GET){    
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
    }   
}

$aulas = ManejadorAulas::getTodasAulasConPaginacion($pagina, $numeroResultados);

$pagina = ($pagina-1)*$numeroResultados;    
$indice = $pagina;

foreach ($aulas as $aula) {
    $indice++;
    $exclusiva = $aula->isExclusiva();
    if($exclusiva){
        $exclusiva = "Sí";
    }else{
        $exclusiva = "No";
    }
    echo "<tr>".
         "<td>".$indice."</td>".
         "<td class='text-left'>".$aula->getNombre()."</td>".
         "<td class='text-left'>".$aula->getCapacidad()."</td>".
         "<td class='text-left'>".$exclusiva."</td>".
         "<td class='text-left'><a id='".$aula->getNombre()."' class='row-delete'><span class='glyphicon glyphicon-remove'></span></a></td>".
         "</tr>";    
}