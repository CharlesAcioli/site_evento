<?php
session_start();
include_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID do evento inválido.');
}

$evento_id = (int)$_GET['id'];

// Buscar dados do evento e organizador
$stmt = $pdo->prepare("
    SELECT e.*, u.nome AS organizador_nome 
    FROM eventos e
    JOIN users u ON e.organizador_id = u.id
    WHERE e.id = ?
");
$stmt->execute([$evento_id]);
$evento = $stmt->fetch();

if (!$evento) {
    die('Evento não encontrado.');
}

// Verificar se usuário está logado
$usuario_id = $_SESSION['usuario_id'] ?? null;
$usuario_tipo = $_SESSION['usuario_tipo'] ?? null;

// Verificar se o usuário já curtiu o evento
$curtido = false;
if ($usuario_id) {
    $stmt = $pdo->prepare("SELECT 1 FROM curtidas WHERE evento_id = ? AND usuario_id = ?");
    $stmt->execute([$evento_id, $usuario_id]);
    $curtido = $stmt->fetch() ? true : false;
}

// Verificar se o usuário já está inscrito
$inscrito = false;
if ($usuario_id) {
    $stmt = $pdo->prepare("SELECT 1 FROM inscricoes WHERE evento_id = ? AND usuario_id = ?");
    $stmt->execute([$evento_id, $usuario_id]);
    $inscrito = $stmt->fetch() ? true : false;
}

// Buscar comentários do evento
$stmt = $pdo->prepare("
    SELECT c.comentario, c.comentado_em, u.nome
    FROM comentarios c
    JOIN users u ON c.usuario_id = u.id
    WHERE c.evento_id = ?
    ORDER BY c.comentado_em DESC
");
$stmt->execute([$evento_id]);
$comentarios = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($evento['titulo']) ?> - Detalhes</title>
    <link rel="stylesheet" href="details.css">
</head>
<body>

<h1><?= htmlspecialchars($evento['titulo']) ?></h1>
<p><strong>Descrição:</strong><br><?= nl2br(htmlspecialchars($evento['descricao'])) ?></p>
<p><strong>Data:</strong> <?= date('d/m/Y', strtotime($evento['data_evento'])) ?></p>
<p><strong>Local:</strong> <?= htmlspecialchars($evento['local']) ?></p>
<p><strong>Organizador:</strong> <?= htmlspecialchars($evento['organizador_nome']) ?></p>

<hr>

<?php if ($usuario_id): ?>
    <form action="interation.php" method="post" style="display:inline;">
        <input type="hidden" name="evento_id" value="<?= $evento_id ?>">
        <input type="hidden" name="acao" value="<?= $curtido ? 'descurtir' : 'curtir' ?>">
        <!-- Caso queira desativar o botão para descurtir, basta inserir <?= $curtido ? 'disabled' : '' ?> ao button -->
        <button type="submit">
            <?= $curtido ? 'Curtido' : 'Curtir' ?>
        </button>
    </form>

    <form action="interation.php" method="post" style="display:inline; margin-left: 10px;">
        <input type="hidden" name="evento_id" value="<?= $evento_id ?>">
        <input type="hidden" name="acao" value="<?= $inscrito ? 'cancelar_inscricao' : 'inscrever' ?>">
        <!-- Caso queira desativar o botão de inscrição inserir <?= $inscrito ? 'disabled' : '' ?> ao button -->
        <button type="submit">
            <?= $inscrito ? 'Cancelar Inscrição' : 'Inscrever-se' ?>
        </button>
    </form>

    <hr>

    <h3>Comentar</h3>
    <form action="interation.php" method="post">
        <input type="hidden" name="evento_id" value="<?= $evento_id ?>">
        <input type="hidden" name="acao" value="comentar">
        <textarea name="comentario" rows="4" cols="50" required></textarea><br>
        <button type="submit">Enviar comentário</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Faça login</a> para interagir com o evento.</p>
<?php endif; ?>

<hr>

<h3>Comentários</h3>
<?php if (count($comentarios) === 0): ?>
    <p>Seja o primeiro a comentar!</p>
<?php else: ?>
    <?php foreach ($comentarios as $c): ?>
        <p><strong><?= htmlspecialchars($c['nome']) ?></strong> (<?= date('d/m/Y H:i', strtotime($c['comentado_em'])) ?>):<br>
        <?= nl2br(htmlspecialchars($c['comentario'])) ?></p>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>

<p><a href="index.php">Voltar para eventos</a></p>

</body>
</html>