/**
 * Cuando se carga el documento se completan algunos datos del evento
 * Likes/dislikes
 * Estado
 * 
 * También se permite que se de like/dislike
 */
$(document).ready(function() {

    /**
     * Obtiene los likes y dislikes del evento y los actualiza en el documento
     */
    function updatelikes() {
        $path = window.location.pathname.split('/');
        $id = $path[$path.length - 1];
        $.ajax({
            url: window.location.origin + "/~agomezm1819/foroquejas/events/getlikes/" + $id,
            type: "GET",
            success: function(data) {
                $('#likes').text(data.likes);
            },
            failure: function(jqXHR, textStatus, errorThrown) { 
                console.log(jqXHR)
                console.log(textStatus)
                console.log(errorThrown)
            }
        });

        $.ajax({
            url: window.location.origin + "/~agomezm1819/foroquejas/events/getdislikes/" + $id,
            type: "GET",
            success: function(data) {
                $('#dislikes').text(data.dislikes);
            },
            failure: function(jqXHR, textStatus, errorThrown) { 
                console.log(jqXHR)
                console.log(textStatus)
                console.log(errorThrown)
            }
        });
    }

    /**
     * Obtiene el estado del evento y lo actualiza en el documento
     */
    function updatestatus() {
        $path = window.location.pathname.split('/');
        $id = $path[$path.length - 1];
        $.ajax({
            url: window.location.origin + "/~agomezm1819/foroquejas/events/getstatus/" + $id,
            type: "GET",
            success: function(data) {
                $('#status').text(data.value);
            },
            failure: function(jqXHR, textStatus, errorThrown) { 
                console.log(jqXHR)
                console.log(textStatus)
                console.log(errorThrown)
            }
        });
    }

    /**
     * Se actualiza el valor de los likes/dislikes y estado tras cargar el documento
     */
    $(function() {
        updatelikes();
        updatestatus();
    });


    /**
     * Da like al evento al pulsar el boton
     */
    $('#like').on('click', function() {
        $path = window.location.pathname.split('/');
        $id = $path[$path.length - 1];
        $.ajax({
            url: window.location.origin + "/~agomezm1819/foroquejas/events/like/" + $id,
            type: "GET",
            success: function(data) {
                if (data.status == true) {
                    console.log("Liked post: #" + $id);
                } else {
                    console.log("Couldn't like post: #" + $id);
                    alert('Ya has dado like a la queja.');
                }
                updatelikes();
            },
            failure: function(jqXHR, textStatus, errorThrown) { 
                console.log(jqXHR)
                console.log(textStatus)
                console.log(errorThrown)
            }
        });
    });

    /**
     * Da dislike al evento al pulsar el boton
     */
    $('#dislike').on('click', function() {
        $path = window.location.pathname.split('/');
        $id = $path[$path.length - 1];
        $.ajax({
            url: window.location.origin + "/~agomezm1819/foroquejas/events/dislike/" + $id,
            type: "GET",
            success: function(data) {
                if (data.status == true) {
                    console.log("Disliked post: #" + $id);
                } else {
                    console.log("Couldn't dislike post: #" + $id);
                    alert('Ya has dado dislike a la queja.');
                }
                updatelikes();
            },
            failure: function(jqXHR, textStatus, errorThrown) { 
                console.log(jqXHR)
                console.log(textStatus)
                console.log(errorThrown)
            }
        });
    });

    /**
     * Elimina un comentario al pulsar el boton
     */
    $('#removecomment').on('click', function() {
        var commentid = $('#removecomment').val();
        var r = confirm("¿Realmente deseas eliminar este comentario?");
        if (r) {
            $.ajax({
                url: window.location.origin + "/~agomezm1819/foroquejas/events/deletecomment/" + commentid,
                type: "GET",
                success: function(data) {
                    if (data.status == true) {
                        console.log("Removed post: #" + $id);
                        location.reload();
                    } else {
                        console.log("Couldn't remove post: #" + $id);
                    }
                },
                failure: function(jqXHR, textStatus, errorThrown) { 
                    console.log(jqXHR)
                    console.log(textStatus)
                    console.log(errorThrown)
                }
            });
        }
    });

    /**
     * Cambia el estado del evento al cambiar el seleccionador
     */
    $('#changestatus').on('change', function() {
        $path = window.location.pathname.split('/');
        $id = $path[$path.length - 1];
        var value = $('#changestatus').val();

        if (value != '-' && value != $('#status').text()) {
            $.ajax({
                url: window.location.origin + "/~agomezm1819/foroquejas/events/changeeventstatus/" + value + "/" + $id,
                type: "GET",
                success: function(data) {
                    if (data.status == true) {
                        console.log("Updated status to " + value + " in post: #" + $id);
                        updatestatus();
                    } else {
                        console.log("Couldn't update status to " + value + " in post: #" + $id);
                    }
                },
                failure: function(jqXHR, textStatus, errorThrown) { 
                    console.log(jqXHR)
                    console.log(textStatus)
                    console.log(errorThrown)
                }
            });
        }
    });
});


