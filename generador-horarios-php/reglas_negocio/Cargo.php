<?php
/**
 * Description of Cargo
 *
 * @author abs
 */
class Cargo {
    
    private $id_cargo;
    private $id_dia_exento;
    
    function __construct($id_cargo, $dia_exento) {
        $this->id_cargo = $id_cargo;
        $this->id_dia_exento = $dia_exento;
    }
    
    public function getId_cargo() {
        return $this->id_cargo;
    }

    public function getId_dia_exento() {
        return $this->id_dia_exento;
    }

    public function setId_cargo($id_cargo) {
        $this->id_cargo = $id_cargo;
    }

    public function setId_dia_exento($id_dia_exento) {
        $this->id_dia_exento = $id_dia_exento;
    }
    
}
