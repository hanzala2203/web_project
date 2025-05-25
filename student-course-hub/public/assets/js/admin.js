
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function hideModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function confirmDelete(programmeId) {
    if (confirm('Are you sure you want to delete this programme?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${programmeId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function editProgramme(programmeId) {
    // Fetch programme details and show edit modal
    fetch(`/api/programmes/${programmeId}`)
        .then(response => response.json())
        .then(data => {
            // Populate and show edit modal
            showModal('editProgramModal');
            // ... populate form fields
        });
}