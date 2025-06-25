document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar functionality
    initSidebar();
    
    // Initialize tab navigation
    initTabNavigation();
    
    // Initialize modals
    initModals();
    
    // Load mock data
    loadMockData();
    
    // Initialize form submissions
    initFormSubmissions();
    
    // Initialize search and filter functionality
    initSearchAndFilter();
});

// Sidebar functionality
function initSidebar() {
    const menuItems = document.querySelectorAll('.has-submenu .menu-item');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            const parent = this.parentElement;
            
            // Toggle active class
            if (parent.classList.contains('active')) {
                parent.classList.remove('active');
            } else {
                // Close other open submenus
                document.querySelectorAll('.has-submenu.active').forEach(el => {
                    if (el !== parent) {
                        el.classList.remove('active');
                    }
                });
                
                parent.classList.add('active');
            }
        });
    });
    
    // Handle logout click
    document.querySelector('.logout').addEventListener('click', function() {
        if (confirm('Tem certeza que deseja sair?')) {
            alert('Sessão encerrada com sucesso!');
            // In a real application, this would redirect to the login page
        }
    });
}

// Tab navigation
function initTabNavigation() {
    const navLinks = document.querySelectorAll('.nav-links li');
    const submenuLinks = document.querySelectorAll('.submenu li');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    // Function to show a specific tab
    function showTab(tabId) {
        // Hide all tab panes
        tabPanes.forEach(pane => {
            pane.classList.remove('active');
        });
        
        // Show the selected tab pane
        const selectedPane = document.getElementById(tabId);
        if (selectedPane) {
            selectedPane.classList.add('active');
        }
        
        // Update active state in navigation
        navLinks.forEach(link => {
            if (link.dataset.tab === tabId || link.querySelector(`li[data-tab="${tabId}"]`)) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
        
        submenuLinks.forEach(link => {
            if (link.dataset.tab === tabId) {
                link.classList.add('active');
                // Also activate parent
                const parentMenu = link.closest('.has-submenu');
                if (parentMenu) {
                    parentMenu.classList.add('active');
                }
            } else {
                link.classList.remove('active');
            }
        });
    }
    
    // Add click event to main nav links
    navLinks.forEach(link => {
        if (!link.classList.contains('has-submenu')) {
            link.addEventListener('click', function() {
                showTab(this.dataset.tab);
            });
        }
    });
    
    // Add click event to submenu links
    submenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent bubbling to parent
            showTab(this.dataset.tab);
        });
    });
}

// Modal functionality
function initModals() {
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.close-modal, .cancel-btn');
    
    // Open student modal
    document.getElementById('add-student-btn').addEventListener('click', function() {
        document.getElementById('student-modal').classList.add('active');
        document.getElementById('student-form').reset();
        document.getElementById('student-modal-title').textContent = 'Adicionar Novo Aluno';
    });
    
    // Open teacher modal
    document.getElementById('add-teacher-btn').addEventListener('click', function() {
        document.getElementById('teacher-modal').classList.add('active');
        document.getElementById('teacher-form').reset();
        document.getElementById('teacher-modal-title').textContent = 'Adicionar Novo Funcionário';
    });
    
    // Open guardian modal if it exists
    const addGuardianBtn = document.getElementById('add-guardian-btn');
    if (addGuardianBtn) {
        addGuardianBtn.addEventListener('click', function() {
            document.getElementById('guardian-modal').classList.add('active');
            document.getElementById('guardian-form').reset();
            document.getElementById('guardian-modal-title').textContent = 'Adicionar Novo Encarregado';
        });
    }
    
    // Close modals
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.classList.remove('active');
        });
    });
    
    // Close modal when clicking outside
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
}

