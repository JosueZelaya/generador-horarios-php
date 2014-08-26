<?php

/**
 * Almacena todos los datos de la base de datos necesarios para la generacion de un horario
 *
 * @author arch
 */

chdir(dirname(__FILE__));
require_once 'ManejadorAulas.php';
chdir(dirname(__FILE__));
require_once 'ManejadorDias.php';
chdir(dirname(__FILE__));
require_once 'ManejadorGrupos.php';
chdir(dirname(__FILE__));
require_once '../acceso_datos/Conexion.php';
chdir(dirname(__FILE__));

class Facultad {
    private $aulas;
    private $agrupaciones;
    private $departamentos;
    private $materias;
    private $docentes;
    private $cargos;
    private $reservaciones;
    private $carreras;
    private $grupos;

    public function __construct($departamentos,$cargos,$reservaciones,$año,$ciclo) {
        $this->aulas = ManejadorAulas::getTodasAulas();
        foreach ($this->aulas as $aula){
            $dias = ManejadorDias::getDias($año,$ciclo);
            for ($x = 0; $x < count($dias); $x++) {
                $horas = ManejadorDias::getHorasDia($dias[$x]->getId(),$año,$ciclo);
                $dias[$x]->setHoras($horas);
            }
            $aula->setDias($dias);
        }
        $this->departamentos = $departamentos;
        $this->cargos = $cargos;
        $this->reservaciones = $reservaciones;
    }
    
    public function getAulas() {
        return $this->aulas;
    }

    public function getMaterias() {
        return $this->materias;
    }

    public function setAulas($aulas) {
        $this->aulas = $aulas;
    }

    public function setMaterias($materias) {
        $this->materias = $materias;
    }
    
    public function getDocentes() {
        return $this->docentes;
    }

    public function setDocentes($docentes) {
        $this->docentes = $docentes;
    }
    
    public function getAgrupaciones() {
        return $this->agrupaciones;
    }

    public function setAgrupaciones($agrupaciones) {
        $this->agrupaciones = $agrupaciones;
    }
    
    public function getCargos() {
        return $this->cargos;
    }

    public function setCargos($cargos) {
        $this->cargos = $cargos;
    }
    
    public function getReservaciones() {
        return $this->reservaciones;
    }

    public function setReservaciones($reservaciones) {
        $this->reservaciones = $reservaciones;
    }
    
    public function getDepartamentos() {
        return $this->departamentos;
    }

    public function setDepartamentos($departamentos) {
        $this->departamentos = $departamentos;
    }
    
    public function getCarreras() {
        return $this->carreras;
    }

    public function setCarreras($carreras) {
        $this->carreras = $carreras;
    }
    
    public function getGrupos() {
        return $this->grupos;
    }

    public function setGrupos($grupos) {
        $this->grupos = $grupos;
    }
    
    public function guardarHorario($año,$ciclo){
        $tipos = ManejadorGrupos::getTipos();
        foreach ($this->aulas as $aula){
            $dias = $aula->getDias();
            foreach ($dias as $dia){
                $horas = $dia->getHoras();
                foreach ($horas as $hora){
                    $grupo = $hora->getGrupo();
                    if($grupo->getId_grupo()!=0){
                        $tipo = ManejadorGrupos::getIdTipo($grupo->getTipo(), $tipos);
                        foreach ($grupo->getDocentes() as $docente){
                            $insert = "INSERT INTO asignaciones VALUES('".$aula->getNombre()."',".$dia->getId().",".$hora->getIdHora().",$año,$ciclo,".$grupo->getId_grupo().",".$grupo->getAgrup()->getId().",".$tipo.",".$docente->getIdDocente().")";
                            conexion::consulta($insert);
                        }
                    }
                }
            }
        }
        return 0;
    }
    
