<?php
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
include_once '../../acceso_datos/Conexion.php';
chdir(dirname(__FILE__));
include_once 'administracionHorario.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorAulas.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorGrupos.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Dia.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Grupo.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Hora.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/Agrupacion.php';

if(isset($_GET['op'])){
    $op = htmlentities($_GET['op'], ENT_QUOTES, "UTF-8");
    if($op == 'inicio'){
        showInit();
    } elseif($op == "existe" || $op == "construir"){
        $año = htmlentities($_GET['anio'], ENT_QUOTES, "UTF-8");
        $ciclo = htmlentities($_GET['ciclo'], ENT_QUOTES, "UTF-8");
        if($op == "existe"){existe($año,$ciclo);}
        elseif($op == "construir"){construirHorario($año,$ciclo);}
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

function construirHorario($año,$ciclo){
    $facultad = asignarInfo($año, $ciclo);
    $query = "select cod_aula,(select d.nombre_dia from dias as d where d.id=a.id_dia) as nombre_dia,id_hora,id_agrupacion,id_grupo,(select t.tipo from tipos_grupos as t where t.id=a.tipo_grupo) as tipo,id_docente from asignaciones as a natural join aulas where año=$año and ciclo=$ciclo order by id_agrupacion,id_grupo,tipo_grupo";
    $resultados = Conexion::consulta($query);
    $id_grupo = 0;
    $tipo = "";
    $id_agrup = 0;
    while ($fila = pg_fetch_array($resultados)){
        if($id_grupo != $fila['id_grupo'] || $tipo != $fila['tipo'] || $id_agrup != $fila['id_agrupacion']){
            $id_grupo = $fila['id_grupo'];
            $tipo = $fila['tipo'];
            $id_agrup = $fila['id_agrupacion'];
            $grupo = ManejadorGrupos::getGrupo($id_grupo, $tipo, $id_agrup, $facultad->getGrupos());
        }
        $aula = ManejadorAulas::getAula($facultad->getAulas(), $fila['cod_aula']);
        $dia = $aula->getDia($fila['nombre_dia']);
        $hora = $dia->getHoraXID($fila['id_hora']);
        $hora->setGrupo($grupo);
        $hora->setDisponible(false);
    }
    $_SESSION['facultad'] = $facultad;
    exit(json_encode(0));
}

function existe($año,$ciclo){
    $query = "select * from asignaciones where año=$año and ciclo=$ciclo limit 1";
    $resul = pg_fetch_all(Conexion::consulta($query));
    if(!$resul){
        exit(json_encode(1));
    } else{
        exit(json_encode(0));
    }
}