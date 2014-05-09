<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Materia
 *
 * @author abs
 */
class Materia {
    private $codigo;
    private $nombre;
    private $ciclo;
    private $unidadesValorativas;
    private $departamento;
    private $codigoCarrera;
    private $planEstudio;
    private $idAgrupacion;
    private $incompleta;
    
    function __construct($codigo, $nombre, $ciclo, $unidadesValorativas, $departamento, $codigoCarrera, $planEstudio, $idAgrupacion, $incompleta) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->ciclo = $ciclo;
        $this->unidadesValorativas = $unidadesValorativas;
        $this->departamento = $departamento;
        $this->codigoCarrera = $codigoCarrera;
        $this->planEstudio = $planEstudio;
        $this->idAgrupacion = $idAgrupacion;
        $this->incompleta = $incompleta;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getCiclo() {
        return $this->ciclo;
    }

    public function getUnidadesValorativas() {
        return $this->unidadesValorativas;
    }

    public function getDepartamento() {
        return $this->departamento;
    }

    public function getCodigoCarrera() {
        return $this->codigoCarrera;
    }

    public function getPlanEstudio() {
        return $this->planEstudio;
    }

    public function getIdAgrupacion() {
        return $this->idAgrupacion;
    }

    public function getIncompleta() {
        return $this->incompleta;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setCiclo($ciclo) {
        $this->ciclo = $ciclo;
    }

    public function setUnidadesValorativas($unidadesValorativas) {
        $this->unidadesValorativas = $unidadesValorativas;
    }

    public function setDepartamento($departamento) {
        $this->departamento = $departamento;
    }

    public function setCodigoCarrera($codigoCarrera) {
        $this->codigoCarrera = $codigoCarrera;
    }

    public function setPlanEstudio($planEstudio) {
        $this->planEstudio = $planEstudio;
    }

    public function setIdAgrupacion($idAgrupacion) {
        $this->idAgrupacion = $idAgrupacion;
    }

    public function setIncompleta($incompleta) {
        $this->incompleta = $incompleta;
    }
    
    public function getTotalHorasRequeridas(){
        $total = round(($this->unidadesValorativas*20)/16);
        return $total;
    }
}
