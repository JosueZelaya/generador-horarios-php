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
                    "<div id='"+ui.item.id+"' class='docente_arrastrable'><div style='display:inline-block; height:50px;' class='btn btn-default btn-lg btn-info' docente='"+ui.item.value+"'><p>"+ui.item.value+"</p></div></div>"
                );
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
                grupos = new Object();
                indice=0;
                var nombreAgrupacion = ui.item.label;
                var codMateria = ui.item.cod_materia;
                var id_agrupacion = ui.item.id;               
                $("#mostrar_grupos").html("<h3>"+nombreAgrupacion+" codigo: "+codMateria+"</h3><br/>");
                var dataString = "agrupacion="+id_agrupacion;
                $.ajax({
                    type: "GET",
                    url: "buscarGrupos.php",
                    data: dataString,
                    success: function(data){
                        var respuesta = jQuery.parseJSON(data);                                              
                        var numGrupos = respuesta.length;
                        numGrupos = parseInt(numGrupos);                        
                        for(i=0;i<numGrupos;i++){
                            var grupo = new Object();                            
                            grupo["agrupacion"] = respuesta[i].agrupacion;
                            grupo["id"] = respuesta[i].id;
                            if(respuesta[i].tipo==='1'){                                
                                grupo["tipo"] = 'teorico';
                            }else if(respuesta[i].tipo==='2'){
                                grupo["tipo"] = 'laboratorio';
                            }else{
                                grupo["tipo"] = 'discusion';
                            }                            
                            if(respuesta[i].docentes===""){
                                grupo["docentes"] = "";
                                grupo["nombre_docentes"] = "";
                                grupo["numDocentes"] = 0; 
                            }else{
                                var numDocentes = parseInt(respuesta[i].docentes.length);                                                        
                                grupo["docentes"] = [];
                                grupo["nombre_docentes"] = [];
                                grupo["numDocentes"] = numDocentes;                            
                                for(j=0;j<numDocentes;j++){                                
                                    grupo["docentes"][j] = respuesta[i].id_docentes[j];                                
                                    grupo["nombre_docentes"][j] = respuesta[i].docentes[j];                                
                                }
                            }                            
                            grupos[i+1]=grupo;                                  
                        }
                        mostrarGrupos(grupos,numGrupos);
                    }
                });
            }
       });
   });
    
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
    
    $(document).on("click",".quitarDocente",function(){
        quitarDocente($(this).attr('indiceDocente'),$(this).attr('docente'));
        $(".g"+$(this).attr('indiceDocente')+"d"+$(this).attr('docente')).hide();
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
                var ultimoDocente = grupos[indice]["docentes"].length;                    
                grupo["docentes"] = grupos[indice]["docentes"];
                grupo["docentes"][ultimoDocente] = ui.draggable.attr("id");                
            }else{
                grupo["docentes"] = [];
                grupo["docentes"][0] = ui.draggable.attr("id");
            }            
            grupos[indice] = grupo;                               
            var btn_quitarDocente = "<div style='display:inline-block; height:50px;' class='btn btn-default btn-lg btn-danger col-lg-1 center quitarDocente g"+indice+"d"+ui.draggable.attr("id")+"' indiceDocente='"+indice+"' docente='"+ui.draggable.attr("id")+"'><span class='glyphicon glyphicon-remove-circle center'></div>";
            $(this).html($(this).html()+"<div class='btn-info col-lg-11 g"+indice+"d"+ui.draggable.attr("id")+"'>"+inventor+"</div>"+btn_quitarDocente);                                            

        }
    });
    
}


function quitarDocente(grupo,docente){
    var docentes = [];
    var ultimoDocente = grupos[grupo]["docentes"].length;       
    var cont=0;
    for(i=0;i<ultimoDocente;i++){        
        if(grupos[grupo]["docentes"][i]!==docente){            
            docentes[cont] = grupos[grupo]["docentes"][i];
            cont++;
        }
    }
    grupos[grupo]["docentes"] = docentes;
}

function mostrarDocentes(docentes){    
    var docentes_mostrar = "";
    for(i=0;i<docentes.length;i++){
        docentes_mostrar = docentes_mostrar+" "+docentes[i];        
    }
    alert("Docentes: "+docentes_mostrar);
}

