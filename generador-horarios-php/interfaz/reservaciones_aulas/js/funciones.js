var dia="";
var inicio="";
var fin="";
var diaSeleccionado="";
var horaInicioSeleccionada="";
var horaFinSeleccionada="";
$(function (){
    $(document).on("click",".grupo",function(){        
        $(".grupo").css("background","");        
        seleccionarCeldas($(this));
        dia = diaSeleccionado;
        inicio = horaInicioSeleccionada;
        fin = horaFinSeleccionada;        
        iluminarCeldas(dia,inicio,fin,"reservaciones");        
    });  
    
    $(document).on("click","#reservarHoras",function(){         
        var aula = $("#aulas").val();        
        var dataString = "dia="+dia+"&hora_inicio="+horaInicioSeleccionada+"&hora_fin="+horaFinSeleccionada+"&aula="+aula;
        $.ajax({
            type: "GET",
            url: './reservar.php',
            data: dataString,
            success: function(respuesta){
                respuesta = jQuery.parseJSON(respuesta);
                if(respuesta==="ok"){
                    dataString = 'aula='+$("#aulas").val();
                    $.ajax({
                        type: "GET",
                        url: './mostrarHorario.php',
                        data: dataString,
                        success: function(datos){
                           asignarDiasHoras("","","");
                           $("#mostrarHorario").html(datos);                           
                        }
                    });                     
                }else{
                    alert(respuesta);
                }                               
            }            
        });
    });
    
    $(document).on("click","#liberarHoras",function(){
        var aula = $("#aulas").val();        
        var dataString = "dia="+dia+"&hora_inicio="+horaInicioSeleccionada+"&hora_fin="+horaFinSeleccionada+"&aula="+aula;
        $.ajax({
            type: "GET",
            url: './liberarHoras.php',
            data: dataString,
            success: function(respuesta){
                respuesta = jQuery.parseJSON(respuesta);
                if(respuesta==="ok"){
                    dataString = 'aula='+$("#aulas").val();
                    $.ajax({
                        type: "GET",
                        url: './mostrarHorario.php',
                        data: dataString,
                        success: function(datos){
                           asignarDiasHoras("","","");
                           $("#mostrarHorario").html(datos);                           
                        }
                    });                     
                }else{
                    alert(respuesta);
                }                               
            }            
        });
    });
    
    $(document).on("change","#aulas",function(){         
        var aula = $(this).val();
        var dataString = 'aula='+aula;
        $.ajax({
            type: "GET",
            url: './mostrarHorario.php',
            data: dataString,
            success: function(data){
               $("#mostrarHorario").html(data);
            }
        });
    });
    
});

function seleccionarCeldas(celda){    
    if(diaSeleccionado===""){ //Si aun no se ha elegido ningún día
        celda.css("background","#9CEEE6");
        asignarDiasHoras(celda.attr("data-dia"),celda.attr("data-hora"),celda.attr("data-hora"));        
    }else{ //Si ya se había elegido alguna celda en algún día
        if(diaSeleccionado===celda.attr("data-dia")){ //Si se desa elegir una celda del mismo día anterior            
            asignarDiasHoras(diaSeleccionado,horaInicioSeleccionada,horaFinSeleccionada);
            var horaActual = celda.attr("data-hora");
            if(esUnaCeldaSeleccionada(horaActual,horaInicioSeleccionada,horaFinSeleccionada)){ //Si se selecciona una celda que ya había sido seleccionada                                
                asignarDiasHoras("","","");                    
            }else if(horaFinSeleccionada > horaActual){ //Cuando se selecciona de abajo hacia arriba                                
                horaInicioSeleccionada = celda.attr("data-hora");  
                asignarDiasHoras(diaSeleccionado,horaInicioSeleccionada,horaFinSeleccionada);                                                
            }else if(horaFinSeleccionada < horaActual){ //Cuando se selecciona de arriba hacia abajo                    
                horaFinSeleccionada = celda.attr("data-hora");                
                asignarDiasHoras(diaSeleccionado,horaInicioSeleccionada,horaFinSeleccionada);                                                
            } 
        }else{ //Si se elige una celda en un día distinto al anterior                          
           asignarDiasHoras(celda.attr("data-dia"),celda.attr("data-hora"),celda.attr("data-hora"));
           celda.css("background","#9CEEE6");                          
        }
    }    
}

function asignarDiasHoras(dia,horaInicio,horaFin){    
    diaSeleccionado=dia;
    horaInicioSeleccionada=parseInt(horaInicio);
    horaFinSeleccionada=parseInt(horaFin);    
}

function esUnaCeldaSeleccionada(horaActual,horaInicio,horaFin){    
    horaActual = parseInt(horaActual);
    horaInicio = parseInt(horaInicio);  
    horaFin = parseInt(horaFin);
    if(horaInicio < horaActual && horaActual < horaFin){
        return true;
    }else if(horaFin === horaActual || horaInicio === horaActual){
        return true;
    }
    return false;
}

function iluminarCeldas(dia,horaInicio,horaFin,div){    
    if(horaInicio !== "" && horaFin !==""){
        if(horaInicio < horaFin){
        horaInicio = parseInt(horaInicio);
        intermedio = horaInicio+1;
        }else{
            intermedio = horaInicio;
        }   
        hi = parseInt(horaInicio);
        hf = parseInt(horaFin);
        for (i=hi;i<=hf;i++){
            $("#"+dia+i).css("background","#9CEEE6"); 
        }
    }    
}