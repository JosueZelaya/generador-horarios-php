$(document).ready(function() {
    var c = {};
    var indice=0;
    var grupos = new Object();    
    
    $(document).on("keydown.autocomplete","#buscar_docente",function(){        
        $(this).autocomplete({
            delay: 0,
            source : 'buscarDocente.php',
            select: function(event,ui){
                $('#mostrar_docente').html(
                    "<div id='"+ui.item.id+"' class='docente_arrastrable'><div class='btn btn-default btn-lg btn-info' height='10px'><p>"+ui.item.value+"</p></div></div>"
                );
            }
        });
    });
    
    $.widget("custom.catcomplete", $.ui.autocomplete, {
        _create: function() {
            this._super();
            this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
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
                $("#mostrar_grupos").html("");                                                    
                grupos = new Object();
                indice=1;
                var numGrupos = parseInt(ui.item.num_grupos);       //GRUPOS TEÓRICOS               
                var numGruposL = parseInt(ui.item.num_grupos_l);    //GRUPOS DE LABORATORIO                
                var numGruposD = parseInt(ui.item.num_grupos_d);    //GRUPOS DE DISCUCIÓN   
                var codMateria = ui.item.cod_materia;
                var nombreAgrupacion = ui.item.label;
                
                $("#mostrar_grupos").html("<h3>"+nombreAgrupacion+" codigo: "+codMateria+"</h3>");
                
                for(i=1;i<=numGrupos;i++){
                    var grupo = new Object();
                    grupo["docentes"] = "";
                    grupo["agrupacion"] = ui.item.id;
                    grupo["id"] = i;
                    grupo["tipo"] = 'teorico';
                    grupos[indice]=grupo;
                    $("#mostrar_grupos").html($("#mostrar_grupos").html()+
                        "<div id='g"+i+"' numGrupo='"+i+"' grupo='"+indice+"' tipo='teorico' agrupacion='"+ui.item.id+"' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: rgb(217, 237, 247);color: rgb(0, 136, 204);' class='grupo grupoTeorico'>Grupo Teórico "+i+"</div><br/>"                        
                    );                    
                    indice++;
                }                   
                for(i=1;i<=numGruposL;i++){
                    var grupo = new Object();
                    grupo["docentes"] = "";
                    grupo["agrupacion"] = ui.item.id;
                    grupo["id"] = i;
                    grupo["tipo"] = 'laboratorio';
                    grupos[indice]=grupo;
                    $("#mostrar_grupos").html($("#mostrar_grupos").html()+
                        "<div id='gl"+i+"' numGrupo='"+i+"' grupo='"+indice+"' tipo='laboratorio' agrupacion='"+ui.item.id+"' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: #90EE90; color: rgb(0, 136, 0);' class='grupo grupoLaboratorio'>Grupo Laboratorio "+i+"</div><br/>"                        
                    );                    
                    indice++;
                }                
                for(i=1;i<=numGruposD;i++){
                    var grupo = new Object();
                    grupo["docentes"] = "";
                    grupo["agrupacion"] = ui.item.id;
                    grupo["id"] = i;
                    grupo["tipo"] = 'discusion';
                    grupos[indice]=grupo;
                    $("#mostrar_grupos").html($("#mostrar_grupos").html()+
                        "<div id='gd"+i+"' numGrupo='"+i+"' grupo='"+indice+"' tipo='discusion' agrupacion='"+ui.item.id+"' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: #B0C4DE; color: rgb(0, 0, 204);' class='grupo grupoDiscusion'>Grupo Discusion "+i+"</div><br/>"                        
                    );
                    indice++;
                }                
            }
       }); 
    });
    
