<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));
include 'paginacionConfig.php';
chdir(dirname(__FILE__));

$usuarios;

if($_GET){    
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
    }    
}

$usuarios = ManejadorPersonal::getTodosDocentesConPaginacion($pagina, $numeroResultados);

foreach ($usuarios as $usuario) {
    echo "<tr>".
         "<td id='nombre".$usuario->getIdDocente()."' class='text-left'>".$usuario->getNombres()."</td>".
         "<td id='apellido".$usuario->getIdDocente()."' class='text-left'>".$usuario->getApellidos()."</td>".
         "<td class='text-left'>".$usuario->getContratacion()."</td>".
         "<td class='text-left'>".$usuario->getDepar()."</td>".
         "<td class='text-left'>".$usuario->getCargo()."</td>".  
         "<td class='text-center'><a usuario='f' id='".$usuario->getIdDocente()."' class='row-delete'><span class='glyphicon glyphicon-remove'></span></a></td>".   
         "<tr/>";
}