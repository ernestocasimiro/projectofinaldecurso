
  // Abrir o modal
  document.getElementById("add-guardian-btn").addEventListener("click", function () {
    document.getElementById("guardian-modal").style.display = "block";
  });

  // Fechar o modal (ícone X)
  document.querySelector("#guardian-modal .close-modal").addEventListener("click", function () {
    document.getElementById("guardian-modal").style.display = "none";
  });

  // Fechar o modal (botão cancelar)
  document.getElementById("cancel-guardian-btn").addEventListener("click", function () {
    document.getElementById("guardian-modal").style.display = "none";
  });

  // Mostrar/ocultar senha
  document.querySelector(".password-toggle").addEventListener("click", function () {
    const input = document.getElementById("guardian-password");
    if (input.type === "password") {
      input.type = "text";
      this.querySelector("i").classList.remove("fa-eye");
      this.querySelector("i").classList.add("fa-eye-slash");
    } else {
      input.type = "password";
      this.querySelector("i").classList.remove("fa-eye-slash");
      this.querySelector("i").classList.add("fa-eye");
    }
  });

// Salvar o encarregado
  document.getElementById('guardian-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const data = {
        name: document.getElementById('guardian-name').value,
        gender: document.getElementById('guardian-gender').value,
        dob: document.getElementById('guardian-dob').value,
        bi: document.getElementById('bi-number').value,
        address: document.getElementById('guardian-address').value,
        phone: document.getElementById('guardian-contact').value,
        email: document.getElementById('guardian-email').value,
        password: document.getElementById('guardian-password').value
    };

    fetch('save_guardian.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(data)
    })
    .then(res => res.json())
    .then(response => {
        alert(response.message);
        if (response.status === 'success') {
            location.reload(); // ou atualizar tabela sem reload
        }
    })
    .catch(err => {
        console.error('Erro:', err);
        alert('Erro ao salvar o encarregado.');
    });
});


// Função para carregar os encarregados via AJAX
function loadGuardians() {
    fetch('guardian.php')  // Caminho para o arquivo PHP que retorna os dados JSON
        .then(response => response.json())  // Converter a resposta para JSON
        .then(data => {
            const tableBody = document.getElementById('guardian-table-body');
            tableBody.innerHTML = '';  // Limpar tabela antes de preencher

            // Verificar se 'data' é um array e contém objetos
            if (Array.isArray(data)) {
                data.forEach(guardian => {
                    const row = document.createElement('tr');

                    // Criação de células para cada dado do encarregado
                    row.innerHTML = `
                        <td>${guardian.id}</td>
                        <td>${guardian.nome}</td>
                        <td>${guardian.telefone} / ${guardian.email}</td>
                        <td>
                            <button class="edit-btn">Editar</button>
                            <button class="delete-btn">Excluir</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                console.error('Dados recebidos não são um array válido:', data);
            }
        })
        .catch(error => console.error('Erro ao carregar os encarregados:', error));
}

// Carregar os encarregados quando a página carregar
document.addEventListener('DOMContentLoaded', loadGuardians);

