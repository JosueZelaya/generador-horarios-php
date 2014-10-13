<?php
//Se ordena por id de grupo en orden ascendente
function ordenarHorarioMateria($horario){
    if(isset($horario["TEORICO"])){
        $horario["TEORICO"] = ordenarGrupos($horario["TEORICO"]);
    }
    if(isset($horario["LABORATORIO"])){
        $horario["LABORATORIO"] = ordenarGrupos($horario["LABORATORIO"]);
    }
    if(isset($horario["DISCUSION"])){
        $horario["DISCUSION"] = ordenarGrupos($horario["DISCUSION"]);
    }
    return $horario;
}

function ordenarGrupos($grupos){
    for ($i = 0; $i < count($grupos)-1; $i++) {
        for ($j = $i+1; $j < count($grupos); $j++) {
            if($grupos[$i]["grupo"] > $grupos[$j]["grupo"]){
                $aux = $grupos[$i];
                $grupos[$i] = $grupos[$j];
                $grupos[$j] = $aux;
            }
        }
    }
    return $grupos;
}

function imprimirMallaTodasAulas($tabla){
    for($i=0;$i<count($tabla);$i++){
        echo "<div class='col'>";
        for($j=0;$j<count($tabla[$i]);$j++){                
            if($j==0){
                echo "<div class='col-header'>".$tabla[$i][$j]."</div>";
            }else if($i==0){
                echo "<div class='celda-hora'><div class='centrar'>".$tabla[$i][$j]."</div></div>";
            }else{
                $celda = $tabla[$i][$j];
                if($celda!=""){
                    if(isset($celda['texto'])){
                        if($celda['texto']!=""){
                            echo "<div class='celda-hora verHora' dia='".$celda['idDia']."' hora='".$celda['idHora']."' depar='".$celda['depar']."'><div class='centrar'><a href='#'>Info <span class='glyphicon glyphicon-zoom-in'></span></a></div></div>";
                        }else{
                            echo "<div class='celda-hora'><div class='centrar'>Libre</div></div>";
                        }
                    }                    
                }
            }
        }
        echo '</div>';
    }
}

