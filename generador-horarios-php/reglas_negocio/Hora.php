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
    private $id;
    private $inicio;
    private $fin;
    
    public function __construct($id) {
        
    }
    
    public function getId() {
        return $this->id;
    }

    public function getInicio() {
        return $this->inicio;
    }

    public function getFin() {
        return $this->fin;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setInicio($inicio) {
        $this->inicio = $inicio;
    }

    public function setFin($fin) {
        $this->fin = $fin;
    }


    
    
}
