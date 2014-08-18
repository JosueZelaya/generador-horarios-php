//Exit 10 = cuando no se ha seleccionado nada en las cuadriculas
//Exit 0 = cuando fue exitoso
//Exit 11 = no esta autorizado

var diaAntes="";
var inicioAntes="";
var finAntes="";
var diaDespues="";
var inicioDespues="";
var finDespues="";
var diaSeleccionado1="";
var diaSeleccionado2="";
var horaInicioSeleccionada1="";
var horaInicioSeleccionada2="";
var horaFinSeleccionada1="";
var horaFinSeleccionada2="";
var areaHTML="";

$(function (){
    
    $(document).on("click","#goToIndex",function(){
        window.location.href = 'index.php';
    });
    
    $(document).on("click","#aprobar",function(){
        $("#mensaje_modal_config").html("");
        var btn = $(this);
        btn.button('loading');
        var año=$("#año").val();
        var ciclo=$("#ciclo").val();
        var clonar=$("#año_clonar").val();
        var clonar_horario=document.getElementById('clonar_horario').checked;
        var forzar=document.getElementById('forzar').checked;        
        var dataString = 'año='+año+"&ciclo="+ciclo+"&año_clonar="+clonar+"&clonar_horario="+clonar_horario+"&forzar="+forzar;        
        $.ajax({            
            type: "GET",
            url: "./configurarCiclo.php",
            data: dataString,            
            success: function(datos){
                datos = jQuery.parseJSON(datos);
                if(datos==="ok"){
                    $('#configuracion_modal').modal("hide");                    
                }else{
                    $("#mensaje_modal_config").html("<font color='red'>"+datos+"</font>");                 
                }               
            }
        }).always(function(){
            btn.button('reset');
        });;            
    });
    
    $(document).on("click","#generarHorario",function(){
        limpiarMain();
        addFiltro();
        addContent();
        $.ajax({            
            type: "GET",
            url: "./hayHorarioGenerado.php",
            success: function(datos){
                datos = jQuery.parseJSON(datos);
                if(datos==="si"){
                    var mensaje = "¡Ya hay un horario generado!<br/>\n\
                                    ¿Realmente desea generar uno nuevo?";
                    bootbox.confirm(mensaje,function(resultado){
                        if(resultado===true){
                            $('#contenido').load("./cargando.php");
                            generarHorario();
                        }else{
                            addFiltro();
                            $('#filtro').load("./formularioFiltro.php");
                        }
                    });
                }else{
                    $('#contenido').load("./cargando.php");
                    generarHorario();
                }
            },
            error: function(datos){
                bootbox.alert("error: "+datos,function(){});
            }
        });        
    });
    
    $(document).on("click","#verFiltro",function(){
        $.ajax({            
            type: "GET",
            url: "./hayHorarioGenerado.php",
            success: function(datos){
                datos = jQuery.parseJSON(datos);
                if(datos==="si"){
                    limpiarMain();
                    addFiltro();
                    addContent();
                    $('#filtro').load("./formularioFiltro.php");
                }else{
                    bootbox.alert("¡Aun no ha generado o cargado ningún horario!");
                }
            },
            error: function(datos){
                bootbox.alert("error: "+datos,function(){});
            }
        });    
    });
    
    $(document).on("click","#mostrarHorario",function(){        
        var aula = $("#aula").val();
        var departamento = $('#departamento').val();       
        var carrera = $('#carrera').val();
        var dataString = 'aula='+aula+"&departamento="+departamento+"&carrera="+carrera;
        dibujarHorario(dataString);
    });
    
    $(document).on("click","#mostrarHorarioDepartamento",function(){
        var aula = $("#aula").val();
        var departamento = $('#departamento').val();       
        var carrera = $('#carrera').val();       
        var dataString = 'aula='+aula+"&departamento="+departamento+"&carrera="+carrera;
        if(departamento!=='todos'){
            dibujarHorario(dataString);
        }else{
            bootbox.alert("¡Debe seleccionar un departamento para filtrar!",function(){});
        } 
    });
    
    $(document).on("click","#mostrarHorarioMateria",function(){        
        var departamento = $('#departamento').val();       
        if(departamento==='todos'){
            bootbox.alert("¡Debe seleccionar un departamento para filtrar!",function(){});
        }else{
            var carrera = $('#carrera').val(); 
            var materia = $('#materia').val();
            var ciclo = $('#ciclo').val();
            var dataString = "departamento="+departamento+"&carrera="+carrera+"&materia="+materia+"&ciclo="+ciclo;     
            dibujarHorario(dataString);        
        }        
    });
    
    $(document).on("click","#mostrarHorarioHora",function(){        
        var departamento = $('#departamento').val();       
//        if(departamento==='todos'){
//            bootbox.alert("¡Debe seleccionar un departamento para filtrar!",function(){});
//        }else{
            var carrera = $('#carrera').val();            
            var dataString = "aula=todos"+"&departamento="+departamento+"&carrera="+carrera;
            dibujarHorario(dataString);
//        }        
    });
         
    $(document).on("change","#departamento",function(){
        var dataString = 'departamento='+$(this).val(); 
        $.ajax({            
            type: "GET",
            url: "./carreras.php",
            data: dataString,
            success: function(data){                
                $('#carrera').html(data);
            }
        });
        $.ajax({            
            type: "GET",
            url: "./aulasDepartamento.php",
            data: dataString,
            success: function(data){                
                $('#aula').html(data);
            }
        }); 
        dataString = dataString+"&carrera="+$('#carrera').val();
        $.ajax({            
            type: "GET",
            url: "./materias.php",
            data: dataString,
            success: function(data){                
                $('#materia').html(data);
            }
        });
    });
    
    $(document).on("change","#carrera",function(){
        var dataString = 'carrera='+$(this).val()+"&departamento="+$('#departamento').val();        
        if($(this).attr('data-tipo')==='materia'){
            $.ajax({            
                type: "GET",
                url: "./materias.php",
                data: dataString,
                success: function(data){                
                    $('#materia').html(data);
                }
            });
        }else{
            $.ajax({            
                type: "GET",
                url: "./aulasDepartamento.php",
                data: dataString,
                success: function(data){                
                    $('#aula').html(data);
                }
            });            
        }        
    });
        
    $(document).on("click","#filtroMateria",function(){          
       var dataString = 'criterio=materia';
       $.ajax({            
            type: "GET",
            url: "./formularioFiltro.php",            
            data: dataString,
            success: function(data){                                
                $('#filtro').html(data);
                $('#contenido').html("");
            }
        });         
    });
    
    $(document).on("click","#filtroDepartamento",function(){        
       var dataString = 'criterio=departamento';
       $.ajax({            
            type: "GET",
            url: "./formularioFiltro.php",            
            data: dataString,
            success: function(data){                                
                $('#filtro').html(data);
                $('#contenido').html("");
            }
        }); 
    });
    
    $(document).on("click","#filtroTODO",function(){        
        var dataString = 'criterio=todo';
           $.ajax({            
                type: "GET",
                url: "./formularioFiltro.php",            
                data: dataString,
                success: function(data){                                
                    $('#filtro').html(data);
                    $('#contenido').html("");
                }
            });
    });
    
    $(document).on("click","#filtroHora",function(){        
       var dataString = 'criterio=hora';
       $.ajax({            
            type: "GET",
            url: "./formularioFiltro.php",            
            data: dataString,
            success: function(data){                                
                $('#filtro').html(data);
                $('#contenido').html("");
            }
        });
        dibujarHorario(dataString);
    });
    
    $(document).on("click",".verHora",function(){
       var dia = $(this).attr("dia"); 
       var hora = $(this).attr("hora");       
       var depar = $('#departamento').val();
       var dataString = "dia="+dia+"&hora="+hora+"&depar="+depar;
       $.ajax({            
            type: "GET",
            url: "./vista_hora.php",            
            data: dataString,
            success: function(data){
                if(depar==="todos"){
                    $('#contenido').html(data);
                }else{
                    bootbox.alert(data); 
                }                
            }
        });
    });
   
    $(document).on("click","#intercambioHorario",function(){        
        $.ajax({            
            type: "GET",
            url: "./hayHorarioGenerado.php",
            success: function(datos){
                datos = jQuery.parseJSON(datos);            
                if(datos==="si"){
                    limpiarMain();
                    addContent(); 
                    $.ajax({
                        type: "GET",
                        url: "./areaIntercambio.php",
                        success: function(data){
                            $('#contenido').html(data);
                            var aula = $("#aula-intercambio1").val();
                            mostrarAreaIntercambio1(aula);
                            mostrarAreaIntercambio2(aula);
                            resetearDiasHoras();
                            $("#buscarHoras").hide();
                        }
                    });
                }else{
                    var mensaje = "¡Aun no ha generado o cargado ningun horario!";
                    bootbox.alert(mensaje, function() {}); 
                }
            },
            error: function(err){
                bootbox.alert("Los datos no se pudieron enviar!",function(){});                
            }
        });     
    });
    
    $(document).on("click","#guardarHorario",function(){
        limpiarMain();
        addContent();
        $("#contenido").load("./cargando.php");
        setTimeout(function(){
            $.ajax({
                type: "GET",
                url: "save.php",
                success: function(datos){
                    datos = jQuery.parseJSON(datos);            
                    if(datos===0){
                        bootbox.alert("¡Horario guardado!",function(){
                            window.location.href = 'index.php';
                        });
                    }else{
                        bootbox.alert("¡Error al guardar el horario!",function(){});
                    } 
                },
                error: function(datos){
                    bootbox.alert("error"+datos,function(){});
                }
            });
        },1000);
    });
    
    $(document).on("click","#abrirHorario",function(){
        limpiarMain();
        addContent();
        var dataString = "op=inicio";
        $.ajax({
            type: "GET",
            url: "open.php",
            data: dataString,
            success: function(datos){
                datos = jQuery.parseJSON(datos);            
                $("#contenido").html(datos);
                activarCombos();
            },
            error: function(datos){
                bootbox.alert("error"+datos,function(){});
            }
        });
    });
    
    $(document).on("click","#openSch",function(){
        var anio = $("#comboAnios").btComboBox('value');
        var ciclo = $("#comboCiclos").btComboBox('value');
        var dataString = "op=existe&anio="+anio+"&ciclo="+ciclo;
        $.ajax({            
            type: "GET",
            url: "./open.php",            
            data: dataString,
            success: function(data){
                var msj = jQuery.parseJSON(data);
                if (msj === 0)
                    bootbox.alert("Esto puede tardar unos minutos. Haga click en 'OK' para comenzar",function(){
                        limpiarMain();
                        addContent();
                        $("#contenido").load("./cargando.php");
                        construirHorario(anio,ciclo);
                    });
                else if (msj === 1)
                    bootbox.alert("No existe un horario para el año y ciclo especificado");
            }
        });
    });
    
    $(document).on("change","#aula-intercambio1",function(){
        var aula = $("#aula-intercambio1").val();
        mostrarAreaIntercambio1(aula);
        resetearDiasHorasArea("Intercambio1");
    });
    
    $(document).on("change","#aula-intercambio2",function(){
        var aula = $("#aula-intercambio2").val();
        mostrarAreaIntercambio2(aula);
        resetearDiasHorasArea("Intercambio2");
    });
    
    $(document).on("click","#intercambiarHoras",function(event, aula){
        var aula1 = $('#aula-intercambio1').val();
        if($('#aula-intercambio2').length)
            var aula2 = $('#aula-intercambio2').val();
        else
            var aula2 = aula;
        var dia1 = diaAntes;
        var dia2 = diaDespues;
        var desde1 = inicioAntes;
        var desde2 = inicioDespues;
        var hasta1 = finAntes;
        var hasta2 = finDespues;
        var dataString = 'op=intercambio&aula1='+aula1+'&aula2='+aula2+'&dia1='+dia1+'&dia2='+dia2+'&desde1='+desde1+"&desde2="+desde2+'&hasta1='+hasta1+'&hasta2='+hasta2;
        $.ajax({
            type: "GET",
            url: './administracionHorario.php',
            data: dataString,
            success: function(data){
                var msj = jQuery.parseJSON(data);
                if(msj === 1)
                    bootbox.alert("Eliga numero de horas iguales en cada bloque de intercambio");
                else if(msj === 0){
                    actualizarIntercambio(aula1,aula2);
                }
                else if(msj === 10)
                    bootbox.alert("Debe seleccionar al menos 1 hora en cada area de intercambio");
                else if(msj === 11)
                    bootbox.alert("Ud no está autorizado para este intercambio, realice una solicitud al administrador");
                else{
                    bootbox.confirm(msj,function(resultado){
                        if(resultado===true){
                            segundaFaseIntercambio(aula1,dia1,desde1,hasta1,aula2,dia2,desde2,hasta2);
                        }
                    });
                }
            }
        });        
    });
    
    $(document).on("change","input:radio[id$='Search']",function(){
        var msj = $(this).attr("id");
        if(msj === "advSearch"){
            $("div.row:eq(3)").html('');
            $("#buscarHoras").show();
            $("#intercambiarHoras").hide();
        } else
            $("#intercambioHorario").click();
    });
    
    $(document).on("click","#intercambiarHoraBusqueda",function(){
        var contenedorDatos = $(this).parent().parent();
        var aula = $(contenedorDatos).attr("data-aula");
        var dia = $(contenedorDatos).attr("data-dia");
        var inicio = $(contenedorDatos).attr("data-inicio");
        var fin = $(contenedorDatos).attr("data-fin");
        diaDespues = dia;
        obtenerIdHora(inicio,"inicio").done(function(response){
            var msj = jQuery.parseJSON(response);
            if(msj === "null")
                bootbox.alert("Hora no encontrada");
            else{
                inicioDespues = msj;
                obtenerIdHora(fin,"fin").done(function(response){
                    var msj = jQuery.parseJSON(response);
                    if(msj === "null")
                        bootbox.alert("Hora no encontrada");
                    else
                        finDespues = msj;
                    $('#intercambiarHoras').trigger('click', aula);
                });
            }
        });
    });
    
    $(document).on("click","#buscarHoras",function(){
        var btn = $(this);
        btn.button('loading');
        if($("div#search").length)
            $("div.row:eq(3)").html('');
        var aula = $('#aula-intercambio1').val();
        var dia = diaAntes;
        var desde = inicioAntes;
        var hasta = finAntes;
        var dataString = 'op=buscar&aula='+aula+'&dia='+dia+'&desde='+desde+'&hasta='+hasta;
        $.ajax({
            type: "GET",
            url: './administracionHorario.php',
            data: dataString,
            success: function(data){
                var msj = jQuery.parseJSON(data);
                if(msj === 10)
                    bootbox.alert("Debe seleccionar al menos 1 hora en el area de intercambio");
                else if(msj === 1)
                    bootbox.alert("Debe seleccionar horas de una misma agrupacion, grupo y tipo de grupo");
                else if(msj === 2)
                    bootbox.alert("No se encontro ningun espacio disponible");
                else{
                    $("div.row:eq(3)").html(msj);
                    paginarBusqueda();
                }
            }
        }).always(function(){
            btn.button('reset');
        });
    });
    
    $(document).on("click",".grupo",function(){  
        $(".grupoSeleccionado").removeClass("grupoSeleccionado").addClass("grupo");
        $(".grupo").css("background","");                
        $(".grupoSeleccionado").css("background","");                
        $(".grupoVacio").css("background","");
        var grupo = $(this).attr("data-grupo");
        $('.'+grupo).css("background","#9CEEE6");        
        $('.'+grupo).removeClass("grupo").addClass("grupoSeleccionado");        
    });
    
    $(document).on("click",".grupoSeleccionado",function(){
        $(".grupoSeleccionado").css("background","");
        $(".grupoSeleccionado").removeClass("grupoSeleccionado").addClass("grupo");
        $(this).css("background","#9CEEE6");        
    });
    
    $(document).on("click",".grupoVacio",function(){
        $(".grupo").css("background","");
        $(".grupoSeleccionado").css("background","");
    });

    $(document).on("click",".intercambio1",function(){                                    
        $(".intercambio1").css("background","");        
        seleccionarCeldas("Intercambio1",$(this));
        diaAntes = diaSeleccionado1;
        inicioAntes = horaInicioSeleccionada1;
        finAntes = horaFinSeleccionada1;        
        iluminarCeldas(diaAntes,inicioAntes,finAntes,"intercambio1");          
    });
    
    $(document).on("click",".intercambio2",function(){
        $(".intercambio2").css("background","");        
        seleccionarCeldas("Intercambio2",$(this));
        diaDespues = diaSeleccionado2;
        inicioDespues = horaInicioSeleccionada2;
        finDespues = horaFinSeleccionada2;
        iluminarCeldas(diaDespues,inicioDespues,finDespues,"intercambio2");
    });
    
    $(document).on("click","#moreInfo",function(){
        var div = $(this).parent().parent().parent();
        if($("#aula").length != 0)
            var aula = $("#aula").val();
        else if(div.hasClass('intercambio1'))
            var aula = $("#aula-intercambio1").val();
        else if(div.hasClass('intercambio2'))
            var aula = $("#aula-intercambio2").val();
        else{
            div = $(this).parent();
            var aula = div.attr('data-aula');
        }
        var dia = div.attr("data-dia");
        var hora = div.attr("data-hora");
        var dataString = 'op=moreInfo&aula='+aula+'&dia='+dia+'&hora='+hora;
        $.ajax({
            type: "GET",
            url: './administracionHorario.php',
            data: dataString,
            success: function(data){
                bootbox.alert(data);
            }
        });
    });
});

