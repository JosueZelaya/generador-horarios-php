$(document).ready(function() {
    
    var materias=[];
    var agrupacion="";
    
    $(document).on("keydown.autocomplete","#buscar_materia",function(){        
        $(this).autocomplete({
            delay: 0,
            source : 'buscarMateria.php',
            select : function(event,ui){                
                var dataString = "materia="+ui.item.value;
                $.ajax({
                    type: "GET",
                    url: "contenidoMaterias.php",
                    data: dataString,
                    success: function(data){
                        $('#cabecera_materias').html(""+                        
                            "<tr>"+                        
                                "<th class='text-center'>Codigo</th>"+
                                "<th class='text-center'>Materia</th>"+
                                "<th class='text-center'>Carrera</th>"+
                                "<th class='text-center'>Plan Estudio</th>"+
                                "<th class='text-center'>Departamento</th>"+
                            "</tr>"                        
                        );
                        $('#contenido_materias').html(data);
                        $('#paginacion').load('paginadorMaterias.php');
                    }
                });
            }
        });
    });
    
    $(document).on("click",".paginaMaterias",function(){        
        var page = $(this).attr('data');        
        var dataString = 'pagina='+page;          
        $.ajax({
            type: "GET",
            url: "contenidoMaterias.php",
            data: dataString,
            success: function(data) {                
                $('#cabecera_materias').html(""+                        
                    "<tr>"+                        
                        "<th class='text-center'>Codigo</th>"+
                        "<th class='text-center'>Materia</th>"+
                        "<th class='text-center'>Carrera</th>"+
                        "<th class='text-center'>Plan Estudio</th>"+
                        "<th class='text-center'>Departamento</th>"+
                    "</tr>"                        
                );
                $('#contenido_materias').html(data);                        
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
    
    $.widget("custom.catcomplete", $.ui.autocomplete, {
        _create: function() {
            this._super();
            this.widget().menu( "option", "items", "> :no<td></tr>t(.ui-autocomplete-category)" );
        },
        _renderMenu: function( ul, items ) {
            var that = this,
            currentCategory = "";
            $.each( items, function( index, item ) {
                var li;
                if ( item.category != currentCategory ) {
                    ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                    currentCategory = item.category;
                }
                li = that._renderItemData( ul, item );
                if ( item.category ) {
                    li.attr( "aria-label", item.category + " : " + item.label );
                }
            });
        }
    });
    
    $(document).on("keydown.catcomplete","#buscar_agrupacion",function(){        
       $(this).catcomplete({
            delay: 0,
            source : 'buscarAgrupacion.php',
            select: function(event,ui){
                materias = [];                
                var id_agrupacion = ui.item.id;
                agrupacion = id_agrupacion;
                var dataString = "agrupacion="+id_agrupacion;
                $.ajax({
                    type: "GET",
                    url: "buscarMateriasDeAgrupacion.php",
                    data: dataString,
                    success: function(respuesta){
                        var respuesta = jQuery.parseJSON(respuesta);
                        var numMaterias = respuesta.length;
                        numMaterias = parseInt(numMaterias);                        
                        for(i=0;i<numMaterias;i++){
                            var materia = new Object();
                            materia['index'] = i;
                            materia['codigo'] = respuesta[i].codigo;
                            materia['nombre'] = respuesta[i].nombre;
                            materia['carrera'] = respuesta[i].carrera;
                            materia['id_carrera'] = respuesta[i].id_carrera;
                            materia['plan_estudio'] = respuesta[i].plan_estudio;
                            materia['departamento'] = respuesta[i].departamento;
                            materia['agrupacion'] = respuesta[i].agrupacion;
                            materias[i]=materia;                                  
                        }
                        mostrarMaterias(materias);
                    }
                });
            }
       });
   });
   
   //ELIMINAR DOCENTES Y USUARIOS
    $('table').footable({
        breakpoints: {
            tiny: 180,                
            phone: 256,
            medium: 512,
            tablet: 768,
            laptop: 1024
        }
    }).on('click','.row-delete',function(e){         
         e.preventDefault();
        //get the footable object
        var footable = $('table').data('footable');
        //get the row we are wanting to delete
        var row = $(this).parents('tr:first');        
        var codigo = $(this).attr("codigo");
        var id_carrera = $(this).attr("id_carrera");
        var plan_estudios = $(this).attr("plan_estudio");
        var agrupacion = $(this).attr("agrupacion");
        materias = quitarMateria(codigo,id_carrera,plan_estudios,agrupacion,materias);
        footable.removeRow(row);
        
    });
    
    var c = {};
    $(document).on("mouseover.draggable",".arrastrable",function(){
        $(this).draggable({           
            appendTo:'body',
            helper: "clone",
            start: function(event, ui) {                   
                $("#panelAgrupaciones").removeClass("panel-danger").addClass("panel-warning");
                $("#panelAgrupaciones").removeClass("panel-success").addClass("panel-warning");
                $("#cabeceraPanel").html("<p class='center'>Suelte acá las materias que desea que formen parte de la agrupación</p>");
                c.tr = this;
                c.codigo = $(this).attr("cod_materia");
                c.carrera = $(this).attr("cod_carrera");
                c.plan = $(this).attr("plan_estudio");
                c.helper = ui.helper;
            }
        });
    });
    
    $("#panelAgrupaciones").droppable({        
        tolerance: "touch",
        drop: function(event, ui) { 
            if(materias.length>0){
                var inventor = ui.draggable.html();  
                var materia = new Object();            
                materia['codigo'] = c.codigo;                        
                materia['id_carrera'] = c.carrera;
                materia['plan_estudio'] = c.plan;            
                materia['agrupacion'] = c.agrupacion;
                materias[materias.length]=materia; 
                $("#cabecera_materias_arrastradas").html("<th class='text-center'>Codigo</th>"+
                                "<th class='text-center'>Materia</th>"+
                                "<th class='text-center'>Carrera</th>"+
                                "<th class='text-center'>Plan Estudio</th> "+
                                "<th class='text-center'>Departamento</th>"+
                                "<th class='text-center'>Eliminar</th>");
                $('#contenido_materias_arrastradas').html($('#contenido_materias_arrastradas').html()+"<tr>"+inventor+"<td align='center'><a agrupacion='"+agrupacion+"' codigo='"+c.codigo+"' id_carrera='"+c.carrera+"' plan_estudio='"+c.plan+"' id='' class='center centre-block row-delete'><span class='glyphicon glyphicon-remove'></span></a></td><tr/>");           
                $(c.tr).remove();
                $(c.helper).remove();            
            }else{
                $("#panelAgrupaciones").removeClass("panel-warning").addClass("panel-danger");
                $("#panelAgrupaciones").removeClass("panel-success").addClass("panel-danger");
                $("#cabeceraPanel").html("<p class='center'> Error: Primero debe elegir una agrupación para luego soltar las materias en ella</p>");
            }            
        }
    });
    
    $("#eliminar_agrupacion").click(function(){
        if(agrupacion===""){
            $("#panelAgrupaciones").removeClass("panel-warning").addClass("panel-danger");
                $("#panelAgrupaciones").removeClass("panel-success").addClass("panel-danger");
                $("#cabeceraPanel").html("<p class='center'> Error: Primero debe elegir una agrupación</p>");
        }else if(materias.length<=1){
                $("#panelAgrupaciones").removeClass("panel-success").addClass("panel-danger");
                $("#cabeceraPanel").html("<p class='center'> Error: Esta materia no está agrupada con ninguna otra.<br/>\n\
                                          <a href='../administrar_materias/eliminar/index.php'>¿Desea eliminar la materia?</a></p>");
        }else{
            var mensaje = "¿Realmente desea eliminar esta agrupación?<br/>Se eliminarán con ella todos sus datos y deberá volver a ingresarlos para cada materia";
            bootbox.confirm(mensaje, function(resultado) {
                if(resultado===true){
                    var dataString = "agrupacion="+agrupacion;
                    $.ajax({
                        dataType: "json",
                        type: "GET",
                        url: "./eliminarAgrupacion.php",
                        data: dataString,
                        success: function(data){
                            if(data==="ok"){
                                $("#panelAgrupaciones").removeClass("panel-danger").addClass("panel-success");
                                $("#panelAgrupaciones").removeClass("panel-warning").addClass("panel-success");                    
                                $("#contenido_materias_arrastradas").html("");
                                $("#cabecera_materias_arrastradas").html("");
                                $("#cabeceraPanel").html("Agrupación Eliminada");
                                materias = [];
                            }else{
                                $("#panelAgrupaciones").removeClass("panel-success").addClass("panel-danger");
                                $("#panelAgrupaciones").removeClass("panel-warning").addClass("panel-danger");
                                $("#cabeceraPanel").html(data);
                                materias = [];
                            }
                        }
                    });
                }
            });
        }
    });
    
    $("#actualizar_agrupacion").click(function(){
        var materiasEnviar;        
        materiasEnviar = jQuery.makeArray(materias);
        materiasEnviar = JSON.stringify(materiasEnviar);        
        var dataString = 'materias='+materiasEnviar+"&agrupacion="+agrupacion;
        $.ajax({
            dataType: "json",
            type: "GET",
            url: "./setMateriasDeAgrupacion.php",
            data: dataString,
            success: function(data){       
                if(data==="exito"){
                    $("#panelAgrupaciones").removeClass("panel-danger").addClass("panel-success");
                    $("#panelAgrupaciones").removeClass("panel-warning").addClass("panel-success");                    
                    $("#contenido_materias_arrastradas").html("");
                    $("#cabecera_materias_arrastradas").html("");
                    $("#cabeceraPanel").html("Agrupación Actualizada Exitosamente");
                    materias = [];                    
                }else{
                    $("#panelAgrupaciones").removeClass("panel-success").addClass("panel-danger");
                    $("#panelAgrupaciones").removeClass("panel-warning").addClass("panel-danger");
                    $("#contenido_materias_arrastradas").html("");
                    $("#cabecera_materias_arrastradas").html("");                    
                    $("#cabeceraPanel").html(data);
                    materias = [];                    
                }                
                agrupacion="";
            }
        });
        
        
    });
    
    function quitarMateria(codigo,id_carrera,plan_estudio,agrupacion,materias){        
//        alert("codigo: "+codigo+" carrera: "+id_carrera+" plan: "+plan_estudio+" agrupacion: "+agrupacion);
//          alert("cant mat en array: "+materias.length);          
          var nMaterias = [];            
          var cont=0;
          for(i=0;i<materias.length;i++){
              if(materias[i]["codigo"]===codigo && materias[i]["id_carrera"]===id_carrera && materias[i]["plan_estudio"]===plan_estudio){
//                  alert("codigo: "+codigo+" carrera: "+id_carrera+" plan: "+plan_estudio+" agrupacion: "+agrupacion);
              }else{
                  nMaterias[cont] = materias[i];                  
                  cont++;
              }              
          }                    
          materias = nMaterias;          
          return materias;
    }
   
   function mostrarMaterias(materias){
       $('#cabecera_materias_arrastradas').html(""+                        
                            ""+                        
                                "<th class='text-center'>Codigo</th>"+
                                "<th class='text-center'>Materia</th>"+
                                "<th class='text-center'>Carrera</th>"+
                                "<th class='text-center'>Plan Estudio</th>"+
                                "<th class='text-center'>Departamento</th>"+
                                "<th class='text-center'>Eliminar</th>"+
                            "");
       var materias_a_mostrar="";
       for(i=0;i<materias.length;i++){           
           materias_a_mostrar = materias_a_mostrar+"<tr><td>"+materias[i]["codigo"]+"</td><td>"+materias[i]["nombre"]+"</td><td>"+materias[i]["carrera"]+"</td><td>"+materias[i]["plan_estudio"]+"</td><td>"+materias[i]["departamento"]+"</td><td align='center'><a agrupacion='"+materias[i]["agrupacion"]+"' codigo='"+materias[i]["codigo"]+"' id_carrera='"+materias[i]["id_carrera"]+"' plan_estudio='"+materias[i]["plan_estudio"]+"' id='"+materias[i]["index"]+"' class='center centre-block row-delete'><span class='glyphicon glyphicon-remove'></span></a></td></tr>";           
       }       
       $("#contenido_materias_arrastradas").html(materias_a_mostrar);
   }
    
});
