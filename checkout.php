<?php
require_once 'config.php';
require_once 'cielo_api.php';

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Validar dados
$plano = $_POST['plano'] ?? '';
$valorBase = floatval($_POST['valor_base'] ?? 0);
$parcelas = intval($_POST['parcelas'] ?? 1);
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
$numeroCartao = $_POST['numero_cartao'] ?? '';
$validade = $_POST['validade'] ?? '';
$cvv = $_POST['cvv'] ?? '';
$nomePortador = $_POST['nome_portador'] ?? '';

// Validar campos obrigatórios
if (empty($plano) || $valorBase <= 0 || $parcelas < 1 || $parcelas > 12) {
    header('Location: index.php?erro=dados_invalidos');
    exit;
}

if (empty($nome) || empty($email) || empty($cpf) || empty($numeroCartao) || 
    empty($validade) || empty($cvv) || empty($nomePortador)) {
    header('Location: index.php?erro=campos_obrigatorios');
    exit;
}

// Validar CPF
if (strlen($cpf) != 11) {
    header('Location: index.php?erro=cpf_invalido');
    exit;
}

// Calcular valor da parcela com juros
$valorParcela = calcularParcela($valorBase, $parcelas);
$valorTotal = calcularValorTotal($valorBase, $parcelas);

// Formatar validade (MM/AA -> MMYY)
$validadeFormatada = str_replace('/', '', $validade);
if (strlen($validadeFormatada) == 4) {
    $validadeFormatada = substr($validadeFormatada, 0, 2) . substr($validadeFormatada, 2, 2);
}

// Gerar Order ID único
$orderId = 'PLANO-' . strtoupper($plano) . '-' . time() . '-' . rand(1000, 9999);

// Preparar dados para a Cielo
$dadosPagamento = [
    'order_id' => $orderId,
    'nome' => $nome,
    'email' => $email,
    'cpf' => $cpf,
    'numero_cartao' => $numeroCartao,
    'validade' => $validadeFormatada,
    'cvv' => $cvv,
    'nome_portador' => $nomePortador,
    'valor' => $valorTotal, // Valor total com juros (não exibido ao cliente)
    'parcelas' => $parcelas
];

// Processar pagamento na Cielo
$cielo = new CieloAPI();
$resultado = $cielo->criarPagamento($dadosPagamento);

// Verificar resultado
if ($resultado['success'] && isset($resultado['data'])) {
    $paymentData = $resultado['data'];
    
    // Verificar status do pagamento
    // Status da Cielo: 0=Pendente, 1=Autorizado, 2=Capturado, 3=Negado, 10=Cancelado, 12=Cancelando
    if (isset($paymentData['Payment']['Status'])) {
        $status = $paymentData['Payment']['Status'];
        $paymentId = $paymentData['Payment']['PaymentId'] ?? null;
        
        // Status 1 = Autorizado, Status 2 = Capturado (com Capture: true, deve vir como 2)
        if ($status == 1 || $status == 2) {
            // Sucesso - pagamento autorizado/capturado
            header('Location: sucesso.php?payment_id=' . $paymentId . '&plano=' . $plano . '&parcelas=' . $parcelas);
            exit;
        } else {
            // Pagamento negado, cancelado ou erro
            $returnCode = $paymentData['Payment']['ReturnCode'] ?? 'Erro desconhecido';
            $returnMessage = $paymentData['Payment']['ReturnMessage'] ?? 'Erro ao processar pagamento';
            
            // Mensagens mais amigáveis para códigos comuns
            if ($status == 3) {
                $returnMessage = 'Pagamento negado. Verifique os dados do cartão ou entre em contato com seu banco.';
            }
            
            header('Location: erro.php?codigo=' . urlencode($returnCode) . '&mensagem=' . urlencode($returnMessage));
            exit;
        }
    } else {
        // Erro na resposta - verificar se há mensagens de erro na resposta
        $errorMessage = 'Erro ao processar resposta da Cielo';
        if (isset($paymentData[0]['Message'])) {
            $errorMessage = $paymentData[0]['Message'];
        } elseif (isset($paymentData['Message'])) {
            $errorMessage = $paymentData['Message'];
        }
        header('Location: erro.php?codigo=ERRO_API&mensagem=' . urlencode($errorMessage));
        exit;
    }
} else {
    // Erro na requisição
    $erro = $resultado['error'] ?? 'Erro desconhecido';
    header('Location: erro.php?codigo=ERRO_REQUISICAO&mensagem=' . urlencode($erro));
    exit;
}
?>

