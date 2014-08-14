<?php

chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Procesador.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Grupo.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Aula.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Dia.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Hora.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Agrupacion.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Materia.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Departamento.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Carrera.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorMaterias.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorGrupos.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorReservaciones.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorAgrupaciones.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorDepartamentos.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorAulas.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorHoras.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorDocentes.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorPersonal.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorCargos.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorCarreras.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorGrupos.php';
chdir(dirname(__FILE__));
include_once 'funciones.php';
chdir(dirname(__FILE__));
$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];
ManejadorSesion::sec_session_start();

if(isset($_SESSION['facultad'])){
    $facultad = $_SESSION['facultad'];
}

if(isset($_GET['op'])){
    $op = htmlentities($_GET['op'], ENT_QUOTES, "UTF-8");
    if($op == 'generar'){
        ini_set('max_execution_time', 300);
        generarHorario($año,$ciclo);
    } elseif ($op == 'intercambio' || $op == 'intercambio2' || $op == 'intercambio3') {
        $aula1 = htmlentities($_GET['aula1'], ENT_QUOTES, "UTF-8");
        $aula2 = htmlentities($_GET['aula2'], ENT_QUOTES, "UTF-8");
        $dia1 = htmlentities($_GET['dia1'], ENT_QUOTES, "UTF-8");
        $dia2 = htmlentities($_GET['dia2'], ENT_QUOTES, "UTF-8");
        $desde1 = htmlentities($_GET['desde1'], ENT_QUOTES, "UTF-8");
        $desde2 = htmlentities($_GET['desde2'], ENT_QUOTES, "UTF-8");
        $hasta1 = htmlentities($_GET['hasta1'], ENT_QUOTES, "UTF-8");
        $hasta2 = htmlentities($_GET['hasta2'], ENT_QUOTES, "UTF-8");
        if($dia1==null || $dia2==null){ exit(json_encode(10)); }
        if($op == 'intercambio'){inicioIntercambio($aula1, $dia1, $desde1, $hasta1, $aula2, $dia2, $desde2, $hasta2);}
        elseif($op == 'intercambio2'){segundaFaseIntercambio($aula1, $dia1, $desde1, $hasta1, $aula2, $dia2, $desde2, $hasta2);}
        elseif($op == 'intercambio3'){realizarIntercambio($aula1, $dia1, $desde1, $hasta1, $aula2, $dia2, $desde2, $hasta2, $año, $ciclo);}
    } elseif ($op == 'moreInfo') {
        $aula = htmlentities($_GET['aula'], ENT_QUOTES, "UTF-8");
        $dia = htmlentities($_GET['dia'], ENT_QUOTES, "UTF-8");
        $hora = htmlentities($_GET['hora'], ENT_QUOTES, "UTF-8");
        getMoreInfo($aula,$dia,$hora);
    } elseif ($op == "buscar"){
        $aula = htmlentities($_GET['aula'], ENT_QUOTES, "UTF-8");
        $dia = htmlentities($_GET['dia'], ENT_QUOTES, "UTF-8");
        $desde = htmlentities($_GET['desde'], ENT_QUOTES, "UTF-8");
        $hasta = htmlentities($_GET['hasta'], ENT_QUOTES, "UTF-8");
        if($dia==null){exit(json_encode(10));}
        busquedaHoras($aula,$dia,$desde,$hasta);
    } elseif ($op == "id"){
        $hora = htmlentities($_GET['valor'], ENT_QUOTES, "UTF-8");
        $tipo = htmlentities($_GET['tipo'], ENT_QUOTES, "UTF-8");
        obtenerIdHora($hora,$tipo);
    }
}

