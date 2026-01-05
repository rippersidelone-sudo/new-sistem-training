// Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        // Toggle token field based on role
        function toggleTokenField() {
            const roleSelect = document.getElementById('role');
            const tokenField = document.getElementById('tokenField');
            const tokenInput = document.getElementById('token');

            const rolesRequiringToken = ['master-hq', 'training-coordinator', 'trainer', 'branch-coordinator'];
            const selectedRole = roleSelect.value;

            if (rolesRequiringToken.includes(selectedRole)) {
                tokenField.classList.remove('hidden');
                tokenInput.setAttribute('required', 'required');
            } else {
                tokenField.classList.add('hidden');
                tokenInput.removeAttribute('required');
                tokenInput.value = '';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            if (roleSelect && roleSelect.value) {
                toggleTokenField();
            }
        });