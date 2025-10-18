<?php
require_once 'database.php';

// Detecta se está sendo executado via POST (web) ou CLI (index.php)
if (php_sapi_name() === 'cli') {
    $userMessage = $argv[1] ?? '';
} else {
    $userMessage = $_POST['message'] ?? '';
}

$userMessage = trim($userMessage);
if (empty($userMessage)) {
    die("Mensagem vazia.");
}

// Configuração da API
$apiKey = 'AIzaSyDOdZHUDTk-lB7GC0fs0vma8w1Y6IIbjZw';
$model = 'gemini-2.5-flash-lite';
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

// Monta a requisição
$requestData = [
    'contents' => [[ 'parts' => [['text' => $userMessage]] ]]
];
$jsonData = json_encode($requestData);

// Executa cURL
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $jsonData,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_SSL_VERIFYPEER => false
]);
$response = curl_exec($ch);
curl_close($ch);

// Trata a resposta
$responseData = json_decode($response, true);
$botResponse = 'Desculpe, não consegui processar sua pergunta.';

if (isset($responseData['candidates'][0]['content']['parts'])) {
    $parts = $responseData['candidates'][0]['content']['parts'];
    $texts = [];
    foreach ($parts as $part) {
        if (isset($part['text'])) $texts[] = $part['text'];
    }
    if (!empty($texts)) $botResponse = implode("\n", $texts);
}

// Salva no banco
try {
    $sql = "INSERT INTO historico (pergunta, resposta) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userMessage, $botResponse]);
} catch (PDOException $e) {
    error_log("Erro ao salvar no banco: " . $e->getMessage());
}

echo $botResponse;
?>
