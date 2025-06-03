<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    die('Você precisa estar logado para interagir.');
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso inválido.');
}

$acao = $_POST['acao'] ?? '';
$evento_id = (int)($_POST['evento_id'] ?? 0);

if (!$evento_id || !in_array($acao, ['curtir', 'inscrever', 'comentar'])) {
    die('Dados inválidos.');
}

switch ($acao) {
    case 'curtir':
        // Inserir curtida, se não existir
        $stmt = $pdo->prepare("INSERT IGNORE INTO curtidas (evento_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$evento_id, $usuario_id]);
        break;

    case 'inscrever':
        // Inserir inscrição, se não existir
        $stmt = $pdo->prepare("INSERT IGNORE INTO inscricoes (evento_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$evento_id, $usuario_id]);
        break;

    case 'comentar':
        $comentario = trim($_POST['comentario'] ?? '');
        if (!$comentario) {
            die('Comentário vazio.');
        }
        $stmt = $pdo->prepare("INSERT INTO comentarios (evento_id, usuario_id, comentario) VALUES (?, ?, ?)");
        $stmt->execute([$evento_id, $usuario_id, $comentario]);
        break;
}

header("Location: details.php?id=$evento_id");
exit;