$(function (){
    $(document).on("click","#generarHorario",function(){
        limpiarMain();
        addFiltro();
        addContent();        
        $.ajax({            
            type: "GET",
            url: "./interfaz/hayHorarioGenerado.php",
            success: function(datos){
                datos = jQuery.parseJSON(datos);
                if(datos==="si"){
                    var mensaje = "¡Ya hay un horario generado!<br/>\n\
                                    ¿Realmente desea generar uno nuevo?";
                    bootbox.confirm(mensaje,function(resultado){
                        if(resultado===true){
                            generarHorario();
                        }else{
                            $('#filtro').load("./interfaz/formularioFiltro.php");
                        }
                    });
                }else{
                    generarHorario();
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
        var aula = $("#aulaDepartamento").val();
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
        var carrera = $('#carrera').val(); 
        var materia = $('#materia').val();
        var dataString = "departamento="+departamento+"&carrera="+carrera+"&materia="+materia;     
        dibujarHorario(dataString);        
    });
         
    $(document).on("change","#departamento",function(){
        var dataString = 'departamento='+$(this).val();      
        $.ajax({            
            type: "GET",
            url: "./interfaz/carreras.php",
            data: dataString,
            success: function(data){                
                $('#carrera').html(data);
            }
        });
        $.ajax({            
            type: "GET",
            url: "./interfaz/aulasDepartamento.php",
            data: dataString,
            success: function(data){                
                $('#aulaDepartamento').html(data);
            }
        });
    });
    
    $(document).on("change","#carrera",function(){
        var dataString = 'carrera='+$(this).val()+"&departamento="+$('#departamento').val();              
        if($(this).attr('data-tipo')==='materia'){
            $.ajax({            
                type: "GET",
                url: "./interfaz/materiasCarrera.php",
                data: dataString,
                success: function(data){                
                    $('#materia').html(data);
                }
            });
        }else{
            $.ajax({            
                type: "GET",
                url: "./interfaz/aulasDepartamento.php",
                data: dataString,
                success: function(data){                
                    $('#aulaDepartamento').html(data);
                }
            });            
        }        
    });
    
    $(document).on("click","#filtroMateria",function(){        
       var dataString = 'criterio=materia';
       $.ajax({            
            type: "GET",
            url: "./interfaz/formularioFiltro.php",            
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
            url: "./interfaz/formularioFiltro.php",            
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
                url: "./interfaz/formularioFiltro.php",            
                data: dataString,
                success: function(data){                                
                    $('#filtro').html(data);
                    $('#contenido').html("");
                }
            });
    });
   
    $(document).on("click","#intercambioHorario",function(){
        limpiarMain();
        addContent();         
        $.ajax({            
            type: "GET",
            url: "./interfaz/hayHorarioGenerado.php",
            success: function(datos){
                datos = jQuery.parseJSON(datos);            
                if(datos==="si"){
                    $.ajax({
                        type: "GET",
                        url: "./interfaz/areaIntercambio.php",
                        success: function(data){
                            $('#contenido').html(data);
                            var aula = $("#aula-intercambio1").val();
                            var dataString = 'aula='+aula;
                            $('#frame-antes').attr("src","./interfaz/bodyIFrames.php?"+dataString);
                            $('#frame-despues').attr("src","./interfaz/bodyIFrames.php?"+dataString);
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
        $.ajax({
            type: "GET",
            url: "./reglas_negocio/save.php",
            success: function(datos){
                datos = jQuery.parseJSON(datos);            
                if(datos==="exito"){
                    bootbox.alert("¡Horario guardado!",function(){});
                }else{
                    bootbox.alert("¡Error al guardar el horario!",function(){});
                } 
            },
            error: function(datos){
                bootbox.alert("error"+datos,function(){});
            }
        });
    });
    
    $(document).on("click","#abrirHorario",function(){
        limpiarMain();
        $.ajax({
            type: "GET",
            url: "./reglas_negocio/open.php",
            success: function(datos){
                datos = jQuery.parseJSON(datos);            
                if(datos==="exito"){
                    bootbox.alert("¡Horario cargado!",function(){});
                }else{
                    bootbox.alert("¡Error al cargar el horario!",function(){});
                }                
            },
            error: function(datos){
                bootbox.alert("error"+datos,function(){});
            }
        });
    });
    
    $(document).on("change","#aula-intercambio1",function(){
        var aula = $("#aula-intercambio1").val();
        var dataString = 'aula='+aula;
        $('#frame-antes').attr("src","/interfaz/bodyIFrames.php?"+dataString);
    });
    
    $(document).on("change","#aula-intercambio2",function(){
        var aula = $("#aula-intercambio2").val();
        var dataString = 'aula='+aula;
        $('#frame-despues').attr("src","/interfaz/bodyIFrames.php?"+dataString);
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

function dibujarHorario(dataString){         
    $.ajax({
        type: "GET",
        url: "./interfaz/dibujarHorario.php",
        data: dataString,
        success: function(data){                
            $('#contenido').html(data);
            $('.verInfoGrupo').popover({
                title : "Informacion del Grupo",
                animation : true,
                trigger : 'hover',  //Se muestra el popover al pasar el puntero sobre la celda. valores que acepta: hover,manual,click,focus                    
                html : true
            });
        },
        error: function(data){
            bootbox.alert("error: "+data,function(){});
        }
    });
}

function generarHorario(){
    $('#contenido').load("./interfaz/cargando.php",function(){
        $.ajax({
            type: "GET",
            url: "./interfaz/generarHorario.php",
            success: function(data){
                $('#contenido').html(data);
                $('#filtro').load("./interfaz/formularioFiltro.php");
            }
        });
    });
}
