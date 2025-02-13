$( document ).ready(function() {
    const deleteBtn = $('#delete_project_btn');

    deleteBtn.on('click', function(){
        let deleteUrl = $(this).data("delete-url");
        $("#confirmDeleteBtn").attr("href", deleteUrl);
        $("#confirmDeleteBodyModal").text("Are you sure you want to delete this project?");

        let confirmModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
        confirmModal.show();
    });

});