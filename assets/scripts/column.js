$( document ).ready(function() {
    const button = $('#button_add_new_column');
    const addNewColumnContainer = document.querySelector('.add-new-column');
    const closeButton = document.querySelector('.close-column-btn');
    const columnDiv = document.getElementById('columnContainer');
    const projectIdField = document.getElementById('project-id');


    button.on('click', function (event) {
        const projectId = $(this).data('project-id');
        projectIdField.value = projectId; 
        addNewColumnContainer.classList.add('d-none');
        columnDiv.classList.remove('d-none');
    });

    closeButton.addEventListener('click', function (event) {
        event.stopPropagation(); 
        addNewColumnContainer.classList.remove('d-none');
        columnDiv.classList.add('d-none');
    });

    $(".delete_column_btn").on("click", function (event) {
        let deleteUrl = $(this).data("delete-url");
        $("#confirmDeleteBtn").attr("href", deleteUrl);
        $("#confirmDeleteBodyModal").text("Are you sure you want to delete this column?");

        let confirmModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
        confirmModal.show();
    });
    
});