function mostrarGrupos(grupos,cantidad){    
    var respuesta="";      
    for(i=1;i<=cantidad;i++){
        if(grupos[i]["tipo"]==='teorico'){
            respuesta = respuesta+"<div id='g"+grupos[i]["id"]+"' numGrupo='"+grupos[i]["id"]+"' grupo='"+i+"' tipo='teorico' agrupacion='"+grupos[i]["agrupacion"]+"' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: rgb(217, 237, 247);color: rgb(0, 136, 204);' class='grupo grupoTeorico row'>Grupo Teórico "+grupos[i]["id"]+"<br/>";
            var numDocentes = parseInt(grupos[i]["numDocentes"]);   
            if(numDocentes>0){
                for(j=0;j<numDocentes;j++){
                    var btn_quitarDocente = "<div style='display:inline-block; height:50px;' class='btn btn-default btn-lg btn-danger col-lg-1 center quitarDocente g"+i+"d"+grupos[i]["docentes"][j]+"' indiceDocente='"+i+"' docente='"+grupos[i]["docentes"][j]+"'><span class='glyphicon glyphicon-remove-circle center'></div>";
                    var div = "<div style='display:inline-block; height:50px;' class='btn btn-default btn-lg btn-info' docente='"+grupos[i]["nombre_docentes"][j]+"'><p>"+grupos[i]["nombre_docentes"][j]+"</p></div>";
                    respuesta = respuesta+"<div class='btn-info col-lg-11 g"+i+"d"+grupos[i]["docentes"][j]+"'>"+div+"</div>"+btn_quitarDocente;
                }
            }            
            respuesta = respuesta+"</div><br/>";
        }else if(grupos[i]["tipo"]==='laboratorio'){
            respuesta = respuesta+"<div id='gl"+grupos[i]["id"]+"' numGrupo='"+grupos[i]["id"]+"' grupo='"+i+"' tipo='laboratorio' agrupacion='"+grupos[i]["agrupacion"]+"' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: #90EE90; color: rgb(0, 136, 0);' class='grupo grupoLaboratorio row'>Grupo Laboratorio "+grupos[i]["id"]+"<br/>";
            var numDocentes = parseInt(grupos[i]["numDocentes"]);            
            if(numDocentes>0){
                for(j=0;j<numDocentes;j++){
                    var btn_quitarDocente = "<div style='display:inline-block; height:50px;' class='btn btn-default btn-lg btn-danger col-lg-1 center quitarDocente g"+i+"d"+grupos[i]["docentes"][j]+"' indiceDocente='"+i+"' docente='"+grupos[i]["docentes"][j]+"'><span class='glyphicon glyphicon-remove-circle center'></div>";
                    var div = "<div style='display:inline-block; height:50px;' class='btn btn-default btn-lg btn-info' docente='"+grupos[i]["nombre_docentes"][j]+"'><p>"+grupos[i]["nombre_docentes"][j]+"</p></div>";
                    respuesta = respuesta+"<div class='btn-info col-lg-11 g"+i+"d"+grupos[i]["docentes"][j]+"'>"+div+"</div>"+btn_quitarDocente;
                }
            }            
            respuesta = respuesta+"</div><br/>";
        }else{
            respuesta = respuesta+"<div id='gd"+grupos[i]["id"]+"' numGrupo='"+grupos[i]["id"]+"' grupo='"+i+"' tipo='discusion' agrupacion='"+grupos[i]["agrupacion"]+"' style='padding: 8px 35px 8px 14px; border: 1px solid rgb(188, 232, 241); color: rgb(58, 135, 173); background-color: #B0C4DE; color: rgb(0, 0, 204);' class='grupo grupoDiscusion row'>Grupo Discusion "+grupos[i]["id"]+"<br/>";
            var numDocentes = parseInt(grupos[i]["numDocentes"]);  
            if(numDocentes>0){
                for(j=0;j<numDocentes;j++){
                    var btn_quitarDocente = "<div style='display:inline-block; height:50px;' class='btn btn-default btn-lg btn-danger col-lg-1 center quitarDocente g"+i+"d"+grupos[i]["docentes"][j]+"' indiceDocente='"+i+"' docente='"+grupos[i]["docentes"][j]+"'><span class='glyphicon glyphicon-remove-circle center'></div>";
                    var div = "<div style='display:inline-block; height:50px;' class='btn btn-default btn-lg btn-info' docente='"+grupos[i]["nombre_docentes"][j]+"'><p>"+grupos[i]["nombre_docentes"][j]+"</p></div>";
                    respuesta = respuesta+"<div class='btn-info col-lg-11 g"+i+"d"+grupos[i]["docentes"][j]+"'>"+div+"</div>"+btn_quitarDocente;
                }
            }            
            respuesta = respuesta+"</div><br/>";
        }
    }
    $("#mostrar_grupos").html($("#mostrar_grupos").html()+respuesta);
}
     
});