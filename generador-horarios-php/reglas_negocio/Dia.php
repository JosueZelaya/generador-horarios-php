<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dia
 *
 * @author arch
 */
class Dia {
    
    private $nombre;
    private $horas;
    
    public function __construct(){
        $this->nombre = "";
        $this->horas=array();
    }
    
    public function getNombre() {
        return $this->nombre;
    }

    public function getHoras() {
        return $this->horas;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setHoras($horas) {
        $this->horas = $horas;
    }

}
