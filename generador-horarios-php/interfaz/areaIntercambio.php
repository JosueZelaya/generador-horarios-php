<?php
include_once '../reglas_negocio/Facultad.php';
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
        <label for='dia-intercambio1'>Dias:</label>
        <select id='dia-intercambio1' class='form-control'>
            <?php
            $dias = $aulas[0]->getDias();
            for ($index = 0; $index < count($dias); $index++) {    
                echo "<option value='".$dias[$index]->getNombre()."'>".$dias[$index]->getNombre()."</option>";    
            } ?>
        </select>
        <label for='desde-intercambio1'>Desde:</label>
        <select id='desde-intercambio1' class='form-control'>
            <?php
            $horas = $dias[0]->getHoras();
            for ($index = 0; $index < count($horas); $index++) {
                $inicio = $horas[$index]->getInicio();
                $fin = $horas[$index]->getFin();
                echo "<option value='".$horas[$index]->getIdHora()."'>"."$inicio - $fin"."</option>";    
            } ?>
        </select>
        <label for='hasta-intercambio1'>Hasta:</label>
        <select id='hasta-intercambio1' class='form-control'>
            <?php
            for ($index = 0; $index < count($horas); $index++) {
                $inicio = $horas[$index]->getInicio();
                $fin = $horas[$index]->getFin();
                echo "<option value='".$horas[$index]->getIdHora()."'>"."$inicio - $fin"."</option>";    
            } ?>
        </select></form><br>
        <iframe id="frame-antes" src="" frameborder="0" width="90%" height="300"></iframe>
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
        <label for='dia-intercambio2'>Dias:</label>
        <select id='dia-intercambio2' class='form-control'>
            <?php
            for ($index = 0; $index < count($dias); $index++) {    
                echo "<option value='".$dias[$index]->getNombre()."'>".$dias[$index]->getNombre()."</option>";    
            } ?>
        </select>
        <label for='desde-intercambio2'>Desde:</label>
        <select id='desde-intercambio2' class='form-control'>
            <?php
            for ($index = 0; $index < count($horas); $index++) {
                $inicio = $horas[$index]->getInicio();
                $fin = $horas[$index]->getFin();
                echo "<option value='".$horas[$index]->getIdHora()."'>"."$inicio - $fin"."</option>";    
            } ?>
        </select>
        <label for='hasta-intercambio2'>Hasta:</label>
        <select id='hasta-intercambio2' class='form-control'>
            <?php
            for ($index = 0; $index < count($horas); $index++) {
                $inicio = $horas[$index]->getInicio();
                $fin = $horas[$index]->getFin();
                echo "<option value='".$horas[$index]->getIdHora()."'>"."$inicio - $fin"."</option>";    
            } ?>
        </select>
        </form><br>
        <iframe id="frame-despues" src="" frameborder="0" width="90%" height="300"></iframe>
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