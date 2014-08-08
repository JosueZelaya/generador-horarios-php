<?php
if(isset($_GET['año']) && isset($_GET['ciclo'])){
    if($_GET['año']!="" && $_GET['ciclo']!=""){
        $matriz = array("informacion_ciclo_estudio"=>["año"=>$_GET['año'],"ciclo"=>$_GET['ciclo']]);
        $archivo = "../cicloinfo.ini";
        $resultado = modifica_ini($matriz, $archivo);
        if($resultado!=-1 && $resultado!=-2){
            echo json_encode("ok");
        }else if($resultado!=-1){
            echo json_encode("Error: no se pudo abrir el archivo, es posible que no tenga los privilegios adecuados.");
        }else{
            echo json_encode("Error: no se pudo escribir en el archivo.");
        }
    }else{
        echo json_encode("Debe ingresar un año y un ciclo");
    }
}

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