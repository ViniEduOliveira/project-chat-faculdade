// Aguarda o carregamento completo da página para executar o código
document.addEventListener('DOMContentLoaded', () => {

    // Pega os elementos do HTML com os quais vamos interagir
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const chatBox = document.getElementById('chat-box');

    // Adiciona um "ouvinte" para o evento de envio do formulário
    messageForm.addEventListener('submit', (event) => {
        // Previne o comportamento padrão do formulário, que é recarregar a página
        event.preventDefault();

        // Pega a mensagem digitada pelo usuário e remove espaços em branco
        const userMessage = messageInput.value.trim();

        // Se a mensagem não estiver vazia
        if (userMessage) {
            // 1. Adiciona a mensagem do usuário ao chat
            addMessage(userMessage, 'user-message');

            // 2. Limpa o campo de input
            messageInput.value = '';
            
            // 3. Envia a mensagem para o backend (PHP)
            fetch('chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `message=${encodeURIComponent(userMessage)}`
            })
            .then(response => response.text()) // Converte a resposta do PHP para texto
            .then(botResponse => {
                // 4. Adiciona a resposta do bot (vinda do PHP) ao chat
                addMessage(botResponse, 'bot-message');
            })
            .catch(error => {
                // Em caso de erro, exibe uma mensagem de falha
                console.error('Erro:', error);
                addMessage('Desculpe, ocorreu um erro ao contatar o servidor.', 'bot-message');
            });
        }
    });

    // Função auxiliar para criar e adicionar os balões de mensagem na tela
    function addMessage(text, className) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${className}`;
        
        const messageP = document.createElement('p');
        messageP.textContent = text;
        
        messageDiv.appendChild(messageP);
        chatBox.appendChild(messageDiv);
        
        // Rola a caixa de chat para a última mensagem
        chatBox.scrollTop = chatBox.scrollHeight;
    }
});