// Mock data loading
function loadMockData() {
  
    
    // Load teachers data
    const teacherTableBody = document.getElementById('teacher-table-body');
    if (teacherTableBody) {
        const teachersData = [
            { id: 'TCH001', name: 'António Martins', role: 'Professor', department: 'Matemática', contact: '+244 923 456 794', subjects: 'Matemática, Álgebra' },
            { id: 'TCH002', name: 'Luísa Pereira', role: 'Professora', department: 'Ciências', contact: '+244 923 456 795', subjects: 'Física, Química' },
            { id: 'TCH003', name: 'Manuel Sousa', role: 'Coordenador', department: 'Coordenação', contact: '+244 923 456 796', subjects: 'Gestão Escolar' },
            { id: 'TCH004', name: 'Teresa Almeida', role: 'Professora', department: 'Português', contact: '+244 923 456 797', subjects: 'Português, Literatura' },
            { id: 'TCH005', name: 'Carlos Santos', role: 'Professor', department: 'História', contact: '+244 923 456 798', subjects: 'História, Geografia' }
        ];
        
        teacherTableBody.innerHTML = teachersData.map(teacher => `
            <tr>
                <td>${teacher.id}</td>
                <td>${teacher.name}</td>
                <td>${teacher.role}</td>
                <td>${teacher.department}</td>
                <td>${teacher.contact}</td>
                <td>${teacher.subjects}</td>
                <td>
                    <button class="action-btn edit" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="action-btn view" title="Ver"><i class="fas fa-eye"></i></button>
                    <button class="action-btn delete" title="Eliminar"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `).join('');
        
        // Add event listeners to action buttons
        addActionButtonListeners('teacher');
    }
    
    // Load guardians data
    const guardianTableBody = document.getElementById('guardian-table-body');
    if (guardianTableBody) {
        const guardiansData = [
            { id: 'GRD001', name: 'António Silva', student: 'João Silva', relation: 'Pai', contact: '+244 923 456 789', email: 'antonio.silva@email.com' },
            { id: 'GRD002', name: 'Luísa Santos', student: 'Maria Santos', relation: 'Mãe', contact: '+244 923 456 790', email: 'luisa.santos@email.com' },
            { id: 'GRD003', name: 'Manuel Oliveira', student: 'Pedro Oliveira', relation: 'Pai', contact: '+244 923 456 791', email: 'manuel.oliveira@email.com' },
            { id: 'GRD004', name: 'Teresa Costa', student: 'Ana Costa', relation: 'Mãe', contact: '+244 923 456 792', email: 'teresa.costa@email.com' }
        ];
        
        guardianTableBody.innerHTML = guardiansData.map(guardian => `
            <tr>
                <td>${guardian.id}</td>
                <td>${guardian.name}</td>
                <td>${guardian.student}</td>
                <td>${guardian.relation}</td>
                <td>${guardian.contact}</td>
                <td>${guardian.email}</td>
                <td>
                    <button class="action-btn edit" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="action-btn view" title="Ver"><i class="fas fa-eye"></i></button>
                    <button class="action-btn delete" title="Eliminar"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `).join('');
        
        // Add event listeners to action buttons
        addActionButtonListeners('guardian');
    }
    
    // Load messages data
    const messageList = document.getElementById('message-list');
    const messageContent = document.getElementById('message-content');
    
    if (messageList && messageContent) {
        const messagesData = [
            { 
                id: 'MSG001', 
                sender: 'António Silva', 
                preview: 'Bom dia, gostaria de agendar uma reunião...', 
                time: '10:30',
                isUnread: false,
                isActive: true,
                avatar: 'https://via.placeholder.com/40',
                role: 'Encarregado de Educação - João Silva',
                messages: [
                    { text: 'Bom dia, gostaria de agendar uma reunião para discutir o progresso do João. Quando estaria disponível?', time: '10:30', type: 'received' },
                    { text: 'Bom dia Sr. António. Estou disponível na quinta-feira às 14h ou na sexta-feira às 10h. Qual prefere?', time: '10:45', type: 'sent' },
                    { text: 'Quinta-feira às 14h seria perfeito. Obrigado pela disponibilidade.', time: '11:02', type: 'received' },
                    { text: 'Excelente. Vou agendar a reunião para quinta-feira às 14h. Até lá!', time: '11:10', type: 'sent' }
                ]
            },
            { 
                id: 'MSG002', 
                sender: 'Luísa Santos', 
                preview: 'Sobre a avaliação da Maria...', 
                time: '09:15',
                isUnread: true,
                isActive: false,
                avatar: 'https://via.placeholder.com/40',
                role: 'Encarregada de Educação - Maria Santos',
                messages: [
                    { text: 'Bom dia, gostaria de saber mais detalhes sobre a avaliação da Maria no último teste de matemática.', time: '09:15', type: 'received' }
                ]
            },
            { 
                id: 'MSG003', 
                sender: 'Manuel Oliveira', 
                preview: 'Obrigado pela informação...', 
                time: 'Ontem',
                isUnread: false,
                isActive: false,
                avatar: 'https://via.placeholder.com/40',
                role: 'Encarregado de Educação - Pedro Oliveira',
                messages: [
                    { text: 'Obrigado pela informação sobre as aulas de recuperação. O Pedro irá participar.', time: 'Ontem', type: 'received' },
                    { text: 'Ótimo! As aulas serão às terças e quintas, das 15h às 16h30.', time: 'Ontem', type: 'sent' }
                ]
            },
            { 
                id: 'MSG004', 
                sender: 'Teresa Costa', 
                preview: 'Quando será a próxima reunião de pais?', 
                time: 'Ontem',
                isUnread: false,
                isActive: false,
                avatar: 'https://via.placeholder.com/40',
                role: 'Encarregada de Educação - Ana Costa',
                messages: [
                    { text: 'Quando será a próxima reunião de pais? Preciso organizar minha agenda.', time: 'Ontem', type: 'received' },
                    { text: 'A próxima reunião de pais está agendada para o dia 15 de abril, das 14h às 18h.', time: 'Ontem', type: 'sent' }
                ]
            }
        ];
        
        // Render message list
        messageList.innerHTML = messagesData.map(message => `
            <div class="message-item ${message.isActive ? 'active' : ''} ${message.isUnread ? 'unread' : ''}" data-id="${message.id}">
                <div class="message-avatar">
                    <img src="${message.avatar}" alt="${message.sender}">
                </div>
                <div class="message-preview">
                    <div class="message-header">
                        <h4>${message.sender}</h4>
                        <span class="message-time">${message.time}</span>
                    </div>
                    <p>${message.preview}</p>
                </div>
            </div>
        `).join('');
        
        // Render active message content
        const activeMessage = messagesData.find(message => message.isActive);
        if (activeMessage) {
            renderMessageContent(activeMessage);
        }
        
        // Add click event to message items
        const messageItems = document.querySelectorAll('.message-item');
        messageItems.forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all items
                messageItems.forEach(el => el.classList.remove('active'));
                
                // Add active class to clicked item
                this.classList.add('active');
                
                // Remove unread class if present
                this.classList.remove('unread');
                
                // Find and render the selected message
                const messageId = this.dataset.id;
                const selectedMessage = messagesData.find(message => message.id === messageId);
                if (selectedMessage) {
                    renderMessageContent(selectedMessage);
                }
            });
        });
        
        // Function to render message content
        function renderMessageContent(message) {
            messageContent.innerHTML = `
                <div class="message-header-bar">
                    <div class="contact-info">
                        <img src="${message.avatar}" alt="${message.sender}">
                        <div>
                            <h3>${message.sender}</h3>
                            <p>${message.role}</p>
                        </div>
                    </div>
                    <div class="message-actions">
                        <button class="action-btn" title="Arquivar"><i class="fas fa-archive"></i></button>
                        <button class="action-btn" title="Eliminar"><i class="fas fa-trash"></i></button>
                        <button class="action-btn" title="Marcar como não lida"><i class="fas fa-envelope"></i></button>
                    </div>
                </div>
                <div class="message-body">
                    ${message.messages.map(msg => `
                        <div class="message-bubble ${msg.type}">
                            <div class="message-text">
                                <p>${msg.text}</p>
                            </div>
                            <div class="message-time">${msg.time}</div>
                        </div>
                    `).join('')}
                </div>
                <div class="message-input">
                    <textarea placeholder="Escreva sua mensagem..."></textarea>
                    <button class="send-btn"><i class="fas fa-paper-plane"></i></button>
                </div>
            `;
            
            // Add send message functionality
            const sendBtn = messageContent.querySelector('.send-btn');
            const textarea = messageContent.querySelector('textarea');
            
            sendBtn.addEventListener('click', function() {
                const messageText = textarea.value.trim();
                if (messageText) {
                    const messageBody = messageContent.querySelector('.message-body');
                    const now = new Date();
                    const timeString = `${now.getHours()}:${now.getMinutes().toString().padStart(2, '0')}`;
                    
                    const newMessage = document.createElement('div');
                    newMessage.className = 'message-bubble sent';
                    newMessage.innerHTML = `
                        <div class="message-text">
                            <p>${messageText}</p>
                        </div>
                        <div class="message-time">${timeString}</div>
                    `;
                    
                    messageBody.appendChild(newMessage);
                    textarea.value = '';
                    
                    // Scroll to bottom of message body
                    messageBody.scrollTop = messageBody.scrollHeight;
                }
            });
            
            // Allow sending message with Enter key
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendBtn.click();
                }
            });
        }
    }
    
    // Load notices data
    const noticesContainer = document.getElementById('notices-container');
    if (noticesContainer) {
        const noticesData = [
            { 
                id: 'NOT001', 
                title: 'Reunião de Pais e Professores', 
                type: 'important', 
                content: 'Informamos que a próxima reunião de pais e professores será realizada no dia 15 de abril de 2025, das 14h às 18h. A presença de todos os encarregados de educação é fundamental.',
                author: 'Direção',
                date: '01/04/2025'
            },
            { 
                id: 'NOT002', 
                title: 'Calendário de Exames Finais', 
                type: 'academic', 
                content: 'O calendário de exames finais para o semestre atual já está disponível. Os alunos podem consultar as datas e horários no portal do estudante ou no quadro de avisos da secretaria.',
                author: 'Coordenação Pedagógica',
                date: '30/03/2025'
            },
            { 
                id: 'NOT003', 
                title: 'Feira de Ciências', 
                type: 'event', 
                content: 'A Feira de Ciências anual será realizada no dia 22 de abril de 2025. Os alunos interessados em participar devem inscrever seus projetos até o dia 10 de abril com seus professores de ciências.',
                author: 'Departamento de Ciências',
                date: '28/03/2025'
            }
        ];
        
        noticesContainer.innerHTML = noticesData.map(notice => `
            <div class="notice-card ${notice.type}">
                <div class="notice-header">
                    <h3>${notice.title}</h3>
                    <span class="notice-badge ${notice.type === 'academic' ? 'academic' : notice.type === 'event' ? 'event' : ''}">${notice.type === 'important' ? 'Importante' : notice.type === 'academic' ? 'Acadêmico' : 'Evento'}</span>
                </div>
                <div class="notice-body">
                    <p>${notice.content}</p>
                </div>
                <div class="notice-footer">
                    <div class="notice-info">
                        <span><i class="fas fa-user"></i> ${notice.author}</span>
                        <span><i class="fas fa-calendar"></i> ${notice.date}</span>
                    </div>
                    <div class="notice-actions">
                        <button class="action-btn edit" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="action-btn delete" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    // Load transactions data
    const transactionTableBody = document.getElementById('transaction-table-body');
    if (transactionTableBody) {
        const transactionsData = [
            { id: 'TRX001', date: '01/04/2025', description: 'Mensalidade - Turma 10', category: 'Mensalidades', type: 'income', amount: '$5,000', status: 'completed' },
            { id: 'TRX002', date: '28/03/2025', description: 'Pagamento de Salários - Março', category: 'Salários', type: 'expense', amount: '$12,000', status: 'completed' },
            { id: 'TRX003', date: '25/03/2025', description: 'Compra de Material Escolar', category: 'Material Escolar', type: 'expense', amount: '$2,500', status: 'completed' },
            { id: 'TRX004', date: '20/03/2025', description: 'Mensalidade - Turma 11', category: 'Mensalidades', type: 'income', amount: '$4,800', status: 'completed' },
            { id: 'TRX005', date: '15/03/2025', description: 'Manutenção do Laboratório', category: 'Manutenção', type: 'expense', amount: '$1,800', status: 'pending' }
        ];
        
        transactionTableBody.innerHTML = transactionsData.map(transaction => {
            let statusText, statusClass;
            
            switch(transaction.status) {
                case 'completed':
                    statusText = 'Concluído';
                    statusClass = 'completed';
                    break;
                case 'pending':
                    statusText = 'Pendente';
                    statusClass = 'pending';
                    break;
                default:
                    statusText = 'Desconhecido';
                    statusClass = '';
            }
            
            return `
                <tr>
                    <td>${transaction.id}</td>
                    <td>${transaction.date}</td>
                    <td>${transaction.description}</td>
                    <td>${transaction.category}</td>
                    <td><span class="transaction-type ${transaction.type}">${transaction.type === 'income' ? 'Receita' : 'Despesa'}</span></td>
                    <td>${transaction.amount}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td>
                        <button class="action-btn edit" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="action-btn view" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="action-btn delete" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        }).join('');
        
        // Add event listeners to action buttons
        addActionButtonListeners('transaction');
    }
}

