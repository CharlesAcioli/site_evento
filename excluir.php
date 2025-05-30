<?php
session_start();
include_once 'config.php';

// Verifica se usuário está logado e é organizador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'organizador') {
    die('Acesso negado.');
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $evento_id = (int)($_POST['evento_id'] ?? 0);

    if (!$evento_id) {
        die('ID do evento inválido.');
    }

    // Verifica se o evento pertence ao organizador
    $stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = ? AND organizador_id = ?");
    $stmt->execute([$evento_id, $usuario_id]);
    $evento = $stmt->fetch();

    if (!$evento) {
        die('Evento não encontrado ou sem permissão para excluir.');
    }

    // Apaga o evento
    $stmt = $pdo->prepare("DELETE FROM eventos WHERE id = ? AND organizador_id = ?");
    $stmt->execute([$evento_id, $usuario_id]);

    // Opcional: apagar dados relacionados (comentários, inscrições, curtidas) — cuidado com integridade

    header('Location: organizador.php');
    exit;
} else {
    die('Acesso inválido.');
}

?>