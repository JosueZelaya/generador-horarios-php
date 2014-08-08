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
