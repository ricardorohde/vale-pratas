<?php

/*
 * BANCO DE DADOS
 */
define('SIS_DB_HOST', 'localhost'); //Link do banco de dados
define('SIS_DB_USER', 'valeprat_novo'); //Usuário do banco de dados
define('SIS_DB_PASS', 'nzk]crHL.toX'); //Senha  do banco de dados
define('SIS_DB_DBSA', 'valeprat_novo'); //Nome  do banco de dados

/*
 * URL DO SISTEMA
 */
$COM_COMBR = (!empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'www.valepratas.com.br');
define('BASE', "http://{$COM_COMBR}"); //Url rais do site
define('THEME', 'ecommerce'); //template do site

/*
 * CACHE E CONFIG
 */
define('SIS_CACHE_TIME', 10); //Tempo em minutos de sessão
define('SIS_CONFIG_WC', true); //Registrar configuraçõs no banco para gerenciar pelo painel!
/*
 * AUTO MANAGER
 */
define('DB_AUTO_TRASH', false); //Remove todos os itens não gerenciados do banco!
define('DB_AUTO_PING', false); //Tenta enviar 1x por dia o sitemap e o RSS para o Google/Bing
/*
 * TABELAS
 */
define('DB_CONF', 'ws_config'); //Tabela de Configurações
define('DB_USERS', 'ws_users'); //Tabela de usuários
define('DB_USERS_ADDR', 'ws_users_address'); //Tabela de endereço de usuários
define('DB_POSTS', 'ws_posts'); //Tabela de posts
define('DB_POSTS_IMAGE', 'ws_posts_images'); //Tabela de imagens de posts
define('DB_CATEGORIES', 'ws_categories'); //Tabela de categorias de posts
define('DB_SEARCH', 'ws_search'); //Tabela de pesquisas
define('DB_PAGES', 'ws_pages'); //Tabela de páginas
define('DB_PAGES_IMAGE', 'ws_pages_images'); //Tabela de imagens da página
define('DB_COMMENTS', 'ws_comments'); //Tabela de Comentários
define('DB_COMMENTS_LIKES', 'ws_comments_likes'); //Tabela GOSTEI dos Comentários
define('DB_PDT', 'ws_products'); //Tabela de produtos
define('DB_PDT_IMAGE', 'ws_products_images'); //Tabela de imagem de produtos
define('DB_PDT_GALLERY', 'ws_products_gallery'); //Tabela de galeria de produtos
define('DB_PDT_CATS', 'ws_products_categories'); //Tabela de categorias de produtos
define('DB_PDT_BRANDS', 'ws_products_brands'); //Tabela de fabricantes/marcas de produtos
define('DB_PDT_COUPONS', 'ws_products_coupons'); //Tabela de Cupons de desconto
define('DB_ORDERS', 'ws_orders'); //Tabela de pedidos
define('DB_ORDERS_ITEMS', 'ws_orders_items'); //Tabela de itens do pedido
define('DB_VIEWS_VIEWS', 'ws_siteviews_views'); //Controle de acesso ao site
define('DB_VIEWS_ONLINE', 'ws_siteviews_online'); //Controle de usuários online

/*
  AUTO LOAD DE CLASSES
 */

function MyAutoLoad($Class) {
    $cDir = ['Conn', 'Helpers', 'Models'];
    $iDir = null;

    foreach ($cDir as $dirName):
        if (!$iDir && file_exists(__DIR__ . '/' . $dirName . '/' . $Class . '.class.php') && !is_dir(__DIR__ . '/' . $dirName . '/' . $Class . '.class.php')):
            include_once (__DIR__ . '/' . $dirName . '/' . $Class . '.class.php');
            $iDir = true;
        endif;
    endforeach;
}

spl_autoload_register("MyAutoLoad");
/*
 * Define todas as constantes do banco dando sua devida preferência!
 */
define('LDEV', 'valepratas');
$WorkControlDefineConf = null;
if (SIS_CONFIG_WC):
    $Read = new Read;
    $Read->FullRead("SELECT conf_key, conf_value FROM " . DB_CONF);
    if ($Read->getResult()):
        foreach ($Read->getResult() as $WorkControlDefineConf):
            define("{$WorkControlDefineConf['conf_key']}", "{$WorkControlDefineConf['conf_value']}");
        endforeach;
        $WorkControlDefineConf = true;
    endif;
endif;

require 'Config/Config.inc.php';
require 'Config/Agency.inc.php';
require 'Config/Client.inc.php';
Conn::Ldev();
/*
 * Exibe erros lançados
 */

function Erro($ErrMsg, $ErrNo = null) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? 'trigger_info' : ($ErrNo == E_USER_WARNING ? 'trigger_alert' : ($ErrNo == E_USER_ERROR ? 'trigger_error' : 'trigger_success')));
    echo "<div class='trigger {$CssClass}'>{$ErrMsg}<span class='ajax_close'></span></div>";
}

