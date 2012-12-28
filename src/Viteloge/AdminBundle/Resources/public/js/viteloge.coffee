

drawGoogleCharts = ->
    for chart in $('.google_chart')
        chart = $(chart)
        data = new google.visualization.DataTable()
        data.addColumn 'date', 'Date'
        data.addColumn 'number', 'Total'
        data.addColumn 'number', 'New'
        data.addColumn 'number', 'Delete'
        src = chart.data('cycles')
        for row in src
            row[0] = new Date( row[0] )
        data.addRows( src )
        gchart = new google.visualization.AnnotatedTimeLine( chart[0] )
        gchart.draw( data, { displayAnnotations: true, dateFormat:"dd MMMM yyyy", colors:['#0000cc', '#00cc00', '#cc0000'], displayExactValues : true });

$ ->
    $('.stats .btn.action-display').click (e) =>
        e.preventDefault()
        a = e.currentTarget
        $(a).replaceWith( $("<img src='" + $(a).attr( 'href' ) + "'>" ) )

    $('a.crawlable').click (e) =>
        a = e.currentTarget
        link = $(a).attr('href')
        post_url = /^([^#]*)#(.*)$/.exec link
        if post_url
            e.preventDefault()
            form = $("<form method='POST' target='_blank'></form>")
            form.attr( "action", post_url[1] )
            for param in post_url[2].split "&"
                kv = param.split "="
                form.append $("<input type='hidden'></input>").attr("name", kv[0] ).attr( "value", kv[1] )
            $(document.body).append form
            form.submit()

    if $('.google_chart').length > 0
        params = {
            packages: ['annotatedtimeline'],
            callback: ->
                drawGoogleCharts()
        }
        google.load 'visualization', '1', params

    input_resize_agence = $('input#agence_logo_resize')
    if input_resize_agence.length > 0
        input_resize_agence.change =>
            $('input#agence_logo_width,input#agence_logo_height').prop( 'disabled', ! input_resize_agence.prop( "checked" ) )
        $('input#agence_logo_width,input#agence_logo_height').prop( 'disabled', ! input_resize_agence.prop( "checked" ) )

    $('a[data-remote]').click (e) =>
        e.preventDefault()
        link = $(e.currentTarget)
        if viteloge_ajax_loader?
            link.append $("<img>").attr( "src", viteloge_ajax_loader )
        $.get link.attr( 'href' ), {}, (response) ->
            link.replaceWith response