<?php

chdir(dirname(__FILE__));
require_once '../acceso_datos/Conexion.php';
chdir(dirname(__FILE__));

class Docente {
    
    private $idDocente;
    private $contratacion;
    private $cargo;
    private $grupos;
    private $horario;
    private $depar;
    private $nombres;
    private $apellidos;
            
    function __construct($idDocente,$nombres,$apellidos,$contratacion,$depar) {
        $this->idDocente = $idDocente;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->cargo = null;
        $this->contratacion = $contratacion;
        $this->grupos = array();
        $this->horario=null;
        $this->depar = $depar;
    }
    
    function __toString() {
        return $this->nombres.' '.$this->apellidos;
    }

    public function getIdDocente() {
        return $this->idDocente;
    }

    public function setIdDocente($idDocente) {
        $this->idDocente = $idDocente;
    }
    
    public function getCargo() {
        return $this->cargo;
    }

    public function setCargo($cargo) {
        $this->cargo = $cargo;
    }
    
    public function getContratacion() {
        return $this->contratacion;
    }

    public function setContratacion($contratacion) {
        $this->contratacion = $contratacion;
    }
    
    public function getGrupos() {
        return $this->grupos;
    }

    public function setGrupos($grupos) {
        $this->grupos = $grupos;
    }
    
    public function addGrupo($grupo){
        $this->grupos[] = $grupo;
    }
    
    public function getHorario() {
        return $this->horario;
    }

    public function setHorario($horario) {
        $this->horario = $horario;
    }
    
    public function getDiasHabiles(){
        if($this->horario!=null){
            foreach ($this->horario as $dia) {
                $dias[] = $dia->getId();
            }
            return $dias;
        } else{
            return null;
        }
    }
    
    public function diaHabil($id_dia){
        if($this->horario==null){
            return true;
        } else{
            foreach ($this->horario as $dia) {
                if($dia->getId()==$id_dia){
                    return true;
                }
            }
        }
        return false;
    }

    public function getDepar() {
        return $this->depar;
    }

    public function setDepar($depar) {
        $this->depar = $depar;
    }
    public function getNombre_completo() {
        return $this->nombres.' '.$this->apellidos;
    }
    
    public function getNombres() {
        return $this->nombres;
    }

    public function getApellidos() {
        return $this->apellidos;
    }

    public function setNombres($nombres) {
        $this->nombres = $nombres;
    }

    public function setApellidos($apellidos) {
        $this->apellidos = $apellidos;
    }

    public function desactivar(){
        $consulta = "UPDATE docentes SET activo='f' WHERE id_docente='".$this->idDocente."';";            
        conexion::consulta($consulta);
        $consulta = "UPDATE usuarios SET habilitado='f' WHERE id_docente='".$this->idDocente."';";
        conexion::consulta($consulta);
    }
    
    public function guardar(){
        if($this->cargo=="" || $this->cargo==NULL){
            $consulta = "UPDATE docentes SET nombres='".$this->nombres."',"
            . "apellidos='".$this->apellidos."',"                    
            . "contratacion='".$this->contratacion."',"
            . "id_depar=".$this->depar.","
            . "cargo=NULL"            
            . " WHERE id_docente='".$this->idDocente."'";
        }else{
            $consulta = "UPDATE docentes SET nombres='".$this->nombres."',"
            . "apellidos='".$this->apellidos."',"                    
            . "contratacion='".$this->contratacion."',"
            . "id_depar=".$this->depar.","
            . "cargo=".$this->cargo.""            
            . " WHERE id_docente='".$this->idDocente."'";
        }        
        conexion::consulta($consulta);
    }
}
