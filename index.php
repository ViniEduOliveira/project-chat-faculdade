<?php
// É FUNDAMENTAL iniciar a sessão no topo do arquivo, antes de qualquer HTML.
session_start();

// Inclui o arquivo de conexão com o banco de dados.
require_once 'database.php';

// Inicializa o histórico na sessão se ele ainda não existir.
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Verifica se o formulário foi enviado (se a requisição é do tipo POST).
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty(trim($_POST['message']))) {
    
    // --- ETAPA 1: RECEBER A MENSAGEM DO USUÁRIO ---
    $userMessage = trim($_POST['message']);

    // Adiciona a mensagem do usuário ao histórico da sessão para exibição imediata.
    $_SESSION['chat_history'][] = ['type' => 'user', 'text' => $userMessage];

    // --- ETAPA 2: CONSUMIR A API DO GEMINI USANDO cURL ---
    //  A aplicação deve consumir uma API de IA via PHP (requisição HTTP usando cURL).
    $apiKey = 'SUA_API_KEY'; // <-- COLOQUE SUA CHAVE DA API AQUI
    $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $apiKey;

    $data = ['contents' => [['parts' => [['text' => $userMessage]]]]];
    $jsonData = json_encode($data);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    // --- ETAPA 3: PROCESSAR A RESPOSTA DA IA ---
    $botResponse = 'Desculpe, não consegui processar sua pergunta.';
    $responseData = json_decode($response, true);
    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $botResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
    }

    // Adiciona a resposta do bot ao histórico da sessão.
    $_SESSION['chat_history'][] = ['type' => 'bot', 'text' => $botResponse];
    
    // --- ETAPA 4: SALVAR NO BANCO DE DADOS (PONTO ADICIONAL) ---
    //  Ponto adicional: salvar no banco de dados MySQL perguntas, respostas ou histórico de interações.
    try {
        $sql = "INSERT INTO historico (pergunta, resposta) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userMessage, $botResponse]);
    } catch (PDOException $e) {
        // Lidar com o erro de banco de dados se necessário.
    }

    // --- ETAPA 5: REDIRECIONAR PARA EVITAR REENVIO DO FORMULÁRIO ---
    // Este padrão (Post/Redirect/Get) evita que a mesma mensagem seja enviada novamente se o usuário atualizar a página.
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>ChatBot</title>
</head>
<body>
    <aside class="sidebar">
        </aside>
    <div class="main-content">
        <header class="header">
            </header>

        <main class="chat-container">
            <div id="chat-box">
                <?php if (empty($_SESSION['chat_history'])): ?>
                    <div class="message bot-message">
                        <p>Olá! Como posso ajudar você hoje?</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($_SESSION['chat_history'] as $message): ?>
                    <?php if ($message['type'] === 'user'): ?>
                        <div class="message user-message">
                            <p><?php echo htmlspecialchars($message['text']); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="message bot-message">
                            <p><?php echo htmlspecialchars($message['text']); ?></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <form id="message-form" action="index.php" method="POST">
                <input type="text" id="message-input" name="message" placeholder="Digite sua pergunta..." required autocomplete="off">
                <button type="submit">Enviar</button>
            </form>
        </main>
        
        <footer class="footer">
            <p>&copy; 2025 ChatBot Site. Todos os direitos reservados.</p>
        </footer> 
    </div>
</body>
</html>