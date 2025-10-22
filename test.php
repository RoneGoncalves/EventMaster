<?php
$config = include 'app/config/base.php';

try {
    $pdo = new PDO(
        "{$config['type']}:host={$config['host']};port={$config['port']};dbname={$config['name']}",
        $config['user'],
        $config['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão OK com o banco de dados!";
} catch (PDOException $e) {
    echo "❌ Erro ao conectar: " . $e->getMessage();
}
