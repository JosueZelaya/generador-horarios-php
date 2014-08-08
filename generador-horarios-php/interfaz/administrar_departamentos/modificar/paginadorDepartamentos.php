<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorDepartamentos.php';
chdir(dirname(__FILE__));
include 'paginacionConfig.php';
chdir(dirname(__FILE__));

if(session_status()==PHP_SESSION_NONE){
    ManejadorSesion::sec_session_start();
}
$departamentos = ManejadorDepartamentos::quitarDepartamentosEspeciales(ManejadorDepartamentos::getDepartamentos());
$cantidadDepartamentos = count($departamentos);
$paginasNecesarias = ceil($cantidadDepartamentos/$numeroResultados); //Redondea al número mayor con la función ceil()
$css_class = 'paginaDepartamentos';
if($_GET){
    if(isset($_GET['css_class'])){
        $css_class = $_GET['css_class'];
    }
    if(isset($_GET['pagina'])){
        $pagina = $_GET['pagina'];
    }
}

if($paginasNecesarias>5){
        if($pagina>3 && $pagina<$paginasNecesarias-2){
        echo "<ul class='pagination'>"
        . "<li class='prev'><a href='#'> ← </a></li>";
        for ($index2 = $pagina-2; $index2 <= $pagina+2; $index2++) {
            if($pagina==$index2){
                echo "<li class='active'><a class='".$css_class."' data='".$index2."' href='#'>".$index2."</a></li>";
            }else{
                echo "<li><a class='".$css_class."' data='".$index2."' href='#'>".$index2."</a></li>";
            }        
        }
        echo "<li class='next'><a href='#'> → </a></li>"
        . "</ul>";    
    }else if($pagina>=$paginasNecesarias-2){    
        echo "<ul class='pagination'>"
        . "<li class='prev'><a href='#'> ← </a></li>";
        for ($index2 = $paginasNecesarias-4; $index2 <= $paginasNecesarias; $index2++) {
            if($pagina==$index2){
                echo "<li class='active'><a class='".$css_class."' data='".$index2."' href='#'>".$index2."</a></li>";
            }else{
                echo "<li><a class='".$css_class."' data='".$index2."' href='#'>".$index2."</a></li>";
            }        
        }
        echo "<li class='next'><a href='#'> → </a></li>"
        . "</ul>";
    }else{
        echo "<ul class='pagination'>"
        . "<li class='prev'><a href='#'> ← </a></li>";
        for ($index = 0; $index < 5; $index++) {                                        
            if($pagina==$index+1){
                $page=$index+1;
                echo "<li class='active'><a class='".$css_class."' data='".$page."' href='#'>".$page."</a></li>";
            }else{
                $page=$index+1;
                echo "<li><a class='".$css_class."' data='".$page."' href='#'>".$page."</a></li>";
            }                                        
        }
        echo "<li class='next'><a href='#'> → </a></li>"
        . "</ul>";
    }
}else{
    echo "<ul class='pagination'>"
    . "<li class='prev'><a href='#'> ← </a></li>";
    for ($index = 0; $index < $paginasNecesarias; $index++) {                                        
        if($pagina==$index+1){
            $page=$index+1;
            echo "<li class='active'><a class='".$css_class."' data='".$page."' href='#'>".$page."</a></li>";
        }else{
            $page=$index+1;
            echo "<li><a class='".$css_class."' data='".$page."' href='#'>".$page."</a></li>";
        }                                        
    }
    echo "<li class='next'><a href='#'> → </a></li>"
    . "</ul>";    
}