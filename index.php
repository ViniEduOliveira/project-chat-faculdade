<?php include 'componentes/header.php';?>


    <main class="chat-container">

    <!---
        1 - Colocar nossa logo de fundo - Heitor
        2 - Adicionar a IA - Todos
        3 - Banco de dados - Todos
    -->
            <div id="chat-box">
                <div class="message bot-message">
                    <?php
                        if (verificar_usuario()) {
                            echo 'Olá, ' . htmlspecialchars($_SESSION['usuario']) . '! Como posso ajudar você hoje?';
                        } else {
                            echo 'Olá! Como posso ajudar você hoje?';
                        }
                    ?>
                </div>
            </div>

            <form id="message-form">
                <input type="text" id="message-input" placeholder="Digite sua pergunta..." required>
                <button type="submit">Enviar</button>
            </form>
    </main>


        


<?php include 'componentes/footer.php'; ?>

