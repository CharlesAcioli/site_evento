<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['usuario_id'])) {
    die('Você precisa estar logado para interagir.');
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Acesso inválido.');
}

$acao = $_POST['acao'] ?? '';
$evento_id = (int)($_POST['evento_id'] ?? 0);

if (!$evento_id || !in_array($acao, ['curtir', 'descurtir', 'inscrever', 'cancelar_inscricao', 'comentar'])) {
    die('Dados inválidos.');
}

switch ($acao) {
    case 'curtir':
        // Inserir curtida, se não existir
        $stmt = $pdo->prepare("INSERT IGNORE INTO curtidas (evento_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$evento_id, $usuario_id]);
        break;
        // Remover curtida, caso exista
    case 'descurtir':
        $stmt = $pdo->prepare("DELETE FROM curtidas WHERE evento_id = ? AND usuario_id = ?");
        $stmt->execute([$evento_id, $usuario_id]);
        break;
    case 'inscrever':
        // Inserir inscrição, se não existir
        $stmt = $pdo->prepare("INSERT IGNORE INTO inscricoes (evento_id, usuario_id) VALUES (?, ?)");
        $stmt->execute([$evento_id, $usuario_id]);
        break;
        // Cancelar inscrição, caso exista.
    case 'cancelar_inscricao':
        $stmt = $pdo->prepare("DELETE FROM inscricoes WHERE evento_id = ? AND usuario_id = ?");
        $stmt->execute([$evento_id, $usuario_id]);
        break;
        // Adicionar comentário
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