$(document).ready(function() {
    const createCardBtn = $(".new-card-modal-btn");
    const createCardModal = $('#addCardModal');
    const editCardModal = $('#editCardModal');

    createCardBtn.each(function() {
        $(this).on("click", function() {
            let columnId = $(this).data("column-id");
            $("#card_project_column").val(columnId);
        });
    });

});