    /** Actualiza la tabla asignaciones dentro de la base de datos con los datos de intercambios
     * 
     * @param Grupo[] $grupos1 Grupos que se trasladaran a $dia2
     * @param Grupo[] $grupos2 Grupos que se trasladaran a $dia1
     * @param Dia $dia1 Dia de origen de $grupos1 y de destino de $grupos2
     * @param Dia $dia2 Dia de origen de $grupos2 y de destino de $grupos1
     * @param String $aula1 Aula de origen de $grupos1 y de destino de $grupos2
     * @param String $aula2 Aula de origen de $grupos2 y de destino de $grupos1
     * @param int[] $idHorasInsercion1 
     * @param int[] $idHorasInsercion2 
     */
    public function escribirIntercambio($grupos1,$grupos2,$dia1,$dia2,$aula1,$aula2,$idHorasInsercion1,$idHorasInsercion2,$año,$ciclo){
        $tipos = ManejadorGrupos::getTipos();
        foreach ($idHorasInsercion2 as $idHora){
            list($key,$grupo) = each($grupos1);
            $query = "DELETE FROM asignaciones WHERE cod_aula='$aula2' AND id_dia=".$dia2->getId()." AND id_hora=$idHora AND año=$año AND ciclo=$ciclo";
            Conexion::consulta($query);
            if($grupo->getId_grupo() != 0){
                foreach ($grupo->getDocentes() as $docente){
                    $query = "INSERT INTO asignaciones VALUES('$aula2',".$dia2->getId().",$idHora,$año,$ciclo,".$grupo->getId_grupo().",".$grupo->getAgrup()->getId().",".ManejadorGrupos::getIdTipo($grupo->getTipo(), $tipos).",".$docente->getIdDocente().")";
                    Conexion::consulta($query);
                }
            }
        }
        foreach ($idHorasInsercion1 as $idHora){
            list($key,$grupo) = each($grupos2);
            $query = "DELETE FROM asignaciones WHERE cod_aula='$aula1' AND id_dia=".$dia1->getId()." AND id_hora=$idHora AND año=$año AND ciclo=$ciclo";
            Conexion::consulta($query);
            if($grupo->getId_grupo() != 0){
                foreach ($grupo->getDocentes() as $docente){
                    $query = "INSERT INTO asignaciones VALUES('$aula1',".$dia1->getId().",$idHora,$año,$ciclo,".$grupo->getId_grupo().",".$grupo->getAgrup()->getId().",".ManejadorGrupos::getIdTipo($grupo->getTipo(), $tipos).",".$docente->getIdDocente().")";
                    Conexion::consulta($query);
                }
            }
        }
        return true;
    }
        
    /** Devuelve los años registrados en los cuales hay materias.
     * 
     * @return int = todos los años que tienen materias registradas.
     */
    public static function getAñosRegistrados(){
        $años = array();
        $consulta = "SELECT DISTINCT año FROM agrupacion NATURAL JOIN materia_agrupacion ORDER BY año DESC;";
        $respuesta = Conexion::consulta($consulta);
        while ($fila = pg_fetch_array($respuesta)) {
            $años[] = $fila['año'];
        }
        return $años;
    }
    
    /** Devuelve el ultimo año registrado en el que hay materias.
     * 
     * @return int = ultimo año
     */
    public static function getUltimoAñoRegistrado(){
        $consulta = "SELECT MAX(año) FROM agrupacion NATURAL JOIN materia_agrupacion;";
        $respuesta = Conexion::consulta2($consulta);
        return $respuesta['max'];
    }
    
    /** Devuelve el último ciclo registrado de un año.
     * 
     * @param int = $año año del que se desea saber su último ciclo registrado
     * @return int = el último ciclo registrado
     */
    public static function getUltimoCicloAño($año){
        $consulta = "SELECT MAX(ciclo) FROM agrupacion NATURAL JOIN materia_agrupacion WHERE año='$año';";
        $respuesta = Conexion::consulta2($consulta);
        return $respuesta['max'];
    }
    
    /** Sirve para clonar las materias de un año y ciclo dado, al nuevo año y ciclo especificado.
     * 
     * @param int $año_ant = el año que se desea clonar
     * @param int $ciclo_ant = el ciclo que se desea clonar
     * @param int $año_act = el año que se desea aperturar
     * @param int $ciclo_act = el ciclo que se desea aperturar
     */
    public static function clonar_ciclo($año_ant,$ciclo_ant,$año_act,$ciclo_act,$forzar_clonacion){
        $hayDatosQueClonar = self::hayDatosEnCiclo($año_ant, $ciclo_ant);
        if($hayDatosQueClonar){
            $hayDatosPrevios = self::hayDatosEnCiclo($año_act, $ciclo_act);
            if($forzar_clonacion){
                self::borrar_datos_ciclo($año_act, $ciclo_act);
                self::clonar_datos_ciclo($año_ant, $ciclo_ant, $año_act, $ciclo_act);
            }else{
                if(!$hayDatosPrevios){
                    self::clonar_datos_ciclo($año_ant, $ciclo_ant, $año_act, $ciclo_act);
                }else{
                    throw new Exception("Error: ya hay datos en el ciclo que desea aperturar. Si desea reemplazarlos marque la opción correspondiente para forzar la clonación.");
                }
            }
        }else{
            throw new Exception("Error: no se pudo clonar la información del ciclo porque no hay información guardada del año: ".$año_ant.", ciclo: ".$ciclo_ant);
        }
    }
    