function limpiarMain(){
    $('#main-content').html('');
}

function addFiltro(){
    $('<div/>',{
            id: "filtro"
        }).appendTo('#main-content');
}

function addContent(){
    $('<div/>',{
            id: "contenido"
        }).appendTo('#main-content');
}

function generarHorario(){
    setTimeout(function(){
        $.ajax({
            type: "GET",
            url: "./administracionHorario.php",
            data: "op=generar",
            success: function(data){
                var msj = jQuery.parseJSON(data);
                if(msj !== 0){
                    limpiarMain();
                    bootbox.alert(msj,function(){
                        window.location.href = 'index.php';
                    });
                } else{
                    limpiarMain();
                    addFiltro();
                    addContent();
                    $('#filtro').load("./formularioFiltro.php");
                }
            }
        });
    },1000);
}

function dibujarHorario(dataString){
    $.ajax({
        type: "GET",
        url: "./mallaHorario.php",
        data: dataString,
        success: function(data){                
            $('#contenido').html(data);
            $('.verInfoGrupo').popover({
                title : "Informacion del Grupo",
                animation : true,
                trigger : 'click',  //Se muestra el popover al pasar el puntero sobre la celda. valores que acepta: hover,manual,click,focus                    
                html : true
            });
        },
        error: function(data){
            bootbox.alert("error: "+data,function(){});
        }
    });
}   
    
