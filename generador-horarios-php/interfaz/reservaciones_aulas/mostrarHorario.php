<?php
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../user/funciones.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorDepartamentos.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorReservaciones.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorCargos.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorAgrupaciones.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorReservaciones.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorDocentes.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorCarreras.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorGrupos.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorMaterias.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

$facultad="";
$aulas="";
$aula="";
if($_GET){
    ManejadorSesion::sec_session_start();
    if(isset($_SESSION['reservaciones_facultad'])){
        $facultad = $_SESSION['reservaciones_facultad'];
        $aulas = $facultad->getAulas();
    }
    if(isset($_GET['aula'])){
        $aula = $_GET['aula'];
    }
}else{
    $facultad = asignarInfo($año, $ciclo);
    $_SESSION['reservaciones_facultad'] = $facultad;
    $aulas = $facultad->getAulas();
    $aula = $aulas[0]->getNombre();
}
?>
<form class='form-inline' role='form'>
    <label for='aulas'>Aulas:</label>
    <select id='aulas' class='form-control'>
        <?php
        foreach ($aulas as $aulaRecorrida) {
            if($aula==$aulaRecorrida->getNombre()){
                echo "<option value='".$aulaRecorrida->getNombre()."' selected>".$aulaRecorrida->getNombre()."</option>";    
            }else{
                echo "<option value='".$aulaRecorrida->getNombre()."'>".$aulaRecorrida->getNombre()."</option>";    
            } 
        }
        ?>
    </select>     
    <a class="btn btn-primary" href="#" id='reservarHoras'>
        <i class="fa fa-lock fa-lg"></i> Reservar
    </a>                    
    <a class="btn btn-primary" href="#" id='liberarHoras'>
        <i class="fa fa-chain-broken fa-lg"></i> Liberar
    </a> 
</form>            
<br/>
<?php

$modelo = create_model($facultad);         
$tabla = ManejadorAulas::getHorarioEnAula($aulas,$aula,$modelo);                
imprimirMallaParaReservaciones($tabla);

function asignarInfo($año,$ciclo) {
    $facultad = new Facultad(ManejadorDepartamentos::getDepartamentos(),  ManejadorCargos::obtenerTodosCargos(), ManejadorReservaciones::getTodasReservaciones($año,$ciclo),$año,$ciclo);
    $facultad->setAgrupaciones(ManejadorAgrupaciones::getAgrupaciones($año, $ciclo, $facultad->getAulas()));
    $facultad->setDocentes(ManejadorDocentes::obtenerTodosDocentes($facultad->getCargos(),$año,$ciclo,$facultad->getDepartamentos()));
    $facultad->setCarreras(ManejadorCarreras::getTodasCarreras($facultad->getDepartamentos()));
    $facultad->setGrupos(ManejadorGrupos::obtenerGrupos($año, $ciclo, $facultad->getAgrupaciones(), $facultad->getDocentes()));
    $facultad->setMaterias(ManejadorMaterias::getTodasMaterias($ciclo,$año,$facultad->getAgrupaciones(),$facultad->getCarreras(),$facultad->getAulas()));
    ManejadorReservaciones::asignarRerservaciones($facultad->getReservaciones(),$facultad->getAulas());
    return $facultad;
}