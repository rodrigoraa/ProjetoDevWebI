<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$nome_usuario = htmlspecialchars($_SESSION['user_name']);

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nome_animal'])) {

    if (empty($_POST['nome_animal']) || empty($_POST['especie']) || empty($_POST['data_agendamento'])) {
        $mensagem = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        $nome_animal = $_POST['nome_animal'];
        $especie = $_POST['especie'];
        $raca = $_POST['raca'] ?? '';
        $data_agendamento = $_POST['data_agendamento'];

        try {
            $sql = "INSERT INTO agendamentos (nome_animal, especie, raca, data_agendamento, user_id) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome_animal, $especie, $raca, $data_agendamento, $user_id]);

            header('Location: index.php?msg=sucesso');
            exit();

        } catch (PDOException $e) {
            $mensagem = "Erro ao registrar agendamento: " . $e->getMessage();
        }
    }
}

if (isset($_GET['msg']) && $_GET['msg'] == 'sucesso') {
    $mensagem = "Agendamento registrado com sucesso!";
}

try {
    $sql_read = "SELECT * FROM agendamentos WHERE user_id = ? ORDER BY data_agendamento ASC";
    $stmt_read = $pdo->prepare($sql_read);
    $stmt_read->execute([$user_id]);
    $agendamentos = $stmt_read->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $agendamentos = [];
    $mensagem = "Erro ao buscar agendamentos: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Agendamentos</title>
    <link rel="stylesheet" href="./style.css">
</head>

<body>

    <div class="container">

        <div class="header">
            <div class="user-info">
                <h1>Clínica de Castração</h1>
                <span>Olá, <?php echo $nome_usuario; ?>!</span>
                <a href="logout.php" class="logout">Sair</a>
            </div>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="message <?php echo (strpos($mensagem, 'Erro') !== false) ? 'error' : ''; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <h2>Registrar Novo Agendamento</h2>
            <form action="index.php" method="POST">
                <div class="form-group">
                    <label for="nome_animal">Nome do Animal: (*)</label>
                    <input type="text" id="nome_animal" name="nome_animal" required>
                </div>
                <div class="form-group">
                    <label for="especie">Espécie: (*)</label>
                    <select id="especie" name="especie" required>
                        <option value="">Selecione...</option>
                        <option value="Cachorro">Cachorro</option>
                        <option value="Gato">Gato</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="raca">Raça: (Opcional)</label>
                    <input type="text" id="raca" name="raca">
                </div>
                <div class="form-group">
                    <label for="data_agendamento">Data da Castração: (*)</label>
                    <input type="date" id="data_agendamento" name="data_agendamento" required>
                </div>
                <button type="submit">Agendar</button>
            </form>
        </div>

        <div class="task-list">
            <h2>Meus Agendamentos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Animal</th>
                        <th>Espécie</th>
                        <th>Raça</th>
                        <th>Data Agendada</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($agendamentos)): ?>
                        <tr>
                            <td colspan="5">Nenhum agendamento encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($agendamentos as $agendamento): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($agendamento['nome_animal']); ?></td>
                                <td><?php echo htmlspecialchars($agendamento['especie']); ?></td>
                                <td><?php echo htmlspecialchars($agendamento['raca'] ?? 'Não informada'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></td>
                                <td class="actions">
                                    <a href="edit.php?id=<?php echo $agendamento['id']; ?>" class="btn-edit">Editar</a>
                                    <a href="delete.php?id=<?php echo $agendamento['id']; ?>" class="btn-delete"
                                        onclick="return confirm('Tem certeza que deseja cancelar este agendamento?');">
                                        Cancelar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>