// Add event listeners to action buttons
function addActionButtonListeners(type) {
    const editButtons = document.querySelectorAll(`#${type}-table-body .edit`);
    const viewButtons = document.querySelectorAll(`#${type}-table-body .view`);
    const deleteButtons = document.querySelectorAll(`#${type}-table-body .delete`);
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id = row.cells[0].textContent;
            alert(`Editar ${type} com ID: ${id}`);
            
            // In a real application, this would open the edit modal with the data
            if (type === 'student') {
                document.getElementById('student-modal').classList.add('active');
                document.getElementById('student-modal-title').textContent = 'Editar Aluno';
                // Populate form with data
            } else if (type === 'teacher') {
                document.getElementById('teacher-modal').classList.add('active');
                document.getElementById('teacher-modal-title').textContent = 'Editar Funcionário';
                // Populate form with data
            }
        });
    });
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id = row.cells[0].textContent;
            alert(`Ver detalhes de ${type} com ID: ${id}`);
            // In a real application, this would open a view modal or navigate to a details page
        });
    });
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const id = row.cells[0].textContent;
            if (confirm(`Tem certeza que deseja eliminar ${type} com ID: ${id}?`)) {
                alert(`${type} eliminado com sucesso!`);
                row.remove();
                // In a real application, this would send a delete request to the server
            }
        });
    });
}

