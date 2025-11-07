<?php
session_start();

include 'config.php';

$mensagem_erro = '';

if (!empty($_POST)) {
    if (empty($_POST['email']) || empty($_POST['senha'])) {
        $mensagem_erro = "Por favor, preencha email e senha.";
    } else {
        $email = $_POST['email'];
        $senha_digitada = $_POST['senha'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($senha_digitada, $user['senha'])) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                
                header('Location: index.php');
                exit();

            } else {
                $mensagem_erro = "Email ou senha inválidos.";
            }

        } catch (PDOException $e) {
            $mensagem_erro = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Projeto PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h2>Login</h2>

        <?php if (!empty($mensagem_erro)): ?>
            <div class="message error">
                <?php echo $mensagem_erro; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit">Entrar</button>
        </form>

        <div class="register-link">
            <p>Não tem uma conta? <a href="register.php">Cadastre-se aqui</a></p>
        </div>
    </div>

</body>
</html>