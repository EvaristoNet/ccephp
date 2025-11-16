<?php
require_once 'config.php';
require_once 'cielo_api.php';

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Validar dados
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$numeroCartao = $_POST['numero_cartao'] ?? '';
$validade = $_POST['validade'] ?? '';
$cvv = $_POST['cvv'] ?? '';
$nomePortador = $_POST['nome_portador'] ?? '';

// Validar campos obrigatórios
if (empty($nome) || empty($email) || empty($numeroCartao) || 
    empty($validade) || empty($cvv) || empty($nomePortador)) {
    header('Location: index.php?erro=campos_obrigatorios');
    exit;
}

// Formatar validade (MM/AA -> MMYY)
$validadeFormatada = str_replace('/', '', $validade);
if (strlen($validadeFormatada) == 4) {
    $validadeFormatada = substr($validadeFormatada, 0, 2) . substr($validadeFormatada, 2, 2);
}

// Preparar dados para a Cielo
$dadosDoacao = [
    'nome' => $nome,
    'email' => $email,
    'numero_cartao' => $numeroCartao,
    'validade' => $validadeFormatada,
    'cvv' => $cvv,
    'nome_portador' => $nomePortador
];

// Processar doação na Cielo
$cielo = new CieloAPI();
$resultado = $cielo->criarDoacao($dadosDoacao);

// Verificar resultado
if ($resultado['success'] && isset($resultado['data'])) {
    $paymentData = $resultado['data'];
    
    // Verificar status do pagamento
    if (isset($paymentData['Payment']['Status'])) {
        $status = $paymentData['Payment']['Status'];
        $paymentId = $paymentData['Payment']['PaymentId'] ?? null;
        
        if ($status == 1 || $status == 2) { // Autorizado ou Capturado
            // Sucesso
            header('Location: sucesso.php?tipo=doacao&payment_id=' . $paymentId);
            exit;
        } else {
            // Pagamento negado ou erro
            $returnCode = $paymentData['Payment']['ReturnCode'] ?? 'Erro desconhecido';
            $returnMessage = $paymentData['Payment']['ReturnMessage'] ?? 'Erro ao processar pagamento';
            header('Location: erro.php?codigo=' . urlencode($returnCode) . '&mensagem=' . urlencode($returnMessage));
            exit;
        }
    } else {
        // Erro na resposta
        header('Location: erro.php?codigo=ERRO_API&mensagem=' . urlencode('Erro ao processar resposta da Cielo'));
        exit;
    }
} else {
    // Erro na requisição
    $erro = $resultado['error'] ?? 'Erro desconhecido';
    header('Location: erro.php?codigo=ERRO_REQUISICAO&mensagem=' . urlencode($erro));
    exit;
}
?>

