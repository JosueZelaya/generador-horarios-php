<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
    public $asignaciones_docs;
    public $departamentos;
    private $materias;
    
    public function __construct($agrupaciones,$departamentos,$asignaciones_docs) {
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
        $this->asignaciones_docs = $asignaciones_docs;
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

}
