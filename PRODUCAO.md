# Checklist para Produção - Sistema de Pagamentos Cielo

## ✅ Ajustes Realizados

### 1. **Captura Automática de Pagamentos**
- ✅ Adicionado `'Capture' => true` em todos os pagamentos
- ✅ Pagamentos serão capturados automaticamente após autorização
- ✅ Status 2 (Capturado) será retornado pela API

### 2. **Melhorias de Segurança**
- ✅ Verificação SSL configurada (usa certificado CA se disponível)
- ✅ Timeout de conexão configurado (30s)
- ✅ Tratamento de erros melhorado

### 3. **Tratamento de Erros**
- ✅ Mensagens de erro mais amigáveis
- ✅ Tratamento de diferentes status da Cielo
- ✅ Validação de respostas da API

## ⚠️ Verificações Antes de Produção

### 1. **Credenciais da Cielo**
- [ ] Confirmar que as credenciais são de PRODUÇÃO (não sandbox)
- [ ] Merchant ID: `ab793efc-3fca-422e-b799-ce2dae1a61cf`
- [ ] Merchant Key: `ShZShjv9PqrOFz8FO1IWEj645X5cDkhQRs8wyqlk`
- [ ] Verificar se a conta está ativa e habilitada para receber pagamentos

### 2. **Configuração do Ambiente**
- [ ] `CIELO_ENVIRONMENT` está como `true` em `config.php`
- [ ] URL da API apontando para produção: `https://api.cieloecommerce.cielo.com.br`

### 3. **Certificado SSL**
- [ ] Verificar se o arquivo `cacert.pem` existe na pasta do projeto
- [ ] Se não existir, o sistema funcionará mas com verificação SSL desabilitada
- [ ] **Recomendado**: Manter verificação SSL habilitada em produção

### 4. **Testes Recomendados**
- [ ] Fazer um teste com valor baixo (R$ 1,00) primeiro
- [ ] Verificar se o pagamento é capturado corretamente
- [ ] Verificar se o status retornado é 2 (Capturado)
- [ ] Confirmar no painel da Cielo que a transação aparece como capturada

### 5. **Valores e Planos**
- [ ] Confirmar que os valores dos planos estão corretos
- [ ] Verificar se a taxa de juros (2.99%) está adequada
- [ ] Confirmar que os cálculos de parcelamento estão corretos

### 6. **Informações da Escola**
- [ ] CNPJ correto: `54.863.268/0001-86`
- [ ] Nome correto: `Centro de Consultoria Educacional`
- [ ] SoftDescriptor configurado corretamente

## 🔒 Segurança

### Boas Práticas Implementadas
- ✅ Dados sensíveis não são armazenados localmente
- ✅ Validação de dados no servidor
- ✅ Comunicação HTTPS com a API Cielo
- ✅ Timeout configurado para evitar travamentos

### Recomendações Adicionais
- [ ] Implementar logs de transações (opcional)
- [ ] Configurar monitoramento de erros
- [ ] Fazer backup regular dos dados
- [ ] Usar HTTPS no site (certificado SSL válido)

## 📊 Status da Cielo

### Códigos de Status
- **0** = Pendente
- **1** = Autorizado
- **2** = Capturado ✅ (com Capture: true)
- **3** = Negado
- **10** = Cancelado
- **12** = Cancelando

### Comportamento Esperado
Com `Capture: true`, o pagamento deve:
1. Ser autorizado (Status 1)
2. Ser capturado automaticamente (Status 2)
3. O valor será creditado na conta

## 🚨 Em Caso de Problemas

### Se o pagamento não for capturado:
1. Verificar se `Capture: true` está no payload
2. Verificar logs de erro (se configurado)
3. Consultar o painel da Cielo
4. Verificar se a conta está habilitada para captura automática

### Se houver erro de SSL:
1. Verificar se o arquivo `cacert.pem` existe
2. Verificar permissões do arquivo
3. O sistema funcionará mesmo sem verificação SSL (menos seguro)

### Se o pagamento for negado:
- Verificar dados do cartão
- Verificar se o cartão está ativo
- Verificar limite disponível
- Verificar se a bandeira é aceita

## ✅ Sistema Pronto para Produção

O sistema está configurado e pronto para processar pagamentos reais. 
Certifique-se de fazer os testes recomendados antes de disponibilizar para clientes.

