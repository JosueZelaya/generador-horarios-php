<?php
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorSesion.php';
ManejadorSesion::sec_session_start();
$resultadosXPagina = 6;
$bloques = $_SESSION['bloquesBusqueda'];

if(isset($_GET['op'])){
    $op = htmlentities($_GET['op'], ENT_QUOTES, "UTF-8");
    if($op == "calcular"){
        getNumeroPaginas();
    } elseif ($op == "page" && isset ($_GET['pagina'])) {
        $numeroPagina = htmlentities($_GET['pagina'], ENT_QUOTES, "UTF-8");
        paginar($numeroPagina);
    }
}

function getNumeroPaginas(){
    global $bloques;
    global $resultadosXPagina;
    $totalPaginas = ceil(count($bloques)/$resultadosXPagina);
    exit(json_encode($totalPaginas));
}

function paginar($numeroPagina){
    global $resultadosXPagina;
    global $bloques;
    $cadenaBotonInter = '<a class="btn btn-success" href="#" id="intercambiarHoraBusqueda"><i class="fa fa-retweet fa-1x"></i></a>';
    $retorno = '<table class="table table-striped table-hover table-condensed">'.
            '<thead><th>Aula</th><th>Dia</th><th>Hora Disponible</th><th>Operación</th></thead>';
    $inicio = ($numeroPagina * $resultadosXPagina) - $resultadosXPagina;
    $final = ($numeroPagina * $resultadosXPagina) - 1;
    if($final > (count($bloques)-1)){
        $final = count($bloques)-1;
    }
    for($h=$inicio;$h<=$final;$h++){
        $bloqueAulaDia = $bloques[$h];
        $horasDia = $bloqueAulaDia['horas'];
        $aula = $bloqueAulaDia['aula'];
        $dia = $bloqueAulaDia['dia'];
        for ($i=0;$i<count($horasDia);$i++){
            $retorno .= '<tr data-aula='.$aula.' data-dia='.$dia.' data-inicio='.$horasDia[$i]['inicio'].' data-fin='.$horasDia[$i]['fin'].'><td>'.$aula.'</td><td>'.$dia.'</td><td>'.$horasDia[$i]['inicio'].' - '.$horasDia[$i]['fin'].'</td><td>'.$cadenaBotonInter.'</td></tr>';
        }
    }
    $retorno .= '</table></div>';
    exit(json_encode($retorno));
}