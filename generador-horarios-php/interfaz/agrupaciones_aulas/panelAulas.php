<div class="col-lg-6">
    <div id="panelAulas" class="panel panel-warning">  
    
        <div id="cabeceraPanelAulas" class="panel-heading">  
            <p class="center center-block">Busque las aulas y suelte la materia en el panel</p>
        </div>
        <div class="panel-body">              
            <div class="input-group col-sm-16">
                <input type="text" class="form-control" placeholder="Buscar" id="buscar_aulas" name="buscar_aulas">
                <div class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                </div>                
            </div>
            <label class="checkbox-inline">
                <input type="checkbox" id="teorico" value="gt"> Grupos Teoricos
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" id="laboratorio" value="gl"> Grupos Laboratorio
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" id="discusion" value="gd"> Grupos Discusion
            </label>
            <div class="checkbox">
                <label>
                  <input type="checkbox" id="exclusiva" value="exclusiva">
                  Estas materias solo pueden impartise en esta aula
                </label>                
            </div>
            <br/>
            <div id="formularioAgrupacion" class="container-fluid">
                <button id="enviar" class="btn btn-success center center-block">Asignar Prioridad de Aulas</button>
            </div>
            <hr size="100%" />
<!--            <div   id="mostrar_aulas" class="container-fluid">

            </div>-->
            <div class="table-responsive">
                <div id="example2_wrapper" class="dataTables_wrapper form-inline" role="grid">
                    <table class="table table-bordered table-condensed table-striped table-hover">
    <!--                <table class="table table-condensed table-striped table-hover ">-->
                    <thead id="cabecera_materias">
                        <tr>                        
                            <th data-toggle='true' class='text-left footable-first-column'>Materia</th>
                            <th data-hide='all' class='text-center'>Materias Agrupadas</th>
                            <th class='footable-last-column text-center'>Departamento</th>
                        </tr>
                    </thead>                        
                    <tbody id="materias_arrastradas">
<!--                        <tr>                        
                            <td colspan="2">Aún no ha buscando ninguna materia</td>
                        </tr>                        -->
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
 