<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));
include 'paginacionConfig.php';
chdir(dirname(__FILE__));

if(session_status()!=PHP_SESSION_ACTIVE){
    ManejadorSesion::sec_session_start();
}

$usuarios;
$id_departamento = $_SESSION['id_departamento'];
$nombre_departamento = $_SESSION['nombre_departamento'];

if($_GET){    
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
    }    
}

if($id_departamento=="todos"){
    $usuarios = ManejadorPersonal::getTodosDocentesConPaginacion($pagina, $numeroResultados);
}else{
    $usuarios = ManejadorPersonal::getTodosDocentesConPaginacion($pagina, $numeroResultados, $id_departamento);
}

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