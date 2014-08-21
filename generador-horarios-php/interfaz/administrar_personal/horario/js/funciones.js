var inicio="";
var fin="";
var horaInicioSeleccionada="";
var horaFinSeleccionada="";
var idDocente=0;
$(document).ready(function() {
    // prepare the data
    var source =
    {
            datatype: "json",
            datafields: [
            { name: 'Name'},
            { name: 'Id'}
            ],
            url: 'obtenerDocentes.php',
            async: false
    };
    var dataAdapter = new $.jqx.dataAdapter(source);
    // activar combobox
    $("#cmbDocentes").jqxComboBox(
    {
            source: dataAdapter,
            width: '50%',
            height: 25,
            theme: 'bootstrap',
            autoComplete: true,
            searchMode: 'containsignorecase',
            placeHolder: 'Nombre docente',
            displayMember: 'Name',
            valueMember: 'Id'
    });
    //evento al elegir un docente en el combobox
    $("#cmbDocentes").on('select', function (event) {
        if (event.args) {
            var item = event.args.item;
            if (item) {
                idDocente = item.value;
                var dataString = "op=mostrar&id="+idDocente;
                $.ajax({            
                    type: "GET",
                    url: "./modSchDoc.php",
                    data: dataString,            
                    success: function(datos){
                        var msj = jQuery.parseJSON(datos);
                        $("#mostrarHorario").html(msj);
                    }
                });
            }
        }
    });
    
    $(document).on("click",".hora",function(){        
        $(".hora").css("background","");    
        seleccionarCeldas($(this));
        inicio = horaInicioSeleccionada;
        fin = horaFinSeleccionada;        
        iluminarCeldas(inicio,fin);        
    });
    
    $(document).on("click","#guardarHoras",function(){
        bootbox.confirm("Esto eliminara un horario previamente asignado, si lo hay. <br>Desea continuar?",function(confirm){
            if(confirm === true){
                var dataString = "op=guardar&id="+idDocente+"&desde="+horaInicioSeleccionada+"&hasta="+horaFinSeleccionada;
                $.ajax({            
                    type: "GET",
                    url: "./modSchDoc.php",
                    data: dataString,            
                    success: function(datos){
                        var msj = jQuery.parseJSON(datos);
                        if(msj === 1){
                            bootbox.alert("Debe seleccionar un docente y al menos una hora para su horario");
                        } else if(msj === 0){
                            bootbox.alert("Horario guardado",function(){
                                var item = $("#cmbDocentes").jqxComboBox('getItemByValue', idDocente);
                                $("#cmbDocentes").jqxComboBox('selectItem', item );
                            });
                        } else{
                            bootbox.alert("Error: "+msj);
                        }
                    }
                });
            }
        });
    });
    
    $(document).on("click","#borrarHoras",function(){
        bootbox.confirm("Está seguro que desea borrar el horario asignado?",function(confirm){
           if(confirm===true){
               var dataString = "op=borrar&id="+idDocente;
                $.ajax({            
                    type: "GET",
                    url: "./modSchDoc.php",
                    data: dataString,            
                    success: function(datos){
                        var msj = jQuery.parseJSON(datos);
                        if(msj === 1){
                            bootbox.alert("Debe seleccionar un docente");
                        } else{
                            bootbox.alert("Horario eliminado",function(){
                                var item = $("#cmbDocentes").jqxComboBox('getItemByValue', idDocente);
                                $("#cmbDocentes").jqxComboBox('selectItem', item );
                            });
                        }
                    }
                });
           }
        });
    });
});

function seleccionarCeldas(celda){    
    if(horaInicioSeleccionada==="" || isNaN(horaInicioSeleccionada)){ //Si aun no se ha elegido ningúna hora
        celda.css("background","#9CEEE6");
        asignarHoras(celda.attr("data-hora"),celda.attr("data-hora"));        
    }else{ //Si ya se había elegido alguna celda
        asignarHoras(horaInicioSeleccionada,horaFinSeleccionada);
        var horaActual = celda.attr("data-hora");
        if(esUnaCeldaSeleccionada(horaActual,horaInicioSeleccionada,horaFinSeleccionada)){ //Si se selecciona una celda que ya había sido seleccionada                                
            asignarHoras("","");                    
        }else if(horaFinSeleccionada > horaActual){ //Cuando se selecciona de abajo hacia arriba                                
            horaInicioSeleccionada = celda.attr("data-hora");  
            asignarHoras(horaInicioSeleccionada,horaFinSeleccionada);                                                
        }else if(horaFinSeleccionada < horaActual){ //Cuando se selecciona de arriba hacia abajo                    
            horaFinSeleccionada = celda.attr("data-hora");                
            asignarHoras(horaInicioSeleccionada,horaFinSeleccionada);                                                
        }
    }    
}

function asignarHoras(horaInicio,horaFin){    
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

function iluminarCeldas(horaInicio,horaFin){
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
            $("#hora"+i).css("background","#9CEEE6"); 
        }
    }    
}