//    $(document).on("keydown.autocomplete","#buscar_agrupacion",function(){        
//        $(this).autocomplete({
//            delay: 0,
//            source : 'buscarAgrupacion.php',
//            select: function(event,ui){
//                $("#mostrar_grupos").html("");                                                    
//                grupos = new Object();
//                indice=1;
//                var numGrupos = parseInt(ui.item.num_grupos);       //GRUPOS TEÓRICOS               
//                var numGruposL = parseInt(ui.item.num_grupos_l);    //GRUPOS DE LABORATORIO                
//                var numGruposD = parseInt(ui.item.num_grupos_d);    //GRUPOS DE DISCUCIÓN                  
//                for(i=1;i<=numGrupos;i++){
//                    var grupo = new Object();
//                    grupo["docentes"] = "";
//                    grupo["agrupacion"] = ui.item.id;
//                    grupo["id"] = i;
//                    grupo["tipo"] = 'teorico';
//                    grupos[indice]=grupo;
//                    $("#mostrar_grupos").html($("#mostrar_grupos").html()+
//                        "<div id='g"+i+"' numGrupo='"+i+"' grupo='"+indice+"' tipo='teorico' agrupacion='"+ui.item.id+"' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: rgb(217, 237, 247);color: rgb(0, 136, 204);' class='grupo grupoTeorico'>Grupo Teórico "+i+"</div><br/>"                        
//                    );                    
//                    indice++;
//                }                   
//                for(i=1;i<=numGruposL;i++){
//                    var grupo = new Object();
//                    grupo["docentes"] = "";
//                    grupo["agrupacion"] = ui.item.id;
//                    grupo["id"] = i;
//                    grupo["tipo"] = 'laboratorio';
//                    grupos[indice]=grupo;
//                    $("#mostrar_grupos").html($("#mostrar_grupos").html()+
//                        "<div id='gl"+i+"' numGrupo='"+i+"' grupo='"+indice+"' tipo='laboratorio' agrupacion='"+ui.item.id+"' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: #90EE90; color: rgb(0, 136, 0);' class='grupo grupoLaboratorio'>Grupo Laboratorio "+i+"</div><br/>"                        
//                    );                    
//                    indice++;
//                }                
//                for(i=1;i<=numGruposD;i++){
//                    var grupo = new Object();
//                    grupo["docentes"] = "";
//                    grupo["agrupacion"] = ui.item.id;
//                    grupo["id"] = i;
//                    grupo["tipo"] = 'discusion';
//                    grupos[indice]=grupo;
//                    $("#mostrar_grupos").html($("#mostrar_grupos").html()+
//                        "<div id='gd"+i+"' numGrupo='"+i+"' grupo='"+indice+"' tipo='discusion' agrupacion='"+ui.item.id+"' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: #B0C4DE; color: rgb(0, 0, 204);' class='grupo grupoDiscusion'>Grupo Discusion "+i+"</div><br/>"                        
//                    );
//                    indice++;
//                }                
//            }
//        });
//    }); 
    
    $(document).on("mouseover.draggable",".docente_arrastrable",function(){        
        $(this).draggable({
            appendTo:'body',
            helper: "clone",
            start: function(event, ui) {
                c.tr = this;
                c.helper = ui.helper;
                $("#panelGrupos").removeClass("panel-success").addClass("panel-warning");
                $("#panelGrupos").removeClass("panel-danger").addClass("panel-warning");
                $("#cabeceraPanel").html("<p class='center'>Suelte los docentes sobre el grupo que impartirán</p>");
            }
        });
        activarDroppables();
    });
    
    $(document).on("click","#actualizar_grupos",function(){
        var gruposEnviar;        
        gruposEnviar = jQuery.makeArray(grupos);
        gruposEnviar = JSON.stringify(gruposEnviar);        
        var dataString = 'grupos='+gruposEnviar; 
        $.ajax({
            dataType: "json",
            type: "GET",
            url: "actualizarGrupos.php",
            data: dataString,
            success: function(data){       
                if(data==="exito"){
                    $("#panelGrupos").removeClass("panel-warning").addClass("panel-success");                    
                    $("#mostrar_grupos").html("");                    
                    $("#cabeceraPanel").html("Grupos actualizados!");
                    grupos = new Object();
                    indice=0;
                }else{
                    $("#panelGrupos").removeClass("panel-warning").addClass("panel-danger");                    
                    $("#cabeceraPanel").html(data);
                }                
                                
            }
        });
    });  
    
    
function activarDroppables(){
    
    $(".grupo").droppable({            
        tolerance: "touch",            
        drop: function(event, ui) { 
            var inventor = ui.draggable.html();           
            indice = $(this).attr('grupo');
            var tipoGrupo = $(this).attr('tipo');
            var grupo = new Object();
            grupo["agrupacion"] = $(this).attr('agrupacion');
            grupo["id"] = $(this).attr('numGrupo');                
            grupo["tipo"] = tipoGrupo;            
            if(grupos[indice]["docentes"]!==""){
                grupo["docentes"] = grupos[indice]["docentes"]+","+ui.draggable.attr("id");
            }else{
                grupo["docentes"] = ui.draggable.attr("id");
            }
            
            grupos[indice] = grupo;                    
            
            //alert("grupo: "+grupo["id"]+" docentes: "+grupos[indice]["docentes"]+" agrupacion: "+grupos[indice]["agrupacion"]);
            $(this).html($(this).html()+"<br/>"+inventor);                                
        }
    });
    
}    
     
});