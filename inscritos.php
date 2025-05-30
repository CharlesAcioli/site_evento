<?php
session_start();
include 'config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'organizador') {
    die('Acesso negado.');
}

$organizador_id = $_SESSION['usuario_id'];

if (!isset($_GET['evento_id']) || !is_numeric($_GET['evento_id'])) {
    die('ID do evento inválido.');
}

$evento_id = (int)$_GET['evento_id'];

// Verifica se o evento pertence ao organizador
$stmt = $pdo->prepare("SELECT titulo FROM eventos WHERE id = ? AND organizador_id = ?");
$stmt->execute([$evento_id, $organizador_id]);
$evento = $stmt->fetch();

if (!$evento) {
    die('Evento não encontrado ou você não tem permissão para ver inscritos.');
}

// Busca inscritos do evento
$stmt = $pdo->prepare("
    SELECT u.nome, u.email, i.inscrito_em 
    FROM inscricoes i
    JOIN users u ON i.usuario_id = u.id
    WHERE i.evento_id = ?
    ORDER BY i.inscrito_em DESC
");
$stmt->execute([$evento_id]);
$inscritos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Inscritos no evento: <?= htmlspecialchars($evento['titulo']) ?></title>
</head>
<body>

<h1>Inscritos no evento: <?= htmlspecialchars($evento['titulo']) ?></h1>

<p><a href="organizador.php">Voltar ao painel</a></p>

<?php if (count($inscritos) === 0): ?>
    <p>Não há inscritos neste evento.</p>
<?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Data da inscrição</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($inscritos as $inscrito): ?>
            <tr>
                <td><?= htmlspecialchars($inscrito['nome']) ?></td>
                <td><?= htmlspecialchars($inscrito['email']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($inscrito['inscrito_em'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>