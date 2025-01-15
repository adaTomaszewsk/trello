document.addEventListener('DOMContentLoaded', function () {
    let columnCount = 1;
    const modalElement = document.getElementById('newProjectModal');
    const addColumnBtn = document.getElementById('addColumnBtn');
    const saveProjectBtn = document.querySelector('.btn.btn-primary');
    const columnsContainer = document.getElementById('columnsContainer');
    const firstColumn = document.getElementById('column-1'); 

    if (modalElement) {

        modalElement.addEventListener('shown.bs.modal', function () {
            columnCount = 1;

            Array.from(columnsContainer.children).forEach(child => {
                if (child !== firstColumn && child !== addColumnBtn) {
                    child.remove();
                }
            });

            firstColumn.querySelector('input').value = '';

            const inputs = modalElement.querySelectorAll('input, textarea:not(#columnName-1)');
            inputs.forEach(input => {
                input.value = '';
            });
        });
    }

    addColumnBtn.addEventListener('click', function () {
        columnCount++;
        const newColumnDiv = document.createElement('div');
        newColumnDiv.className = 'mb-2 d-flex align-items-center';
        newColumnDiv.id = `column-${columnCount}`;

        newColumnDiv.innerHTML = `
            <input type="text" class="form-control me-2" id="columnName-${columnCount}" placeholder="e.g. Doing" />
            <button type="button" class="btn btn-danger btn-sm remove-column-btn" data-column-id="column-${columnCount}">
                X
            </button>
        `;

        columnsContainer.insertBefore(newColumnDiv, addColumnBtn);

        newColumnDiv.querySelector('.remove-column-btn').addEventListener('click', function () {
            const columnId = this.getAttribute('data-column-id');
            const columnElement = document.getElementById(columnId);
            if (columnElement) {
                columnElement.remove();
            }
        });
    });

    saveProjectBtn.addEventListener('click', function () {
        const projectName = document.getElementById('projectName').value;
        const columnInputs = document.querySelectorAll('#columnsContainer input');
        const columns = Array.from(columnInputs).map(input => input.value).filter(value => value.trim() !== '');

        if (!projectName.trim() || columns.length === 0) {
            alert('Please provide a project name and at least one column!');
            return;
        }
        const projectData = {
            projectName: projectName,
            columns: columns
        };

        console.log(projectData);

        fetch('/api/create-project', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(projectData),
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
