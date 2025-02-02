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

    $(".card-container").on("click", function() {
        let columnId = $(this).data("column-id");
        let title = $(this).data("card-title"); 
        let id = $(this).data("card-id");
        let description = $(this).data("card-description"); 
    
        $("#edit_column_id").val(columnId);
        $("#card_project_column").val(columnId);
        $("#edit_card_title").val(title);
        $("#edit_card_description").val(description);
        $("#editCardModal form").attr("action", "/card/edit_card/" + id); 
        
        console.log("Edytuję kartę o ID:", id);
    
        let editModal = new bootstrap.Modal(document.getElementById("editCardModal"));
        editModal.show();
    });
    

    $(".delete-card-btn").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        let deleteUrl = $(this).data("delete-url");
        $("#confirmDeleteBtn").attr("href", deleteUrl);
        let confirmModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
        confirmModal.show();
    });

    $(".card-container").draggable({
        revert: "invalid",
        cursor: "move",
        start: function (event, ui) {
        },
        stop: function (event, ui) {
        }
    });

    $(".column").droppable({
        accept: ".card-container",
        drop: function (event, ui) {
            event.preventDefault();
            event.stopPropagation();

            let cardId = ui.draggable.data("card-id");
            let newColumnId = $(this).data("column-id");

            ui.draggable.appendTo($(this).find(".card-list")).css({
                top: "auto",
                left: "auto",
                position: "relative"
            });
            
            $.ajax({
                url: "/card/move",
                type: "POST",
                data: {cardId: cardId, newColumnId: newColumnId},
                success: function (response) {
                    console.log("Karta przeniesiona!");
                },
                error: function () {
                    alert("Błąd podczas przenoszenia karty.");
                }
            })

        }
    });
    
});