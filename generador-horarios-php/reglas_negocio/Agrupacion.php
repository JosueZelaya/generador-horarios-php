<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Agrupacion
 *
 * @author alexander
 */
class Agrupacion {
    
    private $id;
    private $departamento;
    private $num_grupos;
    private $num_alumnos;
    private $numGruposAsignados;
    
    public function __construct($id,$departamento,$num_grupos,$num_alumnos){
        $this->id=$id;
        $this->departamento=$departamento;
        $this->num_grupos=$num_grupos;
        $this->num_alumnos=$num_alumnos;
        $this->numGruposAsignados=0;
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getDepartamento() {
        return $this->departamento;
    }

    public function setDepartamento($departamento) {
        $this->departamento = $departamento;
    }

    public function getNum_grupos() {
        return $this->num_grupos;
    }

    public function setNum_grupos($num_grupos) {
        $this->num_grupos = $num_grupos;
    }

    public function getNum_alumnos() {
        return $this->num_alumnos;
    }

    public function setNum_alumnos($num_alumnos) {
        $this->num_alumnos = $num_alumnos;
    }

    public function getNumGruposAsignados() {
        return $this->numGruposAsignados;
    }

    public function setNumGruposAsignados($numGruposAsignados) {
        $this->numGruposAsignados = $numGruposAsignados;
    }
    
}