function generarHorario($año,$ciclo){
    $err = Facultad::comprobarDatosGeneracion($año, $ciclo);
    if(count($err) > 0){
        exit(json_encode(imprimirMensajeFaltantes($err)));
    }
    $facultad = asignarInfo($año, $ciclo);
    $procesador = new Procesador($facultad->getAulas());
    $clasifDocentes = ManejadorDocentes::clasificarDocentes($facultad->getDocentes());
    $prioridad = true;
    foreach ($clasifDocentes as $clasif) {
        $gruposClasifDocente = ManejadorDocentes::extraerGruposDeDocentes($clasif);
        usort($gruposClasifDocente, "ManejadorGrupos::cmpGruposXAlumnos"); // Se ordenan los grupos de mayor a menor cantidad de alumnos
        $gruposClasifAula = ManejadorGrupos::clasificarGruposPrefAula($gruposClasifDocente);
        procesarGrupos($procesador, $gruposClasifAula, $prioridad);
        $prioridad = false;
    }
    $_SESSION['facultad'] = $facultad;
    exit(json_encode(0));
}

function procesarGrupos($procesador,$gruposClasif,$prioridad){
    foreach ($gruposClasif as $grupos) {
        foreach ($grupos as $grupo) {
            try {
                $procesador->procesarGrupo($grupo,$prioridad);
            } catch (Exception $exc) {
                error_log($exc->getMessage(),0);    //Se produce cuando ya no hay aulas disponibles
            }
        }
    }
}

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

function inicioIntercambio($aula1,$dia1,$desde1,$hasta1,$aula2,$dia2,$desde2,$hasta2){
    global $facultad;
    $grupos1 = ManejadorGrupos::getGruposEnRangoHoras($desde1, $hasta1, $facultad->getAulas(), $aula1, $dia1);
    $grupos2 = ManejadorGrupos::getGruposEnRangoHoras($desde2, $hasta2, $facultad->getAulas(), $aula2, $dia2);
    if(count($grupos1) != count($grupos2)){
        exit(json_encode(1));
    }
    if(!ManejadorPersonal::esPropietario($_SESSION['id_departamento'],$grupos1) || !ManejadorPersonal::esPropietario($_SESSION['id_departamento'],$grupos2)){
        exit(json_encode(11));
    }
    $msgs = capacidadesAulas($grupos1, $grupos2, $aula1, $aula2);
    if(count($msgs)!=0){
        exit(json_encode(imprimirMensajesAulas($msgs)));
    }
    segundaFaseIntercambio($aula1, $dia1, $desde1, $hasta1, $aula2, $dia2, $desde2, $hasta2);
}

function segundaFaseIntercambio($aula1,$dia1,$desde1,$hasta1,$aula2,$dia2,$desde2,$hasta2){
    global $facultad;
    global $año;
    global $ciclo;
    $grupos1 = ManejadorGrupos::getGruposEnRangoHoras($desde1, $hasta1, $facultad->getAulas(), $aula1, $dia1);
    $grupos2 = ManejadorGrupos::getGruposEnRangoHoras($desde2, $hasta2, $facultad->getAulas(), $aula2, $dia2);
    $msgs[0][] = array_unique(testIntercambio($grupos2,$grupos1,$dia1,$dia2,$facultad->getAulas(),$aula1,$aula2,$desde1,$hasta1,$desde2,$hasta2));
    $msgs[0][] = array_unique(testIntercambio($grupos1,$grupos2,$dia2,$dia1,$facultad->getAulas(),$aula2,$aula1,$desde2,$hasta2,$desde1,$hasta1));
    if(count($msgs[0][0])==0 && count($msgs[0][1])==0){
        realizarIntercambio($aula1,$dia1,$desde1,$hasta1,$aula2,$dia2,$desde2,$hasta2,$año,$ciclo);
    } else{
        $msgs[1] = getDatosHorasIntercambio($desde1, $hasta1, $desde2, $hasta2);
        $msgs[2] = array('dia1'=>$dia1,'dia2'=>$dia2);
        exit(json_encode(imprimirMensajesChoquesIntercambios($msgs)));
    }
}

