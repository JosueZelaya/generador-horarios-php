<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Aula
 *
 * @author alexander
 */
class Aula {
    //put your code here
    private $nombre;
    private $capacidad;
    private $disponible;
    private $dias;
    
    public function __construct() {
        $this->nombre = "";
        $this->capacidad = 0;
        $this->disponible = true;
        $this->dias = array();
    }
    
    public function getNombre() {
        return $this->nombre;
    }

    public function getCapacidad() {
        return $this->capacidad;
    }

    public function getDisponible() {
        return $this->disponible;
    }

    public function getDias() {
        return $this->dias;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setCapacidad($capacidad) {
        $this->capacidad = $capacidad;
    }

    public function setDisponible($disponible) {
        $this->disponible = $disponible;
    }

    public function setDias($dias) {
        $this->dias = $dias;
    }    
    
}
