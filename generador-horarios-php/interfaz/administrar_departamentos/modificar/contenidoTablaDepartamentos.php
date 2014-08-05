<?php
chdir(dirname(__FILE__));
require_once 'paginacionConfig.php';
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDepartamentos.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Departamento.php';
chdir(dirname(__FILE__));

if(session_status()==PHP_SESSION_NONE){
    ManejadorSesion::sec_session_start();
}

if($_GET){    
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
    }   
}

$departamentos = ManejadorDepartamentos::getDepartamentosConPaginacion($pagina, $numeroResultados,"ambos");

$pagina = ($pagina-1)*$numeroResultados;    
$indice = $pagina;

$activo_string = "[{value: 't', text: 'Sí'},{value: 'f', text: 'No'}]";

foreach ($departamentos as $departamento) {
    $indice++;
    $activo = $departamento->estaActivo();
    if($activo){
        $activo = "Sí";
    }else{
        $activo = "No";
    }
    echo "<tr>".         
         "<td class='text-left'><div style='cursor: pointer;' id='nombre' class='campoModificable' data-type='text' data-placement='bottom' data-pk='".$departamento->getId()."' data-url='mDepartamentos.php' data-title='Ingrese Nombre'>".$departamento->getNombre()."</div></td>".
         "<td class='text-left'><div style='cursor: pointer;' id='activo' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk='".$departamento->getId()."' data-url='mDepartamentos.php' data-title='Activar' data-source=\"".$activo_string."\">".$activo."</div></td>".         
         "</tr>";    
}