function capacidadesAulas($grupos1,$grupos2,$aula1,$aula2){
    global $facultad;
    $msgs[] = array_unique(ManejadorAulas::gruposExcedenCapacidad($grupos1, ManejadorAulas::getAula($facultad->getAulas(), $aula2)));
    $msgs[] = array_unique(ManejadorAulas::gruposExcedenCapacidad($grupos2, ManejadorAulas::getAula($facultad->getAulas(), $aula1)));
    if(count($msgs[0])==0 && count($msgs[1])==0){
        return array();
    } else{
        return $msgs;
    }
}

function getDatosHorasIntercambio($desde1,$hasta1,$desde2,$hasta2){
    global $facultad;
    $horas['inicio1'] = $facultad->getAulas()[0]->getDias()[0]->getHoras()[$facultad->getAulas()[0]->getDias()[0]->getPosEnDiaHora($desde2)]->getInicio();
    $horas['fin1'] = $facultad->getAulas()[0]->getDias()[0]->getHoras()[$facultad->getAulas()[0]->getDias()[0]->getPosEnDiaHora($hasta2)]->getFin();
    $horas['inicio2'] = $facultad->getAulas()[0]->getDias()[0]->getHoras()[$facultad->getAulas()[0]->getDias()[0]->getPosEnDiaHora($desde1)]->getInicio();
    $horas['fin2'] = $facultad->getAulas()[0]->getDias()[0]->getHoras()[$facultad->getAulas()[0]->getDias()[0]->getPosEnDiaHora($hasta1)]->getFin();
    return $horas;
}

function testIntercambio($gruposReemplazo,$gruposInsercion,$nombreDiaReemplazo,$nombreDiaInsercion,$aulas,$nombreAulaReemplazo,$nombreAulaInsercion,$desdeReemplazo,$hastaReemplazo,$desdeInsercion,$hastaInsercion){
    $aulasPrueba = arrayCopy($aulas); ///Se clonan las aulas para trabajar con informacion duplicada y no con la original
    $agrupaciones = ManejadorAgrupaciones::extraerAgrupacionesDeGrupos($gruposInsercion);
    if(count($agrupaciones)==0){
        return array();
    }
    $diaReemplazo = ManejadorAulas::getDiaEnAula($nombreAulaReemplazo, $aulasPrueba, $nombreDiaReemplazo);
    $idHorasReemplazo = range($desdeReemplazo, $hastaReemplazo);
    foreach ($idHorasReemplazo as $idHora){
        $indicesReemplazo[] = $diaReemplazo->getPosEnDiaHora($idHora);
    }
    ManejadorDias::reemplazarAsignaciones($indicesReemplazo,$diaReemplazo,$gruposReemplazo);
    $idHorasInsercion = range($desdeInsercion, $hastaInsercion);
    foreach ($idHorasInsercion as $idHora){
        $indicesInsercion[] = $diaReemplazo->getPosEnDiaHora($idHora);
    }
    $diaInsercion = ManejadorAulas::getDiaEnAula($nombreAulaInsercion, $aulasPrueba, $nombreDiaInsercion);
    ManejadorDias::borrarAsignaciones($indicesInsercion, $diaInsercion);
    $msgs = ManejadorHoras::choqueIntercambios($nombreDiaInsercion, current($indicesInsercion), end($indicesInsercion)+1, $aulasPrueba, $agrupaciones, ManejadorGrupos::extraerDocentesDeGrupos($gruposInsercion));
    if(count($msgs)!=0){
        return $msgs;
    }
    return array();
}

