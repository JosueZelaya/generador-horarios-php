<?php

/**
 * Description of Facultad
 *
 * @author arch
 */

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
    
    public function __construct($departamentos,$cargos,$reservaciones,$año,$ciclo) {
        $this->aulas = ManejadorAulas::getTodasAulas();
        for ($i = 0; $i < count($this->aulas); $i++) {
            $dias = ManejadorDias::getDias($año,$ciclo);
            for ($x = 0; $x < count($dias); $x++) {
                $horas = ManejadorDias::getHorasDia($dias[$x]->getId(),$año,$ciclo);
                $dias[$x]->setHoras($horas);
            }
            $this->aulas[$i]->setDias($dias);
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
}
