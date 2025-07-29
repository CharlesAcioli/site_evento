<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'site_eventos';

$pdo = new PDO("mysql:host=$host;dbname=$db;", $user, $pass);

$pdo->exec("CREATE DATABASE IF NOT EXISTS site_eventos");

// Usuários
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('usuario', 'organizador') DEFAULT 'usuario',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;");

// Eventos
$pdo->exec("CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    data_evento DATE NOT NULL,
    local VARCHAR(150) NOT NULL,
    organizador_id INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_organizador FOREIGN KEY (organizador_id) REFERENCES users(id)
) ENGINE=InnoDB;");

// Inscrições
$pdo->exec("CREATE TABLE IF NOT EXISTS inscricoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    inscrito_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (evento_id, usuario_id),
    FOREIGN KEY (evento_id) REFERENCES eventos(id),
    FOREIGN KEY (usuario_id) REFERENCES users(id)
) ENGINE=InnoDB;");

// Curtidas
$pdo->exec("CREATE TABLE IF NOT EXISTS curtidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    curtido_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (evento_id, usuario_id),
    FOREIGN KEY (evento_id) REFERENCES eventos(id),
    FOREIGN KEY (usuario_id) REFERENCES users(id)
) ENGINE=InnoDB;");

// Comentários
$pdo->exec("CREATE TABLE IF NOT EXISTS comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    comentario TEXT NOT NULL,
    comentado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id),
    FOREIGN KEY (usuario_id) REFERENCES users(id)
) ENGINE=InnoDB;");


// Explorei um tipo dado diferente ENUM para fazer a escolha usando a TAG Select e option.
// Uso de ENUM quando temos setores definidos e que não variam. O que irá mudar serão os dados salvos.

// Motivo do uso ENGINE=InnoDB:
// Garantir que as referências entre as tabelas por uso das chaves FOREIGN KEY
// não possa inserir valores inválidos, nem deletar o registro PAI sem cuidar dos registros FILHOS.

// Itens a ser estudado:
// - Transações (ACID)
// - Bloqueios eficientes

// OBS: Porblemas enfrentados pro mim, entre as tabelas, foi a conexão,
// onde só consegui fazendo o uso de ENGINE=InnoDB.

// Toda a estrutura da tabela, foi criado do absoluto ZERO.

// As únicas interferências,foram: 
// - uso das FOREIGN KEY's.
// - criar tabela de forma manual.
// - Ao pesquisar vi que pode ser normal a não criação da tabela através do comando por erro no servidor.
// - uso de constraints.

// Fiz uso de um atributo interno do MySQL para adicionar Data e Hora através do TIMESTAMP, apenas como exercício.