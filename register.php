<?php

include 'config.php';

$mensagem = ''; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['senha'])) {
        $mensagem = "Por favor, preencha todos os campos.";
    } else {
        $nome = $_POST['nome'];
        $email = $_POST['email'];

        $senha_hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);

        try {

            $stmt_check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt_check->execute([$email]);
            
            if ($stmt_check->fetch()) {
                $mensagem = "Este email já está cadastrado. Tente outro.";
            } else {
 
                $sql = "INSERT INTO users (nome, email, senha) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$nome, $email, $senha_hash])) {
                    $mensagem = "Usuário cadastrado com sucesso! Você pode fazer login.";
                } else {
                    $mensagem = "Erro ao cadastrar usuário.";
                }
            }
        } catch (PDOException $e) {
            $mensagem = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Projeto PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h2>Cadastro de Usuário</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="message <?php echo (strpos($mensagem, 'sucesso') !== false) ? 'success' : 'error'; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div>
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit">Cadastrar</button>
        </form>

        <div class="login-link">
            <p>Já tem uma conta? <a href="login.php">Faça login aqui</a></p>
        </div>
    </div>

</body>
</html>