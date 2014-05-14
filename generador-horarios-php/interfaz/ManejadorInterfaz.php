<?php

    include_once '../reglas_negocio/ManejadorAgrupaciones.php';
    include_once '../reglas_negocio/Facultad.php';

    session_start();
    
    function imprimir($aula,$departamento,$carrera){
        $facultad = $_SESSION['facultad'];
        $modelo = create_model($facultad);
        if($carrera!='todos'){
            $tabla = ManejadorAulas::getHorarioEnAula_Carrera($facultad->getAulas(), $aula, $facultad->getMaterias(),$modelo,$facultad);
        }else if($departamento!='todos'){                        
            $tabla = ManejadorAulas::getHorarioEnAula_Depar($aula,$departamento,$modelo,$facultad);
        }else{
            $tabla = ManejadorAulas::getHorarioEnAula($facultad->getAulas(), $aula, $facultad->getMaterias(),$modelo,$facultad);
        }        
        
        for($i=0;$i<count($tabla);$i++){         
            echo "<div class='col'>";
            for($j=0;$j<count($tabla[$i]);$j++){                
                if($j==0){
                    echo "<div class='col-header'>".$tabla[$i][$j]."</div>";
                }else if($i==0){
                    echo "<div class='celda-hora'><div class='centrar'>".$tabla[$i][$j]."</div></div>";
                }else{
                    echo "<div class='celda-hora'>";                    
                    $celda = $tabla[$i][$j];
                    $contenido = "Materia: ".$celda['nombre']."<br/>"."Grupo: ".$celda['grupo']."<br/> Departamento: ".$celda['departamento'];
                    echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='top' data-content='".$contenido."'>".$celda['texto'].'</div></div>';
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
