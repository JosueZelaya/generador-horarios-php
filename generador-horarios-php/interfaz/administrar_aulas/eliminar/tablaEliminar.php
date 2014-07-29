<div class="panel panel-default col-sm-11">     
    <div class="panel-heading" style="background: white">
    
        <div id='cuadro-busqueda' class="input-group col-sm-4 cuandro-busqueda">
            <?php require_once 'cuadroBusquedaAulas.php'; ?>
        </div>
        <br/>
        
    </div>
        
        <!--<div class="table-responsive">-->
        <div class="">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline" role="grid">                
                <table class="table table-bordered table-condensed table-striped table-hover tbl-del-doc">
                <thead id='cabeceraEliminar' class='cabeceraEliminar'>
                    <?php require_once 'cabeceraTablaAulas.php'; ?>
                </thead>
                <!--<tbody id ="mostrarUsuarios" class="mostrarUsuarios" role="alert" aria-live="polite" aria-relevant="all">-->
                <tbody id="mostrarEliminar" class="mostrarEliminar">                
                    <?php require_once 'contenidoTablaAulas.php'; ?>
                </tbody>                
            </table>
                
            <div class="row">                
                <div class="col-xs-12">
                    <div class="dataTables_paginate paging_bootstrap">
                        <div id="paginacion">
                                <?php $css_class="paginaAulas"; require_once 'paginadorAulas.php'; ?>
                        </div>                        
                    </div>
                </div>
            </div>
                

            </div>
        </div>        
    </div>

<script src="bootstrap/FooTable-2/js/footable.js?v=2-0-1" type="text/javascript"></script>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/bootbox.min.js"></script>