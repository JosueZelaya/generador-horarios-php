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

$exclusiva_string = "[{value: 't', text: 'Sí'},{value: 'f', text: 'No'}]";

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
         "<td class='text-left'><div style='cursor: pointer;' id='capacidad' class='campoModificable' data-type='text' data-placement='bottom' data-pk='".$aula->getNombre()."' data-url='mAulas.php' data-title='Ingrese Capacidad'>".$aula->getCapacidad()."</div></td>".
         "<td class='text-left'><div style='cursor: pointer;' id='exclusiva' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk='".$aula->getNombre()."' data-url='mAulas.php' data-title='Exclusiva' data-source=\"".$exclusiva_string."\">".$exclusiva."</div></td>".         
         "</tr>";    
}