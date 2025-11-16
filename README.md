# Sistema de Pagamentos - Centro de Consultoria Educacional

Sistema completo de pagamentos com cartão de crédito integrado com a Cielo E-commerce para processamento de pagamentos reais.

## Características

- ✅ Processamento de pagamentos reais via Cielo E-commerce
- ✅ 5 planos disponíveis (30 dias, 3 meses, 6 meses, 9 meses, 12 meses)
- ✅ Parcelamento em até 12x com juros de 2.99% ao mês
- ✅ Sistema de doação de R$ 1,00 separado
- ✅ Interface moderna e responsiva
- ✅ Validação de formulários
- ✅ Máscaras de input automáticas
- ✅ Integração completa com API Cielo

## Estrutura de Arquivos

```
novoprojeto/
├── index.php          # Página principal com seleção de planos
├── checkout.php       # Processamento do pagamento do plano
├── doacao.php         # Processamento da doação
├── sucesso.php        # Página de confirmação de pagamento
├── erro.php           # Página de erro no pagamento
├── config.php         # Configurações do sistema
├── cielo_api.php      # Classe de integração com Cielo
├── assets/
│   ├── css/
│   │   └── style.css  # Estilos do sistema
│   └── js/
│       └── script.js  # JavaScript do sistema
└── README.md          # Este arquivo
```

## Configuração

### Credenciais da Cielo

As credenciais já estão configuradas no arquivo `config.php`:
- Merchant ID: `ab793efc-3fca-422e-b799-ce2dae1a61cf`
- Merchant Key: `ShZShjv9PqrOFz8FO1IWEj645X5cDkhQRs8wyqlk`
- Ambiente: Produção (true)

### Planos Disponíveis

| Plano | Valor | Duração |
|-------|-------|---------|
| 30 Dias | R$ 1.590,00 | 30 dias |
| 3 Meses | R$ 1.260,00 | 3 meses |
| 6 Meses | R$ 999,99 | 6 meses |
| 9 Meses | R$ 799,99 | 9 meses |
| 12 Meses | R$ 599,99 | 12 meses |

### Parcelamento

- Parcelamento disponível em até 12x
- Taxa de juros: 2.99% ao mês
- O valor total com juros não é exibido ao cliente (apenas o valor da parcela)

## Requisitos

- PHP 7.4 ou superior
- Extensão cURL habilitada
- Extensão JSON habilitada
- Servidor web (Apache/Nginx) ou servidor PHP embutido

## Instalação

1. Faça o upload dos arquivos para seu servidor
2. Certifique-se de que as permissões estão corretas
3. Acesse o sistema através do navegador

## Uso

1. O cliente seleciona um plano na página inicial
2. Preenche os dados de parcelamento e informações pessoais
3. Informa os dados do cartão de crédito
4. O sistema processa o pagamento na Cielo
5. O cliente é redirecionado para a página de sucesso ou erro

## Doação

O sistema possui uma seção separada para doações de R$ 1,00, que funciona de forma independente dos planos.

## Segurança

- Validação de dados no servidor
- Validação de dados no cliente
- Comunicação HTTPS com a API Cielo
- Dados sensíveis não são armazenados localmente

## Informações da Escola

- Nome: Centro de Consultoria Educacional
- CNPJ: 54.863.268/0001-86

## Suporte

Para dúvidas ou problemas, entre em contato com o suporte técnico.

