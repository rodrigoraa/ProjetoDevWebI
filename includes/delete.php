<?php
/*
recebe um id via GET e executa o delete no banco e direciona de volta ao index.php
*/
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header('Location: /../login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header('Location: /../index.php');
    exit();
}

$agendamento_id = $_GET['id'];

try {
    
    $sql = "DELETE FROM agendamentos WHERE id = ? AND user_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$agendamento_id, $user_id]);
    
    header('Location: /../index.php');
    exit();

} catch (PDOException $e) {
    die("Erro ao cancelar o agendamento: " . $e->getMessage());
}
?>