// Máscaras de input
function aplicarMascaras() {
    // CPF
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                e.target.value = value;
            }
        });
    }

    // Número do cartão
    const numeroCartaoInputs = document.querySelectorAll('input[name="numero_cartao"], input[id*="numero_cartao"]');
    numeroCartaoInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            e.target.value = value;
        });
    });

    // Validade
    const validadeInputs = document.querySelectorAll('input[name="validade"], input[id*="validade"]');
    validadeInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    });

    // CVV
    const cvvInputs = document.querySelectorAll('input[name="cvv"], input[id*="cvv"]');
    cvvInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    });
}

// Selecionar plano
function selecionarPlano(plano, valor) {
    const planos = {
        '30_dias': 'Plano 30 Dias',
        '3_meses': 'Plano 3 Meses',
        '6_meses': 'Plano 6 Meses',
        '9_meses': 'Plano 9 Meses',
        '12_meses': 'Plano 12 Meses'
    };

    document.getElementById('planoSelecionado').value = plano;
    document.getElementById('valorBase').value = valor;
    document.getElementById('planoInfo').textContent = planos[plano] + ' - R$ ' + valor.toFixed(2).replace('.', ',');
    
    document.querySelector('.planos-section').style.display = 'none';
    document.getElementById('checkoutSection').style.display = 'block';
    
    // Scroll suave
    document.getElementById('checkoutSection').scrollIntoView({ behavior: 'smooth' });
    
    // Resetar parcelas
    document.getElementById('parcelas').value = '';
    document.getElementById('valorParcela').textContent = 'R$ 0,00';
}

// Voltar para seleção
function voltarSelecao() {
    document.querySelector('.planos-section').style.display = 'block';
    document.getElementById('checkoutSection').style.display = 'none';
    document.querySelector('.planos-section').scrollIntoView({ behavior: 'smooth' });
}

// Calcular parcela
function calcularParcela() {
    const valorBase = parseFloat(document.getElementById('valorBase').value);
    const parcelas = parseInt(document.getElementById('parcelas').value);
    
    if (!valorBase || !parcelas || parcelas < 1) {
        document.getElementById('valorParcela').textContent = 'R$ 0,00';
        return;
    }
    
    // Taxa de juros mensal: 2.99%
    const taxaJuros = 0.0299;
    
    let valorParcela;
    if (parcelas == 1) {
        valorParcela = valorBase;
    } else {
        // Fórmula: PMT = PV * (i * (1 + i)^n) / ((1 + i)^n - 1)
        const i = taxaJuros;
        const n = parcelas;
        const pv = valorBase;
        
        valorParcela = pv * (i * Math.pow(1 + i, n)) / (Math.pow(1 + i, n) - 1);
    }
    
    // Arredondar para 2 casas decimais
    valorParcela = Math.round(valorParcela * 100) / 100;
    
    // Formatar e exibir
    document.getElementById('valorParcela').textContent = 
        'R$ ' + valorParcela.toFixed(2).replace('.', ',');
}

// Abrir modal de doação
function abrirDoacao() {
    document.getElementById('modalDoacao').style.display = 'block';
}

// Fechar modal de doação
function fecharDoacao() {
    document.getElementById('modalDoacao').style.display = 'none';
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const modal = document.getElementById('modalDoacao');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Validação de formulário
document.addEventListener('DOMContentLoaded', function() {
    aplicarMascaras();
    
    // Validar formulário de checkout
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const parcelas = document.getElementById('parcelas').value;
            if (!parcelas) {
                e.preventDefault();
                alert('Por favor, selecione o número de parcelas.');
                return false;
            }
        });
    }
    
    // Validar formulário de doação
    const doacaoForm = document.getElementById('doacaoForm');
    if (doacaoForm) {
        doacaoForm.addEventListener('submit', function(e) {
            // Validações básicas já são feitas pelo HTML5 required
        });
    }
    
    // Verificar se há erro na URL
    const urlParams = new URLSearchParams(window.location.search);
    const erro = urlParams.get('erro');
    if (erro) {
        let mensagem = 'Ocorreu um erro. ';
        switch(erro) {
            case 'dados_invalidos':
                mensagem += 'Dados inválidos. Por favor, tente novamente.';
                break;
            case 'campos_obrigatorios':
                mensagem += 'Por favor, preencha todos os campos obrigatórios.';
                break;
            case 'cpf_invalido':
                mensagem += 'CPF inválido. Por favor, verifique o CPF informado.';
                break;
            default:
                mensagem += 'Por favor, tente novamente.';
        }
        alert(mensagem);
    }
});

