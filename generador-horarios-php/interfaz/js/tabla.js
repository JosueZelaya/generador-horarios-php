/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    //toggle `popup` / `inline` mode
    $.fn.editable.defaults.mode = 'popup';   
    
    $(document).on("keydown.autocomplete","#buscar_materia",function(){        
        $(this).autocomplete({
            source : 'buscarMateria.php',          
            select : function(event,ui){
                nuevos= ui.item.alumnos_nuevos;
                otros = ui.item.otros_alumnos;
                nuevos = parseInt(nuevos);
                otros = parseInt(otros);
                total = nuevos+otros;
                $('#mostrarMaterias').slideUp('fast',function(){
                   $('#mostrarMaterias').html(                    
                    "<tr>"+
                    "<td>1</td>"+
                    "<td>"+ui.item.value+"</td>"+
                    "<td>"+ui.item.ciclos+"</td>"+
                    "<td><div style='cursor: pointer;' href='#' id='alumnos_nuevos' class='campoModificable n"+ui.item.id+"' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='modificarAgrupacion.php' data-title='Alumnos Nuevos'>"+nuevos+"</div></td>"+
                    "<td><div style='cursor: pointer;' href='#' id='otros_alumnos' class='campoModificable o"+ui.item.id+"' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='modificarAgrupacion.php' data-title='Otros Alumnos'>"+otros+"</div></td>"+
                    "<td><div id='"+ui.item.id+"'>"+total+"</td>"+
                    "<td><div style='cursor: pointer;' href='#' id='num_grupos' class='campoModificable ng"+ui.item.id+"' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='modificarAgrupacion.php' data-title='Numero de Grupos'>"+ui.item.num_grupos+"</div></td>"+                    
                    "<td><div style='cursor: pointer;' href='#' id='alumnos_grupo' class='campoModificable na"+ui.item.id+"' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='modificarAgrupacion.php' data-title='Numero de Grupos'>"+ui.item.alumnos_grupo+"</div></td>"+                    
                    "</tr>"
                    );                                                            
                });                
                $('#mostrarMaterias').slideDown('fast');
                $("#paginacion").html("");
            }
        });
    });
    
    $(document).on("keydown.autocomplete","#buscar_agrupacion",function(){
        $(this).autocomplete({
            source : 'buscarAgrupacion.php'
        });
    });
    
    $(document).on("keydown.autocomplete","#buscar_materia_para_agrupar",function(){        
        $(this).autocomplete({
            source : 'buscarMateriaAgrupar.php',
            select : function(event,ui){
                $('#mostrarMaterias').html(ui.item.value);
                var dataString = "materia="+ui.item.value;
                $.ajax({
                    type: "GET",
                    url: "contenidoMateriasArrastrables.php",
                    data: dataString,
                    success: function(data){
                        $('#cabecera_materias_arrastrables').html(""+                        
                            "<tr>"+                        
                                "<th class='text-center'>Codigo</th>"+
                                "<th class='text-center'>Materia</th>"+
                                "<th class='text-center'>Carrera</th>"+
                                "<th class='text-center'>Plan Estudio</th>"+
                                "<th class='text-center'>Departamento</th>"+
                            "</tr>"                        
                        );
                        $('#contenido_materias_arrastrables').html(data);
                        $('#paginacion').load('paginadorMaterias.php');
                    }
                });
            }
        });
    });
           
    $(document).on("click",document,function(){
        $(".campoModificable").editable({
            success: function(response, newValue) {
                respuesta = jQuery.parseJSON(response);
                if(respuesta.status === 'error'){
                    return respuesta.msg; //msg will be shown in editable form
                }else if(respuesta.status === 'actualizar_nuevos'){
                    alumnosNuevos = newValue.toString();
                    otrosAlumnos = $('.o'+respuesta.msg).html().toString();
                    alumnosNuevos = parseInt(alumnosNuevos);
                    otrosAlumnos = parseInt(otrosAlumnos);                
                }else if(respuesta.status === 'actualizar_otros'){
                    alumnosNuevos = $('.n'+respuesta.msg).html().toString();
                    otrosAlumnos = newValue.toString();
                    alumnosNuevos = parseInt(alumnosNuevos);
                    otrosAlumnos = parseInt(otrosAlumnos);                
                }else if(respuesta.status === 'actualizar_alumnos_grupo'){
                    alumnosGrupo = newValue.toString();
                    totalAlumnos = $('#'+respuesta.msg).html().toString();
                    alumnosGrupo = parseInt(alumnosGrupo);
                    totalAlumnos = parseInt(totalAlumnos);
                    numGrupos = Math.ceil(totalAlumnos/alumnosGrupo);
                    $('#num_grupos').html(numGrupos);
                }
                if(respuesta.status !== 'ok' && respuesta.status !=='actualizar_alumnos_grupo'){
                    total = alumnosNuevos + otrosAlumnos;
                    $('#'+respuesta.msg).html(total);
                }                
            }
        });
    });
    
    $(document).on("click",".paginaMaterias",function(){        
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
        $.ajax({
            type: "GET",
            url: "contenidoTablaMaterias.php",
            data: dataString,
            success: function(data) {                
                $('#mostrarMaterias').fadeIn(1000).html(data);            
            }            
        });
        $.ajax({
            type: "GET",
            url: "paginadorMaterias.php",
            data: dataString,
            success: function(data){
                $('.pagination').fadeIn(1000).html(data);
            }
        });    
        
    }); 
    
    $(document).on("click",".paginaMateriasParaAgrupar",function(){        
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
    $.ajax({
        type: "GET",
        url: "contenidoMateriasArrastrables.php",
        data: dataString,
        success: function(data) {                
            $('#cabecera_materias_arrastrables').html(""+                        
                "<tr>"+                        
                    "<th class='text-center'>Codigo</th>"+
                    "<th class='text-center'>Materia</th>"+
                    "<th class='text-center'>Carrera</th>"+
                    "<th class='text-center'>Plan Estudio</th>"+
                    "<th class='text-center'>Departamento</th>"+
                "</tr>"                        
            );
            $('#contenido_materias_arrastrables').html(data);                        
        }            
    });
    $.ajax({
        type: "GET",
        url: "paginadorMaterias.php",
        data: dataString,
        success: function(data){
            $('.pagination').fadeIn(1000).html(data);
        }
    });    
        
    }); 

    var c = {};
    var materias = new Object();
    var indice=0;
    
    $(document).on("mouseover.draggable",".arrastrable",function(){
        $(this).draggable({
            helper: "clone",
            start: function(event, ui) {
                $("#panel").removeClass("panel-danger").addClass("panel-warning");
                $("#panel").removeClass("panel-success").addClass("panel-warning");
                $("#cabeceraPanel").html("<p class='center'>Suelte acá las materias que desea que formen parte de la agrupación</p>");
                c.tr = this;
                c.helper = ui.helper;
            }
        });
    });
    
    $("#panel").droppable({
//        over: function( event, ui ) {$(this)},
        tolerance: "pointer",
        drop: function(event, ui) { 
            var inventor = ui.draggable.html();            
            $("#agregarMateria").html("<tr>"+inventor+"</tr>"+$("#agregarMateria").html());
            $("#cabeceraTablaMateriasArrastradas").html("<th class='text-center'>Codigo</th>"+
                            "<th class='text-center'>Materia</th>"+
                            "<th class='text-center'>Carrera</th>"+
                            "<th class='text-center'>Plan Estudio</th> "+
                            "<th class='text-center'>Departamento</th>");
            var materia = new Object();
            materia["codigo"] = ui.draggable.attr("cod_materia");
            materia["carrera"] = ui.draggable.attr("cod_carrera");
            materia["departamento"] = ui.draggable.attr("id_depar");
            materia["plan_estudio"] = ui.draggable.attr("plan_estudio");
            materias[indice] = materia;
            indice++;
            $(c.tr).remove();
            $(c.helper).remove();   
            //imprimirArray(materias);
        }
    });
    
    $("#crearAgrupacion").click(function(){
        var materiasEnviar;        
        materiasEnviar = jQuery.makeArray(materias);
        materiasEnviar = JSON.stringify(materiasEnviar);        
        var dataString = 'materias='+materiasEnviar; 
        $.ajax({
            dataType: "json",
            type: "GET",
            url: "./agregarAgrupacion.php",
            data: dataString,
            success: function(data){       
                if(data==="exito"){
                    $("#panel").removeClass("panel-warning").addClass("panel-success");                    
                    $("#agregarMateria").html("");
                    $("#cabeceraTablaMateriasArrastradas").html("");
                    $("#cabeceraPanel").html("Agrupación Creada Exitosamente");
                    materias = new Object();
                    indice=0;
                }else{
                    $("#panel").removeClass("panel-warning").addClass("panel-danger");                    
                    $("#cabeceraPanel").html(data);
                }                
                                
            }
        });
        
        
    });
    
           
});

function imprimirArray(materias){
    var materiasMostrar = "";    
    
    for (var i in materias) {
        materiasMostrar = materiasMostrar+materias[i]["codigo"]+" "+materias[i]["carrera"]+" "+materias[i]["departamento"]+"\n";            
    }
   
    alert(materiasMostrar);
}
