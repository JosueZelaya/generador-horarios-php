$(function(){
    
    $(document).on("click",".formulario",function(){        
        $("#resultado").html("");
    });

    $(document).on("change",".form-control",function(){        
        $("#resultado").html("");
    });
    
    $(document).on("click","#add",function(){
        $.ajax({
            type: "POST",
            url: "./aMateria.php",
            data: {carrera: $('#carrera :selected').attr("codigo"),plan: $('#carrera :selected').attr("plan"),codigo: $("#codigo").val(),nombre: $("#nombre").val(),tipo: $('#tipo :selected').text(),ciclo: $('#ciclo :selected').text(),uv: $("#uv").val()},
            success: function(datos){
                datos = jQuery.parseJSON(datos);            
                if(datos==="¡Materia Agregada!"){
                    $("#resultado").html("<font color='green'>"+datos+"</font>");
                }else{
                    $("#resultado").html("<font color='red'>"+datos+"</font>");
                }            
            },
            error:function(err){                                  
                $("#resultado").html("Los datos no se pudieron enviar!");
            }
        });
//        var $form = $("#formularioAgregarDocente");        
//        var $inputs = $form.find("input, select, button, textarea");        
//        var serializedData = $form.serialize();        
//        $inputs.prop("disabled", true);        
//        $.ajax({
//            type: "post",
//            url: "./aDocente.php",        
//            data: serializedData,
//            success: function(datos){
//                datos = jQuery.parseJSON(datos);            
//                if(datos==="¡Docente Agregado!"){
//                    $("#resultado").html("<font color='green'>"+datos+"</font>");
//                }else{
//                    $("#resultado").html("<font color='red'>"+datos+"</font>");
//                }            
//            },
//            error:function(err){                                  
//                $("#resultado").html("Los datos no se pudieron enviar!");
//            }
//        });       
//        $inputs.prop("disabled", false);        
//        event.preventDefault();
    });
});
