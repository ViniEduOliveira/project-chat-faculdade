<?php

# "chamo" o add-on do phpdotenv e coloco ele em uma váriavel, após isso, eu o carrego como parte da página  
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

$dotenv->load();

# "chamo" o arquivo com a lógica do banco de dados paara guardar o histórico do chat 
require_once 'logica/database.php';

# Faço uma verificação condicional, onde, caso a requisição da pergunta seja "POST" e não seja vazia
# É guardado informação para começar a requisição (a "conexão") com a api do Gemini 
if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty(trim($_POST['pergunta']))) {
    $pergunta_recebida = trim($_POST["pergunta"]);
    $chaveApi = $_ENV['GEMINI_CHAVE_API'];
    $modelo = "gemini-2.5-flash-lite";
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent?key={$chaveApi}";

    # Esse é o formato exigido pelo gemini para que o texto do usuário seja lida.
    # A pergunta fica armazenada dentro de arrays (listas) de chave e valor (algo parecido com dicionários em python) 
    $requestData = [
        'contents' => [['parts' => [['text' => $pergunta_recebida ]] ]]
    ];

    # A requisição é "empacotada" em json, formato que possibilita a leitura por parte da IA 
    $jsonData = json_encode($requestData);

    # Aqui começa o preparo do curl, responsável por baixar as informações fornecidas pela API mais abaixo 
    $ch = curl_init($apiUrl);

    # Esse array se refere as opções do curl, nesse caso: 
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,

        # O curl fará a requisição no formato post pois, diferente do get, ele tem um "corpo", já que as informações estão em formato JSON 
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $jsonData,
        # O cabeçalho que indica qual documento a api está recebendo 
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);

    #curl é executado e fechado 
    $resposta = curl_exec($ch);
    curl_close($ch);

    #Váriavel com a resposta daa IA, por padrão, caso a pergunta não seja compreendida, ele responderá de forma padrão 
    $resposta_recebida = 'Peço perdão, não consegui processar sua resposta :(.';

    #Caso a resposta (variavel com as informações trazidas pelo curl) esteja correta, 
    if ($resposta) {
        # As informações serão decodificadas, ou seja "traduzida" de volta para os dados brutos, sem json 
        $respostaData = json_decode($resposta, true);
        # Caso a resposta da IA esteja dentro do formato padrão, a váriavel $resposta_recebida vai ser mudada com a reposta do Gemini 
        if (isset($respostaData['candidates'][0]['content']['parts'][0]['text'])) {
            $resposta_recebida = $respostaData['candidates'][0]['content']['parts'][0]['text'];
    }
}
    # Há uma verificação de excessão, caso haja algum erro ao armazenar o histórico no banco de dados 
    try {
        #a variavel $sql_insert tem um comando sql 
        $sql_insert = "INSERT INTO historico (pergunta, resposta) VALUES (?, ?)";
        # a variavel $stmt representa statement, onde a conexão é preparada e pré-compilada para ser realizada várias vezes comm valores diferentes
        $stmt = $pdo->prepare($sql_insert);
        $stmt->execute([$pergunta_recebida, $resposta_recebida]);
    } catch (PDOException $e) {
        die("Erro ao salvar a conversa: " . $e->getMessage());
    }

header("Location: index.php");
    exit;
}

# o histórico é exibido para o usuario 
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

