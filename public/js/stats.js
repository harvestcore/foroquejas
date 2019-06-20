/**
 * Cuando se carga el documento se generan los gráficos
 */
$(document).ready(function() {

    $(function() {
        var usersData;
        var eventData;

        /**
         * Obtención de datos estadísticos de usuarios
         */
        function getUsersData() {
            var url = window.location.origin + "/~agomezm1819/foroquejas/users/getstats";
            $.ajax({
                async: false,
                type: "GET",
                url: url,
                success: function(data) {
                    if (data.status) {
                        usersData = data.data;
                    }
                },
                failure: function(jqXHR, textStatus, errorThrown) { 
                    console.log(jqXHR)
                    console.log(textStatus)
                    console.log(errorThrown)
                }
            });
        }

        /**
         * Obtención de datos estadísticos de eventos
         */
        function getEventData() {
            var url = window.location.origin + "/~agomezm1819/foroquejas/events/getstats";
            $.ajax({
                async: false,
                type: "GET",
                url: url,
                success: function(data) {
                    if (data.status) {
                        eventData = data.data;
                    }
                },
                failure: function(jqXHR, textStatus, errorThrown) { 
                    console.log(jqXHR)
                    console.log(textStatus)
                    console.log(errorThrown)
                }
            });
        }

        /**
         * Cuando se han obtenido los datos de usuarios se muestran en los gráficos
         */
        $.when( getUsersData() ).then( function() {
            $('#usersPie').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Tipos de usuarios'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.percentage:.1f} %',
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Usuarios',
                    colorByPoint: true,
                    data: [
                        {'name' : "Usuarios colaboradores", 'y': usersData.noofusers},
                        {'name' : "Usuarios administradores", 'y': usersData.noofadmins}
                    ]
                }]
            });

            $('#activeUsersPie').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Usuarios activos'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.percentage:.1f} %',
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Usuarios',
                    colorByPoint: true,
                    data: [
                        {'name' : "Usuarios activos", 'y': usersData.noofactives},
                        {'name' : "Usuarios inactivos", 'y': usersData.noofinactives}
                    ]
                }]
            });
        });

        /**
         * Cuando se han obtenido los datos de eventos se muestran en los gráficos
         */
        $.when( getEventData() ).then( function() {
            $('#eventCommentsPie').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Quejas y comentarios'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.percentage:.1f} %',
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Cantidad',
                    colorByPoint: true,
                    data: [
                        {'name' : "Quejas", 'y': eventData.noofevents},
                        {'name' : "Comentarios", 'y': eventData.noofcomments}
                    ]
                }]
            });

            $('#statusEventPie').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Estado de las quejas'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.percentage:.1f} %',
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Cantidad',
                    colorByPoint: true,
                    data: [
                        {'name' : "Checking", 'y': eventData.noofchecking},
                        {'name' : "Checked", 'y': eventData.noofchecked},
                        {'name' : "Processed", 'y': eventData.noofprocessed},
                        {'name' : "Resolved", 'y': eventData.noofresolved},
                        {'name' : "Irresolvable", 'y': eventData.noofirresolvable}
                    ]
                }]
            });

            $('#likeDislikePie').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Likes y dislikes'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '{point.percentage:.1f} %',
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Número',
                    colorByPoint: true,
                    data: [
                        {'name' : "Likes", 'y': eventData.nooflikes},
                        {'name' : "Dislikes", 'y': eventData.noofdislikes}
                    ]
                }]
            });
        });
    });
});
