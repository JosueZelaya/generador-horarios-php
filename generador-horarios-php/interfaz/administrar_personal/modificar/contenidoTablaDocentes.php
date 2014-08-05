<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorPersonal.php';
require_once 'ManejadorDepartamentos.php';
require_once 'ManejadorCargos.php';
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

$contrataciones = "[{value: 'ADHO', text: 'ADHO'}, {value: 'EVHC',text: 'EVHC'},{value: 'EVMT',text: 'EVMT'},{value: 'EVCT',text: 'EVCT'},{value: 'HC',text: 'HC'},{value: 'CT',text: 'CT'},{value: 'TC',text: 'TC'},{value: 'MC',text: 'MT'}]";
$departamentos_string="[";
$cont=1;
$departamentos = ManejadorDepartamentos::quitarDepartamentosEspeciales(ManejadorDepartamentos::getDepartamentos());
foreach ($departamentos as $departamento) {
    if(count($departamentos)==$cont){
        $departamentos_string = $departamentos_string."{value: '".$departamento->getId()."', text: '".$departamento->getNombre()."'}";
    }else{
        $departamentos_string = $departamentos_string."{value: '".$departamento->getId()."', text: '".$departamento->getNombre()."'},";
    }
}
$departamentos_string= $departamentos_string."]";

$cargos_string="[{value: 'ninguno', text: 'ninguno'},";
$cont=1;
$cargos = ManejadorCargos::obtenerTodosCargos();
foreach ($cargos as $cargo) {
    if(count($departamentos)==$cont){
        $cargos_string = $cargos_string."{value: '".$cargo->getId_cargo()."', text: '".$cargo->getNombre()."'}";
    }else{
        $cargos_string = $cargos_string."{value: '".$cargo->getId_cargo()."', text: '".$cargo->getNombre()."'},";
    }
}
$cargos_string=$cargos_string."]";

foreach ($usuarios as $usuario) {
    echo "<tr>".
         "<td class='text-left'><div style='cursor: pointer;' id='nombres' class='campoModificable' data-type='text' data-placement='bottom' data-pk=".$usuario->getIdDocente()." data-url='mDocentes.php' data-title='Ingrese Nombres'>".$usuario->getNombres()."</div></td>".
         "<td class='text-left'><div style='cursor: pointer;' id='apellidos' class='campoModificable' data-type='text' data-placement='bottom' data-pk=".$usuario->getIdDocente()." data-url='mDocentes.php' data-title='Ingrese Apellidos'>".$usuario->getApellidos()."</div></td>".
         "<td class='text-left'><div style='cursor: pointer;' id='contratacion' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk=".$usuario->getIdDocente()." data-url='mDocentes.php' data-title='Contratacion' data-source=\"".$contrataciones."\">".$usuario->getContratacion()."</div></td>".
         "<td class='text-left'><div style='cursor: pointer;' id='departamento' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk=".$usuario->getIdDocente()." data-url='mDocentes.php' data-title='Departamento' data-source=\"".$departamentos_string."\">".$usuario->getDepar()."</div></td>".
         "<td class='text-left'><div style='cursor: pointer;' id='cargo' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk=".$usuario->getIdDocente()." data-url='mDocentes.php' data-title='Cargo' data-source=\"".$cargos_string."\">".$usuario->getCargo()."</div></td>".   
         "<tr/>";
}