document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('button_add_new_column');
    const addNewColumnContainer = document.querySelector('.add-new-column');
    const saveButton = document.querySelector('.add-column-btn');
    const closeButton = document.querySelector('.close-column-btn');
    const columnDiv = document.getElementById('columnContainer');

    button.addEventListener('click', function () {
        addNewColumnContainer.classList.add('d-none');
        columnDiv.classList.remove('d-none');
    });

    closeButton.addEventListener('click', function () {
        console.log('Close button clicked');
        console.log(columnDiv);
        addNewColumnContainer.classList.remove('d-none');
        columnDiv.classList.add('d-none');
        console.log(columnDiv.classList);
    });

    saveButton.addEventListener('click', function () {
        console.log('Save button clicked');
        addNewColumnContainer.classList.remove('d-none');
        columnDiv.classList.add('d-none');
        const projectId = columnDiv.getAttribute('data-id');
        const columnInput = document.getElementById('input-column').value;

        const data = {
            projectId: projectId,
            column: columnInput
        };

         console.log(data);

        fetch('/add_column', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data),
        }).then((response) => {

            if (!response.ok) {
                throw new Error('Failed to create project.');
            }

            return response.json();

        }).then(data => {
            alert(data.message);
            location.reload();
        })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error creating the project.');
            });
     });

});
