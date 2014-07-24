
    <div class="panel-heading" style="background: white">
    
        <div class="input-group col-sm-4">
          <input type="text" class="form-control" placeholder="Buscar" id="buscar_materia" name="buscar_materia">
          <div class="input-group-btn">
            <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
          </div>          
        </div>
        <br/>
        
    </div>
        
        <div class="table-responsive">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <table class="table table-bordered table-condensed table-striped table-hover table-responsive">
<!--                <table class="table table-condensed table-striped table-hover ">-->
                <thead>
                    <tr>
                        <th class="text-center">N°</th>
                        <th class="text-center">Cod Materia</th>
                        <th data-toggle="true" class="text-left footable-first-column text-center">Materia</th>
                        <th data-hide="all" class="text-center">Materias Agrupadas</th>
                        <th class="text-center">Departamento</th>
                        <th class="text-center">Ciclo</th>
                        <th class="text-center">Alumnos Nuevos</th>
                        <th class="text-center">Otros Alumnos</th>
                        <th class="text-center">Total Alumnos</th>
                        <th class="text-center">Grupos Teóricos</th>                        
                        <th class="footable-last-column text-center">Alumnos por Grupo Teórico</th>                        
                        <th class="text-center">Horas Clase</th>
                    </tr>
                </thead>
                <!--<tbody id ="mostrarUsuarios" class="mostrarUsuarios" role="alert" aria-live="polite" aria-relevant="all">-->
                <tbody id="mostrarMaterias" class="mostrarMaterias">
                    <?php
                    require_once './contenidoTablaMaterias.php';
                   ?>
                </tbody>                
            </table>
                
            <div class="row">                
            <div class="col-xs-12">
                <div class="dataTables_paginate paging_bootstrap">
                    <div id="paginacion" class="text-center">
                            <?php require_once 'paginadorMaterias.php'; ?>
                    </div>                        
                </div>
            </div>
            </div>
            
            </div>
        </div>
               
