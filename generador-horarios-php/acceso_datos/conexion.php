<?php

abstract class conexion {
	
	public static function conectar(){
		$cadena_de_conexion = "host=localhost port=5432 dbname=basedatosiglesia user=basedatosiglesia password=password connect_timeout=5";
		$conexion = $dbconn4 = pg_connect($cadena_de_conexion) or die ('No se ha podido conectar a la Base de Datos');
		return $conexion;	
	}
	
	public static function desconectar($conexion){
		pg_close($conexion);
	}
	
	public static function consulta($sql_consulta){
                $conexion = conexion::conectar();
		$respuesta = pg_exec($conexion, $sql_consulta) or die("No se pudo ejecutar la consulta:".$sql_consulta."\n"); 
		$array = pg_fetch_array($respuesta);
		conexion::desconectar($conexion);
		return $array;
	}
	
	public static function consulta2($sql_consulta){
                $conexion = conexion::conectar();
		$respuesta = pg_exec($conexion, $sql_consulta) or die("No se pudo ejecutar la consulta:".$sql_consulta."\n"); 		
		conexion::desconectar($conexion);
		return $respuesta;
	}
	
}

?>