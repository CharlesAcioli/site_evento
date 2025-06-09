<?php
session_start();
require_once 'config.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirm_senha = $_POST['confirm_senha'];
    $tipo = $_POST['tipo']; // 'usuario' ou 'organizador'

    // Validações básicas
    if (!$nome || !$email || !$senha || !$confirm_senha) {
        $erro = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido.";
    } elseif ($senha !== $confirm_senha) {
        $erro = "As senhas não coincidem.";
    } else {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $erro = "Este email já está cadastrado.";
        } else {
            // Criar hash da senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            // Inserir no banco
            $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senha_hash, $tipo]);

            // Redirecionar para login
            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="register.css">
    <title>Cadastro - Site de Eventos</title>
</head>
<body>
    <h2>Cadastro de Usuário</h2>

    <?php if ($erro): ?>
        <p style="color:red"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post" action="register.php">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <label>Confirmar Senha:</label><br>
        <input type="password" name="confirm_senha" required><br><br>

        <label>Tipo de usuário:</label><br>
        <select name="tipo" required>
            <option value="usuario">Usuário</option>
            <option value="organizador">Organizador</option>
        </select><br><br>

        <button type="submit">Cadastrar</button>
    </form>

    <p>Já tem conta? <a href="login.php">Login aqui</a>.</p>
</body>
</html>