// Form submissions
function initFormSubmissions() {
    // Student form submission
    const studentForm = document.getElementById('student-form');
    if (studentForm) {
        studentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const name = document.getElementById('student-name').value;
            const studentClass = document.getElementById('student-class').value;
            const gender = document.getElementById('student-gender').value;
            const dob = document.getElementById('student-dob').value;
            const contact = document.getElementById('student-contact').value;
            const email = document.getElementById('student-email').value;
            const address = document.getElementById('student-address').value;
            
            // In a real application, this would send the data to the server
            alert(`Aluno ${name} salvo com sucesso!`);
            
            // Close modal
            document.getElementById('student-modal').classList.remove('active');
            
            // Add to table (in a real application, this would be done after server confirmation)
            const studentTableBody = document.getElementById('student-table-body');
            if (studentTableBody) {
                const newRow = document.createElement('tr');
                const newId = 'STU' + (Math.floor(Math.random() * 900) + 100);
                
                newRow.innerHTML = `
                    <td>${newId}</td>
                    <td>${name}</td>
                    <td>${studentClass}</td>
                    <td>${gender}</td>
                    <td>${contact}</td>
                    <td>N/A</td>
                    <td>
                        <button class="action-btn edit" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="action-btn view" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="action-btn delete" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                
                studentTableBody.appendChild(newRow);
                
                // Add event listeners to new buttons
                const editBtn = newRow.querySelector('.edit');
                const viewBtn = newRow.querySelector('.view');
                const deleteBtn = newRow.querySelector('.delete');
                
                editBtn.addEventListener('click', function() {
                    alert(`Editar aluno com ID: ${newId}`);
                    document.getElementById('student-modal').classList.add('active');
                    document.getElementById('student-modal-title').textContent = 'Editar Aluno';
                });
                
                viewBtn.addEventListener('click', function() {
                    alert(`Ver detalhes de aluno com ID: ${newId}`);
                });
                
                deleteBtn.addEventListener('click', function() {
                    if (confirm(`Tem certeza que deseja eliminar aluno com ID: ${newId}?`)) {
                        alert(`Aluno eliminado com sucesso!`);
                        newRow.remove();
                    }
                });
            }
        });
    }
    
    // Teacher form submission
    const teacherForm = document.getElementById('teacher-form');
    if (teacherForm) {
        teacherForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const name = document.getElementById('teacher-name').value;
            const role = document.getElementById('teacher-role').value;
            const department = document.getElementById('teacher-department').value;
            const experience = document.getElementById('teacher-experience').value;
            const contact = document.getElementById('teacher-contact').value;
            const email = document.getElementById('teacher-email').value;
            const subjects = document.getElementById('teacher-subjects').value;
            
            // In a real application, this would send the data to the server
            alert(`Funcionário ${name} salvo com sucesso!`);
            
            // Close modal
            document.getElementById('teacher-modal').classList.remove('active');
            
            // Add to table (in a real application, this would be done after server confirmation)
            const teacherTableBody = document.getElementById('teacher-table-body');
            if (teacherTableBody) {
                const newRow = document.createElement('tr');
                const newId = 'TCH' + (Math.floor(Math.random() * 900) + 100);
                
                newRow.innerHTML = `
                    <td>${newId}</td>
                    <td>${name}</td>
                    <td>${role}</td>
                    <td>${department}</td>
                    <td>${contact}</td>
                    <td>${subjects}</td>
                    <td>
                        <button class="action-btn edit" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="action-btn view" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="action-btn delete" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                
                teacherTableBody.appendChild(newRow);
                
                // Add event listeners to new buttons
                const editBtn = newRow.querySelector('.edit');
                const viewBtn = newRow.querySelector('.view');
                const deleteBtn = newRow.querySelector('.delete');
                
                editBtn.addEventListener('click', function() {
                    alert(`Editar funcionário com ID: ${newId}`);
                    document.getElementById('teacher-modal').classList.add('active');
                    document.getElementById('teacher-modal-title').textContent = 'Editar Funcionário';
                });
                
                viewBtn.addEventListener('click', function() {
                    alert(`Ver detalhes de funcionário com ID: ${newId}`);
                });
                
                deleteBtn.addEventListener('click', function() {
                    if (confirm(`Tem certeza que deseja eliminar funcionário com ID: ${newId}?`)) {
                        alert(`Funcionário eliminado com sucesso!`);
                        newRow.remove();
                    }
                });
            }
        });
    }
    
    // Guardian form submission
    const guardianForm = document.getElementById('guardian-form');
    if (guardianForm) {
        guardianForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const name = document.getElementById('guardian-name').value;
            const student = document.getElementById('guardian-student').value;
            const relation = document.getElementById('guardian-relation').value;
            const contact = document.getElementById('guardian-contact').value;
            const email = document.getElementById('guardian-email').value;
            const address = document.getElementById('guardian-address').value;
            
            // In a real application, this would send the data to the server
            alert(`Encarregado ${name} salvo com sucesso!`);
            
            // Close modal
            document.getElementById('guardian-modal').classList.remove('active');
            
            // Add to table (in a real application, this would be done after server confirmation)
            const guardianTableBody = document.getElementById('guardian-table-body');
            if (guardianTableBody) {
                const newRow = document.createElement('tr');
                const newId = 'GRD' + (Math.floor(Math.random() * 900) + 100);
                
                newRow.innerHTML = `
                    <td>${newId}</td>
                    <td>${name}</td>
                    <td>${student}</td>
                    <td>${relation}</td>
                    <td>${contact}</td>
                    <td>${email}</td>
                    <td>
                        <button class="action-btn edit" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="action-btn view" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="action-btn delete" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                
                guardianTableBody.appendChild(newRow);
                
                // Add event listeners to new buttons
                const editBtn = newRow.querySelector('.edit');
                const viewBtn = newRow.querySelector('.view');
                const deleteBtn = newRow.querySelector('.delete');
                
                editBtn.addEventListener('click', function() {
                    alert(`Editar encarregado com ID: ${newId}`);
                });
                
                viewBtn.addEventListener('click', function() {
                    alert(`Ver detalhes de encarregado com ID: ${newId}`);
                });
                
                deleteBtn.addEventListener('click', function() {
                    if (confirm(`Tem certeza que deseja eliminar encarregado com ID: ${newId}?`)) {
                        alert(`Encarregado eliminado com sucesso!`);
                        newRow.remove();
                    }
                });
            }
        });
    }
}

// Search and filter functionality
function initSearchAndFilter() {
    // Student search
    const studentSearch = document.getElementById('student-search');
    if (studentSearch) {
        studentSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#student-table-body tr');
            
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const studentClass = row.cells[2].textContent.toLowerCase();
                
                if (name.includes(searchTerm) || studentClass.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Class filter
    const classFilter = document.getElementById('class-filter');
    if (classFilter) {
        classFilter.addEventListener('change', function() {
            const filterValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#student-table-body tr');
            
            if (filterValue === '') {
                rows.forEach(row => {
                    row.style.display = '';
                });
                return;
            }
            
            rows.forEach(row => {
                const studentClass = row.cells[2].textContent.toLowerCase();
                
                if (studentClass.includes(filterValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Teacher search
    const teacherSearch = document.getElementById('teacher-search');
    if (teacherSearch) {
        teacherSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#teacher-table-body tr');
            
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const department = row.cells[3].textContent.toLowerCase();
                
                if (name.includes(searchTerm) || department.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Department filter
    const departmentFilter = document.getElementById('department-filter');
    if (departmentFilter) {
        departmentFilter.addEventListener('change', function() {
            const filterValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#teacher-table-body tr');
            
            if (filterValue === '') {
                rows.forEach(row => {
                    row.style.display = '';
                });
                return;
            }
            
            rows.forEach(row => {
                const department = row.cells[3].textContent.toLowerCase();
                
                if (department.includes(filterValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Transaction filters
    const filterTransactionsBtn = document.getElementById('filter-transactions-btn');
    if (filterTransactionsBtn) {
        filterTransactionsBtn.addEventListener('click', function() {
            const typeFilter = document.getElementById('transaction-type-filter').value;
            const categoryFilter = document.getElementById('transaction-category-filter').value;
            const dateFrom = new Date(document.getElementById('date-from').value);
            const dateTo = new Date(document.getElementById('date-to').value);
            
            const rows = document.querySelectorAll('#transaction-table-body tr');
            
            rows.forEach(row => {
                const date = new Date(row.cells[1].textContent.split('/').reverse().join('-'));
                const category = row.cells[3].textContent.toLowerCase();
                const type = row.cells[4].textContent.toLowerCase();
                
                let showRow = true;
                
                // Check type filter
                if (typeFilter !== 'all' && !type.includes(typeFilter === 'income' ? 'receita' : 'despesa')) {
                    showRow = false;
                }
                
                // Check category filter
                if (categoryFilter !== 'all' && !category.toLowerCase().includes(categoryFilter)) {
                    showRow = false;
                }
                
                // Check date range
                if (date < dateFrom || date > dateTo) {
                    showRow = false;
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        });
    }
}