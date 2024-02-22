

$(document).ready(function() {

    $('.add_comment').on('click', function(e) {
        e.preventDefault();
    
        var bookId = $('#book_id').val();
        var contenido = $('.contenido').val();
    
        console.log("LLamadooooo");
        $.ajax({
            url: '/book/' + bookId + '/comment',
            method: 'POST',
            data: { content: contenido },
        }).then(function(response) {
            $('.contenido').val('');
            $('#contenedor-comentarios').html("");
            $('#contenedor-comentarios').html(response.subvista);
        });
    });
});