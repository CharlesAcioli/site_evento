<?php
session_start();
include 'config.php';

// Buscar eventos do banco ordenados pela data
$stmt = $pdo->query("SELECT e.id, e.titulo, e.data_evento, e.local, u.nome AS organizador 
                     FROM eventos e 
                     JOIN users u ON e.organizador_id = u.id
                     ORDER BY e.data_evento ASC");
$eventos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="index.css">
    <title>Site de Eventos - Início</title>
</head>
<body>

<header class="header">
    <div class="header-content">
        <?php if (isset($_SESSION['usuario_nome'])): ?>
        <p>Seja bem vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?> | <a href="logout.php">Sair</a></p>
        <?php else: ?>
            <p><a href="login.php">Entrar</a> | <a href="register.php">Cadastrar</a></p>
        <?php endif; ?>
    </div>
</header>

<?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'organizador'): ?>
    <p><a href="cadastrarevento.php">Cadastrar Novo Evento</a></p>
<?php endif; ?>

<?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'organizador'): ?>
    <p><a href="organizador.php">Painel do Organizador</a></p>
<?php endif; ?>

<div class="container">

    <h1>Eventos</h1>

    <?php if (count($eventos) === 0): ?>
        <p>Não há eventos cadastrados.</p>
    <?php else: ?>

        <ul>
        <?php foreach ($eventos as $evento): ?>
            <li>
                <strong><?= htmlspecialchars($evento['titulo']) ?></strong><br>
                Data: <?= date('d/m/Y', strtotime($evento['data_evento'])) ?><br>
                Local: <?= htmlspecialchars($evento['local']) ?><br>
                Organizador: <?= htmlspecialchars($evento['organizador']) ?><br>
                <a href="eventos/detalhe.php?id=<?= $evento['id'] ?>">Ver detalhes</a>
            </li>
            <hr>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
</body>
</html>