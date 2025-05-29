<?php
session_start();
include '../config.php';

// Verifica se usuário está logado e é organizador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'organizador') {
    die('Acesso negado. Apenas organizadores podem editar eventos.');
}

$usuario_id = $_SESSION['usuario_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID do evento inválido.');
}

$evento_id = (int)$_GET['id'];

// Buscar evento para edição, somente se for dono
$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = ? AND organizador_id = ?");
$stmt->execute([$evento_id, $usuario_id]);
$evento = $stmt->fetch();

if (!$evento) {
    die('Evento não encontrado ou você não tem permissão para editar.');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $data_evento = $_POST['data_evento'];
    $local = trim($_POST['local']);

    if (!$titulo || !$descricao || !$data_evento || !$local) {
        $erro = "Preencha todos os campos.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_evento)) {
        $erro = "Data inválida. Use o formato YYYY-MM-DD.";
    } else {
        $stmt = $pdo->prepare("
            UPDATE eventos 
            SET titulo = ?, descricao = ?, data_evento = ?, local = ? 
            WHERE id = ? AND organizador_id = ?
        ");
        $stmt->execute([$titulo, $descricao, $data_evento, $local, $evento_id, $usuario_id]);

        header('Location: ../index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="editar.css">
    <title>Editar Evento</title>
</head>
<body>
    <h2>Editar Evento</h2>

    <?php if ($erro): ?>
        <p style="color:red"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post" action="editar.php?id=<?= $evento_id ?>">
        <label>Título:</label><br>
        <input type="text" name="titulo" value="<?= htmlspecialchars($evento['titulo']) ?>" required><br><br>

        <label>Descrição:</label><br>
        <textarea name="descricao" rows="5" cols="40" required><?= htmlspecialchars($evento['descricao']) ?></textarea><br><br>

        <label>Data do Evento:</label><br>
        <input type="date" name="data_evento" value="<?= htmlspecialchars($evento['data_evento']) ?>" required><br><br>

        <label>Local:</label><br>
        <input type="text" name="local" value="<?= htmlspecialchars($evento['local']) ?>" required><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>
    <form method="post" action="excluir.php" onsubmit="return confirm('Confirma exclusão do evento?');">
      <input type="hidden" name="evento_id" value="<?= $evento['id'] ?>">
      <button type="submit" style="color:red;">Excluir Evento</button>
    </form>

    <p><a href="../index.php">Voltar para eventos</a></p>
</body>
</html>