<div class="col-lg-6">
    <div id="panelAgrupaciones" class="panel panel-warning panelGrupos">
        <div id="cabeceraPanel" class="panel-heading">  
            <p class="center center-block">Busque una agrupacion para ver sus materias</p>
        </div>
        <div class="panel-body">
            <div class="input-group col-sm-4">
                <input type="text" class="form-control" placeholder="Buscar" id="buscar_agrupacion" name="buscar_agrupacion">
                <div class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                </div>                
            </div>
            <br/>
            <div class="row">
                <div id="formularioAgrupacion" class="container-fluid col-lg-6">
                    <button id="actualizar_agrupacion" class="btn btn-info center center-block"><span class="glyphicon glyphicon-refresh"></span> Actualizar Agrupacion</button>                
                </div>
                <div id="formularioAgrupacion" class="container-fluid col-lg-6">
                    <button id="eliminar_agrupacion" class="btn btn-danger center center-block"><span class="glyphicon glyphicon-trash"></span> Eliminar Agrupacion</button>
                </div>
            </div>                        
            <hr size="100%" />
            <div   id="mostrar_agrupacion" class="container-fluid">
                <div class="table-responsive">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline" role="grid">
                        <table class="table table-bordered table-condensed table-striped table-hover">
        <!--                <table class="table table-condensed table-striped table-hover ">-->
                        <thead>
                            <tr id="cabecera_materias_arrastradas">                        
<!--                                <th class="text-center">Codigo</th>
                                <th class="text-center">Materia</th>
                                <th class="text-center">Carrera</th>
                                <th class="text-center">Plan Estudio</th> 
                                <th class="text-center">Departamento</th>                                -->
                            </tr>
                        </thead>                        
                        <tbody id="contenido_materias_arrastradas">
                                                       
                        </tbody>                
                    </table>

                    <div class="row">                
                    <div class="col-xs-12">
                        <div class="dataTables_paginate paging_bootstrap">
                            <div id="paginacion" class="text-center">
                                    <?php //require_once './paginadorMaterias.php'; ?>
                            </div>                        
                        </div>
                    </div>
                    </div>

                    </div>
                </div>

            </div>
        </div>        
    </div>
</div>
