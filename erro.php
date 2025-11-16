<?php
require_once 'config.php';

$codigo = $_GET['codigo'] ?? 'ERRO_DESCONHECIDO';
$mensagem = $_GET['mensagem'] ?? 'Ocorreu um erro ao processar seu pagamento.';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro no Pagamento - Centro de Consultoria Educacional</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="erro-box">
            <div class="erro-icon">✗</div>
            <h1>Erro no Pagamento</h1>
            <p><?php echo htmlspecialchars($mensagem); ?></p>
            <p class="codigo-erro">Código: <?php echo htmlspecialchars($codigo); ?></p>
            <a href="index.php" class="btn-voltar">Tentar Novamente</a>
        </div>
        
        <footer>
            <p>CNPJ: <?php echo ESCOLA_CNPJ; ?></p>
            <p>&copy; <?php echo date('Y'); ?> Centro de Consultoria Educacional. Todos os direitos reservados.</p>
        </footer>
    </div>
</body>
</html>