    /** Borrar los datos de un ciclo y año determinado
     * 
     * @param type $año
     * @param type $ciclo
     */
    public static function borrar_datos_ciclo($año,$ciclo){
        self::borrar_datos_horario($año, $ciclo);
        $consulta = "DELETE FROM reservaciones WHERE año='$año' AND ciclo='$ciclo';";
        $consulta=$consulta."DELETE FROM materia_agrupacion WHERE año='$año' AND ciclo='$ciclo';";
        $consulta=$consulta."DELETE FROM lista_agrup_aula WHERE año='$año' AND ciclo='$ciclo';";
        $consulta=$consulta."DELETE FROM info_agrup_aula WHERE año='$año' AND ciclo='$ciclo';";
        $consulta=$consulta."DELETE FROM docente_horario WHERE año='$año' AND ciclo='$ciclo';";
        $consulta=$consulta."DELETE FROM docente_grupo WHERE año='$año' AND ciclo='$ciclo';";
        $consulta=$consulta."DELETE FROM grupo WHERE año='$año' AND ciclo='$ciclo';";
        $consulta=$consulta."DELETE FROM dia_hora_historial WHERE año='$año' AND ciclo='$ciclo';";
        $consulta=$consulta."DELETE FROM agrupacion WHERE año='$año' AND ciclo='$ciclo';";
        Conexion::consulta($consulta);
    }
    
    
    /** Clona las tablas que contienen la información de un ciclo academico dado, para poder utilizarlas en un nuevo ciclo especificado.
     * 
     * @param int $año_ant = el año que se desea clonar
     * @param int $ciclo_ant = el ciclo que se desea clonar
     * @param int $año_act = el año que se desea aperturar
     * @param int $ciclo_act = el ciclo que se desea aperturar
     */
    private static function clonar_datos_ciclo($año_ant,$ciclo_ant,$año_act,$ciclo_act){
        $conexion = Conexion::conectar();
        $agrupaciones_ctl = "SELECT * FROM agrupacion WHERE año='$año_ant' AND ciclo='$ciclo_ant'";
        $dia_hora_historial_ctl = "SELECT * FROM dia_hora_historial WHERE año='$año_ant' AND ciclo='$ciclo_ant'";
        $grupo_ctl = "SELECT * FROM grupo WHERE año='$año_ant' AND ciclo='$ciclo_ant'";
        $docente_grupo_ctl = "SELECT * FROM docente_grupo WHERE año='$año_ant' AND ciclo='$ciclo_ant'";
        $docente_horario_ctl = "SELECT * FROM docente_horario WHERE año='$año_ant' AND ciclo='$ciclo_ant'";
        $info_agrupo_aula_ctl = "SELECT * FROM info_agrup_aula WHERE año='$año_ant' AND ciclo='$ciclo_ant'";
        $lista_agrup_aula_ctl = "SELECT * FROM lista_agrup_aula WHERE año='$año_ant' AND ciclo='$ciclo_ant'";
        $materia_agrupacion_ctl = "SELECT * FROM materia_agrupacion WHERE año='$año_ant' AND ciclo='$ciclo_ant'";
        $reservaciones_ctl = "SELECT * FROM reservaciones WHERE año='$año_ant' AND ciclo='$ciclo_ant'";
        
        $agrupaciones = Conexion::consultaSinCerrarConexion($conexion,$agrupaciones_ctl);
        $dia_hora_historial = Conexion::consultaSinCerrarConexion($conexion,$dia_hora_historial_ctl);
        $grupo = Conexion::consultaSinCerrarConexion($conexion,$grupo_ctl);
        $docente_grupo = Conexion::consultaSinCerrarConexion($conexion,$docente_grupo_ctl);
        $docente_horario = Conexion::consultaSinCerrarConexion($conexion,$docente_horario_ctl);
        $info_agrupo_aula = Conexion::consultaSinCerrarConexion($conexion,$info_agrupo_aula_ctl);
        $lista_agrup_aula = Conexion::consultaSinCerrarConexion($conexion,$lista_agrup_aula_ctl);
        $materia_agrupacion = Conexion::consultaSinCerrarConexion($conexion,$materia_agrupacion_ctl);
        $reservaciones = Conexion::consultaSinCerrarConexion($conexion,$reservaciones_ctl);
        
        //INSERTAMOS LAS NUEVAS AGRUPACIONES
        $consulta_insertar = "";
        while ($fila = pg_fetch_array($agrupaciones)) {
            $consulta_insertar=$consulta_insertar."INSERT INTO agrupacion(id_agrupacion,alumnos_nuevos,otros_alumnos,alumnos_grupo,año,ciclo,horas_lab_semana,horas_discu_semana,horas_clase,lab_dis_alter) VALUES ";
            $consulta_insertar=$consulta_insertar."(".$fila['id_agrupacion'].",".$fila['alumnos_nuevos'].",".$fila['otros_alumnos'].",".$fila['alumnos_grupo'].",".$año_act.",".$ciclo_act.",".$fila['horas_lab_semana'].",".$fila['horas_discu_semana'].",".$fila['horas_clase'].",'".$fila['lab_dis_alter']."');";
        }
        
        //INSERTAMOS EL HISTORIAL DE DIAS Y HORAS        
        while ($fila = pg_fetch_array($dia_hora_historial)) {
            $consulta_insertar=$consulta_insertar."INSERT INTO dia_hora_historial(id_dia,id_hora,año,ciclo) VALUES ";
            $consulta_insertar=$consulta_insertar."(".$fila['id_dia'].",".$fila['id_hora'].",".$año_act.",".$ciclo_act.");";
        }
        
        //INSERTAMOS LOS NUEVOS GRUPOS
        while ($fila = pg_fetch_array($grupo)) {
            $consulta_insertar=$consulta_insertar."INSERT INTO grupo(id,id_agrupacion,año,ciclo,tipo) VALUES ";
            $consulta_insertar=$consulta_insertar."(".$fila['id'].",".$fila['id_agrupacion'].",".$año_act.",".$ciclo_act.",".$fila['tipo'].");";
        }
        
        //ASIGNAMOS LOS DOCENTES A SUS GRUPOS
        while ($fila = pg_fetch_array($docente_grupo)) {
            $consulta_insertar=$consulta_insertar."INSERT INTO docente_grupo(id_grupo,id_agrupacion,año,ciclo,tipo_grupo,id_docente) VALUES ";
            $consulta_insertar=$consulta_insertar."(".$fila['id_grupo'].",".$fila['id_agrupacion'].",".$año_act.",".$ciclo_act.",".$fila['tipo_grupo'].",".$fila['id_docente'].");";
        }
        
        //ASIGNAMOS LOS HORARIOS DE LOS DOCENTES
        while ($fila = pg_fetch_array($docente_horario)) {
            $consulta_insertar=$consulta_insertar."INSERT INTO docente_horario(id_docente,id_dia,id_hora,año,ciclo) VALUES ";
            $consulta_insertar=$consulta_insertar."(".$fila['id_docente'].",".$fila['id_dia'].",".$fila['id_hora'].",".$año_act.",".$ciclo_act.");";
        }
        
        //ASIGNAMOS LA INFORMACIÓN DE LAS AULAS Y LOS GRUPOS
        while ($fila = pg_fetch_array($info_agrupo_aula)) {
            $consulta_insertar=$consulta_insertar."INSERT INTO info_agrup_aula(id_agrupacion,tipo_grupo,exclusiv_aula,año,ciclo) VALUES ";
            $consulta_insertar=$consulta_insertar."(".$fila['id_agrupacion'].",".$fila['tipo_grupo'].",'".$fila['exclusiv_aula']."',".$año_act.",".$ciclo_act.");";
        }
        
        //ASIGNAMOS LA LISTA DE AULAS PARA LOS GRUPOS
        while ($fila = pg_fetch_array($lista_agrup_aula)) {
            $consulta_insertar=$consulta_insertar."INSERT INTO lista_agrup_aula(id_agrupacion,tipo_grupo,cod_aula,año,ciclo) VALUES ";
            $consulta_insertar=$consulta_insertar."(".$fila['id_agrupacion'].",".$fila['tipo_grupo'].",'".$fila['cod_aula']."',".$año_act.",".$ciclo_act.");";
        }
        
        //ASIGNAMOS LAS RESERVACIONES
        while ($fila = pg_fetch_array($reservaciones)) {
            $consulta_insertar=$consulta_insertar."INSERT INTO reservaciones(id_hora,id_dia,cod_aula,año,ciclo) VALUES ";
            $consulta_insertar=$consulta_insertar."(".$fila['id_hora'].",".$fila['id_dia'].",'".$fila['cod_aula']."',".$año_act.",".$ciclo_act.");";
        }
        
        //ASIGNAMOS LAS AGRUPACIONES DE LAS MATERIAS
        while ($fila = pg_fetch_array($materia_agrupacion)) {
            $consulta_insertar=$consulta_insertar."INSERT INTO materia_agrupacion(plan_estudio,id_carrera,cod_materia,id_agrupacion,año,ciclo) VALUES ";
            $consulta_insertar=$consulta_insertar."(".$fila['plan_estudio'].",'".$fila['id_carrera']."','".$fila['cod_materia']."',".$fila['id_agrupacion'].",".$año_act.",".$ciclo_act.");";
        }
        
        //EJECUTAMOS LA CONSULTA, MODIFICAMOS TODAS LAS TABLAS EN UNA SOLA CONSULTA PORQUE SI ALGO SALE MAL SE HARÁ ROLLBACK A TODAS LAS TABLAS MODIFICADAS
        Conexion::consultaSinCerrarConexion($conexion,$consulta_insertar);
        Conexion::desconectar($conexion);
    }
    
