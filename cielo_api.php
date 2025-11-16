<?php
/**
 * Classe para integração com API Cielo E-commerce
 * Implementação manual da API REST da Cielo
 */

require_once 'config.php';

class CieloAPI {
    private $merchantId;
    private $merchantKey;
    private $environment;
    private $apiUrl;
    
    public function __construct() {
        $this->merchantId = CIELO_MERCHANT_ID;
        $this->merchantKey = CIELO_MERCHANT_KEY;
        $this->environment = CIELO_ENVIRONMENT;
        $this->apiUrl = CIELO_API_URL;
    }
    
    /**
     * Cria uma transação de pagamento
     */
    public function criarPagamento($dadosPagamento) {
        $url = $this->apiUrl . '/1/sales';
        
        $headers = [
            'Content-Type: application/json',
            'MerchantId: ' . $this->merchantId,
            'MerchantKey: ' . $this->merchantKey
        ];
        
        $payload = [
            'MerchantOrderId' => $dadosPagamento['order_id'],
            'Customer' => [
                'Name' => $dadosPagamento['nome'],
                'Email' => $dadosPagamento['email'],
                'Identity' => $dadosPagamento['cpf'],
                'IdentityType' => 'CPF'
            ],
            'Payment' => [
                'Type' => 'CreditCard',
                'Amount' => (int)($dadosPagamento['valor'] * 100), // Cielo usa centavos
                'Installments' => $dadosPagamento['parcelas'],
                'Capture' => true, // Captura automática do pagamento
                'CreditCard' => [
                    'CardNumber' => str_replace(' ', '', $dadosPagamento['numero_cartao']),
                    'Holder' => $dadosPagamento['nome_portador'],
                    'ExpirationDate' => $dadosPagamento['validade'],
                    'SecurityCode' => $dadosPagamento['cvv'],
                    'Brand' => $this->identificarBandeira($dadosPagamento['numero_cartao'])
                ],
                'SoftDescriptor' => ESCOLA_NOME
            ]
        ];
        
        $response = $this->fazerRequisicao($url, 'POST', $payload, $headers);
        
        return $response;
    }
    
    /**
     * Cria uma doação
     */
    public function criarDoacao($dadosDoacao) {
        $url = $this->apiUrl . '/1/sales';
        
        $headers = [
            'Content-Type: application/json',
            'MerchantId: ' . $this->merchantId,
            'MerchantKey: ' . $this->merchantKey
        ];
        
        $payload = [
            'MerchantOrderId' => 'DOACAO-' . time(),
            'Customer' => [
                'Name' => $dadosDoacao['nome'],
                'Email' => $dadosDoacao['email']
            ],
            'Payment' => [
                'Type' => 'CreditCard',
                'Amount' => 100, // R$ 1,00 em centavos
                'Installments' => 1,
                'Capture' => true, // Captura automática do pagamento
                'CreditCard' => [
                    'CardNumber' => str_replace(' ', '', $dadosDoacao['numero_cartao']),
                    'Holder' => $dadosDoacao['nome_portador'],
                    'ExpirationDate' => $dadosDoacao['validade'],
                    'SecurityCode' => $dadosDoacao['cvv'],
                    'Brand' => $this->identificarBandeira($dadosDoacao['numero_cartao'])
                ],
                'SoftDescriptor' => ESCOLA_NOME . ' - Doacao'
            ]
        ];
        
        $response = $this->fazerRequisicao($url, 'POST', $payload, $headers);
        
        return $response;
    }
    
    /**
     * Consulta status de uma transação
     */
    public function consultarPagamento($paymentId) {
        $url = $this->apiUrl . '/1/sales/' . $paymentId;
        
        $headers = [
            'MerchantId: ' . $this->merchantId,
            'MerchantKey: ' . $this->merchantKey
        ];
        
        $response = $this->fazerRequisicao($url, 'GET', null, $headers);
        
        return $response;
    }
    
