<?php
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$agendamento = null;
$mensagem_erro = '';

$amanha = date('Y-m-d', strtotime('+1 day'));

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $agendamento_id = $_GET['id'];
    try {
        $sql = "SELECT * FROM agendamentos WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$agendamento_id, $user_id]);
        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$agendamento) {
            $mensagem_erro = "Agendamento não encontrado ou não tem permissão para o editar.";
            $agendamento = null;
        }
    } catch (PDOException $e) {
        $mensagem_erro = "Erro ao procurar o agendamento: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $agendamento_id = $_POST['agendamento_id'];
    $nome_animal = $_POST['nome_animal'];
    $especie = $_POST['especie'];
    $raca = $_POST['raca'] ?? '';
    $data_agendamento = $_POST['data_agendamento'];
    $sexo = $_POST['sexo'];
    $idade = $_POST['idade'] ?? 0;
    $cor = $_POST['cor'] ?? '';

    if (empty($nome_animal) || empty($especie) || empty($data_agendamento) || empty($sexo)) {
        $mensagem_erro = "Por favor, preencha todos os campos obrigatórios (*).";
    } else if ($data_agendamento < $amanha) {
        $mensagem_erro = "Erro: A data do agendamento deve ser uma data futura (a partir de amanhã).";
    } else {
        try {
            $sql_update = "UPDATE agendamentos SET 
                    nome_animal = ?, 
                    especie = ?, 
                    raca = ?, 
                    data_agendamento = ?,
                    sexo = ?,
                    idade = ?,
                    cor = ? 
               WHERE 
                    id = ? AND user_id = ?";

            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([
                $nome_animal,
                $especie,
                $raca,
                $data_agendamento,
                $sexo,
                $idade,
                $cor,
                $agendamento_id,
                $user_id
            ]);

            header('Location: index.php?msg=editado');
            exit();

        } catch (PDOException $e) {
            $mensagem_erro = "Erro ao atualizar o agendamento: " . $e->getMessage();
        }
    }

    if (!empty($mensagem_erro)) {
        $sql_reload = "SELECT * FROM agendamentos WHERE id = ? AND user_id = ?";
        $stmt_reload = $pdo->prepare($sql_reload);
        $stmt_reload->execute([$agendamento_id, $user_id]);
        $agendamento = $stmt_reload->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<title>Editar Agendamento</title>

<div class="form-section">
    <h2>Editar Agendamento</h2>

    <?php if (!empty($mensagem_erro)): ?>
        <div class="message error"><?php echo $mensagem_erro; ?></div>
    <?php endif; ?>

    <?php if ($agendamento): ?>
        <form action="edit.php" method="POST">

            <input type="hidden" name="agendamento_id" value="<?php echo $agendamento['id']; ?>">

            <div class="form-group">
                <label for="nome_animal">Nome do Animal: (*)</label>
                <input type="text" id="nome_animal" name="nome_animal"
                    value="<?php echo htmlspecialchars($agendamento['nome_animal']); ?>" required>
            </div>

            <div class="form-group">
                <label for="especie">Espécie: (*)</label>
                <select id="especie" name="especie" required>
                    <option value="">Selecione...</option>
                    <option value="Cachorro" <?php echo ($agendamento['especie'] == 'Cachorro') ? 'selected' : ''; ?>>
                        Cachorro
                    </option>
                    <option value="Gato" <?php echo ($agendamento['especie'] == 'Gato') ? 'selected' : ''; ?>>
                        Gato
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="sexo">Sexo: (*)</label>
                <select id="sexo" name="sexo" required>
                    <option value="Macho" <?php echo ($agendamento['sexo'] == 'Macho') ? 'selected' : ''; ?>>
                        Macho
                    </option>
                    <option value="Fêmea" <?php echo ($agendamento['sexo'] == 'Fêmea') ? 'selected' : ''; ?>>
                        Fêmea
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="idade">Idade (anos):</label>
                <input type="number" id="idade" name="idade" min="0"
                    value="<?php echo htmlspecialchars($agendamento['idade']); ?>">
            </div>

            <div class="form-group">
                <label for="raca">Raça: (Opcional)</label>
                <input type="text" id="raca" name="raca" value="<?php echo htmlspecialchars($agendamento['raca']); ?>">
            </div>

            <div class="form-group">
                <label for="cor">Cor: (Opcional)</label>
                <input type="text" id="cor" name="cor" value="<?php echo htmlspecialchars($agendamento['cor'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="data_agendamento">Data da Castração: (*)</label>
                <input type="date" id="data_agendamento" name="data_agendamento"
                    value="<?php echo htmlspecialchars($agendamento['data_agendamento']); ?>" min="<?php echo $amanha; ?>"
                    required>
            </div>

            <button type="submit" class="btn btn-azul">Salvar Alterações</button>
            <a href="index.php" class="btn btn-vermelho">Cancelar</a>
        </form>
    <?php else: ?>
        <p>Agendamento não encontrado.</p>
        <a href="index.php">Voltar para o Dashboard</a>
    <?php endif; ?>

</div>

<?php
include 'includes/footer.php';
?>