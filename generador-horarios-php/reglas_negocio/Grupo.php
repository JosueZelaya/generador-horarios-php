<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Grupo
 *
 * @author abs
 */
class Grupo {
    
    private $id_agrup;
    private $id_grupo;
    private $id_docente;
    private $horasAsignadas;
    private $incompleto;
    
    function __construct() {
        $this->incompleto=false;
        $this->id_agrup = 0;
        $this->id_grupo = 0;
        $this->id_docente = 0;
        $this->horasAsignadas = 0;
    }
    
    public function getId_agrup() {
        return $this->id_agrup;
    }

    public function getId_grupo() {
        return $this->id_grupo;
    }

    public function getId_docente() {
        return $this->id_docente;
    }

    public function getHorasAsignadas() {
        return $this->horasAsignadas;
    }

    public function isIncompleto() {
        return $this->incompleto;
    }

    public function setId_agrup($id_agrup) {
        $this->id_agrup = $id_agrup;
    }

    public function setId_grupo($id_grupo) {
        $this->id_grupo = $id_grupo;
    }

    public function setId_docente($id_docente) {
        $this->id_docente = $id_docente;
    }

    public function setHorasAsignadas($horasAsignadas) {
        $this->horasAsignadas = $horasAsignadas;
    }

    public function setIncompleto($incompleto) {
        $this->incompleto = $incompleto;
    }

}
