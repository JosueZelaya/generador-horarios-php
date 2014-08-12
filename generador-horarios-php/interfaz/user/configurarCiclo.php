<?php
chdir(dirname(__FILE__));
require_once '../../acceso_datos/Conexion.php';
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));

if(isset($_GET['año']) && isset($_GET['ciclo']) && isset($_GET['año_clonar']) && isset($_GET['forzar']) && isset($_GET['forzar'])){
    
    if($_GET['año_clonar']!="false" && is_numeric($_GET['año_clonar'])){
        if($_GET['año']!="" && $_GET['ciclo']!=""){
            $año_act = $_GET['año'];
            $ciclo_act = $_GET['ciclo'];
            $año_clonar = $_GET['año_clonar'];
            $clonar_horario = $_GET['clonar_horario'];    
            $forzar = $_GET['forzar'];
            if($forzar=="true"){
                $forzar=true;
            }else{
                $forzar=false;
            }
            try{
                //Clonar el ciclo actual del año especificado.  
                Facultad::clonar_ciclo($año_clonar,$ciclo_act, $año_act, $ciclo_act,$forzar); 
                if($clonar_horario=="true"){
                    //Clonar el horario del año especificado.                
                    Facultad::clonar_horario($año_clonar, $ciclo_act, $año_act, $ciclo_act,$forzar);                    
                }
                establecer_año_ciclo($año_act, $ciclo_act);
            }catch(Exception $ex){
                echo json_encode($ex->getMessage());
            }
        }else{
            echo json_encode("Debe ingresar un año y un ciclo");
        }
    }else{
        if(is_numeric($_GET['año_clonar']) || $_GET['año_clonar']=="false"){
            if($_GET['clonar_horario']=="true"){
                echo json_encode("Error: No ha seleccionado un año para clonar su información, sin embargo ha marcado que desea clonar un horario.<br/>Por favor, seleccione el año que desea clonar.");
            }else{
                $año = $_GET['año'];
                $ciclo = $_GET['ciclo'];    
                $hayDatosPrevios = Facultad::hayDatosEnCiclo($año, $ciclo);
                $forzar = $_GET['forzar'];
                if($forzar=="true"){
                    $forzar=true;
                }else{
                    $forzar=false;
                }
                if(!$hayDatosPrevios){
                    aperturar_materias($año, $ciclo);
                    establecer_año_ciclo($año, $ciclo);
                }else{
                    if($forzar){
                        Facultad::borrar_datos_ciclo($año, $ciclo);
                        aperturar_materias($año, $ciclo);
                        establecer_año_ciclo($año, $ciclo);
                    }else{
                        echo json_encode("Error: Ha indicado que no desea clonar información, sin embargo ya existe información para el año: ".$año.", ciclo: ".$ciclo.". Utilice la opción forzar si desea que esta información sea borrada y se aperture un ciclo en limpio.");
                    }
                }                
            }           
        }else{
            echo json_encode("Error: el año a clonar debe ser un valor numérico.");
        }        
    }
}

function aperturar_materias($año,$ciclo){
    //Se obtienen todas las materias del ciclo par o impar
    $materias = ManejadorMaterias::getTodasMateriasDeCiclo($ciclo);
    try{
        //se crean agrupaciones de esas materias para el nuevo año y ciclo espeficiado
        foreach ($materias as $materia) {
            ManejadorAgrupaciones::crearAgrupacionParaMateria($materia,$año, $ciclo);
        }
    }catch(Exception $ex){
        echo json_encode("Error Grave: ".$ex->getMessage()." Si no sabe cómo resolver este error comuníquese con los desarrolladores.");
    }
}

function establecer_año_ciclo($año,$ciclo){
    $matriz = array("informacion_ciclo_estudio"=>["año"=>$año,"ciclo"=>$ciclo]);
    $archivo = "../cicloinfo.ini";
    $resultado = modifica_ini($matriz, $archivo);
    if($resultado!=-1 && $resultado!=-2){                
        echo json_encode("ok");
    }else if($resultado!=-1){
        echo json_encode("Error: no se pudo abrir el archivo, es posible que no tenga los privilegios adecuados.");
    }else{
        echo json_encode("Error: no se pudo escribir en el archivo.");
    }
}

/** MODIFICA EL ARCHIVO DE CONFIGURACIÓN .INI QUE SE LE INDIQUE
 * 
 * @param type $matriz = array que contiene la clave y el valor a guardar
 * @param type $archivo = el archivo a modificar(ruta completa o ruta relativa).
 * @param type $multi_secciones = Si el archivo está dividido en varias secciones
 * @param type $modo = El modo con que se abre el archivo, w para escritura.
 * @return type = -1 si el archivo no pudo abrirse para escritura || -2 si no pudo escribirse en él || el número de bytes escrito por fwrite()
 */
function modifica_ini($matriz, $archivo, $multi_secciones = true, $modo = 'w') {
    $salida = '';
 
    # saltos de línea (usar "\r\n" para Windows)
    define('SALTO', "\n");
 
    if (!is_array(current($matriz))) {
        $tmp = $matriz;
        $matriz['tmp'] = $tmp; # no importa el nombre de la sección, no se usará
        unset($tmp);
   } 
   foreach($matriz as $clave => $matriz_interior) {
       if ($multi_secciones) {
           $salida .= '['.$clave.']'.SALTO;
        }
        foreach ($matriz_interior as $clave2 => $valor) {
            $salida .= $clave2 . ' = "' . $valor . '"' . SALTO;
        }
        if ($multi_secciones) {
            $salida .= SALTO;
        }
    }
    $puntero_archivo = fopen($archivo, $modo);

    if ($puntero_archivo !== false) {
        $escribo = fwrite($puntero_archivo, $salida);
        if ($escribo === false) {
           $devolver = -2;
           } else {
               $devolver = $escribo;
           }
            fclose($puntero_archivo);
    }else{
        $devolver = -1;
    }
        return $devolver;
}