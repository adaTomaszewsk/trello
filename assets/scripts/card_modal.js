$(document).ready(function() {
    const createCardBtn = $(".new-card-modal-btn");
    const modal = $("#editCardModal");

    createCardBtn.each(function() {
        $(this).on("click", function() {
            $("#card_title").val("");
            $("#card_description").val("");
            let columnId = $(this).data("column-id");
            $("#card_project_column").val(columnId);
        });
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