<?php

$host = $_ENV['DB_HOST']; // ou o host do seu DB
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER']; // ex: 'root'
$pass = $_ENV['DB_PASS'];

try {
    // Cria uma conexão usando PDO (PHP Data Objects), que é mais seguro
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Configura o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Se a conexão falhar, encerra a execução e exibe o erro
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}
?>