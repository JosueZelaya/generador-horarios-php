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
    public $agrupaciones;
    private $asignaciones_docs;
    public $departamentos;
    private $materias;
    private $docentes;
    private $cargos;
    
    public function __construct($agrupaciones,$departamentos,$materias,$cargos) {
        $this->aulas = ManejadorAulas::getTodasAulas();
        for ($i = 0; $i < count($this->aulas); $i++) {
            $dias = ManejadorDias::getDias();
            for ($x = 0; $x < count($dias); $x++) {
                $horas = ManejadorDias::getHorasDia($dias[$x]->getNombre());
                $dias[$x]->setHoras($horas);
            }
            $this->aulas[$i]->setDias($dias);
        }
        $this->agrupaciones = $agrupaciones;
        $this->departamentos = $departamentos;
        $this->materias = $materias;
        $this->cargos = $cargos;
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

    public function getAsignaciones_docs() {
        return $this->asignaciones_docs;
    }

    public function setAsignaciones_docs($asignaciones_docs) {
        $this->asignaciones_docs = $asignaciones_docs;
    }
    
    public function getCargos() {
        return $this->cargos;
    }

    public function setCargos($cargos) {
        $this->cargos = $cargos;
    }
}
