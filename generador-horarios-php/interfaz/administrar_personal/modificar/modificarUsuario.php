<?php
chdir(dirname(__FILE__));
require_once '../../../reglas_negocio/ManejadorSesion.php';
chdir(dirname(__FILE__));
if(session_status()!=PHP_SESSION_ACTIVE){
    ManejadorSesion::sec_session_start();
}

if (ManejadorSesion::comprobar_sesion() == true) : ?>

<div class="panel panel-default col-sm-11">
    <!-- Default panel contents -->
    <div class="panel-heading" style="background: white">
    
        <div class="input-group col-sm-4">
          <input type="text" class="form-control" placeholder="Buscar" id="buscar_usuario_modificar" name="buscar_usuario_modificar">
          <div class="input-group-btn">
            <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
          </div>          
        </div>
        <br/>
        
    </div>
        
        <div class="table-responsive">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <!--<table class="table table-bordered table-condensed table-striped table-hover">-->
                <table class="table table-bordered table-condensed table-striped table-hover ">
<!--                <table class="table table-condensed table-striped table-hover ">-->
                <thead>
                    <tr>                        
                        <th data-toggle="true" class="text-left footable-first-column">Login</th>
                        <th class="text-left">Docente</th>                                                
                    </tr>
                </thead>                
                <tbody id="mostrarUsuarios" class="mostrarUsuarios">

                    <?php include_once 'contenidoTablaUsuarios.php'; ?>
                </tbody>                
            </table>
            <div class="row">                
                <div class="col-xs-12">
                    <div class="dataTables_paginate paging_bootstrap">
                        <div id="paginacion">
                                <?php $css_class='paginaUsuariosModificar'; require_once 'paginadorUsuarios.php'; ?>
                        </div>                        
                    </div>
                </div>
            </div>
            </div>
        </div>
        
    </div>

<?php else : ?>
    <p>
        <span class="">Usted no está autorizado para acceder a esta página.</span> Por favor <a href="../capa_interfaz/index.php">Autentíquese</a>.
    </p>
<?php endif; ?>