function imprimirMallaHora($tabla,$fullInfo){
    if($fullInfo){
        echo "<h3>".$tabla[0]['dia']." ".$tabla[0]['horaInicio']." - ".$tabla[0]['horaFin']."</h3>";
        echo '<table class="table table-striped table-hover">'.
            '<thead>'.
                '<th>Materia</th>'.
                '<th>Departamento</th>'.
                '<th>Grupo</th>'.
                '<th>Tipo</th>'.
                '<th>Aula</th>'.
            '</thead>';
        foreach ($tabla as $fila) {
            echo "<tr>";
            echo "<td>".$fila['nombre']."</td>";
            echo "<td>".$fila['nombre_depar']."</td>";
            echo "<td>".$fila['grupo']."</td>";
            echo "<td>".$fila['tipo']."</td>";
            echo "<td>".$fila['aula']."</td>";
            echo "</tr>";
        }
    }else{
        echo "<h3>".$tabla[0]['dia']." ".$tabla[0]['horaInicio']." - ".$tabla[0]['horaFin']."</h3>";
        echo '<table class="table table-striped table-hover">'.
            '<thead>'.
                '<th>Materia</th>'.
                '<th>Grupo</th>'.
                '<th>Tipo</th>'.
                '<th>Aula</th>'.
            '</thead>';
        foreach ($tabla as $fila) {
            echo "<tr>";
            echo "<td>".$fila['nombre']."</td>";
            echo "<td>".$fila['grupo']."</td>";
            echo "<td>".$fila['tipo']."</td>";
            echo "<td>".$fila['aula']."</td>";
            echo "</tr>";
        }
    }
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
                if($celda!=""){
                    $contenido = "Materia: ".$celda['nombre']."<br/>"."Grupo: ".$celda['tipo']." ".$celda['grupo']."<br/> Departamento: ".$celda['departamento'];
                    if($celda['more']){
                        $contenido .= '<br><a id="moreInfo" href="#">Mas</a>';
                    }
                    if(!strcmp($celda['grupo'],"")==0){                   
                        echo "<div class='celda-hora grupo ".$celda['codigo'].$celda['grupo'].$celda['tipo'].$i."' data-grupo='".$celda['codigo'].$celda['grupo'].$celda['tipo'].$i."' data-iniciobloque='".$celda['inicioBloque']."' data-finbloque='".$celda['finBloque']."' data-hora='".$celda['idHora']."' data-dia='".$celda['dia']."'>";
                        if($j<3){
                            echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='bottom' data-content='".$contenido."'>".$celda['texto'].'</div></div>';
                    }else{
                            echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='top' data-content='".$contenido."'>".$celda['texto'].'</div></div>';
                        }                    
                    }else{
                        if(strcmp($celda['texto'],"reservada")==0){
                            echo "<div class='celda-hora ".$celda['dia'].$celda['idHora']."' style='background-color: #E6E6FA;' data-hora='".$celda['idHora']."' data-dia='".$celda['dia']."'>";
                            echo "<div class='centrar'>".$celda['texto'].'</div></div>';
                        }else{
                            echo "<div class='celda-hora grupoVacio ".$celda['dia'].$celda['idHora']."' data-hora='".$celda['idHora']."' data-dia='".$celda['dia']."'>";
                            //echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='top' data-content='".$contenido."'>".$celda['texto'].'</div></div>';
                            echo '</div>';
                        }                    
                    }                                        
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

function crearModelDia($horas){
    $cols = 2;
    $rows = count($horas);
    $modelo = array();
    for($i=0;$i<$cols;$i++){
        $modelo[$i] = array();
        for($y=0;$y<$rows;$y++){
            if ($i == 0) {
                $inicio = $horas[$y]->getInicio();
                $fin = $horas[$y]->getFin();
                $modelo[$i][$y] = "$inicio - $fin";
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
            '<th>Tipo</th>'.
            '<th>Aula</th>'.
            '<th>Dia</th>'.
            '<th>Inicio</th>'.
            '<th>Fin</th>'.
        '</thead>';
    foreach ($horario as $tipoGrupo){
        for ($index = 0; $index < count($tipoGrupo); $index++) {
            if($tipoGrupo[$index]['more']){
                $more = true;
            } elseif ($tipoGrupo[$index]['cloned']){
                $cloned = true;
            }
            echo "<tr>";                        
            if($index>0){
                if(strcmp($tipoGrupo[$index-1]['grupo'],$tipoGrupo[$index]['grupo'])==0 && strcmp($tipoGrupo[$index-1]['tipo'],$tipoGrupo[$index]['tipo'])==0){
                    echo "<td></td><td></td>";
                }else{
                    echo "<td>".$tipoGrupo[$index]['grupo']."</td>";
                    echo "<td>".$tipoGrupo[$index]['tipo']."</td>";
                }
            }else{
                echo "<td>".$tipoGrupo[$index]['grupo']."</td>";
                echo "<td>".$tipoGrupo[$index]['tipo']."</td>";
            }                               
            echo "<td>".$tipoGrupo[$index]['aula']."</td>
                <td>".$tipoGrupo[$index]['dia']."</td>
                <td>".$tipoGrupo[$index]['horaInicio']."</td>
                <td>".$tipoGrupo[$index]['horaFin']."</td>
                </tr>";
        }
    }
    echo '</table>';
    $tipoGrupo = array_pop($horario);
    if(isset($more) && $more){
        echo '<div class="alert alert-info" data-aula='.$tipoGrupo[0]['aula'].' data-dia='.$tipoGrupo[0]['dia'].' data-hora='.$tipoGrupo[0]['horaInicio'].'>
                Existen mas materias agrupadas.&nbsp;
                <a href="#" class="alert-link" id="moreInfo">Ver aqui.</a>
            </div>';
    }elseif (isset ($cloned) && $cloned) {
        echo '<div class="alert alert-info" data-aula='.$tipoGrupo[0]['aula'].' data-dia='.$tipoGrupo[0]['dia'].' data-hora='.$tipoGrupo[0]['horaInicio'].'>
                Materia Clonada.&nbsp;
                <a href="#" class="alert-link" id="moreInfo">Ver aqui.</a>
            </div>';
    }
}

function imprimirMallaParaReservaciones($tabla){
    for($i=0;$i<count($tabla);$i++){
        echo "<div class='col'>";
        for($j=0;$j<count($tabla[$i]);$j++){                
            if($j==0){
                echo "<div class='col-header'>".$tabla[$i][$j]."</div>";
            }else if($i==0){
                echo "<div class='celda-hora'><div class='centrar'>".$tabla[$i][$j]."</div></div>";
            }else{                
                $celda = $tabla[$i][$j];                
                $dia = getDia($i);
                if($celda!=""){
                    if($celda['texto']!=""){
                        echo "<div id='".$dia.$j."' class='celda-hora grupo hora-reservada' data-hora='".$j."' data-dia='".$dia."'><div class='centrar'>".$celda['texto']."</div></div>";                                    
                    }else{
                        echo "<div id='".$dia.$j."' class='celda-hora grupo' data-hora='".$j."' data-dia='".$dia."'></div>";                                    
                    }
                }else{
                    echo "<div id='".$dia.$j."' class='celda-hora grupo' data-hora='".$j."' data-dia='".$dia."'></div>";                                    
                }                                
            }
        }
        echo '</div>';
    }        
}

function imprimirMallaHorarioDocente($tabla){
    $retorno = "";
    for($i=0;$i<count($tabla);$i++){
        $retorno .= "<div class='col'>";
        for($j=0;$j<count($tabla[$i]);$j++){                
            if($i==0){
                $retorno .= "<div class='celda'><div class='centrar'>".$tabla[$i][$j]."</div></div>";
            }else{
                $celda = $tabla[$i][$j];
                if($celda['texto']!=""){
                    $retorno .= "<div id='hora".$celda['idHora']."' class='celda hora hora-asignada' data-hora='".$celda['idHora']."'><div class='centrar'>".$celda['texto']."</div></div>";
                } else{
                    $retorno .= "<div id='hora".$celda['idHora']."' class='celda hora' data-hora='".$celda['idHora']."'><div class='centrar'>Hora libre</div></div>";
                }
            }
        }
        $retorno .= '</div>';
    }
    return $retorno;
}

//Obtiene nombre de dia; se usa en imprimirMallaParaReservaciones
function getDia($indice){
    switch ($indice){
        case 1:
            return "lunes";
        case 2:
            return "martes";
        case 3:
            return "miercoles";
        case 4:
            return "jueves";
        case 5:
            return "viernes";
        case 6:
            return "sabado";    
        case 7:
            return "domingo";            
    }
}

function arrayCopy( array $array ) {
        $result = array();
        foreach( $array as $key => $val ) {
            if( is_array( $val ) ) {
                $result[$key] = arrayCopy( $val );
            } elseif ( is_object( $val ) ) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
}

function imprimirMensajesChoquesIntercambios($msgs){
    $retorno = '';
    $retorno.= '<div class="panel panel-danger">'.
            '<div class="panel-heading">Conflictos</div>'.
            '<table class="table">'.
            '<tr><th>'.$msgs[2]['dia2'].', '.$msgs[1]['inicio1'].'-'.$msgs[1]['fin1'].'</th><th>'.$msgs[2]['dia1'].', '.$msgs[1]['inicio2'].'-'.$msgs[1]['fin2'].'</th></tr><tr>';
    foreach ($msgs[0] as $msgsIntercambio){
        $retorno.= '<td><ul class="list-group">';
        foreach ($msgsIntercambio as $msgConflict){
            $retorno.= '<li class="list-group-item">'.$msgConflict.'</li>';
        }
        $retorno .= '</ul></td>';
    }
    $retorno.= '</tr></table></div>¿Desea continuar?';
    return $retorno;
}

function imprimirMensajesAulas($msgs){
    $retorno = '';
    $retorno.= '<div class="panel panel-danger">'.
            '<div class="panel-heading">Conflictos</div>'.
            '<ul class="list-group">';
    foreach ($msgs as $msgsIntercambio) {
        foreach ($msgsIntercambio as $msgConflict){
            $retorno.= '<li class="list-group-item">'.$msgConflict.'</li>';
        }
    }
    $retorno .= '</ul></div>¿Desea continuar?';
    return $retorno;
}

function imprimirResultadoBusqueda(){
    $retorno = '<div id="search">';
    $retorno .= '<h3>Resultados de la busqueda</h3>';
    $retorno .= '<div id="contentResul"></div><center>';
    $retorno .= '<div id="page-selection"></div></center>';
    return $retorno;
}

function imprimirMensajeFaltantes($err){
    $retorno = '<div class="panel panel-danger">'.
            '<div class="panel-heading">Conflictos</div>'.
            '<ul class="list-group">';
    foreach ($err as $error){
        $retorno.= '<li class="list-group-item">'.$error.'</li>';
    }
    $retorno .= '</ul></div>';
    return $retorno;
}