    /**
     * Identifica a bandeira do cartão
     */
    private function identificarBandeira($numeroCartao) {
        $numero = preg_replace('/\D/', '', $numeroCartao);
        $primeiroDigito = substr($numero, 0, 1);
        $primeirosDois = substr($numero, 0, 2);
        $primeirosQuatro = substr($numero, 0, 4);
        $primeirosSeis = substr($numero, 0, 6);
        
        // Visa: começa com 4
        if ($primeiroDigito == '4') {
            return 'Visa';
        }
        // Mastercard: 51-55 ou 2221-2720
        if (($primeirosDois >= 51 && $primeirosDois <= 55) || 
            ($primeirosQuatro >= 2221 && $primeirosQuatro <= 2720)) {
            return 'Master';
        }
        // Amex: 34 ou 37
        if ($primeirosDois == 34 || $primeirosDois == 37) {
            return 'Amex';
        }
        // Elo: vários ranges
        if (in_array($primeirosDois, [50, 51, 52, 53, 54, 55]) || 
            ($primeirosDois >= 40 && $primeirosDois <= 49) ||
            ($primeirosQuatro >= 4011 && $primeirosQuatro <= 4012) ||
            ($primeirosQuatro >= 4312 && $primeirosQuatro <= 4312) ||
            ($primeirosQuatro >= 4389 && $primeirosQuatro <= 4389) ||
            ($primeirosQuatro >= 4514 && $primeirosQuatro <= 4514) ||
            ($primeirosQuatro >= 4573 && $primeirosQuatro <= 4573) ||
            ($primeirosQuatro >= 5041 && $primeirosQuatro <= 5041) ||
            ($primeirosQuatro >= 5066 && $primeirosQuatro <= 5067) ||
            ($primeirosQuatro >= 5090 && $primeirosQuatro <= 5090) ||
            ($primeirosQuatro >= 6277 && $primeirosQuatro <= 6277) ||
            ($primeirosQuatro >= 6362 && $primeirosQuatro <= 6363) ||
            ($primeirosQuatro >= 6504 && $primeirosQuatro <= 6504) ||
            ($primeirosQuatro >= 6507 && $primeirosQuatro <= 6507) ||
            ($primeirosQuatro >= 6509 && $primeirosQuatro <= 6509) ||
            ($primeirosQuatro >= 6516 && $primeirosQuatro <= 6516) ||
            ($primeirosQuatro >= 6550 && $primeirosQuatro <= 6550)) {
            return 'Elo';
        }
        // Hipercard: 606282
        if ($primeirosSeis == '606282') {
            return 'Hipercard';
        }
        // Diners: 300-305, 309, 36, 38
        if (($primeirosQuatro >= 3000 && $primeirosQuatro <= 3059) ||
            ($primeirosQuatro >= 3090 && $primeirosQuatro <= 3099) ||
            $primeirosDois == 36 || $primeirosDois == 38) {
            return 'Diners';
        }
        
        return 'Visa'; // Default
    }
    
    /**
     * Faz requisição HTTP
     */
    private function fazerRequisicao($url, $method = 'GET', $data = null, $headers = []) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout de 30 segundos
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout de conexão
        
        // Em produção, é recomendado verificar SSL, mas se houver problemas, pode desabilitar
        // Verificar se existe arquivo de certificado CA
        $cacertFile = __DIR__ . '/cacert.pem';
        if (file_exists($cacertFile) && $this->environment) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_CAINFO, $cacertFile);
        } else {
            // Desabilitar verificação SSL apenas se necessário (não recomendado em produção)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
        }
        
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            return [
                'success' => false,
                'error' => $error,
                'http_code' => $httpCode
            ];
        }
        
        $responseData = json_decode($response, true);
        
        // Log de erros em produção (opcional - descomente se necessário)
        // if ($httpCode >= 400) {
        //     error_log("Cielo API Error - HTTP Code: $httpCode - Response: " . $response);
        // }
        
        return [
            'success' => ($httpCode >= 200 && $httpCode < 300),
            'http_code' => $httpCode,
            'data' => $responseData
        ];
    }
}

