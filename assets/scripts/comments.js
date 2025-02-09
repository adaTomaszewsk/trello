$(document).ready(function() {
    tinymce.init({
        selector: '#edit-mytextarea',
        menubar: false,
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright',
        setup: function(editor) {
            editor.on('init', function() {
                editor.setContent('');
            });
        }
    });

    $("form").on("submit", function(e) {
        tinyMCE.triggerSave(); 
    });

    $(document).on('click', '.comment-edit', function() {
        console.log('comment-edit');
        let commentId = $(this).data('comment-id');
        let commentText = $(this).data('comment-text');
        console.log(commentId);
        tinyMCE.get('edit-mytextarea').setContent(commentText);
        $('#edit-comment-id').val(commentId);
        
        $('#edit-comment-modal').fadeIn();
    });

    $(document).on('click', '.close, .close-modal', function() {
        $('#edit-comment-modal').fadeOut();
        tinyMCE.get('edit-mytextarea').setContent(''); 
    });

    $('#edit-comment-form').on("submit", function(e) {
        e.preventDefault();  
        
        let newText = $('#edit-mytextarea').val();
        tinyMCE.triggerSave(); 
    
        console.log(newText);
        if (!newText) {
            alert('Komentarz nie może być pusty!');
            return;
        }
    
        let commentId = $('#edit-comment-id').val();  
        console.log(commentId);  
        $.ajax({
            url: '/comment/edit/' + commentId,  
            method: 'POST',
            data: {
                'edit-mytextarea': newText
            },
            success: function(response) {
                console.log(response);
                alert('Komentarz został zaktualizowany!');
                $('#edit-comment-modal').fadeOut();
            },
            error: function(xhr, status, error) {
                console.error('Wystąpił błąd: ' + error);
            }
        });
    });
});