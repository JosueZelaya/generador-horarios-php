<?php
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
include_once '../../acceso_datos/Conexion.php';
ManejadorSesion::sec_session_start();

if(isset($_GET['op'])){
    $op = htmlentities($_GET['op'], ENT_QUOTES, "UTF-8");
    if($op == 'inicio'){
        showInit();
    } elseif($op == "abrir"){
        $año = htmlentities($_GET['anio'], ENT_QUOTES, "UTF-8");
        $ciclo = htmlentities($_GET['ciclo'], ENT_QUOTES, "UTF-8");
        abrirHorario($año,$ciclo);
    }
}

function showInit(){
    $query = "SELECT distinct año,ciclo FROM asignaciones ORDER BY año,ciclo ASC LIMIT 6";
    $resul = pg_fetch_all(Conexion::consulta($query));
    $retorno = '<br><br><center>';
    $retorno .= '<label for="comboAnios">Año</label> <select id="comboAnios" name="anios" data-btn-class="btn-success">';
    foreach ($resul as $fila){
        $retorno .= '<option value="'.$fila['año'].'">'.$fila['año'].'</option>';
    }
    $retorno .= '</select>&nbsp;&nbsp;&nbsp;&nbsp;';
    $retorno .= '<label for="comboCiclos">Ciclo</label> <select id="comboCiclos" name="ciclos" data-btn-class="btn-success">';
    $retorno .= '<option value="1">1</option>'.'<option value="2">2</option>'.'</select>&nbsp;&nbsp;&nbsp;&nbsp;'.
            '<a class="btn btn-md btn-default" href="#" id="openSch">'.
            '<i class="fa fa-folder-open"></i> Abrir'.
            '</a></center>';
    exit(json_encode($retorno));
}

function abrirHorario($año,$ciclo){
    if(!existe($año, $ciclo)){
        exit(json_encode(1));
    }
    
    exit(json_encode(0));
}

function existe($año,$ciclo){
    $query = "select * from asignaciones where año=$año and ciclo=$ciclo limit 1";
    $resul = pg_fetch_all(Conexion::consulta($query));
    if(!$resul){
        return false;
    } else{
        return true;
    }
}