function mostrarAreaIntercambio1(aula){    
    var dataString = 'aula='+aula;
    $.ajax({
        type: "GET",
        url: "./areaIntercambio1.php",
        data: dataString,
        success: function(data){
            $('#antes-intercambio').html(data);
        }
    });
}

function mostrarAreaIntercambio2(aula){
    var dataString = 'aula='+aula;
    $.ajax({
        type: "GET",
        url: "./areaIntercambio2.php",
        data: dataString,
        success: function(data){
            $('#despues-intercambio').html(data);
        }
    });    
}

function seleccionarCeldas(area,celda){
    if(area === "Intercambio1"){
        diaSeleccionado = diaSeleccionado1;
        horaInicioSeleccionada = horaInicioSeleccionada1;
        horaFinSeleccionada = horaFinSeleccionada1;
    } else{
        diaSeleccionado = diaSeleccionado2;
        horaInicioSeleccionada = horaInicioSeleccionada2;
        horaFinSeleccionada = horaFinSeleccionada2;
    }
    if(diaSeleccionado===""){ //Si aun no se ha elegido ningún día
        celda.css("background","#9CEEE6");
        asignarDiasHoras(celda.attr("data-dia"),celda.attr("data-hora"),celda.attr("data-hora"),area);
    }else{ //Si ya se había elegido alguna celda en algún día
        if(diaSeleccionado===celda.attr("data-dia")){ //Si se desa elegir una celda del mismo día anterior            
            asignarDiasHoras(diaSeleccionado,horaInicioSeleccionada,horaFinSeleccionada,area);
            horaActual = celda.attr("data-hora");
            if(esUnaCeldaSeleccionada(horaActual,horaInicioSeleccionada,horaFinSeleccionada)){ //Si se selecciona una celda que ya había sido seleccionada                
                asignarDiasHoras("","","",area);
            }else if(horaFinSeleccionada > horaActual){ //Cuando se selecciona de abajo hacia arriba                
                asignarDiasHoras(diaSeleccionado,celda.attr("data-hora"),horaFinSeleccionada,area);
            }else if(horaFinSeleccionada < horaActual){ //Cuando se selecciona de arriba hacia abajo    
                asignarDiasHoras(diaSeleccionado,horaInicioSeleccionada,celda.attr("data-hora"),area);
            } 
        }else{ //Si se elige una celda en un día distinto al anterior                          
           asignarDiasHoras(celda.attr("data-dia"),celda.attr("data-hora"),celda.attr("data-hora"),area);
           celda.css("background","#9CEEE6");               
           $(".grupoSeleccionado"+area).css("background","");
           $(".grupoSeleccionado"+area).removeClass("grupoSeleccionado"+area).addClass("grupo"+area); 
        }
    }    
}

