

$ ->
    complete = $('input.ville_autocomplete')
    complete_target = complete.data( 'target-field' )
    complete_path = complete.data( 'complete-path' )
    complete.autocomplete(
        source: complete_path
        minLength: 3
        select: ( event, ui )->
            complete.val ui.item.nom
            $( "input##{complete_target}" ).val ui.item.codeInsee
            false
    ).data( 'autocomplete' )._renderItem = ( ul, item ) ->
        $('<li></li>')
            .data( "item", item )
            .append( "<a>#{item.nom}</a>" )
            .appendTo ul
    $('#form_stats_city').on 'submit', ->
        $("input##{complete_target}").val().length > 0
    chart_div = $('#chart_div')
    if chart_div.length > 0
        window.vl_visual_loaded = ->
            console.log "callback loading"
            gdata = new google.visualization.DataTable()
            gdata.addColumn "number", "Biens"
            gdata.addColumn "number", "Clicks"
            chart = new google.visualization.ScatterChart( chart_div[0] )
            data = chart_div.data "content"
            for ag_info in data
                r = new Array(2)
                r[0] = parseInt ag_info["pl"]
                r[1] = parseInt ag_info["cl"]
                gdata.addRow r
            options = {
                title: 'Nbre de biens vs clicks'
                hAxis: {title: 'Nombre de biens', minValue: 0, maxValue: 15}
                vAxis: {title: 'Clicks', minValue: 0, maxValue: 15}
                legend: 'none'
            }
            chart.draw gdata, options
        google.load "visualization", "1", { packages: ["corechart"], callback: vl_visual_loaded }
