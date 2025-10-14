<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'database.php';

// --- ETAPA 1: RECEBER A MENSAGEM DO USUÁRIO ---
$userMessage = '';
// Verifica se o campo 'message' foi enviado via POST
if (isset($_POST['message'])) {
    $userMessage = trim($_POST['message']);
}

// Se não houver mensagem, encerra o script.
if (empty($userMessage)) {
    die('Nenhuma mensagem recebida.');
}

// --- ETAPA 2: CONSUMIR A API DE IA (GEMINI) USANDO cURL ---
// Substitua 'SUA_API_KEY' pela chave que você gerar no Google AI Studio
$apiKey = 'SUA_API_KEY';
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $apiKey;

// Prepara os dados para enviar à API no formato JSON
$data = [
    'contents' => [
        [
            'parts' => [
                ['text' => $userMessage]
            ]
        ]
    ]
];
$jsonData = json_encode($data);

// Inicia a sessão cURL
$ch = curl_init($apiUrl);

// Configura as opções do cURL para a requisição HTTP
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
curl_setopt($ch, CURLOPT_POST, true); // Define o método como POST
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Define o corpo da requisição
curl_setopt($ch, CURLOPT_HTTPHEADER, [ // Define os cabeçalhos
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
]);

// Executa a requisição e armazena a resposta
$response = curl_exec($ch);
// Fecha a sessão cURL
curl_close($ch);

// --- ETAPA 3: PROCESSAR A RESPOSTA DA IA ---
$botResponse = 'Desculpe, não consegui processar sua pergunta.'; // Mensagem padrão de erro

// Decodifica a resposta JSON da API
$responseData = json_decode($response, true);

// Extrai o texto da resposta da IA
if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    $botResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
}

// --- ETAPA 4: SALVAR NO BANCO DE DADOS (PONTO ADICIONAL) ---
try {
    // Prepara a instrução SQL para evitar injeção de SQL
    $sql = "INSERT INTO historico (pergunta, resposta) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // Executa a instrução com os dados da conversa
    $stmt->execute([$userMessage, $botResponse]);

} catch (PDOException $e) {
    // Em caso de erro no banco, você pode logar o erro ou lidar com ele.
    // Por simplicidade, não faremos nada aqui para não interromper o chat.
}

// --- ETAPA 5: ENVIAR A RESPOSTA DE VOLTA PARA O JAVASCRIPT ---
echo $botResponse;
?>
