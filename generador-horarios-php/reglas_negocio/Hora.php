<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Hora
 *
 * @author arch
 */
class Hora {
    
    private $idHora;
    private $inicio;
    private $fin;
    private $grupo;
    private $disponible;

    public function __construct() {
        $this->idHora = 0;
        $this->inicio = "";
        $this->fin = "";
        $this->grupo = new Grupo();
        $this->disponible = true;
    }
    
    function __clone() {
        $this->grupo = clone $this->grupo;
    }

    public function getIdHora() {
        return $this->idHora;
    }

    public function getInicio() {
        return $this->inicio;
    }

    public function getFin() {
        return $this->fin;
    }

    public function setIdHora($id) {
        $this->idHora = $id;
    }

    public function setInicio($inicio) {
        $this->inicio = $inicio;
    }

    public function setFin($fin) {
        $this->fin = $fin;
    }
    
    public function getGrupo() {
        return $this->grupo;
    }

    public function estaDisponible() {
        return $this->disponible;
    }

    public function setGrupo($grupo) {
        $this->grupo = $grupo;
    }

    public function setDisponible($disponible) {
        $this->disponible = $disponible;
    }

}
