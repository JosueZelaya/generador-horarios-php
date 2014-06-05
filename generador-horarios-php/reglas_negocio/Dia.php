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
    
    private $id;
    private $nombre;
    private $horas;
    
    function __construct($id, $nombre) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->horas = null;
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
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
