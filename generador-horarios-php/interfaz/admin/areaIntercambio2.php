<?php

include_once '../../reglas_negocio/Facultad.php';

session_start();
$facultad = $_SESSION['facultad'];
$modelo = create_model($facultad);

if (isset($_GET['aula'])) {
    $aula = $_GET['aula'];
    $tabla = ManejadorAulas::getHorarioEnAula($facultad->getAulas(), $aula, $modelo);
    echo imprimir($tabla);
    ?>
    <script type="text/javascript">
        $('.verInfoGrupo').popover({
            title : "Informacion del Grupo",
            animation : true,
            trigger : 'click',  //Se muestra el popover al pasar el puntero sobre la celda. valores que acepta: hover,manual,click,focus                    
            html : true
        });
    </script>
    <?php
}

function imprimir($tabla) {
    for ($i = 0; $i < count($tabla); $i++) {
        echo "<div class='col'>";
        for ($j = 0; $j < count($tabla[$i]); $j++) {
            if ($j == 0) {
                echo "<div class='col-header'>" . $tabla[$i][$j] . "</div>";
            } else if ($i == 0) {
                echo "<div class='celda-hora'><div class='centrar'>" . $tabla[$i][$j] . "</div></div>";
            } else {
                $celda = $tabla[$i][$j];
                $contenido = "Materia: " . $celda['nombre'] . "<br/>" . "Grupo: " . $celda['grupo'] . "<br/> Departamento: " . $celda['departamento'];
                if($celda['more']){
                    $contenido .= '<br><a id="moreInfo" href="#">Mas</a>';
                }
                if(!strcmp($celda['grupo'],"")==0){
                    echo "<div class='celda-hora intercambio2 grupoIntercambio2 intercambio2". $celda['codigo'].$celda['grupo'].$i." intercambio2".$celda['dia'].$celda['idHora']."' data-grupo='" . $celda['codigo'] . $celda['grupo'] . $i . "' data-iniciobloque='" . $celda['inicioBloque'] . "' data-finbloque='" . $celda['finBloque'] . "' data-hora='" . $celda['idHora'] . "' data-dia='".$celda['dia']."'>";
                    if ($j < 3) {
                        echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='bottom' data-content='" . $contenido . "'>" . $celda['texto'] . '</div></div>';
                    } else {
                        echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='top' data-content='" . $contenido . "'>" . $celda['texto'] . '</div></div>';
                    }    
                }else{
                    echo "<div class='celda-hora intercambio2 grupoVacioIntercambio2 intercambio2".$celda['dia'].$celda['idHora']."' data-hora='".$celda['idHora']."' data-dia='".$celda['dia']."'>";
                    echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='top' data-content='".$contenido."'>".$celda['texto'].'</div></div>';
                }
            }
        }
        echo '</div>';
    }
}

function create_model($facultad) {
    $dias = $facultad->getAulas()[0]->getDias();
    $horas = $dias[0]->getHoras();
    $cols = count($dias) + 1;
    $rows = count($horas) + 1;
    $modelo = array();
    for ($i = 0; $i < $cols; $i++) {
        $modelo[$i] = array();
        for ($y = 0; $y < $rows; $y++) {
            if ($i == 0 && $y == 0) {
                $modelo[$i][$y] = "Horas";
                continue;
            } elseif ($i == 0) {
                $inicio = $horas[$y - 1]->getInicio();
                $fin = $horas[$y - 1]->getFin();
                $modelo[$i][$y] = "$inicio - $fin";
                continue;
            } elseif ($i != 0 && $y == 0) {
                $modelo[$i][$y] = $dias[$i - 1]->getNombre();
                continue;
            }
            $modelo[$i][$y] = '';
        }
    }
    return $modelo;
}
