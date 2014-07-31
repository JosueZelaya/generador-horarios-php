<?php
    chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/ManejadorAgrupaciones.php';
    chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/ManejadorMaterias.php';
    chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/ManejadorAulas.php';
    chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/Facultad.php';
    chdir(dirname(__FILE__));
    include_once 'funciones.php';
    chdir(dirname(__FILE__));
    include_once '../../reglas_negocio/ManejadorSesion.php';
    chdir(dirname(__FILE__));
    ManejadorSesion::sec_session_start();
    $facultad = $_SESSION['facultad'];
    $modelo = create_model($facultad);

    if(isset($_GET['aula']) && isset($_GET['departamento']) && isset($_GET['carrera'])){
        $aula = $_GET['aula'];
        $departamento = $_GET['departamento'];
        $carrera = $_GET['carrera'];
        if($carrera!='todos'){                        
            $ids_agrupaciones = ManejadorAgrupaciones::obtenerAgrupacionesDeCarrera($carrera);
            $tabla = ManejadorAulas::getHorarioEnAula_Carrera($facultad->getAulas(),$aula,$ids_agrupaciones,$modelo);
        }else if($departamento!='todos'){
            $tabla = ManejadorAulas::getHorarioEnAula_Depar($aula,$departamento,$modelo,$facultad);
        }else{
            $tabla = ManejadorAulas::getHorarioEnAula($facultad->getAulas(), $aula, $modelo);
        }
        echo imprimirMalla($tabla);
    } elseif(isset($_GET['departamento']) && isset($_GET['carrera']) && isset($_GET['materia'])){
        $aulas = $facultad->getAulas();
        $cod_materia = $_GET['materia'];
        $id_depar = $_GET['departamento'];
        $horario = ManejadorMaterias::getHorarioMateria($aulas, $cod_materia, $id_depar);
        $horario = ordenarHorarioMateria($horario);
        imprimirMallaMateria($horario);
    }elseif (isset($_GET['aula'])) {
        $aula = $_GET['aula'];
        $tabla = ManejadorAulas::getHorarioEnAula($facultad->getAulas(), $aula, $modelo);
        echo imprimirMalla($tabla);
    }