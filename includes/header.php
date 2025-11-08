<?php

session_start();

include_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$nome_usuario = htmlspecialchars($_SESSION['user_name']);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>

    <div class="container">

        <div class="header">
            <div class="header-info">
                <h1>Clínica de Castração</h1>
                <span>Olá, <?php echo $nome_usuario; ?>!</span>
                <a href="includes/logout.php" class="btn btn-vermelho">Sair</a>
            </div>
        </div>