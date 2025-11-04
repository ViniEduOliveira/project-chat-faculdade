<?php

    require_once __DIR__ .  '/../logica/funcao.php';
    iniciar_sessao();


    if (isset($_GET['acao']) && $_GET['acao'] == 'sair') {
        sair_usuario();
        
        header('Location: index.php');
        exit();
    }

    if (isset($_GET['acao']) && $_GET['acao'] == 'excluir_chat') {
        if (isset($pdo)) {
            try {
                $pdo->exec("TRUNCATE TABLE historico");
            } catch (PDOException $e) {
                die("Erro ao excluir o histórico: " . $e->getMessage());
            }
        }
        header('Location: index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">

    <link rel="icon" href="Imagens/logo.png" type="image/logo-icon">
    <title>DolphinIA</title>
</head>
<body>
    <aside class="sidebar">
        <details class="main-menu-details"> 

            <summary class="container-icon-menu">&#9776;</summary>
            
            <div class="menu-content-wrapper">

                <div class="menu-topo">
                    <a href="index.php?acao=excluir_chat"><button type="button">Novo Chat</button></a>
                </div>
                
                <div>
                    </div>

                <div class="menu-rodape">
                    <?php if (verificar_usuario()): ?>
                        <span>Usuário: <?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
                        <details>
                            <summary>Configurações</summary>
                                <a href="index.php?acao=sair" class="btn-config">SAIR</a>
                        </details>

                    <?php else: ?>
                        <span>Usuário: Visitante</span>
                        <details>
                            <summary>Configurações</summary>
                            <a href="login.php" class="btn-config">ENTRAR</a>
                        </details>
                    <?php endif; ?>
                </div>
            </div> 
        </details>
    </aside>

    <div class="main-content">
        <header class="header">
            <h1><a href="/index.php">DolphinIA</a></h1>
            <h1><a href="/empresa.php">Quem Somos</a></h1>
        </header>