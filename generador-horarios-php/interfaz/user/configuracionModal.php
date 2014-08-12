<?php
chdir(dirname(__FILE__));
require_once '../../reglas_negocio/Facultad.php';
chdir(dirname(__FILE__)); 
?>
<div id="configuracion_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Aperturar un nuevo ciclo</h4>
                <div id="mensaje_modal_config">
                    
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <label for="año">Año: </label>
                        <select class="form-control" id="año">                            
                            <?php 
                                $ultimo_año = Facultad::getUltimoAñoRegistrado();
                                for ($i = $ultimo_año; $i <= 2050; $i++) {
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
                    <div class="form-group">
                        <label for="año_clonar">Clonar la información del año: </label>
                        <select class="form-control" id="año_clonar">
                            <option value="false">No clonar</option>
                            <?php 
                                $años = Facultad::getAñosRegistrados();
                                foreach ($años as $año) {
                                    echo "<option value='$año'>$año</option>";
                                }
                            ?>
                        </select>    
                    </div>
                    <div class="checkbox">
                        <label>
                            <input id="clonar_horario" type="checkbox"> <b>Clonar También horario:</b>
                            Si desea también clonar el horario que se generó en el año especificado marque esta opción.                            
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input id="forzar" type="checkbox"> <b>Forzar:</b>
                            Al forzar se reemplazarán todos los datos que pudieran encontrarse
                            en el año y ciclo que desea aperturar. Debe usarse con precaución porque los datos
                            borrados ya no podrán recuperarse.
                        </label>
                    </div>
                </div>               
            </div>
            <div class="modal-footer">
                <div class="row">                    
                    <button id="aprobar" type="button" class="btn btn-primary" data-loading-text="procesando... por favor espere...">
                        <i class="fa fa-check-circle fa-lg"></i>
                        Aperturar
                    </button>                    
                </div>               
            </div>
        </div>
    </div>
</div>
