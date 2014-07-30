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

$departamentos = ManejadorDepartamentos::getDepartamentosConPaginacion($pagina, $numeroResultados,"activos");

$pagina = ($pagina-1)*$numeroResultados;    
$indice = $pagina;

foreach ($departamentos as $departamento) {    
    echo "<tr>".         
         "<td class='text-left'>".$departamento->getNombre()."</td>".
         "<td class='text-left'><a nombre='".$departamento->getNombre()."' id='".$departamento->getId()."' class='row-delete'><span class='glyphicon glyphicon-remove'></span></a></td>".
         "</tr>";    
}