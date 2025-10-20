<?php 

    require_once 'logica/funcao.php';
    iniciar_sessao();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        novo_usuario();
        header('Location: index.php');
        exit();
    }

    if (verificar_usuario()) {
        header('location: index.php');
        exit;
    }
?>

<?php include 'componentes/header.php'; ?>

    <!DOCTYPE html>
    <html lang="PT-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <title>Entrar - DolphinIA</title>
    </head>
    <body>
        <!--- 
            1 - Deixar a tela mais bonita - Heitor
            2 - Colocar sugestões de foto de perfil - Vinicius 
        -->

        <div>
            <form action = "login.php" method = "POST">
                <label for="nome_usuario">Digite seu nome:</label>
                <input type="text" id="nome_usuario" name="nome_usuario" required>
                <button type="submit">Salvar</button>
            </form>
        </div>


    </body>

<?php include 'componentes/footer.php'; ?>