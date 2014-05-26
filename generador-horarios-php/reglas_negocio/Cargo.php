<?php
/**
 * Description of Cargo
 *
 * @author abs
 */
class Cargo {
    
    private $nombre;
    private $dia_exento;
    
    function __construct($nombre, $dia_exento) {
        $this->nombre = $nombre;
        $this->dia_exento = $dia_exento;
    }
    
    public function getNombre() {
        return $this->nombre;
    }

    public function getDia_exento() {
        return $this->dia_exento;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setDia_exento($dia_exento) {
        $this->dia_exento = $dia_exento;
    }
    
}
