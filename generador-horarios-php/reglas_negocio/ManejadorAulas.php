<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManejadorAulas
 *
 * @author arch
 */

include_once '../acceso_datos/Conexion.php';
include_once 'Dia.php';
include_once 'Hora.php';
include_once 'Aula.php';
include_once 'Grupo.php';
include_once 'ManejadorAulas.php';
include_once 'Procesador.php';
include_once 'ManejadorDepartamentos.php';
include_once 'ManejadorAgrupaciones.php';

abstract class ManejadorAulas {

    
    /**
     * Devuelve todas las aulas de la facultad por capacidad ascendente
     * 
     * @return \Aula = array de aulas
     */
    public static function getTodasAulas(){
        $aulas = array();
        $sql_consulta = "SELECT * FROM aulas ORDER BY capacidad ASC";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){            
            $aula = new Aula();
            $aula->setNombre($fila['cod_aula']);
            $aula->setCapacidad($fila['capacidad']);
            $aula->setDisponible(TRUE);
            $aulas[] = $aula;
        }
        return $aulas;
    }
    
    /**
     * Devuelve todas las aulas de la facultad ordenadas por un criterio específico
     * 
     * @param type $ordenarPor = criterio
     * @return \Aula = array de aulas
     */
    public static function getTodasAulasOrdenadas($ordenarPor){
        $aulas = array();
        $sql_consulta = "SELECT * FROM aulas ORDER BY ".$ordenarPor." DESC";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){            
            $aula = new Aula();
            $aula->setNombre($fila['nombre']);
            $aula->setCapacidad($fila['capacidad']);
            $aula->setDisponible($fila['disponible']);
            $aulas[] = $aula;
        }
        return $aulas;
    }
    
    /**
     * Elige un aula diferente a las aulas ya usadas
     * 
     * @param type $aulas = las aulas de la facultad
     * @param type $aulasUsadas = las aulas ya usadas
     * @return null,$aulas = devuelve null si ya no hay más aulas, o devuelve el array con las aulas
     */
    public static function elegirAulaDiferente($aulas,$aulasUsadas){
        //Si ya se usaron todas las aulas entonces no seguimos buscando y devolvemos null
        if(count($aulas) ==  count($aulasUsadas)){
            return null;
        }
        $aula = ManejadorAulas::elegirAula($aulas);
        for ($index = 0; $index < count($aulasUsadas); $index++) {
            if(strcmp($aula->getNombre(),$aulasUsadas[$index]->getNombre())==0){
                $aula = ManejadorAulas::elegirAulaDiferente($aulas, $aulasUsadas);
            }
        }
        return $aulas;
    }
    
    /**
     * Elige un aula de manera aleatoria
     * 
     * @param type $aulas = aulas entre las cuales elegir
     * @return type = el aula elegida
     */
    public static function elegirAula($aulas){
        $desde=0;
        $hasta = count($aulas)-1;
        $aula = Procesador::getNumeroAleatorio($desde, $hasta);
        return $aulas[$aula];
    }
    
    /**
     * Devuelve las aulas capaces de albergar a la cantidad de alumnos especificada
     * 
     * @param type $aulas = las aulas entra las cuales elegir
     * @param type $num_alumnos = cantidad de alumnos especificada
     * @return type = las aulas seleccionadas
     */
    public static function obtenerAulasPorCapacidad($aulas,$num_alumnos){
        $aulasSeleccionadas = array();
        for ($index = 0; $index < count($aulas); $index++) {
            $aula = $aulas[$index];
            $capacidad = $aula->getCapacidad();
            if($capacidad >= $num_alumnos){
                $aulasSeleccionadas[] = $aulas[$index];
            }
        }
        return $aulasSeleccionadas;
    }

    /**
     * Devuelve el horario de la semana para un aula específica
     * 
     * @param type $aulas = Las aulas
     * @param type $aula = El nombre del aula
     * @param type $materias = Las materias
     * @param array $tabla Matriz que contendra el horario
     * @return type = una tabla que representa al horario de la semana
     */
    public static function getHorarioEnAula($aulas,$aula,$materias,$tabla,$facultad){
        for ($i = 0; $i < count($aulas); $i++) {
            if(strcmp($aulas[$i]->getNombre(),$aula)==0){
                $dias = $aulas[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];
                        $grupo = $hora->getGrupo();
                        if(!$hora->estaDisponible() && $grupo->getId_Agrup() != 0){
                            //$propietario = ManejadorAgrupaciones::obtenerNombrePropietario($grupo->getId_Agrup(),$materias);
                            $cod_materia = ManejadorAgrupaciones::obtenerCodigoPropietario($grupo->getId_agrup(),$materias);
                            $nombre = ManejadorAgrupaciones::obtenerNombrePropietario($grupo->getId_agrup(), $materias);
                            $idDepartamento = ManejadorAgrupaciones::obtenerIdDepartamento($grupo->getId_agrup(), $facultad->agrupaciones);
                            $departamento = ManejadorDepartamentos::getNombreDepartamento($idDepartamento, $facultad->departamentos);
                            $texto = $cod_materia."<br/> GT: ".$grupo->getId_grupo();
                            $array = [
                                "texto" => $texto,
                                "nombre" => $nombre,
                                "codigo" => $cod_materia,                                
                                "grupo" => $grupo->getId_grupo(),
                                "departamento" => $departamento
                            ];
                            $tabla[$x+1][$y+1] = $array;
                        }else{
                            $tabla[$x+1][$y+1] = "";
                        }
                    }
                }
                break;
            }
        }
        return $tabla;
    }
    
    /**
     * Devuelve el horario de la semana para un aula y filtrado por departamento
     * 
     * @param type $aulas = las aulas del campus
     * @param type $aula = el nombre del aula
     * @param type $id_depar = el identificador del departamento
     * @param type $agrups = las agrupaciones de la facultad
     * @param type $materias = las materias
     * @return type = una tabla que representa al horario de la semana
     */
    public static function getHorarioEnAula_Depar($aulas,$aula,$id_depar,$agrups,$materias,$tabla){
        for ($i = 0; $i < count($aulas); $i++) {
            if(strcmp($aulas[$i]->getNombre(), $aula)==0){
                $dias = $aulas[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];
                        $grupo = $hora->getGrupo();
                        if(ManejadorDepartamentos::getIdDepartamento($grupo->getId_Agrup(),$agrups)==$id_depar){
                            $texto = ManejadorAgrupaciones::obtenerNombrePropietario($grupo->getId_Agrup(), $materias)." GT: ".$grupo->getId_grupo();
                            $tabla[$x][$y] = $texto;
                        }else{
                            $tabla[$x][$y] = "";
                        }
                    }
                }
                break;
            }
        }
        return $tabla;
    }
    
    /**
     * Devuelve el horario para un aula filtrado por carrera
     * 
     * @param type $aulas = las aulas de la facultad
     * @param type $aula = nombre del aula solicitada
     * @param type $ids_agrups = identificadores de las agrupaciones
     * @param type $materias = las materias
     * @return string = una tabla que representa al horario de la semana
     */
    public static function getHorarioEnAula_Carrera($aulas,$aula,$ids_agrups,$materias,$tabla){
        for ($i = 0; $i < count($aulas); $i++) {
            if(strcmp($aulas[$i]->getNombre(),$aula)==0){
                $dias = $aulas[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];
                        $grupo = $hora->getGrupo();
                        for ($z = 0; $z < count($ids_agrups); $z++) {
                            if($ids_agrups[$z]==$grupo->getId_Agrup()){
                                $texto = ManejadorAgrupaciones::obtenerNombrePropietario($grupo->getId_Agrup(), $materias)." GT: ".$grupo->getId_grupo();
                                $tabla[$x][$y] = $texto;
                            }else{
                                $tabla[$x][$y] = "";
                            }
                        }
                    }                    
                }
                break;
            }            
        }
        return $tabla;
    }   
}
