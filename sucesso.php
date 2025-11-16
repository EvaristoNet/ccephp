<?php
require_once 'config.php';

$paymentId = $_GET['payment_id'] ?? '';
$tipo = $_GET['tipo'] ?? 'plano';
$plano = $_GET['plano'] ?? '';
$parcelas = $_GET['parcelas'] ?? 1;

if ($tipo == 'doacao') {
    $titulo = 'Doação Realizada com Sucesso!';
    $mensagem = 'Sua doação de R$ 1,00 foi processada com sucesso. Obrigado por apoiar nossa escola!';
} else {
    $planoInfo = $planos[$plano] ?? null;
    $titulo = 'Pagamento Realizado com Sucesso!';
    $mensagem = 'Seu pagamento foi processado com sucesso. ';
    if ($planoInfo) {
        $mensagem .= 'Você adquiriu o ' . $planoInfo['nome'] . ' em ' . $parcelas . 'x.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Realizado - Centro de Consultoria Educacional</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="sucesso-box">
            <div class="sucesso-icon">✓</div>
            <h1><?php echo $titulo; ?></h1>
            <p><?php echo $mensagem; ?></p>
            <?php if ($paymentId): ?>
                <p class="payment-id">ID da Transação: <?php echo htmlspecialchars($paymentId); ?></p>
            <?php endif; ?>
            <a href="index.php" class="btn-voltar">Voltar ao Início</a>
        </div>
        
        <footer>
            <p>CNPJ: <?php echo ESCOLA_CNPJ; ?></p>
            <p>&copy; <?php echo date('Y'); ?> Centro de Consultoria Educacional. Todos os direitos reservados.</p>
        </footer>
    </div>
</body>
</html>

