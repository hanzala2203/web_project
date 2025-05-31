const registerInterest = async (programmeId) => {
    try {
        console.log('Attempting to register interest for programme:', programmeId);
        const response = await fetch(`${BASE_URL}/student/register_interest_api.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ programme_id: programmeId })
        });

        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);

        if (!response.ok) {
            throw new Error(data.error || 'Failed to register interest');
        }
        
        if (data.success) {
            // Update UI to show registered state
            const button = document.querySelector(`button[data-programme-id="${programmeId}"]`);
            if (button) {
                button.outerHTML = `
                    <button class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-slate-200 text-slate-800 rounded-md cursor-not-allowed">
                        <i class="fas fa-bookmark mr-2"></i> Registered
                    </button>
                `;
            }
        } else {
            throw new Error(data.error || 'Failed to register interest');
        }
    } catch (error) {
        console.error('Error registering interest:', error);
        alert(error.message || 'Failed to register interest. Please try again later.');
    }
};

// Initialize event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to register interest buttons
    document.querySelectorAll('button[data-programme-id]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const programmeId = this.getAttribute('data-programme-id');
            registerInterest(programmeId);
        });
    });
});
