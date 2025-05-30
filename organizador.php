<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'organizador') {
    die('Acesso negado.');
}

$organizador_id = $_SESSION['usuario_id'];

// Buscar eventos do organizador
$stmt = $pdo->prepare("SELECT * FROM eventos WHERE organizador_id = ? ORDER BY data_evento ASC");
$stmt->execute([$organizador_id]);
$eventos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="organizador.css">
    <title>Painel do Organizador</title>
</head>
<body>

<h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></h1>
<p><a href="index.php">Voltar para lista de eventos</a> | <a href="logout.php">Sair</a></p>

<h2>Meus Eventos</h2>

<p><a href="cadastrarevento.php">Cadastrar Novo Evento</a></p>

<?php if (count($eventos) === 0): ?>
    <p>Você ainda não cadastrou eventos.</p>
<?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Título</th>
                <th>Data</th>
                <th>Local</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($eventos as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['titulo']) ?></td>
                <td><?= date('d/m/Y', strtotime($e['data_evento'])) ?></td>
                <td><?= htmlspecialchars($e['local']) ?></td>
                <td>
                    <a href="editar.php?id=<?= $e['id'] ?>">Editar</a>&nbsp;|&nbsp;
                    <form action="excluir.php" method="post" style="display:inline;" onsubmit="return confirm('Confirma exclusão do evento?');">
                        <input type="hidden" name="evento_id" value="<?= $e['id'] ?>">
                        <button type="submit" style="color:red; background:none; border:none; cursor:pointer;">Excluir</button>
                    </form>&nbsp;|&nbsp;
                    <!-- &nbsp inserido para tratamento de erros em telas diferentes, além de evitar quebras estranhas ou deslizamentos. -->
                    <a href="inscritos.php?evento_id=<?= $e['id'] ?>">Ver inscritos</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>