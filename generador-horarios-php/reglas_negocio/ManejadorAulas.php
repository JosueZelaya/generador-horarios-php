<?php

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
        $sql_consulta = "SELECT * FROM aulas ORDER BY cod_aula ASC";
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
    
    public static function getAula($todas_aulas, $nombre_aula){
        foreach ($todas_aulas as $aula){
            if(strcmp($aula->getNombre(),$nombre_aula)==0){
                return $aula;
            }
        }
        return null;
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
    
    public static function getRangoHoras($horas,$grupo){
        $grupoAnterior="";
        $rangoHoras=["inicio"=>"","fin"=>""];        
        foreach ($horas as $hora) {
            $grupoActual = $hora->getGrupo();
            if($grupoActual===$grupo){
                if($grupoAnterior===$grupo){                                    
//                    $rangoHoras['fin'] = $hora->getFin();
                      $rangoHoras['fin'] = $hora->getIdHora();   
                }else{                    
//                    $rangoHoras['inicio'] = $hora->getInicio();
//                    $rangoHoras['fin'] = $hora->getFin();
                      $rangoHoras['inicio'] = $hora->getIdHora(); 
                    $grupoAnterior = $grupo;
                }
            }            
        }
        return $rangoHoras;
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
                            $cod_materia = ManejadorAgrupaciones::obtenerCodigoPropietario($grupo->getId_agrup(),$materias);
                            $nombre = ManejadorAgrupaciones::obtenerNombrePropietario($grupo->getId_agrup(), $materias);
                            $idDepartamento = ManejadorAgrupaciones::obtenerIdDepartamento($grupo->getId_agrup(), $facultad->agrupaciones);
                            $departamento = ManejadorDepartamentos::getNombreDepartamento($idDepartamento, $facultad->departamentos);
                            $texto = $cod_materia."<br/> GT: ".$grupo->getId_grupo();
                            $rango = ManejadorAulas::getRangoHoras($horas, $grupo);
                            $array = [
                                "texto" => $texto,
                                "nombre" => $nombre,
                                "codigo" => $cod_materia,                                
                                "grupo" => $grupo->getId_grupo(),
                                "departamento" => $departamento,
                                "inicioBloque" => $rango['inicio'],
                                "finBloque" => $rango['fin'],
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre()
                            ];
                            $tabla[$x+1][$y+1] = $array;
                        }else{
                            $array = [
                                "texto" => "",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre()
                            ];
                            $tabla[$x+1][$y+1] = $array;
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
    public static function getHorarioEnAula_Depar($aula,$id_depar,$tabla,$facultad){
        for ($i = 0; $i < count($facultad->getAulas()); $i++) {
            if(strcmp($facultad->getAulas()[$i]->getNombre(), $aula)==0){
                $dias = $facultad->getAulas()[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];                        
                        $grupo = $hora->getGrupo();
                        if(!$hora->estaDisponible() && $grupo->getId_Agrup() != 0){                            
                            if(ManejadorAgrupaciones::obtenerIdDepartamento($grupo->getId_agrup(),$facultad->agrupaciones)==$id_depar){                                
                                $cod_materia = ManejadorAgrupaciones::obtenerCodigoPropietario($grupo->getId_agrup(),$facultad->getMaterias());
                                $nombre = ManejadorAgrupaciones::obtenerNombrePropietario($grupo->getId_agrup(), $facultad->getMaterias());                                
                                $departamento = ManejadorDepartamentos::getNombreDepartamento($id_depar, $facultad->departamentos);
                                $texto = $cod_materia."<br/> GT: ".$grupo->getId_grupo();
                                $rango = ManejadorAulas::getRangoHoras($horas, $grupo);
                                $array = [
                                "texto" => $texto,
                                "nombre" => $nombre,
                                "codigo" => $cod_materia,                                
                                "grupo" => $grupo->getId_grupo(),
                                "departamento" => $departamento,
                                "inicioBloque" => $rango['inicio'],
                                "finBloque" => $rango['fin'],
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre()    
                                ];
                                $tabla[$x+1][$y+1] = $array;
                            }else{
                                 $array = [
                                "texto" => "",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre()     
                                ];
                                $tabla[$x+1][$y+1] = $array;
                            }
                            
                        }else{
                            $array = [
                                "texto" => "",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre()
                            ];
                            $tabla[$x+1][$y+1] = $array;
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
    public static function getHorarioEnAula_Carrera($aulas,$aula,$ids_agrups,$tabla,$facultad){
        for ($i = 0; $i < count($aulas); $i++) {
            if(strcmp($aulas[$i]->getNombre(),$aula)==0){
                $dias = $aulas[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];
                        $grupo = $hora->getGrupo();
                        for ($z = 0; $z < count($ids_agrups); $z++) {                            
                            if(strcmp($ids_agrups[$z],$grupo->getId_Agrup())==0){                                                                
                                $cod_materia = ManejadorAgrupaciones::obtenerCodigoPropietario($grupo->getId_agrup(),$facultad->getMaterias());
                                $nombre = ManejadorAgrupaciones::obtenerNombrePropietario($grupo->getId_agrup(), $facultad->getMaterias());
                                $idDepartamento = ManejadorAgrupaciones::obtenerIdDepartamento($grupo->getId_agrup(), $facultad->agrupaciones);
                                $departamento = ManejadorDepartamentos::getNombreDepartamento($idDepartamento, $facultad->departamentos);
                                $texto = $cod_materia."<br/> GT: ".$grupo->getId_grupo();
                                $rango = ManejadorAulas::getRangoHoras($horas, $grupo);
                                $array = [
                                    "texto" => $texto,
                                    "nombre" => $nombre,
                                    "codigo" => $cod_materia,                                
                                    "grupo" => $grupo->getId_grupo(),
                                    "departamento" => $departamento,
                                    "inicioBloque" => $rango['inicio'],
                                    "finBloque" => $rango['fin'],
                                    "idHora" => $hora->getIdHora(),
                                    "dia" => $dias[$x]->getNombre()
                                ];
                                $tabla[$x+1][$y+1] = $array;
                                break;
                            }else{                                
                                 $array = [
                                "texto" => "",
                                "nombre" => "",
                                "codigo" => "",                                
                                "grupo" => "",
                                "departamento" => "",
                                "inicioBloque" => "",
                                "finBloque" => "",
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre()    
                                ];
                                $tabla[$x+1][$y+1] = $array;
                            }
                        }
                    }                    
                }
                break;
            }            
        }
        return $tabla;
    }
    
    /**
     * Devuelve las aulas en las cuales el departamento especificado tiene clases
     * 
     * @param type $idDepartamento = departamento
     * @return string $aulas = array de aulas (solo el nombre del aula)
     */
    public static function getAulasDepartamento($id_depar,$facultad){
        $aulas=array();
        $numAulas = count($facultad->getAulas());
        for ($i = 0; $i < $numAulas; $i++) {
            $aula = $facultad->getAulas()[$i];            
                $dias = $aula->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();                    
                    for ($y = 0; $y < count($horas); $y++) {                        
                        $hora = $horas[$y];                        
                        $grupo = $hora->getGrupo();
                        if(!$hora->estaDisponible() && $grupo->getId_Agrup() != 0){                            
                            if(ManejadorAgrupaciones::obtenerIdDepartamento($grupo->getId_agrup(),$facultad->agrupaciones)==$id_depar){                                
                                $aulas[] = $aula->getNombre();                                
                                goto next;
                            }   
                        }                      
                    }
                }
            next:    
        }
        return $aulas;
    }
    
    public static function getAulasCarrera($ids_agrups,$facultad){
        $aulasSeleccionadas=array();
        $aulas = $facultad->getAulas();
        $numAulas = count($aulas);                
        for ($i = 0; $i < $numAulas; $i++) {            
            $aula = $aulas[$i];                        
            $dias = $aula->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];
                        $grupo = $hora->getGrupo();                        
                        for ($z = 0; $z < count($ids_agrups); $z++) {                                                        
                            if(strcmp($ids_agrups[$z],$grupo->getId_Agrup())==0){                                                                                                
                                $aulasSeleccionadas[] = $aula->getNombre();                                                                
                                goto next;
                            }
                        }
                    }                    
                }
            next:                
        }
        return $aulasSeleccionadas;
    }
}
