<?php

/**
 * Description of ManejadorGrupo
 *
 * @author abs
 */

include_once '../acceso_datos/Conexion.php';
include_once 'Dia.php';
include_once 'Hora.php';
include_once 'ManejadorAgrupaciones.php';
include_once 'Grupo.php';

abstract class ManejadorGrupos {
    
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
                    if(ManejadorAgrupaciones::obtenerIdAgrupacion($cod_materia, $id_depar, $materias) == $grupo->getId_Agrup() && $grupo->getId_grupo() == $id_grupo){
                        $texto = ManejadorAgrupaciones::obtenerNombrePropietario($id_agrup, $materias)+" GT: "+$grupo->getId_grupo();
                        $tabla[$y][$x] = $texto;
                    } else{
                        $tabla[$y][$x] = '';
                    }
                }
            }
        }
        return $tabla;
    }
    
    public static function gruposIgualesEnBloque($grupos){
        if($grupos[0]->getId_grupo() != 0){
            $base = $grupos[0];
        } else{
            $base = $grupos[1];
        }
        for ($i=1;$i<count($grupos);$i++){
            if($base->getId_agrup() != $grupos[$i]->getId_agrup() || $base->getId_grupo() != $grupos[$i]->getId_grupo()){
                if ($i == count($grupos)-1 && $grupos[$i]->getId_agrup() == 0){
                    break;
                } else{
                    return false;
                }
            }
        }
        return true;
    }
}
