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

        <!--- 
            1 - Deixar a tela mais bonita - Heitor
            2 - Colocar sugestões de foto de perfil - Vinicius 
        -->
        <main>
            <p class="titulo-login"> Seja Bem-Vindo à DolphinIA</p>
            <div class="login-page">
                <img src="Imagens/logo.png" alt="">
                <form action = "login.php" method = "POST">
                    <p>Cadastra-se em nosso Site Oficial</p>
                    <label for="nome_usuario">Digite seu nome:</label>
                    <input type="text" id="nome_usuario" name="nome_usuario" required>
                    <button type="submit">Cadastrar</button>
                </form>
            </div>
        </main>


<?php include 'componentes/footer.php'; ?>