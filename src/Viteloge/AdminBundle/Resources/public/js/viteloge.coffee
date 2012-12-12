

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