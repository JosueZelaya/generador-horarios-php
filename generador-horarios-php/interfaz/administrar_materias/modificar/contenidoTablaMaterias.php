<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorMaterias.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Materia.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Departamento.php';
chdir(dirname(__FILE__));
include 'paginacionConfig.php';
chdir(dirname(__FILE__));

if(session_status()==PHP_SESSION_NONE){
    ManejadorSesion::sec_session_start();
}

$materias;

if($_GET){    
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
    }    
    if(isset($_GET['materia'])){
        $materia_a_buscar = $_GET['materia'];
        $materias = ManejadorMaterias::getMateriasParaAgrupar($materia_a_buscar,"todos",$_SESSION['id_departamento']);               
    }else{
        if(isset($_SESSION['datos_tabla_materias'])){
            $materias = $_SESSION['datos_tabla_materias'];
        }else{
            $materias = ManejadorMaterias::getTodasMateriasSinPaginacion();
        }
    }    
}else{
    $materias = ManejadorMaterias::getTodasMateriasSinPaginacion();
}

$_SESSION['datos_tabla_materias'] = $materias;
$_SESSION['numero_filas'] = count($materias); 

$pagina = ($pagina-1)*$numeroResultados;    
$inicio = $pagina;
$fin = $inicio + $numeroResultados;
if($fin > count($materias)){
    $fin = count($materias);
}

$ciclos_string="[";
for ($i = 1;$i<=14;$i++) {
    if($i==14){
        $ciclos_string = $ciclos_string."{value: '$i', text: '$i'}]";
    }else{
        $ciclos_string = $ciclos_string."{value: '$i', text: '$i'},";
    }
}

$tipos_string = "[{value: 'Optativa', text: 'Optativa'},{value: 'Obligatoria', text: 'Obligatoria'}]";


for ($i = $inicio; $i < $fin; $i++) {
    $materia = $materias[$i];    
    $pk = $materia->getCodigo().",".$materia->getPlan_estudio().",".$materia->getCarrera()->getCodigo();
    
    echo    "<tr>".            
            "<td class='text-left'>".$materia->getCodigo()."</div></td>".
            "<td class='text-left'><div style='cursor: pointer;' id='nombre' class='campoModificable' data-type='text' data-placement='bottom' data-pk='$pk' data-url='mMaterias.php' data-title='Ingrese Nombre'>".$materia->getNombre()."</div></td>".
            "<td class='text-left'>".$materia->getPlan_estudio()."</div></td>".
            "<td class='text-left'>".$materia->getCarrera()->getNombre()."</div></td>".            
            "<td class='text-left'><div style='cursor: pointer;' id='ciclo' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk='$pk' data-url='mMaterias.php' data-title='Ciclo' data-source=\"".$ciclos_string."\">".$materia->getCiclo()."</div></td>".
            "<td class='text-left'><div style='cursor: pointer;' id='uv' class='campoModificable' data-type='text' data-placement='bottom' data-pk='$pk' data-url='mMaterias.php' data-title='Unidades Valorativas'>".$materia->getUnidadesValorativas()."</div></td>".
            "<td class='text-left'><div style='cursor: pointer;' id='tipo' class='campoSeleccionable' data-type='select' data-placement='bottom' data-pk='$pk' data-url='mMaterias.php' data-title='Tipo' data-source=\"".$tipos_string."\">".$materia->getTipo()."</div></td>".             
            "</tr>";
}