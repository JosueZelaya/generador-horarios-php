$(document).ready(function() {
    var c = {};
    var aulas = "";
    var materias = "";
    
    $('table').footable({
        breakpoints: {
            tiny: 180,                
            phone: 256,
            medium: 512,
            tablet: 768,
            laptop: 1024
        }
    });
    
   $(document).on("keydown.autocomplete","#buscar_materia",function(){        
        $(this).autocomplete({
            delay: 0,
            source : 'buscarMateria.php', 
            select : function(event,ui){
                var dataString = "materia="+ui.item.value;
                $.ajax({
                    type: "GET",
                    url: "buscarMateria.php",
                    data: dataString,                    
                    success: function(data){                        
                        $('#contenido_materias').html(data).trigger('footable_redraw');                        
                    }
                });
                $("#paginacion").html(""); 
                
            }
        });
    });
    
    function split( val ) {
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }    
    
    
    $(document).on("click","#buscar_aulas",function(){
        $(this).bind("keydown",function(event){
            if(event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active){
                event.preventDefault();
            }    
        }).autocomplete({
            source: function(request,response){
                $.getJSON( "buscarAulas.php", {
                    term: extractLast( request.term )
                },response );
            },
            search: function() {
                // para que comience la busqueda a partir del primer caracter introducido
                var term = extractLast( this.value );
                if ( term.length < 1 ) {
                    return false;
                }
            },
            select: function( event, ui ) {
                var terms = split( this.value );
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push( ui.item.value );
                // add placeholder to get the comma-and-space at the end                
                terms.push( "" );
                this.value = terms.join(","); 
                aulas = this.value;                
                return false;
            }            
        });
    });        
    
    

    $(document).on("mouseover.draggable",".arrastrable",function(){
        $(this).draggable({            
            appendTo:'body',
            helper: "clone",
            start: function(event, ui) {                   
                $("#panelAulas").removeClass("panel-danger").addClass("panel-warning");
                $("#panelAulas").removeClass("panel-success").addClass("panel-warning");
                $("#cabeceraPanelAulas").html("<p class='center'>Suelte la materia en este panel</p>");
                c.tr = this;
                c.helper = ui.helper;
                c.agrupacion = $(this).attr("id-agrupacion");
            }
        });
    });
    
    $("#panelAulas").droppable({        
        tolerance: "touch",
        drop: function(event, ui) { 
            var inventor = ui.draggable.html();
            var id_agrupacion = c.agrupacion;
            if(materias!==""){
                materias = materias+","+id_agrupacion;
            }else{
                materias = +id_agrupacion;
            }                       
            $('#materias_arrastradas').html($('#materias_arrastradas').html()+"<tr>"+inventor+"<tr/>").trigger('footable_redraw');                        
            $(c.tr).remove();
            $(c.helper).remove();               
        }
    });
    
    $(document).on("click","#enviar",function(){
        var exclusiva = document.getElementById('exclusiva').checked;
        var gt = document.getElementById('teorico').checked;
        var gl = document.getElementById('laboratorio').checked;
        var gd = document.getElementById('discusion').checked;        
        var dataString = "materias="+materias+"&aulas="+aulas+"&exclusiva="+exclusiva+"&gt="+gt+"&gl="+gl+"&gd="+gd;
        $.ajax({
            dataType: "json",
            type: "GET",
            url: "asignarAulas.php",
            data: dataString,                    
            success: function(data){                  
                if(data==="ok"){
                    $("#panelAulas").removeClass("panel-danger").addClass("panel-success");
                    $("#panelAulas").removeClass("panel-warning").addClass("panel-success");
                    $("#cabeceraPanelAulas").html(" <p class='center center-block'>Se asignaron correctamente las aulas</p>");
                }else{
                    $("#panelAulas").removeClass("panel-success").addClass("panel-danger");
                    $("#panelAulas").removeClass("panel-warning").addClass("panel-danger");
                    $("#cabeceraPanelAulas").html(" <p class='center center-block'>"+data+"</p>");
                }
            }
        });
    });

});
