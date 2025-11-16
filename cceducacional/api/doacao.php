<?php
$input=file_get_contents('php://input');
$data=json_decode($input,true);
if(!$data){http_response_code(400);echo json_encode(['success'=>false,'message'=>'Payload inválido']);exit;}
require_once __DIR__.'/../../config.php';
require_once __DIR__.'/../../cielo_api.php';
$nome=$data['nome']??'';
$email=$data['email']??'';
$numeroCartao=$data['numero_cartao']??'';
$validade=$data['validade']??'';
$cvv=$data['cvv']??'';
$nomePortador=$data['nome_portador']??'';
if(empty($nome)||empty($email)||empty($numeroCartao)||empty($validade)||empty($cvv)||empty($nomePortador)){http_response_code(422);echo json_encode(['success'=>false,'message':'Campos obrigatórios ausentes']);exit;}
$validadeFormatada=str_replace('/','',$validade);
if(strlen($validadeFormatada)==4){$validadeFormatada=substr($validadeFormatada,0,2).substr($validadeFormatada,2,2);} 
$dadosDoacao=[
 'nome'=>$nome,
 'email'=>$email,
 'numero_cartao'=>$numeroCartao,
 'validade'=>$validadeFormatada,
 'cvv'=>$cvv,
 'nome_portador'=>$nomePortador
];
$cielo=new CieloAPI();
$resultado=$cielo->criarDoacao($dadosDoacao);
if($resultado['success']&&isset($resultado['data']['Payment']['Status'])){
 $status=$resultado['data']['Payment']['Status'];
 $paymentId=$resultado['data']['Payment']['PaymentId']??null;
 if($status==1||$status==2){echo json_encode(['success'=>true,'redirect'=>"/sucesso.php?payment_id=$paymentId&plano=doacao&parcelas=1"]);exit;}
 $returnCode=$resultado['data']['Payment']['ReturnCode']??'ERRO';
 $returnMessage=$resultado['data']['Payment']['ReturnMessage']??'Pagamento negado';
 echo json_encode(['success'=>false,'message'=>$returnMessage,'redirect'=>"/erro.php?codigo=$returnCode&mensagem=$returnMessage"]);exit;
}
$msg=$resultado['error']??'Erro ao processar doação';
http_response_code(500);echo json_encode(['success'=>false,'message'=>$msg]);