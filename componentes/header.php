<?php

    require_once __DIR__ .  '/../logica/funcao.php';
    iniciar_sessao();


    if (isset($_GET['acao']) && $_GET['acao'] == 'sair') {
        sair_usuario();
        
        header('Location: index.php');
        exit();
    }

?>

<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">

    <link rel="icon" href="Imagens/logo.png" type="image/logo-icon">
    <title>DolphinIA</title>
</head>
<body>

    <!---
        1 - Deixar mais organizado, a parte de baixo que é as configurações  - Heitor
        2 - Procurar imagnes para cada função - Vinicius
    -->

    <aside class="sidebar">
        <details class="main-menu-details"> 
            <summary class="container-icon-menu">&#9776;</summary>
            
            <div class="menu-content-wrapper">
                
                <div class="menu-topo">
                    <button type="button">Novo Chat</button>
                    <button type="button">Excluir Chat</button>
                </div>
                
                <div class="menu-rodape">
                    <?php if (verificar_usuario()): ?>
                        <span><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
                        <details>
                            <summary>Configurações</summary>
                            <a href="login.php">ENTRAR</a>
                            <button>CLARO</button>
                            <button>ESCURO</button>
                            <a href="index.php?acao=sair" class="btn-config">SAIR</a>
                        </details>
                    <?php else: ?>
                        <span>Visitante</span>
                        <details>
                            <summary>Configurações</summary>
                            <a href="login.php" class="btn-config">ENTRAR</a>
                            <button>CLARO</button>
                            <button>ESCURO</button>
                        </details>
                    <?php endif; ?>
                </div>

            </div> </details>
    </aside>

    <div class="main-content">
        <header class="header">
            <h1><a href="/index.php">DolphinIA</a></h1>
            <h1><a href="/empresa.php">Quem Somos</a></h1>
        </header>