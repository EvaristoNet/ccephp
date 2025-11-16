<?php
$input=file_get_contents('php://input');
$data=json_decode($input,true);
if(!$data){http_response_code(400);echo json_encode(['success'=>false,'message'=>'Payload inválido']);exit;}
require_once __DIR__.'/../../config.php';
require_once __DIR__.'/../../cielo_api.php';
$plano=$data['plano']??'';
$valorBase=floatval($data['valor_base']??0);
$parcelas=intval($data['parcelas']??1);
$nome=$data['nome']??'';
$email=$data['email']??'';
$cpf=preg_replace('/\D/','',$data['cpf']??'');
$numeroCartao=$data['numero_cartao']??'';
$validade=$data['validade']??'';
$cvv=$data['cvv']??'';
$nomePortador=$data['nome_portador']??'';
if(empty($plano)||$valorBase<=0||$parcelas<1||$parcelas>12){http_response_code(422);echo json_encode(['success'=>false,'message'=>'Dados inválidos']);exit;}
if(empty($nome)||empty($email)||empty($cpf)||empty($numeroCartao)||empty($validade)||empty($cvv)||empty($nomePortador)){http_response_code(422);echo json_encode(['success'=>false,'message'=>'Campos obrigatórios ausentes']);exit;}
if(strlen($cpf)!=11){http_response_code(422);echo json_encode(['success'=>false,'message'=>'CPF inválido']);exit;}
$valorParcela=calcularParcela($valorBase,$parcelas);
$valorTotal=calcularValorTotal($valorBase,$parcelas);
$validadeFormatada=str_replace('/','',$validade);
if(strlen($validadeFormatada)==4){$validadeFormatada=substr($validadeFormatada,0,2).substr($validadeFormatada,2,2);} 
$orderId='PLANO-'.strtoupper($plano).'-'.time().'-'.rand(1000,9999);
$dadosPagamento=[
 'order_id'=>$orderId,
 'nome'=>$nome,
 'email'=>$email,
 'cpf'=>$cpf,
 'numero_cartao'=>$numeroCartao,
 'validade'=>$validadeFormatada,
 'cvv'=>$cvv,
 'nome_portador'=>$nomePortador,
 'valor'=>$valorTotal,
 'parcelas'=>$parcelas
];
$cielo=new CieloAPI();
$resultado=$cielo->criarPagamento($dadosPagamento);
if($resultado['success']&&isset($resultado['data']['Payment']['Status'])){
 $status=$resultado['data']['Payment']['Status'];
 $paymentId=$resultado['data']['Payment']['PaymentId']??null;
 if($status==1||$status==2){echo json_encode(['success'=>true,'redirect'=>"/sucesso.php?payment_id=$paymentId&plano=$plano&parcelas=$parcelas"]);exit;}
 $returnCode=$resultado['data']['Payment']['ReturnCode']??'ERRO';
 $returnMessage=$resultado['data']['Payment']['ReturnMessage']??'Pagamento negado';
 echo json_encode(['success'=>false,'message'=>$returnMessage,'redirect'=>"/erro.php?codigo=$returnCode&mensagem=$returnMessage"]);exit;
}
$msg=$resultado['error']??'Erro ao processar pagamento';
http_response_code(500);echo json_encode(['success'=>false,'message'=>$msg]);