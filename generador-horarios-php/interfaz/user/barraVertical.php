<div class="col-lg-2">
    <div class="btn-group-vertical" id="barraVertical">
        <?php if($_SESSION['usuario_login'] == "admin") : ?>
        <a href="#" id="generarHorario" class="btn btn-default" role="button"> Generar Horario</a>
        <?php endif; ?>
        <a href="#" id="verFiltro" class="btn btn-default" role="button"> Ver Horario </a>
        <div class="btn-group">
            <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" role="button">
                Gestionar Horario
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li><a href="#" id="intercambioHorario">Intercambios</a></li>
            </ul>
        </div>
        <a href="#" id="abrirHorario" class="btn btn-default" role="button"> Abrir</a>
        <?php if($_SESSION['usuario_login'] == "admin") : ?>
        <a href="#" id="guardarHorario" class="btn btn-default" role="button"> Guardar</a>
        <?php endif; ?>
        <a href="#" class="btn btn-default" role="button"> Exportar</a>
        <a href="#" class="btn btn-default" role="button"> Imprimir</a>
    </div>    
</div>    
