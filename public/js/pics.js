$(document).ready(function() {

    if (window.File && window.FileList && window.FileReader) {

        /**
         * Cuando se agregan imagenes permite visualizarlas y eliminarlas
         */
        $("#event-images").on("change", function(e) {
            // Obtengo formulario
            var myForm = document.getElementById('myForm');
            // Obtengo datos del formulario
            var formData = new FormData(myForm);
            // Obtengo archivos (imagenes)
            var files = e.target.files, filesLength = files.length;
            // vector auxiliar para almacenar las imagenes
            var filesaux = new Array();

            // Recorro array de archivos
            for (var i = 0; i < filesLength; i++) {
                var f = files[i]
                // agrego al vector auxiliar las imagenes
                filesaux.push(f);
                // lector de archivos
                var fileReader = new FileReader();
                fileReader.onload = (function(e) {
                    var file = e.target;
                    
                    // muestro la imagen y el botón para eliminarla
                    $("<div class=\"form-row my-3 text-center\"><span class=\"pip\">" +
                        "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/ style=\"width:100px\">" +
                        "<br/><span class=\"remove btn btn-danger my-2\">Eliminar imagen</span>" +
                        "</span><\div>").insertAfter("#event-images");
                    
                    // Si pulso el botón para eliminar la foto
                    $(".remove").click(function(){
                        // ya no muestro la imagen
                        $(this).parent(".pip").remove();
                        var ff = new Array();
                        // borro los datos del formulario
                        formData.delete("event-images[]");
                        
                        // Agrego al formulario todas las imagenes salvo la que acabo de eliminar
                        filesaux.forEach(element => {
                            if (element.name != f.name)
                                formData.append("event-images[]", element);
                        });
                    });
                });

                fileReader.readAsDataURL(f);
            }
        });
    }
});