    /** Se clona el horario(resultado de la simulación), de un año y ciclo especificado, a otro año y ciclo dado.
     * 
     * @param type $año_ant = año que se desea clonar
     * @param type $ciclo_ant = ciclo que se desea clonar
     * @param type $año_act = año en el que se clonará
     * @param type $ciclo_act = ciclo en el que se clonará
     * @param type $forzar_clonacion = si es TRUE, borrará los datos que pudieran haber en el año y ciclo que se clonará.
     * @throws Exception = Si no se fuerza la clonación lanzará una expeción si encuentra datos en el año y ciclo que se clonarán los datos.
     */
    public static function clonar_horario($año_ant,$ciclo_ant,$año_act,$ciclo_act,$forzar_clonacion){
        $hayHorarioQueClonar = self::hayDatosEnHorario($año_ant, $ciclo_ant);
        if($hayHorarioQueClonar){
            $hayDatosPrevios = self::hayDatosEnHorario($año_act, $ciclo_act);
            if($forzar_clonacion){
                self::borrar_datos_horario($año_act, $ciclo_act);
                self::clonar_datos_horario($año_ant, $ciclo_ant, $año_act, $ciclo_act);
            }else{
                if(!$hayDatosPrevios){
                    self::clonar_datos_horario($año_ant, $ciclo_ant, $año_act, $ciclo_act);
                }else{
                    throw new Exception("Error: ya hay un horario guardado para ese ciclo. Si desea reemplazarlo marque la opción correspondiente para forzar la clonación.");
                }
            }
        }else{
            throw new Exception("Error: no se pudo clonar el horario porque no hay un horario guardado del año: ".$año_ant.", ciclo: ".$ciclo_ant);
        }
    }
    
