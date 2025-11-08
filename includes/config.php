<?php
$db_path = __DIR__ . '/../database/database.db';

try {
    $pdo = new PDO('sqlite:' . $db_path);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec('PRAGMA foreign_keys = ON;');

} catch (PDOException $e) {
    echo "Falha na conexão com o banco de dados: " . $e->getMessage();
    exit();
}

?>