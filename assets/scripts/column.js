$( document ).ready(function() {
    const button = $('#button_add_new_column');
    const addNewColumnContainer = document.querySelector('.add-new-column');
    const closeButton = document.querySelector('.close-column-btn');
    const columnDiv = document.getElementById('columnContainer');

    console.log('dump');
    console.log(button);

    button.on('click', function (event) {
        addNewColumnContainer.classList.add('d-none');
        columnDiv.classList.remove('d-none');
        console.log('CLick!!!!');
    });

    closeButton.addEventListener('click', function (event) {
        event.stopPropagation(); 
        console.log('Close button clicked');
        console.log(columnDiv);
        addNewColumnContainer.classList.remove('d-none');
        columnDiv.classList.add('d-none');
        console.log(columnDiv.classList);
    });

    $(".delete_column_btn").on("click", function (event) {
        let deleteUrl = $(this).data("delete-url");
        console.log(deleteUrl);
        $("#confirmDeleteBtn").attr("href", deleteUrl);
        let confirmModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
        confirmModal.show();
    });
    
});
