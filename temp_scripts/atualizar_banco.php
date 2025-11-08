<?php
include 'config.php';

try {
    $pdo->exec("ALTER TABLE agendamentos ADD COLUMN cor VARCHAR(50)");
    echo "<p style='color:green;'>✓ Coluna 'cor' adicionada com sucesso!</p>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'duplicate column name: cor') !== false) {
        echo "<p style='color:orange;'>! Coluna 'cor' já existia.</p>";
    } else {
        die("<p style='color:red;'>Erro ao adicionar 'cor': " . $e->getMessage() . "</p>");
    }
}
echo "<h2>Atualização concluída. Pode apagar este ficheiro.</h2>";
?>