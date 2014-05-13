<?php

include_once '../reglas_negocio/Facultad.php';

session_start();
$facultad = $_SESSION['facultad'];

$aulas = $facultad->getAulas();

echo "<div class='row top-buffer'>";
echo "<form class='form-inline' role='form'>";

echo "<div class='col-lg-2'>";
echo "<select id='aula' class='aula form-control'>";
for ($index = 0; $index < count($aulas); $index++) {    
    echo "<option value='".$aulas[$index]->getNombre()."'>".$aulas[$index]->getNombre()."</option>";    
}
echo "</select>";
echo "</div>";

echo "<div class='col-lg-10'>";
echo "<input type='button' name='mostrarHorario' id='mostrarHorario' class='btn btn-primary' value='Mostrar Horario' tabindex='4'>";
echo "</div>";

echo "</form>";


echo "</div>";
echo "<br/>";