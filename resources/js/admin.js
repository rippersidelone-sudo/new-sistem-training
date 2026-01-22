// Toggle Token Field saat memilih role (untuk Create User)
function toggleTokenField() {
    const roleSelect = document.getElementById('role');
    const tokenField = document.getElementById('tokenCreateUser');
    
    if (!roleSelect || !tokenField) return;
    
    const selectedOption = roleSelect.options[roleSelect.selectedIndex];
    const requireToken = selectedOption.getAttribute('data-require-token');

    if (requireToken === '1') {
        tokenField.classList.remove('hidden');
        document.getElementById('tokenInputUser').setAttribute('required', 'required');
    } else {
        tokenField.classList.add('hidden');
        document.getElementById('tokenInputUser').removeAttribute('required');
        document.getElementById('tokenInputUser').value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success/error messages after 3 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('[x-data*="show: true"]');
        alerts.forEach(alert => {
            const alpineData = Alpine.$data(alert);
            if (alpineData && alpineData.show) {
                alpineData.show = false;
            }
        });
    }, 3000);

    // Initialize token field state on page load
    const roleSelect = document.getElementById('role');
    if (roleSelect && roleSelect.value) {
        toggleTokenField();
    }
});