    /** Borra los datos guardados de una simulación para un año y ciclo dado.
     * 
     * @param type $año
     * @param type $ciclo
     */
    public static function borrar_datos_horario($año,$ciclo){
        $consulta = "DELETE FROM asignaciones WHERE año='$año' AND ciclo='$ciclo';";
        Conexion::consulta($consulta);
    }
    
    /** Clona la/s tabla/s de horario de un de un año y ciclo especificado, a otro año y ciclo dado.
     * 
     * @param type $año_ant = año que se desea clonar
     * @param type $ciclo_ant = ciclo que se desea clonar
     * @param type $año_act = año en el que se clonará
     * @param type $ciclo_act = ciclo en el que se clonará
     */
    private static function clonar_datos_horario($año_ant,$ciclo_ant,$año_act,$ciclo_act){
        $conexion = Conexion::conectar();
        $asignaciones_ctl = "SELECT * FROM asignaciones WHERE año='$año_ant' AND ciclo='$ciclo_ant'";
        $asignaciones = Conexion::consultaSinCerrarConexion($conexion,$asignaciones_ctl);
        //INSERTAMOS LAS NUEVAS ASIGNACIONES
        $consulta_insertar = "";
        while ($fila = pg_fetch_array($asignaciones)) {
            $consulta_insertar=$consulta_insertar."INSERT INTO asignaciones(cod_aula,id_dia,id_hora,año,ciclo,id_grupo,id_agrupacion,tipo_grupo,id_docente) VALUES ";
            $consulta_insertar=$consulta_insertar."('".$fila['cod_aula']."',".$fila['id_dia'].",".$fila['id_hora'].",".$año_act.",".$ciclo_act.",".$fila['id_grupo'].",".$fila['id_agrupacion'].",".$fila['tipo_grupo'].",'".$fila['id_docente']."');";
        }
        //EJECUTAMOS LA CONSULTA, MODIFICAMOS TODAS LAS TABLAS EN UNA SOLA CONSULTA PORQUE SI ALGO SALE MAL SE HARÁ ROLLBACK A TODAS LAS TABLAS MODIFICADAS
        Conexion::consultaSinCerrarConexion($conexion,$consulta_insertar);
        Conexion::desconectar($conexion);
    }
    
