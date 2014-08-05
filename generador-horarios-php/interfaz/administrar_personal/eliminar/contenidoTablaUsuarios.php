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

$usuarios = ManejadorPersonal::getTodosUsuariosConPaginacion($pagina, $numeroResultados);

foreach ($usuarios as $usuario) {
    echo "<tr>".
         "<td usuario='t' id='login".$usuario->getId()."' class='text-left'>".$usuario->getLogin()."</td>".
         "<td class='text-left'>".$usuario->getDocente()->getNombre_completo()."</td>".         
         "<td class='text-center'><a usuario='t' id='".$usuario->getId()."' class='row-delete'><span class='glyphicon glyphicon-remove'></span></a></td>".   
         "<tr/>";
}