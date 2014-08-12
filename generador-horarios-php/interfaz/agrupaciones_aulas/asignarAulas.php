<?php
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorAulas.php';
chdir(dirname(__FILE__));

$cicloinfo = parse_ini_file('../cicloinfo.ini');
$año = $cicloinfo['año'];
$ciclo = $cicloinfo['ciclo'];

if(isset($_GET)){
    if(isset($_GET['materias'])     && 
       isset($_GET['aulas'])        &&    
       isset($_GET['exclusiva'])    &&
       isset($_GET['gt'])           &&
       isset($_GET['gl'])           &&
       isset($_GET['gd'])             
     ){
        if($_GET['materias']==""){
            echo json_encode("¡No ha agregado materias!");
        }else if($_GET['aulas']==""){
            echo json_encode("¡No ha seleccionado aulas!");
        }else if($_GET['gt']=="false" && $_GET['gl']=="false" && $_GET['gd']=="false"){
            echo json_encode("¡Debe elegir al menos un tipo de grupo!");
        }else{
            $materias = explode(",",$_GET['materias']);
            $aulas = $_GET['aulas'];
            $fin = strlen($aulas)-1;            
            $aulas = substr($aulas,0,$fin);
            $aulas = explode(",",$aulas);            
            $exclusiva = $_GET['exclusiva'];            
            if($exclusiva=="true")
                $exclusiva = "t";
            else
                $exclusiva = "f";                        
            $gt = $_GET['gt'];
            if($gt=="true")
                $gt = TRUE;
            else
                $gt = FALSE;
            $gl = $_GET['gl'];
            if($gl=="true")
                $gl = TRUE;
            else
                $gl = FALSE;
            $gd = $_GET['gd'];
            if($gd=="true")
                $gd = TRUE;
            else
                $gd = FALSE;
            try{
                ManejadorAulas::asignarAulas($materias, $aulas, $exclusiva, $gt, $gl, $gd,$año,$ciclo);
                echo json_encode("ok");
            }catch(Exception $ex){
                echo json_encode($ex->getMessage());
            }            
        }
    }   
}