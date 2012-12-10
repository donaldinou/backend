

$ ->
    $('.stats .btn.action-display').click (e) =>
        e.preventDefault()
        a = e.currentTarget
        $(a).replaceWith( $("<img src='" + $(a).attr( 'href' ) + "'>" ) )
