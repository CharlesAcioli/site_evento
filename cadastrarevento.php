<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'organizador') {
    die('Acesso negado. Apenas organizadores podem cadastrar eventos.');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $data_evento = $_POST['data_evento'];
    $local = trim($_POST['local']);
    $organizador_id = $_SESSION['usuario_id'];

    if (!$titulo || !$descricao || !$data_evento || !$local) {
        $erro = "Preencha todos os campos.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_evento)) {
        $erro = "Data inválida. Use o formato YYYY-MM-DD.";
        // preg_match()->verifica se uma string casa com uma expressão regular (regex).
        // Retorna 1 se casar, 0 se não casar e false se houver erro na regex.

        // Exemplo REGEX:
        // ^ Início da string
        // \d{4} Exatamente 4 dígitos (ex: 2025)
        // - Um hífen literal (-)
        // \d{2} Exatamente 2 dígitos (ex: 05)
        // - Outro hífen
        // \d{2} Mais 2 dígitos (ex: 30)
        // $ Final da string
    } else {
        $stmt = $pdo->prepare("INSERT INTO eventos (titulo, descricao, data_evento, local, organizador_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $descricao, $data_evento, $local, $organizador_id]);

        header('Location: organizador.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="cadastrarevento.css">
    <title>Cadastrar Novo Evento</title>
</head>
<body>
    <h2>Cadastrar Novo Evento</h2>
    <?php if ($erro): ?>
        <p style="color:red"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <form method="post" action="cadastrarevento.php">
        <label>Título:</label><br>
        <input type="text" name="titulo" required><br><br>

        <label>Descrição:</label><br>
        <textarea name="descricao" rows="5" cols="40" required></textarea><br><br>

        <label>Data do Evento:</label><br>
        <input type="date" name="data_evento" required><br><br>

        <label>Local:</label><br>
        <input type="text" name="local" required><br><br>

        <button type="submit">Cadastrar Evento</button>
    </form>
    <p><a href="organizador.php">Voltar para eventos</a></p>
</body>
</html>