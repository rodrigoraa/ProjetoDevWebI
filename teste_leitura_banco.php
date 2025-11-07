<?php

include 'config.php';

echo "<h1>Testando o banco 'users'</h1>";

try {

    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($usuarios) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Senha (Hash)</th></tr>";

        foreach ($usuarios as $usuario) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($usuario['id']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['nome']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['email']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['senha']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";

    } else {
        echo "Nenhum usuário encontrado no banco de dados.";
    }

} catch (PDOException $e) {
    die("Erro ao ler o banco de dados: " . $e->getMessage());
}
?>