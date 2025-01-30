$(document).ready(function() {
    const createCardBtn = $(".new-card-modal-btn");
    const cardContainer = $(".card-container");
    const modal = $("#editCardModal");
    const form = modal.find("form");

    createCardBtn.each(function() {
        $(this).on("click", function() {
            $("#card_title").val("");
            $("#card_description").val("");
            let columnId = $(this).data("column-id");
            $("#card_project_column").val(columnId);
        });
    });

    cardContainer.on("click", function() {
        let columnId = $(this).data("column-id");
        let title = $(this).data("cardTitle");
        let id = $(this).data("cardId");
        let description = $(this).data("cardDescription");
        $("#edit_column_id").val(columnId);
        $("#card_project_column").val(columnId);
        $("#edit_card_title").val(title);
        $("#edit_card_description").val(description);
        $("#editCardModal #cardForm_id").val(id);
        console.log(columnId);
        form.attr("action", `/card/edit_card/${id}`);
    });
    
});