document.addEventListener('DOMContentLoaded', function() {
    // Configurar o formulário de aluno
    const studentForm = document.getElementById('student-form');
    
    if (studentForm) {
        studentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(studentForm);
            
            fetch('save_student.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    // Recarregar a página para mostrar o novo aluno
                    window.location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Ocorreu um erro ao enviar o formulário.');
            });
        });
    }

    // Configurar o preview das imagens do BI
    const biFrontInput = document.getElementById('bi-front');
    const biBackInput = document.getElementById('bi-back');
    const biFrontPreview = document.getElementById('bi-front-preview');
    const biBackPreview = document.getElementById('bi-back-preview');

    if (biFrontInput && biFrontPreview) {
        biFrontInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    biFrontPreview.innerHTML = `<img src="${e.target.result}" alt="Frente do BI" style="max-width: 100px; max-height: 100px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (biBackInput && biBackPreview) {
        biBackInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    biBackPreview.innerHTML = `<img src="${e.target.result}" alt="Verso do BI" style="max-width: 100px; max-height: 100px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Configurar o dropdown de encarregados
    const parentsSelect = document.getElementById('parents');
    if (parentsSelect) {
        // Os encarregados já são preenchidos pelo PHP
    }

    // Configurar o toggle de senha
    const passwordToggle = document.querySelector('.password-toggle');
    if (passwordToggle) {
        passwordToggle.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
});