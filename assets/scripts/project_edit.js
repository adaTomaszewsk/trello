$(document).ready(function(){
    const input = $('#projectTitle');
    const title = $('#projectTitle').data('project-name');
    input.val(title);

    $(".title-edit-container").on('click', '#editTitleBtn', function () {
        $(".title-wrapper").removeClass('d-none');
        $(".title-edit-container").addClass('d-none');
    });

    $('.reset-title-btn').on('click', function(){
        input.val(title);
        $(".title-wrapper").addClass('d-none');
        $(".title-edit-container").removeClass('d-none');
    });

    $('#projectTitleSubmit').on('click', function(){
        const projectId = $('#projectTitleSubmit').data('project-id');
        $(".title-wrapper").addClass('d-none');
        $(".title-edit-container").removeClass('d-none');
        changedTitle = input.val();


        fetch(`/project/update_title/${projectId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ newTitle: changedTitle }),
        }).then((response) => {

            if (!response.ok) {
                throw new Error('Failed to rename project.');
            }

            return response.json();

        }).then(data => {
            alert(data.message);
            location.reload();
        })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error renamig the project.');
            });

    });

    $(".delete-contributor-btn").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        let deleteUrl = $(this).data("delete-url");
        $("#confirmDeleteBtn").attr("href", deleteUrl);
        $("#confirmDeleteBodyModal").text("Are you sure you want to remove this contributor?");

        let confirmModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
        confirmModal.show();
    });

})