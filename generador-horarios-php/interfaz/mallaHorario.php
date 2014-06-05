<?php

    include_once '../reglas_negocio/ManejadorAgrupaciones.php';
    include_once '../reglas_negocio/ManejadorCarreras.php';
    include_once '../reglas_negocio/ManejadorMaterias.php';
    include_once '../reglas_negocio/Facultad.php';

    session_start();
    $facultad = $_SESSION['facultad'];
    $modelo = create_model($facultad);

    if(isset($_GET['aula']) && isset($_GET['departamento']) && isset($_GET['carrera'])){
        $aula = $_GET['aula'];
        $departamento = $_GET['departamento'];
        $carrera = $_GET['carrera'];
        if($carrera!='todos'){                        
            $ids_agrupaciones = ManejadorAgrupaciones::obtenerAgrupacionesDeCarrera($carrera);
            $tabla = ManejadorAulas::getHorarioEnAula_Carrera($facultad->getAulas(), $aula,$ids_agrupaciones,$modelo);
        }else if($departamento!='todos'){                        
            $tabla = ManejadorAulas::getHorarioEnAula_Depar($aula,$departamento,$modelo,$facultad);
        }else{
            $tabla = ManejadorAulas::getHorarioEnAula($facultad->getAulas(), $aula, $modelo);
        }
        echo imprimirMalla($tabla);
    } elseif(isset($_GET['departamento']) && isset($_GET['carrera']) && isset($_GET['materia'])){
        $aulas = $facultad->getAulas();
        $cod_materia = $_GET['materia'];
        $id_depar = $_GET['departamento'];
        $horario = ManejadorMaterias::getHorarioMateria($aulas, $cod_materia, $id_depar);
        $horario = ordenarHorario($horario);  
        imprimirMallaMateria($horario);
    }elseif (isset($_GET['aula'])) {
        $aula = $_GET['aula'];
        $tabla = ManejadorAulas::getHorarioEnAula($facultad->getAulas(), $aula, $modelo);
        echo imprimirMalla($tabla);
    }
    
    //Se ordena por id de grupo en orden ascendente
    function ordenarHorario($horario){
        for ($i = 0; $i < count($horario)-1; $i++) {
            for ($j = $i+1; $j < count($horario); $j++) {
                if($horario[$i]['grupo']>$horario[$j]['grupo']){
                    $aux = $horario[$i];
                    $horario[$i] = $horario[$j];
                    $horario[$j] = $aux;
                }
            }
        }
        return $horario;
    }
    
    function imprimirMalla($tabla){
        for($i=0;$i<count($tabla);$i++){
            echo "<div class='col'>";
            for($j=0;$j<count($tabla[$i]);$j++){                
                if($j==0){
                    echo "<div class='col-header'>".$tabla[$i][$j]."</div>";
                }else if($i==0){
                    echo "<div class='celda-hora'><div class='centrar'>".$tabla[$i][$j]."</div></div>";
                }else{
                    $celda = $tabla[$i][$j];
                    $contenido = "Materia: ".$celda['nombre']."<br/>"."Grupo: ".$celda['grupo']."<br/> Departamento: ".$celda['departamento'];
                    if($celda['more']){
                        $contenido .= '<br><a id="moreInfo" href="#">Mas</a>';
                    }
                    if(!strcmp($celda['grupo'],"")==0){
                        echo "<div class='celda-hora grupo ".$celda['codigo'].$celda['grupo'].$i."' data-grupo='".$celda['codigo'].$celda['grupo'].$i."' data-iniciobloque='".$celda['inicioBloque']."' data-finbloque='".$celda['finBloque']."' data-hora='".$celda['idHora']."' data-dia='".$celda['dia']."'>";
                        if($j<3){
                            echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='bottom' data-content='".$contenido."'>".$celda['texto'].'</div></div>';
                        }else{
                            echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='top' data-content='".$contenido."'>".$celda['texto'].'</div></div>';
                        }
                    }else{
                        echo "<div class='celda-hora grupoVacio ".$celda['dia'].$celda['idHora']."' data-hora='".$celda['idHora']."' data-dia='".$celda['dia']."'>";
                        echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='top' data-content='".$contenido."'>".$celda['texto'].'</div></div>';
                    }                                        
                }
            }
            echo '</div>';
        }        
    }
    
    function create_model($facultad){
        $dias = $facultad->getAulas()[0]->getDias();
        $horas = $dias[0]->getHoras();
        $cols = count($dias)+1;
        $rows = count($horas)+1;
        $modelo = array();
        for($i=0;$i<$cols;$i++){
            $modelo[$i] = array();
            for($y=0;$y<$rows;$y++){
                if($i == 0 && $y == 0){
                    $modelo[$i][$y] = "Horas";
                    continue;
                }
                elseif ($i == 0) {
                    $inicio = $horas[$y-1]->getInicio();
                    $fin = $horas[$y-1]->getFin();
                    $modelo[$i][$y] = "$inicio - $fin";
                    continue;
                }
                elseif($i != 0 && $y == 0){
                    $modelo[$i][$y] = $dias[$i-1]->getNombre();
                    continue;
                }
                $modelo[$i][$y] = '';
            }
        }
        return $modelo;
    }
    
    function imprimirMallaMateria($horario){
        echo '<table class="table table-striped table-hover">'.
            '<thead>'.
                '<th>Grupo</th>'.
                '<th>Aula</th>'.
                '<th>Dia</th>'.
                '<th>Inicio</th>'.
                '<th>Fin</th>'.
            '</thead>';
        for ($index = 0; $index < count($horario); $index++) {
            if($horario[$index]['more']){
                $more = true;
            } elseif ($horario[$index]['cloned']){
                $cloned = true;
            }
            echo "<tr>";                        
            if($index>0){
                if(strcmp($horario[$index-1]['grupo'],$horario[$index]['grupo'])==0){
                echo "<td></td>";
                }else{
                    echo "<td>".$horario[$index]['grupo']."</td>";
                }
            }else{
                echo "<td>".$horario[$index]['grupo']."</td>";
            }                               
            echo "<td>".$horario[$index]['aula']."</td>
                <td>".$horario[$index]['dia']."</td>
                <td>".$horario[$index]['horaInicio']."</td>
                <td>".$horario[$index]['horaFin']."</td>
                </tr>";
        }
        echo '</table>';
        if(isset($more) && $more){
            echo '<div class="alert alert-info" data-aula='.$horario[0]['aula'].' data-dia='.$horario[0]['dia'].' data-hora='.$horario[0]['horaInicio'].'>
                    Existen mas materias agrupadas.&nbsp;
                    <a href="#" class="alert-link" id="moreInfo">Ver aqui.</a>
                </div>';
        } elseif (isset ($cloned) && $cloned) {
            echo '<div class="alert alert-info">
                    Materia Clonada.
                </div>';
        }
    }
