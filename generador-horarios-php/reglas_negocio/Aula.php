<?php
/**
 * Description of Aula
 *
 * @author alexander
 */
class Aula {

    private $nombre;
    private $capacidad;
    private $disponible;
    private $dias;
    private $exclusiva;
    
    public function __construct() {
        $this->nombre = "";
        $this->capacidad = 0;
        $this->disponible = true;
        $this->dias = array();
        $this->exclusiva = false;
    }
    
    function __clone() {
        foreach ($this->dias as $dia){
            $dias[] = clone $dia;
        }
        $this->dias = $dias;
    }


    public function getNombre() {
        return $this->nombre;
    }

    public function getCapacidad() {
        return $this->capacidad;
    }


    public function estaDisponible() {
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
    
    /*
     * Devuelve el día que le indiquemos por medio del nombre
     * Si no lo encuentra devuelve null
     */
    public function getDia($nombre_dia){
        foreach ($this->dias as $dia){
            if(strcmp($dia->getNombre(),$nombre_dia)==0){
                return $dia;            
            }                
        }
        return null;
    }
    
    public function isExclusiva() {
        if($this->exclusiva=='t'){
            return true;
        } else{
            return false;
        }
    }

    public function setExclusiva($exclusiva) {
        $this->exclusiva = $exclusiva;
    }
    
}
