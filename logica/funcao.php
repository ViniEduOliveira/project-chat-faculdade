<?php

/*
    Criar os restos das funções para tudo  - Todos
*/

    function iniciar_sessao() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }   
    }

    function verificar_usuario() {
        return isset($_SESSION['usuario']);
    }

    function salvar_usuario() {
        if(!empty($nome)) {
            $_SESSION['usuario'] = trim($nome);
            return true;
        }
        return false;
    }

    function sair_usuario() {
        iniciar_sessao();
        $_SESSION = array();
        session_destroy();
    }

    function novo_usuario() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['nome_usuario'])) {
            $_SESSION['usuario'] = trim($_POST['nome_usuario']);
        }
    }
    
    function excluir_chat() {
        
        
    }


?>