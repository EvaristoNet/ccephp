<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Consultoria Educacional - Planos Supletivo Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Centro de Consultoria Educacional</h1>
            <p class="subtitle">Supletivo Online - Conclua seus estudos com qualidade</p>
        </header>

        <main>
            <section class="planos-section">
                <h2>Escolha seu Plano</h2>
                <div class="planos-grid">
                    <?php 
                    $coresPlanos = [
                        '30_dias' => 'vermelho',
                        '3_meses' => 'amarelo',
                        '6_meses' => 'verde',
                        '9_meses' => 'laranja',
                        '12_meses' => 'azul'
                    ];
                    $temposLiberacao = [
                        '30_dias' => 'Imediatamente',
                        '3_meses' => '10 dias',
                        '6_meses' => '30 dias',
                        '9_meses' => '45 dias',
                        '12_meses' => '60 dias'
                    ];
                    $temposEntrega = [
                        '30_dias' => '30 dias úteis',
                        '3_meses' => '90 dias úteis',
                        '6_meses' => '180 dias úteis',
                        '9_meses' => '270 dias úteis',
                        '12_meses' => '365 dias úteis'
                    ];
                    foreach ($planos as $key => $plano): 
                        $corPlano = $coresPlanos[$key] ?? 'azul';
                    ?>
                        <div class="plano-card plano-<?php echo $corPlano; ?>" data-plano="<?php echo $key; ?>" data-valor="<?php echo $plano['valor']; ?>">
                            <div class="plano-header">
                                <h3><?php echo $plano['nome']; ?></h3>
                                <p class="duracao"><?php echo $plano['duracao']; ?></p>
                            </div>
                            <div class="plano-valor">
                                <span class="moeda">R$</span>
                                <span class="valor"><?php echo number_format($plano['valor'], 2, ',', '.'); ?></span>
                            </div>
                            <div class="plano-beneficios">
                                <div class="beneficio-item">
                                    <span class="beneficio-icon">📝</span>
                                    <span class="beneficio-texto">Prova liberada: <?php echo $temposLiberacao[$key]; ?></span>
                                </div>
                                <div class="beneficio-item">
                                    <span class="beneficio-icon">🎓</span>
                                    <span class="beneficio-texto">Certificado válido em todo Brasil</span>
                                </div>
                                <div class="beneficio-item">
                                    <span class="beneficio-icon">📦</span>
                                    <span class="beneficio-texto">Entrega pelos Correios: <?php echo $temposEntrega[$key]; ?></span>
                                </div>
                            </div>
                            <button class="btn-selecionar" onclick="selecionarPlano('<?php echo $key; ?>', <?php echo $plano['valor']; ?>)">
                                Selecionar Plano
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="checkout-section" id="checkoutSection" style="display: none;">
                <h2>Finalizar Pagamento</h2>
                <form id="checkoutForm" method="POST" action="checkout.php">
                    <input type="hidden" name="plano" id="planoSelecionado">
                    <input type="hidden" name="valor_base" id="valorBase">
                    
                    <div class="form-group">
                        <label>Plano Selecionado:</label>
                        <div class="plano-info" id="planoInfo"></div>
                    </div>

                    <div class="form-group">
                        <label for="parcelas">Parcelas:</label>
                        <select name="parcelas" id="parcelas" onchange="calcularParcela()" required>
                            <option value="">Selecione...</option>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?>x</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Valor da Parcela:</label>
                        <div class="valor-parcela" id="valorParcela">R$ 0,00</div>
                    </div>

                    <div class="form-group">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" name="nome" id="nome" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" name="email" id="email" required>
                    </div>

                    <div class="form-group">
                        <label for="cpf">CPF:</label>
                        <input type="text" name="cpf" id="cpf" maxlength="14" placeholder="000.000.000-00" required>
                    </div>

                    <div class="form-group">
                        <label for="numero_cartao">Número do Cartão:</label>
                        <input type="text" name="numero_cartao" id="numero_cartao" maxlength="19" placeholder="0000 0000 0000 0000" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="validade">Validade (MM/AA):</label>
                            <input type="text" name="validade" id="validade" maxlength="5" placeholder="MM/AA" required>
                        </div>

                        <div class="form-group">
                            <label for="cvv">CVV:</label>
                            <input type="text" name="cvv" id="cvv" maxlength="4" placeholder="000" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nome_portador">Nome no Cartão:</label>
                        <input type="text" name="nome_portador" id="nome_portador" required>
                    </div>

                    <button type="submit" class="btn-pagar">Pagar Agora</button>
                    <button type="button" class="btn-voltar" onclick="voltarSelecao()">Voltar</button>
                </form>
            </section>

            <section class="doacao-section">
                <h2>Fazer uma Doação</h2>
                <div class="doacao-info">
                    <div class="doacao-icon">🍽️</div>
                    <p class="doacao-descricao">
                        <strong>Ajude nossa escola com uma doação de R$ 1,00</strong><br>
                        Sua doação será utilizada para ajudar pessoas necessitadas com alimentos. 
                        Juntos podemos fazer a diferença na vida de muitas famílias!
                    </p>
                </div>
                <button class="btn-doacao" onclick="abrirDoacao()">Fazer Doação de R$ 1,00</button>
            </section>

            <div class="modal" id="modalDoacao" style="display: none;">
                <div class="modal-content">
                    <span class="close" onclick="fecharDoacao()">&times;</span>
                    <h2>Doação de R$ 1,00</h2>
                    <form id="doacaoForm" method="POST" action="doacao.php">
                        <div class="form-group">
                            <label for="doacao_nome">Nome Completo:</label>
                            <input type="text" name="nome" id="doacao_nome" required>
                        </div>

                        <div class="form-group">
                            <label for="doacao_email">E-mail:</label>
                            <input type="email" name="email" id="doacao_email" required>
                        </div>

                        <div class="form-group">
                            <label for="doacao_numero_cartao">Número do Cartão:</label>
                            <input type="text" name="numero_cartao" id="doacao_numero_cartao" maxlength="19" placeholder="0000 0000 0000 0000" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="doacao_validade">Validade (MM/AA):</label>
                                <input type="text" name="validade" id="doacao_validade" maxlength="5" placeholder="MM/AA" required>
                            </div>

                            <div class="form-group">
                                <label for="doacao_cvv">CVV:</label>
                                <input type="text" name="cvv" id="doacao_cvv" maxlength="4" placeholder="000" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="doacao_nome_portador">Nome no Cartão:</label>
                            <input type="text" name="nome_portador" id="doacao_nome_portador" required>
                        </div>

                        <button type="submit" class="btn-pagar">Confirmar Doação</button>
                        <button type="button" class="btn-voltar" onclick="fecharDoacao()">Cancelar</button>
                    </form>
                </div>
            </div>
        </main>

        <footer>
            <p>CNPJ: <?php echo ESCOLA_CNPJ; ?></p>
            <p>&copy; <?php echo date('Y'); ?> Centro de Consultoria Educacional. Todos os direitos reservados.</p>
        </footer>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>

