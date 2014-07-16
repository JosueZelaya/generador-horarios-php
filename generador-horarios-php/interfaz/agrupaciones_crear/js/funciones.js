/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    //toggle `popup` / `inline` mode
    $.fn.editable.defaults.mode = 'popup';   
      
    $(document).on("keydown.autocomplete","#buscar_materia_para_agrupar",function(){        
        $(this).autocomplete({
            delay: 0,
            source : 'buscarMateriaAgrupar.php',
            select : function(event,ui){
                //$('#mostrarMaterias').html(ui.item.value);
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
            appendTo:'body',
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
        tolerance: "touch",
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
                    $("#agregarMateria").html("");
                    $("#cabeceraTablaMateriasArrastradas").html("");                    
                    materias = new Object();
                    indice=0;
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
