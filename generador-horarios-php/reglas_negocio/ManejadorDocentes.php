<?php

chdir(dirname(__FILE__));
include_once '../acceso_datos/Conexion.php';
include_once 'Docente.php';
include_once 'ManejadorCargos.php';
include_once 'ManejadorDepartamentos.php';

abstract class ManejadorDocentes{
    
    /**
     * 
     * @param Cargo[] $todos_cargos
     * @param Integer $año año actual
     * @param Integer $ciclo ciclo actual (1|2)
     * @param Departamento[] $todos_depars Todos los departamentos de la facultad
     * @return Docente[] $docentes
     */
    public static function obtenerTodosDocentes($todos_cargos,$año,$ciclo,$todos_depars){
        $sql_consulta = "SELECT id_docente,contratacion,id_depar,cargo FROM docentes where activo=TRUE";
        $respuesta = Conexion::consulta($sql_consulta);
        while ($fila = pg_fetch_array($respuesta)){
            $docente = new Docente($fila['id_docente'],$fila['contratacion'], ManejadorDepartamentos::obtenerDepartamento($fila['id_depar'], $todos_depars));
            if(isset($fila['cargo'])){
                $docente->setCargo(ManejadorCargos::obtenerCargo($fila['cargo'], $todos_cargos));
            }
            $docente->setHorario(self::obtenerHorarioDocente($fila['id_docente'], $año, $ciclo));
            $docentes[] = $docente;
        }
        return $docentes;
    }
    
    /**
     * 
     * @param Integer $id_docente = identificador de docente que se desea obtener horario de trabajo
     * @param Integer $año año actual de trabajo
     * @param Integer $ciclo ciclo actual de trabajo (1|2)
     */
    public static function obtenerHorarioDocente($id_docente,$año,$ciclo){
        $horario = null;
        $consulta = "select id_dia,id_hora from docente_horario as dh where id_docente=$id_docente and dh.año=$año and ciclo=$ciclo order by id_dia,id_hora";
        $respuesta = Conexion::consulta($consulta);
        $dia = new Dia(0, "");
        while($fila = pg_fetch_array($respuesta)){
            if($dia->getId() == $fila['id_dia']){
                $hora = new Hora();
                $hora->setIdHora($fila['id_hora']);
                $dia = $horario[count($horario)-1];
                $dia->addHora($hora);
            } else{
                $dia = new Dia($fila['id_dia'], "");
                $hora = new Hora();
                $hora->setIdHora($fila['id_hora']);
                $dia->addHora($hora);
                $horario[] = $dia;
            } 
        }
        return $horario;
    }
    
    /** Asumiendo que un docente tiene el mismo horario todos los dias, se busca la hora comun entre los docentes que imparten un solo grupo
     *  para determinar el inicio de la clase
     * @param Docente[] $docentes
     */
    public static function intersectarHorarios($docentes){
        $index = 0;
        $horasComunes = null;
        $horas = array();
        foreach ($docentes as $docente){
            if($docente->getHorario()!=null){
                $horasDoc = $docente->getHorario()[0]->getHoras();
                foreach ($horasDoc as $hora) {
                    $horas[$index][] = $hora->getIdHora();
                }
                $index++;
            }
        }
        if(count($horas)!=1){
            $horarioDoc1 = $horas[0];
            foreach ($horarioDoc1 as $hora){ // Todas las horas del docente 0
                for ($i=0; $i<count($horas);$i++){ // horarios de todos los docentes
                    $horasIguales = false;
                    $horarioDocs = $horas[$i];
                    foreach ($horarioDocs as $horaDoc){ // Todas las horas del horario del docente actual en la iteracion
                        if($hora == $horaDoc){
                            $horasIguales = true;
                        }
                    }
                    if($horasIguales && $i != (count($horas)-1)){
                        $horasIguales = false;
                        continue;
                    } elseif ($horasIguales && $i == (count($horas)-1)) {
                        $horasComunes[] = $hora;
                    }else{
                        break;
                    }
                }
            }
            return $horasComunes;
        } elseif(count($horas)==1){
            return $horas[0];
        } elseif (count($horas)==0){
            return null;
        }
    }
    
    public static function obtenerDocente($idDocente,$todos_docentes){
        foreach ($todos_docentes as $docente) {
            if($docente->getIdDocente() == $idDocente){
                return $docente;
            }
        }
        return null;
    }
    
    /** Determina si un docente puede o no laborar en un dia especifico
     * 
     * @param Docente[] $docentes Docentes que imparten clases a un mismo grupo
     * @param Dia $dia Dia posible para asignar al grupo
     * @return boolean true si docente no puede laborar en ese dia
     */
    public static function docenteInhabilitado($docentes,$dia){
        foreach ($docentes as $docente){
            if($docente->getCargo() != null && $docente->getCargo()->getId_dia_exento() == $dia->getId()){
                error_log ("Docente ".$docente->getIdDocente()." no esta habilitado en dia ".$dia->getNombre(),0);
                return true;
            } elseif(!$docente->diaHabil($dia->getId())){
                error_log ("Docente ".$docente->getIdDocente()." no esta habilitado en dia ".$dia->getNombre(),0);
                return true;
            }
        }
        return false;
    }
    
    public static function existeDocRespaldo($docentes){
        foreach ($docentes as $docente) {
            if($docente->getDepar()->getId()==13){
                return true;
            }
        }
        return false;
    }
    
    /** Separar docentes que poseen un horario especial de trabajo de aquellos que son TC
     * 
     * @param Docente[] $todos_docentes Todos los docentes de la facultad
     * @return Docente[]
     */
    public static function clasificarDocentes($todos_docentes){
        foreach ($todos_docentes as $docente){
            if($docente->getHorario()!=null){
                $docentes[0][] = $docente;
            } else{
                $docentes[1][] = $docente;
            }
        }
        return $docentes;
    }
    
    public static function extraerGruposDeDocentes($docentes){
        foreach ($docentes as $docente) {
            $gruposDoc = $docente->getGrupos();
            foreach ($gruposDoc as $grupo) {
                $grupos[] = $grupo;
            }
        }
        return $grupos;
    }
    
    public static function buscarDocentes($buscarComo,$idDepartamento){
        $datos = array();
        $materias = array();
        if($idDepartamento=="todos"){
            $consulta = "SELECT * FROM docentes WHERE (nombres iLIKE '%$buscarComo%' OR apellidos iLIKE '%$buscarComo%') LIMIT 15";
        }else{
            $consulta = "SELECT * FROM docentes WHERE (nombres iLIKE '%$buscarComo%' OR apellidos iLIKE '%$buscarComo%') AND id_depar='$idDepartamento' LIMIT 15";
        }        
        $respuesta = conexion::consulta($consulta);
        while ($row = pg_fetch_array($respuesta)){
            $datos[] = array("value"=>$row['nombres']." ".$row['apellidos'],
                            "id"=>$row['id_docente']);
        }        
        return $datos;
    }
    
}
