<?php
/**
 * Configurações do Sistema - Centro de Consultoria Educacional
 */

// Credenciais da Cielo E-commerce
define('CIELO_MERCHANT_ID', 'ab793efc-3fca-422e-b799-ce2dae1a61cf');
define('CIELO_MERCHANT_KEY', 'ShZShjv9PqrOFz8FO1IWEj645X5cDkhQRs8wyqlk');

// Ambiente (true = produção, false = sandbox)
define('CIELO_ENVIRONMENT', true); // Use false para testes, true para produção

// URL da API Cielo
define('CIELO_API_URL', CIELO_ENVIRONMENT 
    ? 'https://api.cieloecommerce.cielo.com.br' 
    : 'https://apisandbox.cieloecommerce.cielo.com.br');

// Informações da Escola
define('ESCOLA_NOME', 'Centro de Consultoria Educacional');
define('ESCOLA_CNPJ', '54.863.268/0001-86');

// Planos disponíveis
$planos = [
    '30_dias' => [
        'nome' => 'Plano 30 Dias',
        'valor' => 1590.00,
        'duracao' => '30 dias'
    ],
    '3_meses' => [
        'nome' => 'Plano 3 Meses',
        'valor' => 1260.00,
        'duracao' => '3 meses'
    ],
    '6_meses' => [
        'nome' => 'Plano 6 Meses',
        'valor' => 999.99,
        'duracao' => '6 meses'
    ],
    '9_meses' => [
        'nome' => 'Plano 9 Meses',
        'valor' => 799.99,
        'duracao' => '9 meses'
    ],
    '12_meses' => [
        'nome' => 'Plano 12 Meses',
        'valor' => 599.99,
        'duracao' => '12 meses'
    ]
];

// Taxa de juros por parcela (ao mês)
define('TAXA_JUROS_MENSAL', 0.0299); // 2.99% ao mês

// Função para calcular valor da parcela com juros
function calcularParcela($valor, $parcelas) {
    if ($parcelas == 1) {
        return $valor;
    }
    
    // Fórmula: PMT = PV * (i * (1 + i)^n) / ((1 + i)^n - 1)
    $i = TAXA_JUROS_MENSAL;
    $n = $parcelas;
    $pv = $valor;
    
    $pmt = $pv * ($i * pow(1 + $i, $n)) / (pow(1 + $i, $n) - 1);
    
    return round($pmt, 2);
}

// Função para calcular valor total com juros (não será exibido ao cliente)
function calcularValorTotal($valor, $parcelas) {
    if ($parcelas == 1) {
        return $valor;
    }
    
    $valorParcela = calcularParcela($valor, $parcelas);
    return round($valorParcela * $parcelas, 2);
}