    /** Sirve para ver si hay datos guardados de un ciclo y año dado.
     * 
     * @param type $año
     * @param type $ciclo
     * @return boolean = TRUE si lo hay, FALSE en caso contrario
     */
    public static function hayDatosEnCiclo($año,$ciclo){
        $consulta = "SELECT COUNT(*) FROM agrupacion WHERE año='$año' AND ciclo='$ciclo'";
        $respuesta = Conexion::consulta2($consulta);
        if($respuesta['count']>0){
            return true;
        }else{
            return false;
        }
    }
    
    /** Sirve para conocer si hay un horario(resultado de la simulación) guardado de un año y ciclo dado.
     * 
     * @param type $año
     * @param type $ciclo
     * @return boolean = TRUE si lo hay, FALSE en caso contrario
     */
    public static function hayDatosEnHorario($año,$ciclo){
        $consulta = "SELECT COUNT(*) FROM asignaciones WHERE año='$año' AND ciclo='$ciclo'";
        $respuesta = Conexion::consulta2($consulta);
        if($respuesta['count']>0){
            return true;
        }else{
            return false;
        }
    }
    
    public static function comprobarDatosGeneracion($año,$ciclo){
        $errores = array();
        //Determinar si hay agrupaciones sin grupos
        $consulta="select id_agrupacion,nombre_depar from materia_agrupacion natural join carreras natural join departamentos where id_agrupacion not in (select id_agrupacion from grupo where año=$año and ciclo=$ciclo) and año=$año and ciclo=$ciclo";
        $respuesta = pg_fetch_all(Conexion::consulta($consulta));
        if(is_array($respuesta)){
            $depars = array();
            foreach ($respuesta as $fila){
                $depars[] = $fila['nombre_depar'];
            }
            $errores[] = "Existen materias sin grupos en los departamentos: ".implode(", ", array_unique($depars));
        }
        //Determinar si hay grupos sin docente asignado
        $consulta="(select id,id_agrupacion,año,ciclo,tipo,nombre_depar from grupo natural join materia_agrupacion natural join carreras natural join departamentos where año=$año and ciclo=$ciclo) except (select distinct id_grupo,id_agrupacion,año,ciclo,tipo_grupo,nombre_depar from docente_grupo natural join materia_agrupacion natural join carreras natural join departamentos where año=$año and ciclo=$ciclo)";
        $respuesta = pg_fetch_all(Conexion::consulta($consulta));
        if(is_array($respuesta)){
            $depars = array();
            foreach ($respuesta as $fila){
                $depars[] = $fila['nombre_depar'];
            }
            $errores[] = "Existen grupos sin docente asignado en los departamentos: ".implode(", ", array_unique($depars));
        }
        //Determinar si hay agrupaciones que deben ocupar aula pero sin aula asignada
        $consulta = "select id_agrupacion,año,ciclo,tipo_grupo,nombre_depar from info_agrup_aula natural join materia_agrupacion natural join carreras natural join departamentos where año=$año and ciclo=$ciclo except select id_agrupacion,año,ciclo,tipo_grupo,nombre_depar from lista_agrup_aula natural join materia_agrupacion natural join carreras natural join departamentos where año=$año and ciclo=$ciclo";
        $respuesta = pg_fetch_all(Conexion::consulta($consulta));
        if(is_array($respuesta)){
            $depars = array();
            foreach ($respuesta as $fila){
                $depars[] = $fila['nombre_depar'];
            }
            $errores[] = "Existen agrupaciones que necesitan tener asignada un aula, en los departamentos: ".implode(", ", array_unique($depars));
        }
        return $errores;
    }
}