/*
 * Exibe erros lançados por ajax
 */

function AjaxErro($ErrMsg, $ErrNo = null) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? 'trigger_info' : ($ErrNo == E_USER_WARNING ? 'trigger_alert' : ($ErrNo == E_USER_ERROR ? 'trigger_error' : 'trigger_success')));
    return "<div class='trigger trigger_ajax {$CssClass}'>{$ErrMsg}<span class='ajax_close'></span></div>";
}

/*
 * personaliza o gatilho do PHP
 */

function PHPErro($ErrNo, $ErrMsg, $ErrFile, $ErrLine) {
    echo "<div class='trigger trigger_error'>";
    echo "<b>Erro na Linha: #{$ErrLine} ::</b> {$ErrMsg}<br>";
    echo "<small>{$ErrFile}</small>";
    echo "<span class='ajax_close'></span></div>";

    if ($ErrNo == E_USER_ERROR):
        die;
    endif;
}

set_error_handler('PHPErro');


/*
 * Descreve nivel de usuário
 */

function getWcLevel($Level = null) {
    $UserLevel = [
        1 => 'Cliente (user)',
        2 => 'Assinante (user)',
        3 => 'Vendedor (user)',
        6 => 'Colaborador (adm)',
        7 => 'Suporte Geral (adm)',
        8 => 'Gerente Geral (adm)',
        9 => 'Administrador (adm)',
        10 => 'Super Admin (adm)'
    ];

    if (!empty($Level)):
        return $UserLevel[$Level];
    else:
        return $UserLevel;
    endif;
}

/*
 * Descreve estatus de pedidos
 */

function getOrderStatus($Status = null) {
    $OrderStatus = [
        1 => 'Concluído',
        2 => 'Cancelado',
        3 => 'Novo Pedido',
        4 => 'Agd. Pagamento', //OPERADORA
        5 => 'Agd. Pagamento', //CONFIRMAÇÃO MANUAL (BOLETO, DEPÓSITO)
        6 => 'Processando'
    ];

    if (!empty($Status)):
        return $OrderStatus[$Status];
    else:
        return $OrderStatus;
    endif;
}

/*
 * Descreve tipos de pagamentos
 */

function getOrderPayment($Payment = null) {
    $Payments = [
        1 => 'Pendente',
        101 => 'Cartão de Crédito', //PAGSEGURO
        102 => 'Boleto Bancário' //PAGSEGURO
    ];

    if (!empty($Payment)):
        return $Payments[$Payment];
    else:
        return $Payments;
    endif;
}

/*
 * Recupera Meios de Entrega
 */

function getShipmentTag($Tag = null) {
    $ArrShipment = [
        '10001' => 'Envio Padrão', //Código para envio pela trasportadora
        '10002' => 'Envio Gratis', //Código para envio sem custo
        '10003' => 'Envio Fixo', //Código para envio de frete fixo
        '10004' => 'Taxa de Entrega', //Tava de Entrega
        '04014' => 'Sedex', //40010 SEDEX sem contrato. //novo 04014  //antigo 40010
        '4014' => 'Sedex', //40010 SEDEX sem contrato. //novo 04014  //antigo 40010
        '40045' => 'Sedex a Cobrar', //40045 SEDEX a Cobrar, sem contrato.
        '40126' => 'Sedex a Cobrar', //40126 SEDEX a Cobrar, com contrato.
        '40215' => 'Sedex 10', //40215 SEDEX 10, sem contrato.
        '40290' => 'Sedex Hoje', //40290 SEDEX Hoje, sem contrato.
        '40096' => 'Sedex', //40096 SEDEX com contrato.
        '40436' => 'Sedex', //40436 SEDEX com contrato.
        '40444' => 'Sedex', //40444 SEDEX com contrato.
        '40568' => 'Sedex', //40568 SEDEX com contrato.
        '40606' => 'Sedex', //40606 SEDEX com contrato.
        '04510' => 'PAC', //41106 PAC sem contrato. //novo 04510 //antigo 41106
        '4510' => 'PAC', //41106 PAC sem contrato. //novo 04510 //antigo 41106
        '41068' => 'PAC', //41068 PAC com contrato.
        '81019' => 'e-Sedex', //81019 e-SEDEX, com contrato.
        '81027' => 'e-Sedex Prioritário', //81027 e-SEDEX Prioritário, com contrato.
        '81035' => 'e-Sedex Express', //81035 e-SEDEX Express, com contrato.
        '81868' => 'e-Sedex', //81868 (Grupo 1) e-SEDEX, com contrato.
        '81833' => 'e-Sedex', //81833 (Grupo 2) e-SEDEX, com contrato.
        '81850' => 'e-Sedex' //81850 (Grupo 3) e-SEDEX, com contrato.
    ];

    if (!empty($Tag) && array_key_exists($Tag, $ArrShipment)):
        return $ArrShipment[$Tag];
    else:
        return $ArrShipment;
    endif;
}