function realizarIntercambio($aula1,$dia1,$desde1,$hasta1,$aula2,$dia2,$desde2,$hasta2,$año,$ciclo){
    global $facultad;
    $grupos1 = ManejadorGrupos::getGruposEnRangoHoras($desde1, $hasta1, $facultad->getAulas(), $aula1, $dia1);
    $grupos2 = ManejadorGrupos::getGruposEnRangoHoras($desde2, $hasta2, $facultad->getAulas(), $aula2, $dia2);
    $diaInsercion1 = ManejadorAulas::getAula($facultad->getAulas(), $aula1)->getDia($dia1);
    $diaInsercion2 = ManejadorAulas::getAula($facultad->getAulas(), $aula2)->getDia($dia2);
    $idHorasInsercion1 = range($desde1, $hasta1);
    $idHorasInsercion2 = range($desde2, $hasta2);
    foreach ($idHorasInsercion1 as $idHora){
        $indicesInsercion1[] = $diaInsercion1->getPosEnDiaHora($idHora);
    }
    foreach ($idHorasInsercion2 as $idHora){
        $indicesInsercion2[] = $diaInsercion2->getPosEnDiaHora($idHora);
    }
    ManejadorDias::reemplazarAsignaciones($indicesInsercion1, $diaInsercion1, $grupos2);
    ManejadorDias::reemplazarAsignaciones($indicesInsercion2, $diaInsercion2, $grupos1);
    if($facultad->escribirIntercambio($grupos1,$grupos2,$diaInsercion1,$diaInsercion2,$aula1,$aula2,$idHorasInsercion1,$idHorasInsercion2,$año,$ciclo)){
        exit(json_encode(0));
    } else{
        exit(json_encode("No se pudo guardar la información del intercambio en la base de datos"));
    }
}

function getMoreInfo($nombreAula,$nombreDia,$hora){
    global $facultad;
    if(strlen($hora)>2){
        $hora = ManejadorHoras::getIdHoraSegunInicio($hora, $facultad->getAulas()[0]->getDias()[0]->getHoras());
    }
    $grupo = ManejadorGrupos::getGrupoEnHora($facultad->getAulas(), $nombreAula, $nombreDia, $hora);
    $codigos = ManejadorGrupos::obtenerCodigoPropietario($grupo->getAgrup()->getMaterias());
    $nombresMaterias = ManejadorGrupos::obtenerNombrePropietario($grupo->getAgrup()->getMaterias());
    $nombresDeptos = ManejadorGrupos::getNombreDepartamento($grupo->getAgrup()->getMaterias());
    $carreras = ManejadorGrupos::obtenerCarreraPropietario($grupo->getAgrup()->getMaterias());
    $agrupacion = $grupo->getAgrup()->getId();
    echo '<br><br><div class="panel panel-default">'.
            '<div class="panel-heading">Agrupacion: '.$agrupacion.'</div>'.
            '<table class="table">'.
            '<tr><th>Codigo</th><th>Nombre</th><th>Carrera</th><th>Departamento</th></tr>';
    for ($i=0;$i<count($codigos);$i++){
        echo '<tr><td>'.$codigos[$i].'</td><td>'.$nombresMaterias[$i].'</td><td>'.$carreras[$i].'</td><td>'.$nombresDeptos[$i].'</td></tr>';
    }
    echo '</table></div>';
}

function busquedaHoras($aula,$dia,$desde,$hasta){
    global $facultad;
    $grupos = ManejadorGrupos::getGruposEnRangoHoras($desde, $hasta, $facultad->getAulas(), $aula, $dia);
    $agrupaciones = ManejadorAgrupaciones::extraerAgrupacionesDeGrupos($grupos);
    if(count($agrupaciones)!=1 || !ManejadorGrupos::gruposIgualesEnBloque($grupos)){
        exit(json_encode(1));
    }
    $cantidadHoras = ($hasta - $desde) + 1;
    $bloques = ManejadorHoras::buscarHorasParaIntercambio($facultad->getAulas(), $agrupaciones[0], $grupos[0], $cantidadHoras);
    if(count($bloques)!=0){
        $_SESSION['bloquesBusqueda'] = $bloques;
        exit(json_encode(imprimirResultadoBusqueda()));
    }
    exit(2);
}

function obtenerIdHora($hora,$tipo){
    global $facultad;
    if($tipo == "inicio"){
        $idHora = ManejadorHoras::getIdHoraSegunInicio($hora, $facultad->getAulas()[0]->getDias()[0]->getHoras());
    } else{
        $idHora = ManejadorHoras::getIdHoraSegunFin($hora, $facultad->getAulas()[0]->getDias()[0]->getHoras());
    }
    if($idHora == null){
        exit("null");
    }
    exit(json_encode($idHora));
}