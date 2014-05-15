$(function (){
    $(document).on("click","#generarHorario",function(){
        limpiarMain();
        addFiltro();
        addContent();
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
    });
   
    $(document).on("click","#mostrarHorario",function(){
        var dataString;
        var aula = $("#aula").val();
        var departamento = $('#departamento').val();       
        var carrera = $('#carrera').val();
        dataString = 'aula='+aula+"&departamento="+departamento+"&carrera="+carrera;      

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
                alert("error: "+data);
            }
        });
    });
    
    $(document).on("click","#mostrarHorarioDepartamento",function(){
        var aula = $("#aulaDepartamento").val();
        var departamento = $('#departamento').val();       
        var carrera = $('#carrera').val();       
        if(departamento!=='todos'){
            var dataString;       
            dataString = 'aula='+aula+"&departamento="+departamento+"&carrera="+carrera;      
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
                    alert("error: "+data);
                }
            });
        }else{
            alert("¡Debe seleccionar un departamento para filtrar!");
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
    });
    
    $(document).on("change","#carrera",function(){
        var dataString = 'carrera='+$(this).val()+"&departamento="+$('#departamento').val();              
        $.ajax({            
            type: "GET",
            url: "./interfaz/aulasDepartamento.php",
            data: dataString,
            success: function(data){                
                $('#aulaDepartamento').html(data);
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
            url: "./interfaz/areaIntercambio.php",
            success: function(data){
                $('#contenido').html(data);
                var aula = $("#aula-intercambio1").val();
                var dataString = 'aula='+aula;
                $('#frame-antes').attr("src","./interfaz/bodyIFrames.php?"+dataString);
                $('#frame-despues').attr("src","./interfaz/bodyIFrames.php?"+dataString);
            }
        });
    });
    
    $(document).on("click","#guardarHorario",function(){
        $.ajax({
            type: "GET",
            url: "./reglas_negocio/save.php",
            success: function(data){
                alert("Horario guardado");
            }
        });
    });
    
    $(document).on("click","#abrirHorario",function(){
        limpiarMain();
        $.ajax({
            type: "GET",
            url: "./reglas_negocio/open.php",
            success: function(data){
                alert("Horario cargado");
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