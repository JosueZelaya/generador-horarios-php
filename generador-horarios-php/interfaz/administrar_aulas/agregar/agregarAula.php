<?php 
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));

if(session_status()!=PHP_SESSION_ACTIVE){
    ManejadorSesion::sec_session_start();
}


if (ManejadorSesion::comprobar_sesion() == true) : ?>
<div class="panel panel-default col-sm-12">
    <form class="form-horizontal center center-block" id="formularioAgregarDocente">
        <div class="modal-header">            
        </div>
        <div class="modal-body">             
            <div class="form-group">
                <label for="codigo" class="col-lg-2 control-label">Codigo</label>
                <div class="col-lg-4">
                    <input type="text" id="codigo" name="codigo" class="form-control formulario" placeholder="A11" required/>
                </div>
            </div>
            <div class="form-group">
                <label for="capacidad" class="col-lg-2 control-label">Capacidad</label>
                <div class="col-lg-4">
                    <input type="text" id="capacidad" name="capacidad" class="form-control formulario" placeholder="100" required/>
                </div>
                
                    <label>
                        <input id="exclusiva" type="checkbox">
                        Exclusiva
                    </label>
                
            </div>            
        </div>
        <div class="modal-footer"> 
            <div id="resultado" class="resultado"></div>            
            <input type="button" name="add" id="add" class="btn btn-primary" value="Agregar" tabindex="4">
            <input type="reset" name="reset" class="btn btn-default" value="Borrar" tabindex="4">            
        </div>
    </form>
</div>
    


<?php else : ?>
    <p>
        <span class="">Usted no está autorizado para acceder a esta página.</span> Por favor <a href="../../../interfaz/index.php">Autentíquese</a>.
    </p>
<?php endif; ?>

<script type="text/javascript" src="js/picker.js"></script>
<script type="text/javascript" src="js/picker.date.js"></script>
<script type="text/javascript" src="js/es_ES.js"></script>
<script type="text/javascript" src="js/ajaxpost.js"></script>