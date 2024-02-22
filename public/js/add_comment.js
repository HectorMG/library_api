
$(document).ready(function() {
    let id = null;

    $('.add_comment').on('click', function(e) {
        e.preventDefault();

        var bookId = $('#book_id').val();
        var contenido = $('.contenido').val();


        $.ajax({
            url: '/book/' + bookId + '/comment',
            method: 'POST',
            data: { content: contenido },
        }).then(function(response) {
            $('.contenido').val('');
            $('#contenedor-comentarios').html();
            $('#contenedor-comentarios').html(response.subvista);
        });
    });

    $('#edit_comment').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        id = button.data('comment-id');
        var text = $('#comment_'+id).data('comment-content');
        var modal = $(this);
        modal.find('.modal-body textarea').val(text);

    })

    $('#update_comment').on('click', function(e) {
        e.preventDefault();
    
        var modal = $('#edit_comment');
        var contenido = modal.find('.modal-body textarea').val();
    
        $.ajax({
            url: '/comment_edit/' + id,
            method: 'POST',
            data: { content: contenido },
        }).then(function(response) {
            modal.modal('hide');
            $('.modal-backdrop').css('display', 'none');
            $('#contenedor-comentarios').html(response.subvista);
        });
    });

})