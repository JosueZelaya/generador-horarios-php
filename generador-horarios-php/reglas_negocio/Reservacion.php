<?php
/**
 * Description of Reservacion
 *
 * @author arch
 */
class Reservacion {
    
    private $id_dia;
    private $id_hora;
    private $cod_aula;
    
    public function __construct($id_dia, $id_hora, $cod_aula){
        $this->id_dia = $id_dia;
        $this->id_hora = $id_hora;
        $this->cod_aula = $cod_aula;
    }

    public function getId_hora() {
        return $this->id_hora;
    }

    public function getCod_aula() {
        return $this->cod_aula;
    }

    public function setId_hora($id_hora) {
        $this->id_hora = $id_hora;
    }

    public function setCod_aula($cod_aula) {
        $this->cod_aula = $cod_aula;
    }
    
    public function getId_dia() {
        return $this->id_dia;
    }

    public function setId_dia($id_dia) {
        $this->id_dia = $id_dia;
    }
}
