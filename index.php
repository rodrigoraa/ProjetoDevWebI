<?php
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$mensagem = '';

$amanha = date('Y-m-d', strtotime('+1 day'));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nome_animal'])) {

    if (empty($_POST['nome_animal']) || empty($_POST['especie']) || empty($_POST['data_agendamento']) || empty($_POST['sexo'])) {
        $mensagem = "Por favor, preencha todos os campos obrigatórios (*).";
    } else {
        $nome_animal = $_POST['nome_animal'];
        $especie = $_POST['especie'];
        $raca = $_POST['raca'] ?? '';
        $data_agendamento = $_POST['data_agendamento'];
        $sexo = $_POST['sexo'];
        $idade = $_POST['idade'] ?? 0;
        $cor = $_POST['cor'] ?? '';

        if ($data_agendamento < $amanha) {
            $mensagem = "Erro: A data do agendamento deve ser uma data futura (a partir de amanhã).";
        } else {
            try {
                $sql = "INSERT INTO agendamentos (nome_animal, especie, raca, data_agendamento, user_id, sexo, idade, cor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome_animal, $especie, $raca, $data_agendamento, $user_id, $sexo, $idade, $cor]);

                header('Location: index.php?msg=sucesso');
                exit();

            } catch (PDOException $e) {
                $mensagem = "Erro ao registrar agendamento: " . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['msg']) && $_GET['msg'] == 'sucesso') {
    $mensagem = "Agendamento registrado com sucesso!";
}
if (isset($_GET['msg']) && $_GET['msg'] == 'editado') {
    $mensagem = "Agendamento atualizado com sucesso!";
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
<title>Dashboard - Agendamentos</title>

<?php if (!empty($mensagem)): ?>
    <div class="message <?php echo (strpos($mensagem, 'Erro') !== false) ? 'error' : 'success'; ?>">
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
            <label for="sexo">Sexo: (*)</label>
            <select id="sexo" name="sexo" required>
                <option value="Macho">Macho</option>
                <option value="Fêmea">Fêmea</option>
            </select>
        </div>

        <div class="form-group">
            <label for="idade">Idade (anos):</label>
            <input type="number" id="idade" name="idade" min="0" value="0">
        </div>

        <div class="form-group">
            <label for="raca">Raça: (Opcional)</label>
            <input type="text" id="raca" name="raca">
        </div>

        <div class="form-group">
            <label for="cor">Cor: (Opcional)</label>
            <input type="text" id="cor" name="cor">
        </div>

        <div class="form-group">
            <label for="data_agendamento">Data da Castração: (*)</label>
            <input type="date" id="data_agendamento" name="data_agendamento" min="<?php echo $amanha; ?>" required>
        </div>
        <button type="submit" class="btn btn-azul">Agendar</button>
    </form>
</div>

<div class="task-list">
    <h2>Meus Agendamentos</h2>
    <table>
        <thead>
            <tr>
                <th>Animal</th>
                <th>Espécie</th>
                <th>Sexo</th>
                <th>Idade</th>
                <th>Raça</th>
                <th>Cor</th>
                <th>Data Agendada</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($agendamentos)): ?>
                <tr>
                    <td colspan="8">Nenhum agendamento encontrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($agendamentos as $agendamento): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($agendamento['nome_animal']); ?></td>
                        <td><?php echo htmlspecialchars($agendamento['especie']); ?></td>
                        <td><?php echo htmlspecialchars($agendamento['sexo']); ?></td>
                        <td><?php echo htmlspecialchars($agendamento['idade']); ?> anos</td>
                        <td><?php echo htmlspecialchars($agendamento['raca'] ?? 'Não informada'); ?></td>
                        <td><?php echo htmlspecialchars($agendamento['cor'] ?? 'Não informada'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?></td>
                        <td class="actions">
                            <a href="edit.php?id=<?php echo $agendamento['id']; ?>" class="btn btn-verde">Editar</a>
                            <a href="includes/delete.php?id=<?php echo $agendamento['id']; ?>" class="btn btn-vermelho"
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

<?php
include 'includes/footer.php';
?>