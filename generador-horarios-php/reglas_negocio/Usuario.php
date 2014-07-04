<?php
chdir(dirname(__FILE__));
require_once '../acceso_datos/Conexion.php';

class Usuario{	
        
        private $login;
	private $password;
	private $nombres;
	private $apellidos;        	
        private $departamento;	
	private $habilitado;
        
        public function __construct() {            
            $this->login = "";
            $this->password = "";
            $this->nombres="";
            $this->apellidos="";            
            $this->departamento="";            
            $this->habilitado = "";
        }
	
	public function comprobarPassword($password){
		if($this->password == $password){
			return TRUE;
		}else{
			throw new Exception("Usuario y Clave no coinciden");
		}
	}
	
	public function generarPassword(){
		
	}
	
	public function cambiarPassword($password){
		
	}
	
	public function deshabilitar(){
		
	}
	
	public function guardar(){
                $consulta = "UPDATE usuarios SET nombres='".$this->getNombres()."',"
                    . "apellidos='".$this->getApellidos()."',"                                       
                    . "departamento='".$this->getDepartamento()."',"
                    . "login='".$this->getLogin()."',"
                    . "password='".$this->getPassword()."'"    
                    . " WHERE id_usuario='".$this->getId()."'";                           
                conexion::consulta2($consulta);
	}
	
	public function destruir(){
		$consulta = "DELETE FROM usuarios WHERE id_usuario='".$this->getId()."'";            
                conexion::consulta2($consulta);
	}
                
        public function getNombres(){
            return $this->nombres;
        }
        
        public function getApellidos(){
            return $this->apellidos;
        }
               
        public function getDepartamento(){
            return $this->departamento;
        }
	
	public function getLogin(){
		return $this->login;
	}
	
	public function getPassword(){
		return $this->password;
	}
	
	public function estaHabilitado(){
		return $this->habilitado;
	}
                
        public function setNombres($nombres){
            $this->nombres = $nombres;
        }
        
        public function setApellidos($apellidos){
            $this->apellidos = $apellidos;
        }
                       
        public function setDepartamento($departamento){
            $this->departamento = $departamento;
        }
	
	public function setLogin($login){
		$this->login = $login;
	}
	
	public function setPassword($password){
		$this->password = $password;
	}
	
	public function setHabilitado($habilitado){
		$this->habilitado = $habilitado;
	}
        
}

?>