<?php

/**
 * Description of Facultad
 *
 * @author arch
 */

chdir(dirname(__FILE__));
require_once 'ManejadorAulas.php';
require_once 'ManejadorDias.php';

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
}
