<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Dia.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Hora.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Aula.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDias.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDocentes.php';
chdir(dirname(__FILE__));
require_once '../../user/funciones.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

if(isset($_GET['op'])){
    $op = htmlentities($_GET['op'], ENT_QUOTES, "UTF-8");
    if($op == "mostrar"){
        $id_docente = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");
        obtenerHorario($id_docente);
    } elseif($op == "guardar"){
        $id_docente = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");
        $desde = htmlentities($_GET['desde'], ENT_QUOTES, "UTF-8");
        $hasta = htmlentities($_GET['hasta'], ENT_QUOTES, "UTF-8");
        if($id_docente == 0 || !is_numeric($desde) || !is_numeric($hasta)){
            exit(json_encode(1));
        }
        guardarHorario($id_docente, $desde, $hasta);
    } else{
        $id_docente = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");
        if($id_docente == 0){
            exit(json_encode(1));
        }
        borrarHorario($id_docente);
    }
}

function obtenerHorario($id_docente){
    global $año;
    global $ciclo;
    $dias = ManejadorDias::getDias($año, $ciclo);
    foreach ($dias as $dia){
        $dia->setHoras(ManejadorDias::getHorasDia($dia->getId(), $año, $ciclo));
    }
    $modelo = crearModelDia($dias[0]->getHoras());
    $horario = ManejadorDocentes::obtenerHorarioDocente($id_docente, $año, $ciclo);
    $tabla = ManejadorDocentes::horarioDocenteEnDia($horario, $modelo, $dias);
    exit(json_encode(imprimirMallaHorarioDocente($tabla)));
}

function guardarHorario($idDoc,$desde,$hasta){
    global $año;
    global $ciclo;
    $res = ManejadorDocentes::guardarHorarioDocente($año,$ciclo,$desde,$hasta,$idDoc);
    if(!$res){
        exit(json_encode($res));
    } else{
        exit(json_encode(0));
    }
}

function borrarHorario($idDoc){
    global $año;
    global $ciclo;
    ManejadorDocentes::borrarHorarioDocente($idDoc, $año, $ciclo);
    exit(json_encode(0));
}