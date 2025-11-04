<?php 

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

$dotenv->load();

require_once 'logica/database.php';

if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty(trim($_POST['pergunta']))) {
    $pergunta_recebida = trim($_POST["pergunta"]);
    $chaveApi = $_ENV['GEMINI_CHAVE_API'];
    $modelo = "gemini-2.5-flash-lite";
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent?key={$chaveApi}";

    $requestData = [
        'contents' => [['parts' => [['text' => $pergunta_recebida ]] ]]
    ];
    $jsonData = json_encode($requestData);

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);

    $resposta = curl_exec($ch);
    curl_close($ch);

    $resposta_recebida = 'Peço perdão, não consegui processar sua resposta :(.';
    if ($resposta) {
        $respostaData = json_decode($resposta, true);
        if (isset($respostaData['candidates'][0]['content']['parts'][0]['text'])) {
            $resposta_recebida = $respostaData['candidates'][0]['content']['parts'][0]['text'];
    }
}

    try {
        $sql_insert = "INSERT INTO historico (pergunta, resposta) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql_insert);
        $stmt->execute([$pergunta_recebida, $resposta_recebida]);
    } catch (PDOException $e) {
        die("Erro ao salvar a conversa: " . $e->getMessage());
    }

header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM historico ORDER BY id ASC";
$query = $pdo->query($sql);
$mensagens = $query->fetchAll(PDO::FETCH_ASSOC);

include 'componentes/header.php';

$parsedown = new Parsedown();

?>


    <main class="chat-container">
        <div id="chat-box">
            
            <?php if (empty($mensagens)): ?>
                <div class="saudacao-inicial">
                    <?php
                        if (verificar_usuario()) {
                            echo 'Olá, ' . htmlspecialchars($_SESSION['usuario']) . '!';
                        } else {
                            echo 'Olá!';
                        }
                    ?>
                </div>
            <?php else: ?>
                <?php foreach ($mensagens as $mensagem): ?>
                    <div class="user-message">
                        <?= htmlspecialchars($mensagem["pergunta"]) ?>
                    </div>  
                    <div class="bot-message">
                        <?php 
                            $htmlResposta = $parsedown->text($mensagem['resposta']); 
                            echo $htmlResposta;
                        ?>
                    </div>  
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

        <form id="message-form" method="POST" action="index.php">
            <input type="text" id="message-input" name="pergunta" placeholder="Digite sua pergunta..." required>
            <button type="submit">Enviar</button>
        </form>
        <p class="footer">A DolphinIA pode cometer erros. Cheque as respostas.</p>
    </main>

