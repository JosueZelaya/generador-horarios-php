<?php
include_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__));
include_once '../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
ManejadorSesion::sec_session_start();
$facultad = $_SESSION['facultad'];
?>
<div class="row">
    <div class="col-sm-12" style="text-align: left">
        <a class="btn btn-md btn-primary" href="#" id='intercambiarHoras'>
            <i class="fa fa-retweet fa-lg"></i> Intercambiar
        </a>
        <a class="btn btn-md btn-primary" href="#" id='buscarHoras' data-loading-text="Buscando...">
            <i class="fa fa-search fa-lg"></i> Busqueda
        </a>
        <div class="btn-group pull-right" data-toggle="buttons">
            <label class="btn btn-primary active">
              <input type="radio" name="options" id="simSearch" checked> Básico
            </label>
            <label class="btn btn-primary">
              <input type="radio" name="options" id="advSearch"> Avanzado
            </label>
        </div>
        <hr style="background:gray; border:0; height: 1px; width: 100%">
    </div>
</div>
<br>
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
        <div id="despues-intercambio" style="width:100%; height:300px; overflow: scroll;"></div>
    </div>
</div>
<br>
