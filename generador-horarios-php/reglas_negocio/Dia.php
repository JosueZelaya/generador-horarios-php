<?php
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
    
    function __clone() {
        foreach ($this->horas as $hora){
            $horas[] = clone $hora;
        }
        $this->horas = $horas;
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
    
    public function addHora($hora){
        $this->horas[] = $hora;
    }
    
    public function getPosEnDiaHora($idHora){
        $index = 0;
        foreach ($this->horas as $hora) {
            if($hora->getIdHora() == $idHora){
                return $index;
            }
            $index++;
        }
    }
    
    public function getHoraXID($idHora){
        foreach ($this->horas as $hora){
            if($hora->getIdHora() == $idHora){
                return $hora;
            }
        }
        return null;
    }
}
