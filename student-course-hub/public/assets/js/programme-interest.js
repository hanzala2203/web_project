document.addEventListener('DOMContentLoaded', () => {
    const BASE_URL = window.location.origin;
    
    // Handle interest button clicks
    document.addEventListener('click', async (e) => {
        const button = e.target.closest('button[data-programme-id]');
        if (!button) return; // Not an interest button
        
        e.preventDefault();
        const programmeId = button.dataset.programmeId;
        const action = button.dataset.action;
        
        try {
            const response = await fetch(`${BASE_URL}/student/register_interest_api.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    programme_id: programmeId,
                    action: action 
                })
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.error || 'Failed to process request');
            }
            
            if (data.success) {
                // Show success message
                alert(action === 'register' ? 
                    'Interest registered successfully!' : 
                    'Interest withdrawn successfully!'
                );
                
                // Toggle button state
                const newAction = action === 'register' ? 'withdraw' : 'register';
                const isRegistered = action === 'register';
                
                button.dataset.action = newAction;
                button.innerHTML = `
                    <i class="${isRegistered ? 'fas' : 'far'} fa-bookmark mr-2"></i>
                    ${isRegistered ? 'Withdraw Interest' : 'Register Interest'}
                `;
                
                // Update button styling
                button.className = `flex-1 inline-flex justify-center items-center px-4 py-2 ${
                    isRegistered ? 
                    'bg-red-100 text-red-700 hover:bg-red-200' : 
                    'bg-slate-100 text-slate-700 hover:bg-slate-200'
                } rounded-md focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-colors duration-200`;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to process request. Please try again later.');
        }
    });
});
