<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDocentes.php';
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

$docentes_string="[";
$cont=1;
$docentes = ManejadorPersonal::getDocentes();

foreach ($docentes as $docente) {
    if(count($docentes)==$cont){
        $docentes_string = $docentes_string."{value: '".$docente->getIdDocente()."', text: '".$docente->getNombre_completo()."'}";
    }else{
        $docentes_string = $docentes_string."{value: '".$docente->getIdDocente()."', text: '".$docente->getNombre_completo()."'},";        
    }
}
$docentes_string= $docentes_string."]";

foreach ($usuarios as $usuario) {
    echo "<tr>".
         "<td class='text-left'><div style='cursor: pointer;' id='login' class='campoModificable' data-type='text' data-placement='bottom' data-pk=".$usuario->getId()." data-url='mUsuarios.php' data-title='Ingrese Login'>".$usuario->getLogin()."</div></td>".
         "<td class='text-left'><div style='cursor: pointer;' id='docente' class='campoModificable' data-type='select' data-placement='bottom' data-pk=".$usuario->getId()." data-url='mUsuarios.php' data-title='Ingrese Docente' data-source=\"".$docentes_string."\">".$usuario->getDocente()->getNombre_completo()."</div></td>".         
         "<tr/>";
}
