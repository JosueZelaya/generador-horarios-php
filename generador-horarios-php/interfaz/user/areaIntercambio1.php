<?php
include_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
include_once 'funciones.php';
ManejadorSesion::sec_session_start();
$facultad = $_SESSION['facultad'];
$modelo = create_model($facultad);

if (isset($_GET['aula'])) {
    $aula = $_GET['aula'];
    $tabla = ManejadorAulas::getHorarioEnAula($facultad->getAulas(), $aula, $modelo);
    echo imprimir($tabla);
    ?>
    <script type="text/javascript">
        $('.verInfoGrupo').popover({
            title : "Informacion del Grupo",
            animation : true,
            trigger : 'focus',  //Se muestra el popover al pasar el puntero sobre la celda. valores que acepta: hover,manual,click,focus                    
            html : true
        });
    </script>
    <?php
}

function imprimir($tabla){
    for($i=0;$i<count($tabla);$i++){         
        echo "<div class='col'>";
        for($j=0;$j<count($tabla[$i]);$j++){                
            if($j==0){
                echo "<div class='col-header'>".$tabla[$i][$j]."</div>";
            }else if($i==0){
                echo "<div class='celda-hora'><div class='centrar'>".$tabla[$i][$j]."</div></div>";
            }else{
                $celda = $tabla[$i][$j];
                $contenido = "Materia: " . $celda['nombre'] . "<br/>" . "Grupo: ".$celda['tipo']." ".$celda['grupo']."<br/> Departamento: " . $celda['departamento'];
                if($celda['more']){
                    $contenido .= '<br><a id="moreInfo" href="#">Mas</a>';
                }
                if(!strcmp($celda['grupo'],"")==0){
                    echo "<div class='celda-hora intercambio1 grupoIntercambio1 intercambio1".$celda['codigo'].$celda['grupo'].$celda['tipo'].$i."  intercambio1".$celda['dia'].$celda['idHora']."' data-grupo='".$celda['codigo'].$celda['grupo'].$celda['tipo'].$i."' data-iniciobloque='".$celda['inicioBloque']."' data-finbloque='".$celda['finBloque']."' data-hora='".$celda['idHora']."' data-dia='".$celda['dia']."'>";                    
                    echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='top auto' data-content='".$contenido."'>".$celda['texto'].'</div></div>';
                }else{
                    echo "<div class='celda-hora intercambio1 grupoVacioIntercambio1 intercambio1".$celda['dia'].$celda['idHora']."' data-hora='".$celda['idHora']."' data-dia='".$celda['dia']."'>";
//                    echo "<div rel='popover' class='verInfoGrupo centrar' data-toggle='popover' data-placement='top' data-content='".$contenido."'>".$celda['texto'].'</div></div>';
                    echo '</div>';
                }
            }
        }
        echo '</div>';
    }        
}
