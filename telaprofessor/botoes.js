// Adicionar estas funções ao arquivo dashboard-data.js

// Função para inicializar todos os botões e interações do dashboard
function initializeButtonsAndInteractions() {
    // Inicializar botão de toggle do menu
    initializeMenuToggle();
    
    // Inicializar barra de pesquisa
    initializeSearchBar();
    
    // Inicializar notificações
    initializeNotifications();
    
    // Inicializar botões de ação comuns
    initializeActionButtons();
    
    // Inicializar botões específicos de cada página
    initializePageSpecificButtons();
}

// Função para inicializar o botão de toggle do menu
function initializeMenuToggle() {
    const menuToggle = document.getElementById('menuToggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });
    }
}

// Função para inicializar a barra de pesquisa
function initializeSearchBar() {
    const searchInput = document.querySelector('.search-container input');
    if (searchInput) {
        searchInput.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        searchInput.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
        
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                // Simular pesquisa
                alert(`Pesquisando por: ${this.value}`);
                this.value = '';
            }
        });
    }
}

// Função para inicializar notificações
function initializeNotifications() {
    const notificationIcon = document.querySelector('.notification');
    if (notificationIcon) {
        notificationIcon.addEventListener('click', function() {
            alert('Você tem 3 notificações não lidas.');
        });
    }
    
    const helpIcon = document.querySelector('.top-bar-actions .material-symbols-outlined:last-child');
    if (helpIcon) {
        helpIcon.addEventListener('click', function() {
            alert('Centro de Ajuda: Para suporte, entre em contato com o administrador do sistema.');
        });
    }
}

// Função para inicializar botões de ação comuns
function initializeActionButtons() {
    // Botões de adicionar (com ícone add)
    const addButtons = document.querySelectorAll('.btn-primary .material-symbols-outlined:first-child');
    addButtons.forEach(btn => {
        if (btn.textContent === 'add') {
            btn.parentElement.addEventListener('click', function() {
                const buttonText = this.textContent.trim().replace('add', '').trim();
                alert(`Adicionar ${buttonText}`);
            });
        }
    });
    
    // Botões de exportar
    const exportButtons = document.querySelectorAll('button:has(.material-symbols-outlined:first-child[text="file_download"])');
    exportButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            alert('Exportando dados...');
        });
    });
    
    // Botões de salvar
    const saveButtons = document.querySelectorAll('button:has(.material-symbols-outlined:first-child[text="save"])');
    saveButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            alert('Alterações salvas com sucesso!');
        });
    });
    
    // Botões de ação nas tabelas
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('title') || 'Ação';
            const row = this.closest('tr');
            let targetName = 'item';
            
            if (row) {
                const nameElement = row.querySelector('.student-name p') || row.querySelector('.student-name');
                if (nameElement) {
                    targetName = nameElement.textContent.trim();
                }
            }
            
            alert(`${action} para ${targetName}`);
        });
    });
}

// Função para inicializar botões específicos de cada página
function initializePageSpecificButtons() {
    // Verificar em qual página estamos
    const currentPage = window.location.pathname.split('/').pop();
    
    switch (currentPage) {
        case 'boletins.html':
            initializeBoletinsButtons();
            break;
        case 'minipautas.html':
            initializeMinipautasButtons();
            break;
        case 'notas.html':
            initializeNotasButtons();
            break;
        case 'presenca.html':
            initializePresencaButtons();
            break;
        case 'calendario.html':
            initializeCalendarioButtons();
            break;
        case 'mensagens.html':
            initializeMensagensButtons();
            break;
        default:
            // Página genérica ou dashboard
            break;
    }
}

// Função para inicializar botões da página de boletins
function initializeBoletinsButtons() {
    // Botão de impressão
    const printButton = document.getElementById('printButton');
    if (printButton) {
        printButton.addEventListener('click', function() {
            window.print();
        });
    }
    
    // Botão de visualização
    const visualizarBtn = document.getElementById('visualizarBtn');
    if (visualizarBtn) {
        visualizarBtn.addEventListener('click', function() {
            // Obter o primeiro aluno selecionado
            const selectedCheckbox = document.querySelector('.alunos-list .toggle-input:checked');
            if (selectedCheckbox) {
                const studentId = selectedCheckbox.getAttribute('data-student-id');
                loadStudentReportCard(studentId);
                alert('Boletim atualizado com os dados do aluno selecionado.');
            } else {
                alert('Por favor, selecione pelo menos um aluno.');
            }
        });
    }
    
    // Botão de exportar PDF
    const exportarBtn = document.getElementById('exportarBtn');
    if (exportarBtn) {
        exportarBtn.addEventListener('click', function() {
            alert('Exportando boletim como PDF...');
        });
    }
    
    // Botões de zoom
    const zoomInBtn = document.getElementById('zoomInBtn');
    const zoomOutBtn = document.getElementById('zoomOutBtn');
    let currentZoom = 100;
    
    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', function() {
            if (currentZoom < 150) {
                currentZoom += 10;
                document.getElementById('boletim-document').style.transform = `scale(${currentZoom / 100})`;
                document.getElementById('boletim-document').style.transformOrigin = 'top center';
            }
        });
    }
    
    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', function() {
            if (currentZoom > 50) {
                currentZoom -= 10;
                document.getElementById('boletim-document').style.transform = `scale(${currentZoom / 100})`;
                document.getElementById('boletim-document').style.transformOrigin = 'top center';
            }
        });
    }
    
    // Botão de tela cheia
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', function() {
            const previewContent = document.querySelector('.preview-content');
            if (previewContent.requestFullscreen) {
                previewContent.requestFullscreen();
            } else if (previewContent.webkitRequestFullscreen) {
                previewContent.webkitRequestFullscreen();
            } else if (previewContent.msRequestFullscreen) {
                previewContent.msRequestFullscreen();
            }
        });
    }
    
    // Checkbox "Selecionar todos"
    const selecionarTodos = document.getElementById('selecionar-todos');
    if (selecionarTodos) {
        selecionarTodos.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.alunos-list .toggle-input');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Botão "Limpar seleção"
    const limparSelecaoBtn = document.querySelector('.selection-header .btn-text');
    if (limparSelecaoBtn) {
        limparSelecaoBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.alunos-list .toggle-input');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selecionar-todos').checked = false;
        });
    }
}

// Função para inicializar botões da página de minipautas
function initializeMinipautasButtons() {
    // Botão de impressão
     }