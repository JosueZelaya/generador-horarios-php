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
            url: "./aDepartamento.php",
            data: {nombre: $('#nombre').val()},
            success: function(datos){
                datos = jQuery.parseJSON(datos);            
                if(datos==="¡Departamento Agregado!"){
                    $("#resultado").html("<font color='green'>"+datos+"</font>");
                }else{
                    $("#resultado").html("<font color='red'>"+datos+"</font>");
                }            
            },
            error:function(err){                                  
                $("#resultado").html("Los datos no se pudieron enviar!");
            }
        });
    }); 
});
