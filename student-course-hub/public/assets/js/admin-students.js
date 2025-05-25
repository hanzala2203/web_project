function viewDetails(studentId) {
    // Fetch student details
    fetch(`/api/students/${studentId}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('studentDetailsModal');
            const modalBody = modal.querySelector('.modal-body');
            
            // Populate modal with student details
            modalBody.innerHTML = `
                <div class="student-details">
                    <h3>${data.username}</h3>
                    <p><strong>Email:</strong> ${data.email}</p>
                    <p><strong>Registration Date:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                    
                    <h4>Interested Programmes</h4>
                    <ul class="interests-list">
                        ${data.interests.map(interest => `
                            <li>${interest.title} (Since: ${new Date(interest.registered_at).toLocaleDateString()})</li>
                        `).join('')}
                    </ul>
                </div>
            `;
            
            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching student details:', error);
            alert('Failed to load student details');
        });
}

function closeModal() {
    document.getElementById('studentDetailsModal').style.display = 'none';
}

function removeStudent(studentId) {
    if (confirm('Are you sure you want to remove this student?')) {
        fetch(`/api/students/${studentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                throw new Error('Failed to remove student');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to remove student');
        });
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('studentDetailsModal');
    if (event.target === modal) {
        closeModal();
    }
}