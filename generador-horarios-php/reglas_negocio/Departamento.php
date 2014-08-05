<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Departamento
 *
 * @author abs
 */
class Departamento {
    
    private $id;
    private $nombre;
    private $activo;
    
    function __construct($id, $nombre) {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    
    public function setActivo($activo) {
        $this->activo = $activo;
    }

    public function estaActivo() {
        if($this->activo=="t"){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
}
