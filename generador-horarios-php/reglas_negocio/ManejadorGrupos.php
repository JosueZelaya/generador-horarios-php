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
include_once 'Departamento.php';
include_once 'Carrera.php';

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
    
    public static function gruposIgualesEnBloque($grupos){
        if($grupos[0]->getId_grupo() != 0){
            $base = $grupos[0];
        } else{
            $base = $grupos[1];
        }
        for ($i=1;$i<count($grupos);$i++){
            if($base->getAgrup()->getId() != $grupos[$i]->getAgrup()->getId() || $base->getId_grupo() != $grupos[$i]->getId_grupo()){
                if ($i == count($grupos)-1 && $grupos[$i]->getAgrup() == NULL){
                    break;
                } else{
                    return false;
                }
            }
        }
        return true;
    }
    
    public static function mismoDepartamento($agrup,$id_depar){
        $materias = $agrup->getMaterias();
        foreach ($materias as $materia){
            if($materia->getCarrera()->getDepartamento()->getId() == $id_depar){
                return true;
            }
        }
        return false;
    }
    
    /**
     * Devuelve el nombre del propietario un grupo
     * 
     * @param type $materias = la agrupación
     * @return string = El nombre de la materia que es propietaria de la agrupacion
     */
    public static function obtenerNombrePropietario($materias){
        foreach ($materias as $materia){
            $propietario[] = $materia->getNombre();
        }
        return $propietario;
    }
    
    public static function obtenerCodigoPropietario($materias){
        foreach ($materias as $materia){
            $propietario[] = $materia->getCodigo();
        }
        return $propietario;
    }
    
    /**
     * Sirve para conocer el dia del departamento al que pertenece un grupo
     * 
     * @param type $materias = materias para ver su departamento
     * @return array = id del departamento
     */
    public static function obtenerIdDepartamento($materias){
        $departamento = "";
        foreach ($materias as $materia) {
            $departamento += $materia->getCarrera()->getDepartamento()->getId()+" ";
        }
        return substr($departamento,0,-1);
    }
    
    public static function getNombreDepartamento($materias){
        foreach ($materias as $materia){
            $nombreDepar[] = $materia->getCarrera()->getDepartamento()->getNombre();
        }
        return $nombreDepar;
    }
    
    public static function obtenerCarreraPropietario($materias){
        foreach ($materias as $materia){
            $carreras[] = $materia->getCarrera()->getNombre();
        }
        return $carreras;
    }
}
