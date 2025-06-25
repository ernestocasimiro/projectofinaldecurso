// Adicionar esta função ao final do arquivo dashboard-data.js

// Função para atualizar o menu lateral em todas as páginas
function updateSidebarMenu() {
    // Verificar se o item de menu para minipautas já existe
    const menuItems = document.querySelectorAll('.sidebar .menu ul li');
    let minipautasExists = false;
    
    menuItems.forEach(item => {
        const link = item.querySelector('a');
        if (link && link.getAttribute('href') === 'minipautas.html') {
            minipautasExists = true;
        }
    });
    
    // Se o item de menu não existir, adicioná-lo
    if (!minipautasExists) {
        const menu = document.querySelector('.sidebar .menu ul');
        if (menu) {
            // Criar o novo item de menu
            const newMenuItem = document.createElement('li');
            
            // Verificar se estamos na página de minipautas
            const isActive = window.location.href.includes('minipautas.html');
            if (isActive) {
                newMenuItem.className = 'active';
            }
            
            newMenuItem.innerHTML = `
                <a href="minipautas.html">
                    <span class="material-symbols-outlined">summarize</span>
                    <span class="menu-text">Minipautas</span>
                </a>
            `;
            
            // Adicionar o item antes do último item (geralmente "Configurações")
            const lastItem = menu.querySelector('li:last-child');
            if (lastItem) {
                menu.insertBefore(newMenuItem, lastItem);
            } else {
                menu.appendChild(newMenuItem);
            }
        }
    }
}

// Adicionar a chamada da função à inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Outras inicializações...
    updateSidebarMenu();
});

// Adicionar a função ao objeto window.dashboardData
window.dashboardData.updateSidebarMenu = updateSidebarMenu;