function resetearDiasHoras(){
    resetearDiasHorasArea("Intercambio1");
    resetearDiasHorasArea("Intercambio2");
}

function resetearDiasHorasArea(area){
    if(area=="Intercambio1"){
        diaAntes="";
        inicioAntes="";
        finAntes="";
        diaSeleccionado1="";
        horaInicioSeleccionada1="";
        horaFinSeleccionada1="";
    } else{
        diaDespues="";
        inicioDespues="";
        finDespues="";
        diaSeleccionado2="";
        horaInicioSeleccionada2="";
        horaFinSeleccionada2="";
    }
}

function asignarDiasHoras(dia,horaInicio,horaFin,area){    
    if(area === "Intercambio1"){
        diaSeleccionado1=dia;
        horaInicioSeleccionada1=parseInt(horaInicio);
        horaFinSeleccionada1=parseInt(horaFin);
    } else{
        diaSeleccionado2=dia;
        horaInicioSeleccionada2=parseInt(horaInicio);
        horaFinSeleccionada2=parseInt(horaFin);
    }
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
        for(i=horaInicio; i<=horaFin; i++){
            $("."+div+dia+i).css("background","#9CEEE6"); 
        }
    }    
}

function segundaFaseIntercambio(aula1,dia1,desde1,hasta1,aula2,dia2,desde2,hasta2){
    var dataString = 'op=intercambio2&aula1='+aula1+'&aula2='+aula2+'&dia1='+dia1+'&dia2='+dia2+'&desde1='+desde1+"&desde2="+desde2+'&hasta1='+hasta1+'&hasta2='+hasta2;
    $.ajax({
        type: "GET",
        url: './administracionHorario.php',
        data: dataString,
        success: function(data){
            var msj = jQuery.parseJSON(data);
            if(msj !== 0){
                bootbox.confirm(msj,function(resultado){
                    if(resultado===true){
                        realizarIntercambio(aula1,dia1,desde1,hasta1,aula2,dia2,desde2,hasta2);
                    }
                });
            } else{
                actualizarIntercambio(aula1,aula2);
            }
        }
    });
}

