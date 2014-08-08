 <div id="configuracion_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Información del ciclo de estudio</h4>
                <div id="mensaje_modal_config">
                    
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <label for="año">Año: </label>
                        <select class="form-control" id="año">                            
                            <?php 
                                for ($i = 2014; $i <= 2050; $i++) {
                                    echo "<option>$i</option>";
                                }
                            ?>
                        </select>    
                    </div>
                    <div class="form-group">
                        <label for="ciclo">Ciclo: </label>
                        <select class="form-control" id="ciclo">
                            <option>1</option>                            
                            <option>2</option>
                        </select>    
                    </div>
                </div>               
            </div>
            <div class="modal-footer">
                <div class="row">                    
                    <button id="aprobar" type="button" class="btn btn-primary">Aprobar</button>                    
                </div>               
            </div>
        </div>
    </div>
</div>
