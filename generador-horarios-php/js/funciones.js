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
                            $('#contenido').load("./interfaz/cargando.php");
                            generarHorario();
                        }else{
                            $('#filtro').load("./interfaz/formularioFiltro.php");
                        }
                    });
                }else{
                    $('#contenido').load("./interfaz/cargando.php");
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
        if(departamento==='todos'){
            bootbox.alert("¡Debe seleccionar un departamento para filtrar!",function(){});
        }else{
            var carrera = $('#carrera').val(); 
            var materia = $('#materia').val();
            var dataString = "departamento="+departamento+"&carrera="+carrera+"&materia="+materia;     
            dibujarHorario(dataString);        
        }        
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
        dataString = dataString+"&carrera="+$('#carrera').val();
        $.ajax({            
            type: "GET",
            url: "./interfaz/materias.php",
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
                url: "./interfaz/materias.php",
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
        $('#frame-antes').attr("src","./interfaz/bodyIFrames.php?"+dataString);
    });
    
    $(document).on("change","#aula-intercambio2",function(){
        var aula = $("#aula-intercambio2").val();
        var dataString = 'aula='+aula;
        $('#frame-despues').attr("src","./interfaz/bodyIFrames.php?"+dataString);
    });
    
    $(document).on("click","#intercambiarHoras",function(){
        var aula1 = $('#aula-intercambio1').val();
        var aula2 = $('#aula-intercambio2').val();
        var dia1 = $('#dia-intercambio1').val();
        var dia2 = $('#dia-intercambio2').val();
        var desde1 = $('#desde-intercambio1').val();
        var desde2 = $('#desde-intercambio2').val();
        var hasta1 = $('#hasta-intercambio1').val();
        var hasta2 = $('#hasta-intercambio2').val();
        var dataString = 'op=intercambio&aula1='+aula1+'&aula2='+aula2+'&dia1='+dia1+'&dia2='+dia2+'&desde1='+desde1+"&desde2="+desde2+'&hasta1='+hasta1+'&hasta2='+hasta2;
        $.ajax({
            type: "GET",
            url: './interfaz/ManejadorInterfaz.php',
            data: dataString,
            success: function(data){
                var retorno = data.toString();
                if(retorno.search("continuar?") != -1){
                    if(confirm(data)){
                        intercambiarConfirm(aula1,dia1,desde1,aula2,dia2,desde2);
                    }
                } else if(retorno == 'confirmacion')
                    alert("Exito");
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
            url: "./interfaz/ManejadorInterfaz.php",
            data: "op=generar",
            success: function(data){
                $('#contenido').html(data);
                $('#filtro').load("./interfaz/formularioFiltro.php");
            }
        });
    },1000);
}

function intercambiarConfirm(aula1,dia1,desde1,aula2,dia2,desde2){
    var dataString = 'op=confirm&aula1='+aula1+'&aula2='+aula2+'&dia1='+dia1+'&dia2='+dia2+'&desde1='+desde1+"&desde2="+desde2;
    $.ajax({
            type: "GET",
            url: './interfaz/ManejadorInterfaz.php',
            data: dataString,
            success: function(data){
                alert(data);
            }
    });
}

function dibujarHorario(dataString){         
    $.ajax({
        type: "GET",
        url: "./interfaz/mallaHorario.php",
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
