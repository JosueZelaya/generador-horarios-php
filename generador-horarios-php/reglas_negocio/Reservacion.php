<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Reservacion
 *
 * @author arch
 */
class Reservacion {
    
    public $nombre_dia;
    public $inicio;
    public $fin;
    public $cod_aula;
    
    public function __construct($nombre_dia, $inicio, $fin, $cod_aula){
        $this->nombre_dia = $nombre_dia;
        $this->inicio = $inicio;
        $this->fin = $fin;
        $this->cod_aula = $cod_aula;
    }
    
}
