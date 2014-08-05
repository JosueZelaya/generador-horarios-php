<?php

/**
 * Description of ManejadorAulas
 *
 * @author arch
 */
chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Dia.php';
include_once 'Hora.php';
include_once 'Aula.php';
include_once 'Grupo.php';
include_once 'ManejadorAulas.php';
include_once 'Procesador.php';
include_once 'ManejadorGrupos.php';

abstract class ManejadorAulas {
    
    public static function agregarAula($aula){
        if(!self::existe($aula)){
            if($aula->isExclusiva()){
                $exclusiva = "t";
            }else{
                $exclusiva = "f";
            }
            $consulta = "INSERT INTO aulas(cod_aula,capacidad,exclusiva) VALUES ('".$aula->getNombre()."',".$aula->getCapacidad().",'".$exclusiva."')";
            conexion::consulta2($consulta);
        }else{
            throw new Exception("Ya existe un aula con ese codigo");
        }
    }
    
    public static function existe($aula){
        $consulta = "SELECT COUNT(*) FROM aulas WHERE cod_aula='".$aula->getNombre()."'";
        $respuesta = conexion::consulta2($consulta);
        if($respuesta['count']>0){
            return true;
        }else{
            return false;
        }
    }
   
    /**
     * Devuelve todas las aulas de la facultad por capacidad ascendente
     * 
     * @return Aula[] $aulas
     */
    public static function getTodasAulas(){
        $aulas = array();
        $sql_consulta = "SELECT * FROM aulas ORDER BY capacidad ASC";
	$respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $aula = new Aula();
            $aula->setNombre($fila['cod_aula']);
            $aula->setCapacidad($fila['capacidad']);
            $aula->setDisponible(TRUE);
            $aula->setExclusiva($fila['exclusiva']);
            $aulas[] = $aula;
        }
        return $aulas;
    }
    
    /**
     * 
     * @param Aula[] $todas_aulas = Todas las aulas de la facultad
     * @param String $nombre_aula = Nombre del aula que se busca
     * @return null = Si no se encuentra el aula buscada
     */
    public static function getAula($todas_aulas, $nombre_aula){
        foreach ($todas_aulas as $aula){
            if(strcmp($aula->getNombre(),$nombre_aula)==0){
                return $aula;
            }
        }
        return null;
    }
    
    public static function buscarAulas($buscarComo){
        $aulas = array();
        $consulta = "SELECT * FROM aulas WHERE cod_aula iLIKE '$buscarComo%' ORDER BY cod_aula;";
        $respuesta = Conexion::consulta($consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $aula = new Aula();
            $aula->setNombre($fila['cod_aula']);
            $aula->setCapacidad($fila['capacidad']);
            $aula->setDisponible(TRUE);
            $aula->setExclusiva($fila['exclusiva']);
            $aulas[] = $aula;
        }
        return $aulas;
    }
    
    /**
     * Devuelve las aulas capaces de albergar a la cantidad de alumnos especificada
     * 
     * @param Aula $aulas = las aulas entra las cuales elegir
     * @param Integer $num_alumnos = cantidad de alumnos especificada
     * @return Aula[] $aulasSeleccionadas = las aulas seleccionadas que cumplen criterio de capacidad
     */
    public static function obtenerAulasPorCapacidad($aulas,$num_alumnos){
        $aulasSeleccionadas = array();
        foreach ($aulas as $aula){
            $capacidad = $aula->getCapacidad();
            if($capacidad >= $num_alumnos && !$aula->isExclusiva()){
                $aulasSeleccionadas[] = $aula;
            }
        }
        return $aulasSeleccionadas;
    }
    
    /** Devuelve todas las aulas que no han sido evaluadas para la asignacion de un grupo
     *  el array devuelto contiene las aulas en orden de capacidad descendente
     * @param Aula[] $todasAulas = todas las aulas de la facultad
     * @param Aula $aulaRef = aula que limita el area de busqueda en el array, se devolveran aulas desde indice 0 hasta el indice en que esta aula se encuentra en el array
     */
    public static function obtenerAulasCapacidadCercana($todasAulas,$aulaRef){
        for($i=0;$i<count($todasAulas);$i++){
            if($todasAulas[$i] === $aulaRef){
                $index = $i;
                break;
            }
        }
//        if($index!=(count($todasAulas)-1)){
        if($index!=0){
//            $newArray = array_slice($todasAulas, $index);
            $newArray = array_slice($todasAulas, 0, $index);
            foreach ($newArray as $i => $aula){
                if($aula->isExclusiva()){
                    unset($newArray[$i]);
                }
            }
            $invertArray = array_reverse($newArray);
            return $invertArray;
//            return array_values($newArray);
        } else{
            return array($todasAulas[0]);
//            return array(end($todasAulas));
        }
    }
    
    public static function getRangoHoras($horas,$grupo){
        $grupoAnterior=null;
        $rangoHoras=["inicio"=>"","fin"=>""];        
        foreach ($horas as $hora) {
            $grupoActual = $hora->getGrupo();
            if($grupoActual===$grupo){
                if($grupoAnterior===$grupo){
                    $rangoHoras['fin'] = $hora->getIdHora();   
                }else{
                    $rangoHoras['inicio'] = $hora->getIdHora(); 
                    $grupoAnterior = $grupo;
                }
            }elseif($grupoAnterior!=null){
                break;
            }
        }
        return $rangoHoras;
    }
    
    private static function getInfoHoraVacia($index,$dias,$hora){
        return ["texto" => "",
                "nombre" => "",
                "codigo" => "",                                
                "grupo" => "",
                "tipo" => "",
                "departamento" => "",
                "inicioBloque" => "",
                "finBloque" => "",
                "idHora" => $hora->getIdHora(),
                "dia" => $dias[$index]->getNombre(),
                "more" => false];        
    }
    
    private static function getInfoHoraReservada($index,$dias,$hora){
        return ["texto" => "reservada",
                "nombre" => "",
                "codigo" => "",                                
                "grupo" => "",
                "tipo" => "",
                "departamento" => "",
                "inicioBloque" => "",
                "finBloque" => "",
                "idHora" => $hora->getIdHora(),
                "dia" => $dias[$index]->getNombre(),
                "more" => false];        
    }

    /**
     * Devuelve el horario de la semana para un aula específica
     * 
     * @param Aula[] $aulas = Las aulas
     * @param String $aula = El nombre del aula
     * @param array $tabla Matriz que contendra el horario
     * @return array = una tabla que representa al horario de la semana
     */
    public static function getHorarioEnAula($aulas,$aula,$tabla){
        for ($i = 0; $i < count($aulas); $i++) {
            if(strcmp($aulas[$i]->getNombre(),$aula)==0){
                $dias = $aulas[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];
                        $grupo = $hora->getGrupo();
                        if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                            $cod_materia = ManejadorGrupos::obtenerCodigoPropietario($grupo->getAgrup()->getMaterias());
                            $nombres = ManejadorGrupos::obtenerNombrePropietario($grupo->getAgrup()->getMaterias());
                            $nombre = $nombres[0];
                            $departamento = ManejadorGrupos::getNombreDepartamento($grupo->getAgrup()->getMaterias());
                            if(count(array_unique($cod_materia))>1){
                                $nombre .= ' (Clonada)';
                            }
                            if($grupo->getTipo()=='TEORICO'){
                                $texto = $cod_materia[0]."<br/> GT: ".$grupo->getId_grupo();
                            } elseif($grupo->getTipo()=='DISCUSION'){
                                $texto = $cod_materia[0]."<br/> GD: ".$grupo->getId_grupo();
                            } elseif($grupo->getTipo()=='LABORATORIO'){
                                $texto = $cod_materia[0]."<br/> GL: ".$grupo->getId_grupo();
                            }
                            $rango = self::getRangoHoras($horas, $grupo);
                            $array = [
                                "texto" => $texto,
                                "nombre" => $nombre,
                                "codigo" => implode("-", $cod_materia),
                                "grupo" => $grupo->getId_grupo(),
                                "tipo" => $grupo->getTipo(),
                                "departamento" => $departamento[0],
                                "inicioBloque" => $rango['inicio'],
                                "finBloque" => $rango['fin'],
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                            if(count($cod_materia)>1){
                                $array['more']=true;
                            }
                        }else if(!$hora->estaDisponible() && $grupo->getId_grupo() == 0){
                            $array = self::getInfoHoraReservada($x, $dias, $hora);
                        }else{
                            $array = self::getInfoHoraVacia($x, $dias, $hora);
                        }
                        $tabla[$x+1][$y+1] = $array;
                    }
                }
                break;
            }
        }
        return $tabla;
    }
    
    /**
     * Devuelve el horario de la semana para un aula y filtrado por departamento
     * 
     * @param type $aulas = las aulas del campus
     * @param type $aula = el nombre del aula
     * @param type $id_depar = el identificador del departamento
     * @param type $agrups = las agrupaciones de la facultad
     * @param type $materias = las materias
     * @return type = una tabla que representa al horario de la semana
     */
    public static function getHorarioEnAula_Depar($aula,$id_depar,$tabla,$facultad){
        for ($i = 0; $i < count($facultad->getAulas()); $i++) {
            if(strcmp($facultad->getAulas()[$i]->getNombre(), $aula)==0){
                $dias = $facultad->getAulas()[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];                        
                        $grupo = $hora->getGrupo();
                        if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                            if(ManejadorGrupos::mismoDepartamento($grupo->getAgrup(), $id_depar)){
                                $cod_materia = ManejadorGrupos::obtenerCodigoPropietario($grupo->getAgrup()->getMaterias());
                                $nombres = ManejadorGrupos::obtenerNombrePropietario($grupo->getAgrup()->getMaterias());
                                $nombre = $nombres[0];
                                $departamento = ManejadorGrupos::getNombreDepartamento($grupo->getAgrup()->getMaterias());
                                if(count(array_unique($cod_materia))>1){
                                    $nombre .= ' (Clonada)';
                                }
                                if($grupo->getTipo()=='TEORICO'){
                                    $texto = $cod_materia[0]."<br/> GT: ".$grupo->getId_grupo();
                                } elseif($grupo->getTipo()=='DISCUSION'){
                                    $texto = $cod_materia[0]."<br/> GD: ".$grupo->getId_grupo();
                                } elseif($grupo->getTipo()=='LABORATORIO'){
                                    $texto = $cod_materia[0]."<br/> GL: ".$grupo->getId_grupo();
                                }
                                $rango = ManejadorAulas::getRangoHoras($horas, $grupo);
                                $array = [
                                "texto" => $texto,
                                "nombre" => $nombre,
                                "codigo" => implode("-", $cod_materia),
                                "grupo" => $grupo->getId_grupo(),
                                "tipo" => $grupo->getTipo(),
                                "departamento" => $departamento[0],
                                "inicioBloque" => $rango['inicio'],
                                "finBloque" => $rango['fin'],
                                "idHora" => $hora->getIdHora(),
                                "dia" => $dias[$x]->getNombre(),
                                "more" => false];
                                if(count($cod_materia)>1){
                                    $array['more']=true;
                                }
                            }else{
                                $array = self::getInfoHoraVacia($x, $dias, $hora);
                            }
                        }else if(!$hora->estaDisponible() && $grupo->getId_grupo() == 0){
                            $array = self::getInfoHoraReservada($x, $dias, $hora);
                        }else{
                            $array = self::getInfoHoraVacia($x, $dias, $hora);
                        }
                        $tabla[$x+1][$y+1] = $array;
                    }
                }
                break;
            }
        }
        return $tabla;
    }
    
    /**
     * Devuelve el horario para un aula filtrado por carrera
     * 
     * @param type $aulas = las aulas de la facultad
     * @param type $aula = nombre del aula solicitada
     * @param type $ids_agrups = identificadores de las agrupaciones
     * @return string = una tabla que representa al horario de la semana
     */
    public static function getHorarioEnAula_Carrera($aulas,$aula,$ids_agrups,$tabla){
        for ($i = 0; $i < count($aulas); $i++) {
            if(strcmp($aulas[$i]->getNombre(),$aula)==0){
                $dias = $aulas[$i]->getDias();
                for ($x = 0; $x < count($dias); $x++) {
                    $horas = $dias[$x]->getHoras();
                    for ($y = 0; $y < count($horas); $y++) {
                        $hora = $horas[$y];
                        $grupo = $hora->getGrupo();
                        if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                            if(in_array($grupo->getAgrup()->getId(), $ids_agrups)){
                                $cod_materia = ManejadorGrupos::obtenerCodigoPropietario($grupo->getAgrup()->getMaterias());
                                $nombres = ManejadorGrupos::obtenerNombrePropietario($grupo->getAgrup()->getMaterias());
                                $nombre = $nombres[0];
                                $departamento = ManejadorGrupos::getNombreDepartamento($grupo->getAgrup()->getMaterias());
                                if(count(array_unique($cod_materia))>1){
                                    $nombre .= ' (Clonada)';
                                }
                                if($grupo->getTipo()=='TEORICO'){
                                    $texto = $cod_materia[0]."<br/> GT: ".$grupo->getId_grupo();
                                } elseif($grupo->getTipo()=='DISCUSION'){
                                    $texto = $cod_materia[0]."<br/> GD: ".$grupo->getId_grupo();
                                } elseif($grupo->getTipo()=='LABORATORIO'){
                                    $texto = $cod_materia[0]."<br/> GL: ".$grupo->getId_grupo();
                                }
                                $rango = ManejadorAulas::getRangoHoras($horas, $grupo);
                                $array = [
                                    "texto" => $texto,
                                    "nombre" => $nombre,
                                    "codigo" => implode("-", $cod_materia),
                                    "grupo" => $grupo->getId_grupo(),
                                    "tipo" => $grupo->getTipo(),
                                    "departamento" => $departamento[0],
                                    "inicioBloque" => $rango['inicio'],
                                    "finBloque" => $rango['fin'],
                                    "idHora" => $hora->getIdHora(),
                                    "dia" => $dias[$x]->getNombre(),
                                    "more" => false];
                                if(count($cod_materia)>1){
                                    $array['more']=true;
                                }
                            }else{                                
                                $array = self::getInfoHoraVacia($x, $dias, $hora);
                            }                            
                        }else if(!$hora->estaDisponible() && $grupo->getId_grupo() == 0){
                            $array = self::getInfoHoraReservada($x, $dias, $hora);
                        }else{
                            $array = self::getInfoHoraVacia($x, $dias, $hora);
                        }
                        $tabla[$x+1][$y+1] = $array;
                    }
                }
                break;
            }            
        }
        return $tabla;
    }
    
    /**
     * Devuelve las aulas en las cuales el departamento especificado tiene clases
     * 
     * @param type $idDepartamento = departamento
     * @return string $aulas = array de aulas (solo el nombre del aula)
     */
    public static function getAulasDepartamento($id_depar,$facultad){
        $aulasSelec=array();
        $aulas = $facultad->getAulas();
        foreach ($aulas as $aula){
            $dias = $aula->getDias();
            for ($x = 0; $x < count($dias); $x++) {
                $horas = $dias[$x]->getHoras();                    
                for ($y = 0; $y < count($horas); $y++) {                        
                    $hora = $horas[$y];                        
                    $grupo = $hora->getGrupo();
                    if(!$hora->estaDisponible() && $grupo->getAgrup() != null){                            
                        if(ManejadorGrupos::mismoDepartamento($grupo->getAgrup(), $id_depar)){
                            $aulasSelec[] = $aula->getNombre();                                
                            goto next;
                        }   
                    }                      
                }
            }
            next:    
        }
        return $aulasSelec;
    }
    
    public static function getAulasCarrera($ids_agrups,$facultad){
        $aulasSeleccionadas=array();
        $aulas = $facultad->getAulas();
        $numAulas = count($aulas);                
        foreach ($aulas as $aula){
            $dias = $aula->getDias();
            for ($x = 0; $x < count($dias); $x++) {
                $horas = $dias[$x]->getHoras();
                for ($y = 0; $y < count($horas); $y++) {
                    $hora = $horas[$y];
                    $grupo = $hora->getGrupo();
                    if(!$hora->estaDisponible() && $grupo->getAgrup() != null){
                        if(in_array($grupo->getAgrup()->getId(), $ids_agrups)){                                                                                               
                            $aulasSeleccionadas[] = $aula->getNombre();
                            goto next;
                        }
                    }
                }                    
            }
            next:                
        }
        return $aulasSeleccionadas;
    }
    
    public static function asignarAulas($materias,$aulas,$exclusiva,$gt,$gl,$gd,$año,$ciclo){
//        if($exclusiva){
//            throw new Exception("Exclusiva: TRUE");
//        }else{
//            throw new Exception("Exclusiva: FALSE");
//        }
        
        $consulta = "";
        $conexion = Conexion::conectar();
        foreach ($materias as $materia) {
            $consulta = "DELETE FROM info_agrup_aula WHERE id_agrupacion='$materia' AND año='$año' AND ciclo='$ciclo'";
            Conexion::consultaSinCerrarConexion($conexion, $consulta);
            if($gt){
                $consulta = "INSERT INTO info_agrup_aula(id_agrupacion,año,ciclo,tipo_grupo,exclusiv_aula) VALUES ($materia,$año,$ciclo,1,'$exclusiva')";
                Conexion::consultaSinCerrarConexion($conexion, $consulta);
                foreach ($aulas as $aula) {
                    $consulta = "INSERT INTO lista_agrup_aula(id_agrupacion,año,ciclo,tipo_grupo,cod_aula) VALUES ($materia,$año,$ciclo,1,'$aula')";
                    Conexion::consultaSinCerrarConexion($conexion, $consulta);
                }
            }            
            if($gl){
                $consulta = "INSERT INTO info_agrup_aula(id_agrupacion,año,ciclo,tipo_grupo,exclusiv_aula) VALUES ($materia,$año,$ciclo,2,'$exclusiva')";
                Conexion::consultaSinCerrarConexion($conexion, $consulta);
                foreach ($aulas as $aula) {
                    $consulta = "INSERT INTO lista_agrup_aula(id_agrupacion,año,ciclo,tipo_grupo,cod_aula) VALUES ($materia,$año,$ciclo,2,'$aula')";
                    Conexion::consultaSinCerrarConexion($conexion, $consulta);
                }
            }
            if($gd){
                $consulta = "INSERT INTO info_agrup_aula(id_agrupacion,año,ciclo,tipo_grupo,exclusiv_aula) VALUES ($materia,$año,$ciclo,3,'$exclusiva')";
                Conexion::consultaSinCerrarConexion($conexion, $consulta);
                foreach ($aulas as $aula) {
                    $consulta = "INSERT INTO lista_agrup_aula(id_agrupacion,año,ciclo,tipo_grupo,cod_aula) VALUES ($materia,$año,$ciclo,3,'$aula')";
                    Conexion::consultaSinCerrarConexion($conexion, $consulta);
                }
            }
            
        }        
        Conexion::desconectar($conexion);
    }
    
    public static function getTodasAulasConPaginacion($pagina,$numeroResultados){
        $aulas = array();
        $pagina = ($pagina-1)*$numeroResultados;
        $sql_consulta = "SELECT * FROM aulas ORDER BY cod_aula ASC LIMIT ".$numeroResultados." OFFSET ".$pagina;
        $respuesta = conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $aula = new Aula();
            $aula->setNombre($fila['cod_aula']);
            $aula->setCapacidad($fila['capacidad']);
            $aula->setExclusiva($fila['exclusiva']);                        
            $aulas[] = $aula;
        }                   			
        return $aulas;
    }
    
    public static function eliminarAula($aula){
        $consulta = "DELETE FROM aulas WHERE cod_aula='".$aula->getNombre()."'";
        Conexion::consulta2($consulta);
    }
    
    public static function modificarAula($aula,$campo,$valor){
        if($campo=="capacidad" || $campo=="exclusiva"){
            if($campo=="capacidad" && !is_numeric($valor)){
                throw new Exception("La capacidad del aula debe ser un valor numérico.");
            }else if($campo=="exclusiva" && !($valor=="t" || $valor=="f")){
                throw new Exception("Valores no permitidos para la propiedad EXCLUSIVA del aula");
            }else{
                $consulta = "UPDATE aulas SET $campo='$valor' WHERE cod_aula='".$aula."'";
                Conexion::consulta2($consulta);
            }            
        }else{
            throw new Exception("No se permite modificar ese campo.");
        }
    }
    
    public static function getDiaEnAula($nombreAula,$aulas,$nombreDia){
        foreach ($aulas as $aula){
            if($aula->getNombre() == $nombreAula){
                $dia = $aula->getDia($nombreDia);
                return $dia;
            }
        }
        return null;
    }
    
    /** Determinar si los grupos caben en aula para intercambio
     * 
     * @param Grupo[] $grupos = grupos que se desea intercambiar
     * @param Aula $aula = aula hacia donde se quiere hacer el intercambio
     */
    public static function gruposExcedenCapacidad($grupos,$aula){
        $msgs = array();
        foreach ($grupos as $grupo){
            if($grupo->getId_grupo()==0){
                continue;
            }
            $cantidadAlumnos = $grupo->getAgrup()->getNum_alumnos();
            if($aula->getCapacidad() < $cantidadAlumnos){
                $msgs[] = "Grupo ".$grupo->getTipo()." ".$grupo->getId_grupo()." de la agrupacion que contiene a ".$grupo->getAgrup()->getMaterias()[0]->getNombre()." supera capacidad en aula ".$aula->getNombre();
            }
        }
        return $msgs;
    }
}
