/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    //toggle `popup` / `inline` mode
    $.fn.editable.defaults.mode = 'popup';  
    
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
                var dataString = "agrupacion="+ui.item.value;                
                $.ajax({
                    type: "GET",
                    url: "contenidoTablaMaterias.php",
                    data: dataString,
                    success: function(data){
                        $('#mostrarMaterias').html(data).trigger('footable_redraw');                        
                    }
                });
                $("#paginacion").html(""); 
                
                
//                nuevos= ui.item.alumnos_nuevos;
//                otros = ui.item.otros_alumnos;
//                nuevos = parseInt(nuevos);
//                otros = parseInt(otros);
//                total = nuevos+otros;                
//                $('#mostrarMaterias').slideUp('fast',function(){
//                   $('#mostrarMaterias').html(                    
//                    "<tr>"+
//                    "<td>1</td>"+
//                    "<td>"+ui.item.value+"</td>"+
//                    "<td>"+ui.item.nombre_depar+"</td>"+
//                    "<td>"+ui.item.ciclos+"</td>"+
//                    "<td><div style='cursor: pointer;' href='#' id='alumnos_nuevos' class='campoModificable n"+ui.item.id+"' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='modificarAgrupacion.php' data-title='Alumnos Nuevos'>"+nuevos+"</div></td>"+
//                    "<td><div style='cursor: pointer;' href='#' id='otros_alumnos' class='campoModificable o"+ui.item.id+"' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='modificarAgrupacion.php' data-title='Otros Alumnos'>"+otros+"</div></td>"+
//                    "<td><div id='"+ui.item.id+"'>"+total+"</td>"+                    
//                    "<td><div style='cursor: pointer;' href='#' id='num_grupos' class='campoModificable ng"+ui.item.id+"' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='modificarAgrupacion.php' data-title='Numero de Grupos'>"+ui.item.num_grupos+"</div></td>"+                    
//                    "<td><div style='cursor: pointer;' href='#' id='num_grupos_l' class='campoModificable ngl"+ui.item.id+"' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='modificarAgrupacion.php' data-title='Numero de Grupos'>"+ui.item.num_grupos_l+"</div></td>"+                    
//                    "<td><div style='cursor: pointer;' href='#' id='num_grupos_d' class='campoModificable ngd"+ui.item.id+"' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='modificarAgrupacion.php' data-title='Numero de Grupos'>"+ui.item.num_grupos_d+"</div></td>"+                    
//                    "<td><div style='cursor: pointer;' href='#' id='alumnos_grupo' class='campoModificable na"+ui.item.id+"' data-type='text' data-placement='bottom' data-pk="+ui.item.id+" data-url='modificarAgrupacion.php' data-title='Numero de Grupos'>"+ui.item.alumnos_grupo+"</div></td>"+                    
//                    "</tr>"
//                    );                                                            
//                });                
//                $('#mostrarMaterias').slideDown('fast');
//                $("#paginacion").html("");
            }
        });
    });
               
    $(document).on("click",document,function(){
        $(".campoModificable").editable({
            validate: function(value) {
                var regex = /^[0-9]+$/;
                if(value==='0'){
                    return {newValue: '00'}                    
                }
                if(! regex.test(value)) {
                    return 'Solo se permiten números!';
                }
            },
            display: function(value) {
                if(value==='00'){
//                    value = '0';
                      $(this).text('0');  
                }else{
                    $(this).text(value);  
                }      
            }, 
            success: function(response, newValue) {
                var valor;
                if(newValue.toString()==='00'){
                    valor = '0';
                }else{
                    valor = newValue.toString();
                }
                //valor = newValue.toString();
                respuesta = jQuery.parseJSON(response);
                if(respuesta.status === 'error'){
                    return respuesta.msg; //msg will be shown in editable form
                }else if(respuesta.status === 'actualizar_nuevos'){
                    alumnosNuevos = valor;
                    otrosAlumnos = $('.o'+respuesta.msg).html().toString();
                    alumnosNuevos = parseInt(alumnosNuevos);
                    otrosAlumnos = parseInt(otrosAlumnos);                
                }else if(respuesta.status === 'actualizar_otros'){
                    alumnosNuevos = $('.n'+respuesta.msg).html().toString();
                    otrosAlumnos = valor;
                    alumnosNuevos = parseInt(alumnosNuevos);
                    otrosAlumnos = parseInt(otrosAlumnos);                
                }else if(respuesta.status === 'actualizar_alumnos_grupo'){
                    alumnosGrupo = valor;
                    totalAlumnos = $('#'+respuesta.msg).html().toString();
                    alumnosGrupo = parseInt(alumnosGrupo);
                    totalAlumnos = parseInt(totalAlumnos);
                    //numGrupos = Math.ceil(totalAlumnos/alumnosGrupo);
                    //$('#num_grupos').html(numGrupos);
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
                $('#mostrarMaterias').fadeIn(1000).html(data).trigger('footable_redraw');            
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
});
