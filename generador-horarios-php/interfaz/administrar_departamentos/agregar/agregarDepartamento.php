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
                <label for="nombre" class="col-lg-2 control-label">Nombre</label>
                <div class="col-lg-4">
                    <input type="text" id="nombre" name="nombre" class="form-control formulario" placeholder="Ingeniería y Arquitectura" required/>
                </div>
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