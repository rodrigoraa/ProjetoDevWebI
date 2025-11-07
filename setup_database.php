<?php
include 'config.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            senha VARCHAR(255) NOT NULL
        );
    ");

    $pdo->exec("DROP TABLE IF EXISTS tarefas;");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS agendamentos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome_animal VARCHAR(100) NOT NULL,
            especie VARCHAR(50) NOT NULL,
            raca VARCHAR(100),
            data_agendamento TEXT NOT NULL, 
            status VARCHAR(50) DEFAULT 'Agendado',
            
            -- Chave estrangeira que liga ao dono (usuário)
            user_id INTEGER, 
            FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE
        );
    ");

    echo "Tabelas 'users' (verificada) e 'agendamentos' (criada) com sucesso!";
    echo "<br>A tabela antiga 'tarefas' foi removida.";

} catch (PDOException $e) {
    die("Erro ao configurar o banco de dados: " . $e.getMessage());
}
?>