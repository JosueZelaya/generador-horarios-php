<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManejadorGrupo
 *
 * @author abs
 */
abstract class ManejadorGrupo {
    
    public static function getGrupo($aulas,$aulaElegida,$diaElegido,$idHora){
        if(isset($aulaElegida)){
            foreach ($aulas as $aula){
                if(strcmp($aula->getNombre(),$aulaElegida)==0){
                    $dias = $aula->getDias();
                    foreach ($dias as $dia){
                        if(strcmp($dia->getNombre(), $diaElegido)==0){
                            $horas = $dia->getHoras();
                            foreach ($horas as $hora){
                                if($hora->getIdHora() == $idHora){
                                    return $hora->getGrupo();
                                }
                            }
                        }
                    }
                }
            }
        }
        return null;
    }
    
    public static function obtenerHorarioDeGrupo($materias,$aulas,$cod_materia,$id_depar,$id_grupo,$tabla){
        foreach ($aulas as $aula){
            $dias = $aula->getDias();
            $cuentaDias = count($dias);
            for ($x=0;$x<$cuentaDias;$x++){
                $horas = $dias[$x]->getHoras();
                $cuentaHoras = count($horas);
                for ($y=0;$y<$cuentaHoras;$y++){
                    $grupo = $horas[$y]->getGrupo();
                    
                }
            }
        }
    }
    
}
