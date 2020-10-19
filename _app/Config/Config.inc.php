<?php


/*
 * PATCH CONFIG
 */
define('INCLUDE_PATH', BASE . '/themes/' . THEME); //Geral de inclusão (Não alterar)
define('REQUIRE_PATH', 'themes/' . THEME); //Geral de inclusão (Não alterar)

if (!$WorkControlDefineConf):
    /*
     * E-MAIL SERVER
     * Consulte estes dados com o serviço de hospedagem
     */
    define('MAIL_HOST', 'mail.valepratas.com.br'); //Servidor de e-mail
    define('MAIL_PORT', '25'); //Porta de envio
    define('MAIL_USER', 'valepratas@valepratas.com.br'); //E-mail de envio
    define('MAIL_PASS', 'v*;vB!b!#Xq1'); //Senha do e-mail de envio
    define('MAIL_SENDER', 'Vale Pratas'); //Nome do remetente de e-mail
    define('MAIL_TESTER', ''); //E-mail de testes (DEV)

    /*
     * ADMIN CONFIG
     */
    define('ADMIN_NAME', 'Ecommerce');  //Nome do painel de controle (Work Control)
    define('ADMIN_DESC', 'Ecommerce super profissional'); //Descrição do painel de controle (Work Control)
    define('ADMIN_MODE', 2); //1 = website / 2 = e-commerce / 3 = e-learming

    /*
     * MEDIA CONFIG
     */
    define('IMAGE_W', 1600); //Tamanho da imagem (WIDTH)
    define('IMAGE_H', 800); //Tamanho da imagem (HEIGHT)
    define('THUMB_W', 800); //Tamanho da miniatura (WIDTH) PDTS
    define('THUMB_H', 1000); //Tamanho da minuatura (HEIGHT) PDTS
    define('AVATAR_W', 500); //Tamanho da miniatura (WIDTH) USERS
    define('AVATAR_H', 500); //Tamanho da minuatura (HEIGHT) USERS

    /*
     * APP CONFIG
     * Habilitar ou desabilitar modos do sistema
     */
    define('APP_POSTS', false); //Posts
    define('APP_SEARCH', true); //Relatório de Pesquisas
    define('APP_PAGES', true); //Páginas
    define('APP_COMMENTS', true); //Comentários
    define('APP_PRODUCTS', true); //Produtos
    define('APP_ORDERS', true); //Pedidos
    define('APP_USERS', true); //Usuários

    /*
     * APP LINKS
     * Habilitar ou desabilitar campos de links alternaivos
     */
    define('APP_LINK_POSTS', false); //Posts
    define('APP_LINK_PAGES', true); //Páginas
    define('APP_LINK_PRODUCTS', false); //Produtos

    /*
     * ACCOUNT CONFIG
     */
    define('ACC_MANAGER', true); //Conta de usuários (UI)
    define('ACC_TAG', 'Minha Conta!'); //null para OLÁ {NAME} ou texto (Minha Conta, Meu Cadastro, etc)

    /*
     * COMMENT CONFIG
     */
    define('COMMENT_MODERATE', false); //Todos os NOVOS comentários ficam ocultos até serem aprovados
    define('COMMENT_ON_POSTS', true); //Aplica comentários aos posts
    define('COMMENT_ON_PAGES', false); //Aplica comentários as páginas
    define('COMMENT_ON_PRODUCTS', true); //Aplica comentários aos produtos
    define('COMMENT_SEND_EMAIL', true); //Envia emais transicionais para usuários sobre comentários
    define('COMMENT_ORDER', 'DESC'); //Orden de exibição dos comentários (ASC ou DESC)
    define('COMMENT_RESPONSE_ORDER', 'ASC'); //Orden de exibição das respostas (ASC ou DESC)

    /*
     * ECOMMERCE CONFIG
     * IMPORTANTE EM E_ORDER_PAYDATE: Um tempo muito grande para pagamento pode implicar
     * em extender descontos expirados. Uma oferta pode acabar e o usuário ainda consegue
     * pagar neste prazo de dias!
     */
    define('E_PDT_LIMIT', null); //Limite de produtos cadastrados. NULL = sem limite
    define('E_ORDER_DAYS', 10); //Validade do pedido em dias. (Se maior e não pago, não poderá mais ser pago)
    define('ECOMMERCE_TAG', 'Carrinho!'); //Meu Carrinho, Minha Cesta, Minhas Compras, Etc;
    define('ECOMMERCE_STOCK', 'payment'); //payment (baixa estoque no pagamento), cart (baixa estoque no pedido), null (não baixa estoque)
    define('ECOMMERCE_BUTTON_TAG', 'Adicionar ao Carrinho'); //Meu Carrinho, Minha Cesta, Minhas Compras, Etc;
    /*
     * Parcelamento
     */
    define('ECOMMERCE_PAY_SPLIT', true); //Aceita pagamento parcelado?
    define('ECOMMERCE_PAY_SPLIT_MIN', 5); //Qual valor mínimo da parcela? (consultar método de pagamento)
    define('ECOMMERCE_PAY_SPLIT_NUM', 12); //Qual o número máximo de parcelas? (consultar método de pagamento)
    define('ECOMMERCE_PAY_SPLIT_ACM', 2.99); //Juros aplicados ao mês! (consultar método de pagamento)
    define('ECOMMERCE_PAY_SPLIT_ACN', 1); //Parcelas sem Juros (consultar método de pagamento)

    /*
     * SHIPMENT CONFIG
     * 1. Frete gratuito a partir do valor X
     */
    define('ECOMMERCE_SHIPMENT_FREE', false); //Opção de frete grátis a partir do valor X (Informe o valor ou false)
    define('ECOMMERCE_SHIPMENT_FREE_DAYS', 20); //Máximo de dias úteis para a entrega no frete gratuito!
    /*
     * Valor de frete fixo!
     */
    define('ECOMMERCE_SHIPMENT_FIXED', false); //Oferecer frete com valor fixo?
    define('ECOMMERCE_SHIPMENT_FIXED_PRICE', 15.00); //Valor do frete fixo
    define('ECOMMERCE_SHIPMENT_FIXED_DAYS', 15); //Máximo de dias úteis para a entrega! 


    /*
     * Frete fixo por localidade!
     */
    define('ECOMMERCE_SHIPMENT_LOCAL', false); //Entrega padrão para a Cidade (Ex: São Paulo, Florianópolis, false)
    define('ECOMMERCE_SHIPMENT_LOCAL_PRICE', 5.00); //Taxa de entrega local! 
    define('ECOMMERCE_SHIPMENT_LOCAL_DAYS', 1); //Máximo de dias úteis para a entrega!
    /*
     * Frete por correios!
     */
    define('ECOMMERCE_SHIPMENT_CDEMPRESA', 0); //Usuário da empresa se tiver contrato com correios!
    define('ECOMMERCE_SHIPMENT_CDSENHA', 0); //Senha da empresa se tiver contrato com correios!
    define('ECOMMERCE_SHIPMENT_SERVICE', '04014,04510'); //Tipos de serviços a serem consultados! (Consultar em Conig.inc.php Função getShipmentTag())
    define('ECOMMERCE_SHIPMENT_DELAY', 3); //Soma X dias ao prazo máximo de entrega dos correios!
    define('ECOMMERCE_SHIPMENT_FORMAT', 1); //1 Caixa/Pacote, 2 Rolo/Bobina ou 3 Envelope?
    define('ECOMMERCE_SHIPMENT_DECLARE', true); //Declarar valor da compra para seguro?
    define('ECOMMERCE_SHIPMENT_ALERT', false); //Aviso de recebimento?
    /*
     * Frete por trasportadora
     */
    define('ECOMMERCE_SHIPMENT_COMPANY', false); //Oferecer Trasportadora?
    define('ECOMMERCE_SHIPMENT_COMPANY_VAL', 5); //Valor do frete por porcentagem do valor do pedido! (4% do valor do pedido)
    define('ECOMMERCE_SHIPMENT_COMPANY_PRICE', 30); //Valor mínimo para envio via trasportadora. 100 = R$ 100
    define('ECOMMERCE_SHIPMENT_COMPANY_DAYS', 15); //Máximo de dias úteis para a entrega!
    define('ECOMMERCE_SHIPMENT_COMPANY_LINK', 'http://www.dhl.com.br/pt/express/rastreamento.html?AWB='); //Link para rastreamento (EX: http://www.dhl.com.br/pt/express/rastreamento.html?AWB=)

    /*
     * CONFIGURAÇÕES DE PAGAMENTO
     * É aconelhado criar um e-mail padrão para recebimento de pagamentos
     * como por exemplo pagamentos@site.com. E assim configurar todos os
     * meios de pagamentos nele. Para que o gestor da loja tenha acesso
     * as notificações de e-mail!
     * 
     * ATENÇÃO: Para utilizar o checkou trasparente é preciso habilitar a
     * conta junto ao PagSeguro. Para isso:
     * 
     * Acesse: https://pagseguro.uol.com.br/receba-pagamentos.jhtml#checkout-transparent
     * Clique em Regras de uso - Uma modal abre!
     * Clique em entre em contato conosco. E informe os dados solicitados!
     * 
     * PAGSEGURO
     */
    define('PAGSEGURO_ENV', 'production'); //sandbox para teste e production para vender!
    define('PAGSEGURO_EMAIL', 'vendas@valepratas.com.br'); //E-mail do vendedor na pagseguro!
    define('PAGSEGURO_NOTIFICATION_EMAIL', 'vendas@valepratas.com.br'); //E-mail para receber notificações e gerenciar pedidos!

    /*
     * SANDBOX (AMBIENTE DE TESTE)
     */
    define('PAGSEGURO_TOKEN_SANDBOX', ''); //Token Sandbox (https://sandbox.pagseguro.uol.com.br/vendedor/configuracoes.html)
    define('PAGSEGURO_APP_ID_SANDBOX', ''); //Id do APP Sandbox (https://sandbox.pagseguro.uol.com.br/aplicacao/configuracoes.html)
    define('PAGSEGURO_APP_KEY_SANDBOX', ''); //Chave do AP Sandbox

    /*
     * PRODUCTION (AMBIENTE REAL)
     */
    define('PAGSEGURO_TOKEN_PRODUCTION', 'ED1B0CFB7F03417A86155562DEADCE36'); //Token de produção (https://pagseguro.uol.com.br/preferencias/integracoes.jhtml)
    define('PAGSEGURO_APP_ID_PRODUCTION', 'sitevalepratas'); //Id do APP de integração (https://pagseguro.uol.com.br/aplicacao/listagem.jhtml)
    define('PAGSEGURO_APP_KEY_PRODUCTION', 'F00084A81414214114F3CF8CC45A883B'); //Chave do APP de integração
endif;