function realizarIntercambio(aula1,dia1,desde1,hasta1,aula2,dia2,desde2,hasta2){
    var dataString = 'op=intercambio3&aula1='+aula1+'&aula2='+aula2+'&dia1='+dia1+'&dia2='+dia2+'&desde1='+desde1+"&desde2="+desde2+'&hasta1='+hasta1+'&hasta2='+hasta2;
    $.ajax({
        type: "GET",
        url: './administracionHorario.php',
        data: dataString,
        success: function(data){
            var msj = jQuery.parseJSON(data);
            if(msj !== 0){
                bootbox.alert(msj);
            } else{
                actualizarIntercambio(aula1,aula2);
            }
        }
    });
}

function actualizarIntercambio(aula1,aula2){
    if($("#search").length){
        mostrarAreaIntercambio1(aula2);
        resetearDiasHoras();
        $("div.row:eq(3)").html('');
        bootbox.alert("Intercambio realizado");
    }else{
        mostrarAreaIntercambio1(aula1);
        mostrarAreaIntercambio2(aula2);
        resetearDiasHoras();
    }
}

function paginacion(numPags){
    $('#page-selection').bootpag({
        total: numPags,
        page: 1
    }).on("page", function(event, num){
        var dataString="op=page&pagina="+num;
        $.ajax({
            type: "GET",
            url: './paginarIntercambios.php',
            data: dataString,
            success: function(data){
                var msj = jQuery.parseJSON(data);
                $("#contentResul").html(msj);
            }
        });
    });
    $('#page-selection').trigger('page', 1);
}

function paginarBusqueda(){
    var dataString = "op=calcular";
    $.ajax({
        type: "GET",
        url: './paginarIntercambios.php',
        data: dataString,
        success: function(data){
            var msj = jQuery.parseJSON(data);
            paginacion(msj);
        }
    });
}

function obtenerIdHora(valor,tipo){
    var dataString = "op=id&tipo="+tipo+"&valor="+valor;
    return $.ajax({
        type: "GET",
        url: './administracionHorario.php',
        data: dataString
    });
}

function construirHorario(anio,ciclo){
    setTimeout(function(){
        var dataString = "op=construir&anio="+anio+"&ciclo="+ciclo;
        $.ajax({
            type: "GET",
            url: "open.php",
            data: dataString,
            success: function(datos){
                datos = jQuery.parseJSON(datos);            
                if(datos === 0)
                    bootbox.alert("Horario cargado",function(){
                        window.location.href = 'index.php';
                    });
            },
            error: function(datos){
                bootbox.alert("error: "+datos,function(){});
            }
        });
    },1000);
}