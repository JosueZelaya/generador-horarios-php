<?php
include_once '../../reglas_negocio/Facultad.php';
session_start();
$facultad = $_SESSION['facultad'];
?>
<div class="row">
    <div class="col-sm-12">
        <form class='form-inline' role='form'>
        <label for='aula-intercambio1'>Aulas:</label>
        <select id='aula-intercambio1' class='form-control'>
            <?php
            $aulas = $facultad->getAulas();
            for ($index = 0; $index < count($aulas); $index++) {    
                echo "<option value='".$aulas[$index]->getNombre()."'>".$aulas[$index]->getNombre()."</option>";    
            } ?>
        </select>        
        </form><br>
        <!--<iframe id="frame-antes" src="" frameborder="0" width="90%" height="300"></iframe>-->
        <div id="antes-intercambio" style="width:100%; height:300px; overflow: scroll;"></div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-12">
        <br>
        <form class='form-inline' role='form'>
        <label for='aula-intercambio2'>Aulas:</label>
        <select id='aula-intercambio2' class='form-control'>
            <?php
            for ($index = 0; $index < count($aulas); $index++) {    
                echo "<option value='".$aulas[$index]->getNombre()."'>".$aulas[$index]->getNombre()."</option>";    
            } ?>
        </select>        
        </form><br>
        <!--<iframe id="frame-despues" src="" frameborder="0" width="90%" height="300"></iframe>-->
        <div id="despues-intercambio" style="width:100%; height:300px; overflow: scroll;"></div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-12" style="text-align: center">
        <a class="btn btn-lg btn-primary" href="#" id='intercambiarHoras'>
            <i class="fa fa-retweet fa-lg"></i> Intercambiar
        </a>
    </div>
</div>
<br>