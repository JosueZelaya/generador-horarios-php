<?php

/**
 * Almacena todos los datos de la base de datos necesarios para la generacion de un horario
 *
 * @author arch
 */

chdir(dirname(__FILE__));
require_once 'ManejadorAulas.php';
chdir(dirname(__FILE__));
require_once 'ManejadorDias.php';
chdir(dirname(__FILE__));
require_once 'ManejadorGrupos.php';
chdir(dirname(__FILE__));
require_once '../acceso_datos/Conexion.php';

class Facultad {
    private $aulas;
    private $agrupaciones;
    private $departamentos;
    private $materias;
    private $docentes;
    private $cargos;
    private $reservaciones;
    private $carreras;
    private $grupos;

    public function __construct($departamentos,$cargos,$reservaciones,$año,$ciclo) {
        $this->aulas = ManejadorAulas::getTodasAulas();
        foreach ($this->aulas as $aula){
            $dias = ManejadorDias::getDias($año,$ciclo);
            for ($x = 0; $x < count($dias); $x++) {
                $horas = ManejadorDias::getHorasDia($dias[$x]->getId(),$año,$ciclo);
                $dias[$x]->setHoras($horas);
            }
            $aula->setDias($dias);
        }
        $this->departamentos = $departamentos;
        $this->cargos = $cargos;
        $this->reservaciones = $reservaciones;
    }
    
    public function getAulas() {
        return $this->aulas;
    }

    public function getMaterias() {
        return $this->materias;
    }

    public function setAulas($aulas) {
        $this->aulas = $aulas;
    }

    public function setMaterias($materias) {
        $this->materias = $materias;
    }
    
    public function getDocentes() {
        return $this->docentes;
    }

    public function setDocentes($docentes) {
        $this->docentes = $docentes;
    }
    
    public function getAgrupaciones() {
        return $this->agrupaciones;
    }

    public function setAgrupaciones($agrupaciones) {
        $this->agrupaciones = $agrupaciones;
    }
    
    public function getCargos() {
        return $this->cargos;
    }

    public function setCargos($cargos) {
        $this->cargos = $cargos;
    }
    
    public function getReservaciones() {
        return $this->reservaciones;
    }

    public function setReservaciones($reservaciones) {
        $this->reservaciones = $reservaciones;
    }
    
    public function getDepartamentos() {
        return $this->departamentos;
    }

    public function setDepartamentos($departamentos) {
        $this->departamentos = $departamentos;
    }
    
    public function getCarreras() {
        return $this->carreras;
    }

    public function setCarreras($carreras) {
        $this->carreras = $carreras;
    }
    
    public function getGrupos() {
        return $this->grupos;
    }

    public function setGrupos($grupos) {
        $this->grupos = $grupos;
    }
    
    public function guardarHorario($año,$ciclo){
        $tipos = ManejadorGrupos::getTipos();
        foreach ($this->aulas as $aula){
            $dias = $aula->getDias();
            foreach ($dias as $dia){
                $horas = $dia->getHoras();
                foreach ($horas as $hora){
                    $grupo = $hora->getGrupo();
                    if($grupo->getId_grupo()!=0){
                        $tipo = ManejadorGrupos::getIdTipo($grupo->getTipo(), $tipos);
                        foreach ($grupo->getDocentes() as $docente){
                            $insert = "INSERT INTO asignaciones VALUES('".$aula->getNombre()."',".$dia->getId().",".$hora->getIdHora().",$año,$ciclo,".$grupo->getId_grupo().",".$grupo->getAgrup()->getId().",".$tipo.",".$docente->getIdDocente().")";
                            conexion::consulta($insert);
                        }
                    }
                }
            }
        }
        return 0;
    }
    
    /** Actualiza la tabla asignaciones dentro de la base de datos con los datos de intercambios
     * 
     * @param Grupo[] $grupos1 Grupos que se trasladaran a $dia2
     * @param Grupo[] $grupos2 Grupos que se trasladaran a $dia1
     * @param Dia $dia1 Dia de origen de $grupos1 y de destino de $grupos2
     * @param Dia $dia2 Dia de origen de $grupos2 y de destino de $grupos1
     * @param String $aula1 Aula de origen de $grupos1 y de destino de $grupos2
     * @param String $aula2 Aula de origen de $grupos2 y de destino de $grupos1
     * @param int[] $idHorasInsercion1 
     * @param int[] $idHorasInsercion2 
     */
    public function escribirIntercambio($grupos1,$grupos2,$dia1,$dia2,$aula1,$aula2,$idHorasInsercion1,$idHorasInsercion2,$año,$ciclo){
        $tipos = ManejadorGrupos::getTipos();
        foreach ($idHorasInsercion2 as $idHora){
            list($key,$grupo) = each($grupos1);
            $query = "DELETE FROM asignaciones WHERE cod_aula='$aula2' AND id_dia=".$dia2->getId()." AND id_hora=$idHora AND año=$año AND ciclo=$ciclo";
            Conexion::consulta($query);
            if($grupo->getId_grupo() != 0){
                foreach ($grupo->getDocentes() as $docente){
                    $query = "INSERT INTO asignaciones VALUES('$aula2',".$dia2->getId().",$idHora,$año,$ciclo,".$grupo->getId_grupo().",".$grupo->getAgrup()->getId().",".ManejadorGrupos::getIdTipo($grupo->getTipo(), $tipos).",".$docente->getIdDocente().")";
                    Conexion::consulta($query);
                }
            }
        }
        foreach ($idHorasInsercion1 as $idHora){
            list($key,$grupo) = each($grupos2);
            $query = "DELETE FROM asignaciones WHERE cod_aula='$aula1' AND id_dia=".$dia1->getId()." AND id_hora=$idHora AND año=$año AND ciclo=$ciclo";
            Conexion::consulta($query);
            if($grupo->getId_grupo() != 0){
                foreach ($grupo->getDocentes() as $docente){
                    $query = "INSERT INTO asignaciones VALUES('$aula1',".$dia1->getId().",$idHora,$año,$ciclo,".$grupo->getId_grupo().",".$grupo->getAgrup()->getId().",".ManejadorGrupos::getIdTipo($grupo->getTipo(), $tipos).",".$docente->getIdDocente().")";
                    Conexion::consulta($query);
                }
            }
        }
        return true;
    }
}