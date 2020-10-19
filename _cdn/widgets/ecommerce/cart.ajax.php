<?php

session_start();

$getPost = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (empty($getPost) || empty($getPost['action'])):
    die('Acesso Negado!');
endif;

$strPost = array_map('strip_tags', $getPost);
$POST = array_map('trim', $strPost);

$Action = $POST['action'];
$jSON = null;
unset($POST['action']);

usleep(2000);

if (empty($_SESSION['wc_shipment_zip'])):
    unset($_SESSION['wc_shipment']);
endif;

require '../../../_app/Config.inc.php';
$Read = new Read;
$Create = new Create;
$Update = new Update;

$compleURL = '';
$uri = getenv('REQUEST_URI');
$separado_pagina = explode('vend=', $uri);
if (isset($separado_pagina[1]) && $separado_pagina[1] != NULL && $separado_pagina[1] != "") {

    $vendedor = base64_decode($separado_pagina[1]);
    $separa_vendedor = explode('=', $vendedor);
    if (is_numeric((int) $separa_vendedor[1])) {
        $_GET["idVendedor"] = $separa_vendedor[1];
        echo '<input name="idvendedor" type="hidden" value="', $separa_vendedor[1], '"/>';
        $compleURL = '?vend=' . $separado_pagina[1];
    }
}


switch ($Action):
    //CART ADD
    case 'wc_cart_add':
        if (empty($_SESSION['wc_order'])):
            $_SESSION['wc_order'] = array();
        endif;

        $Read->FullRead("SELECT pdt_title, pdt_inventory FROM " . DB_PDT . " WHERE pdt_id = :id", "id={$POST['pdt_id']}");
        if (!$Read->getResult()):
            $jSON['trigger'] = AjaxErro("<b>OPPSSS:</b> O produto solicitado não foi encontrado. Por favor, tente novamente!", E_USER_NOTICE);
        else:
            if (empty($_SESSION['wc_order'][$POST['pdt_id']])) {
                $_SESSION['wc_order'][$POST['pdt_id']] = intval($POST['item_amount']);
            } else {
                $_SESSION['wc_order'][$POST['pdt_id']] += intval($POST['item_amount']);
            }
            if (isset($_POST["idvendedor"]) && $_POST["idvendedor"] != NULL && $_POST["idvendedor"] != "") {
                $_SESSION['wc_order']["idvendedor"][] = array('idproduto' => $POST['pdt_id'], 'codvendedor' => $_POST["idvendedor"]);
            }
            //STOCK CONTROL
            if ($Read->getResult()[0]['pdt_inventory'] <= $_SESSION['wc_order'][$POST['pdt_id']] && $Read->getResult()[0]['pdt_inventory']):
                $_SESSION['wc_order'][$POST['pdt_id']] = intval($Read->getResult()[0]['pdt_inventory']);
            endif;
            $jSON['cart_product'] = $Read->getResult()[0]['pdt_title'];
        endif;

        $jSON['cart_amount'] = count($_SESSION['wc_order']);
        break;

    //CART REMOVE
    case 'wc_cart_remove':
        unset($_SESSION['wc_order'][$POST['pdt_id']]);

        $CartTotal = 0;
        foreach ($_SESSION['wc_order'] as $ItemId => $ItemAmount):
            $Read->FullRead("SELECT pdt_price, pdt_offer_price, pdt_offer_start, pdt_offer_end FROM " . DB_PDT . " WHERE pdt_id = :id", "id={$ItemId}");
            if (!$Read->getResult()):
                unset($_SESSION['wc_order'][$ItemId]);
            else:
                extract($Read->getResult()[0]);
                $CartTotal += ($pdt_offer_price && $pdt_offer_start <= date('Y-m-d H:i:s') && $pdt_offer_end >= date('Y-m-d H:i:s') ? $pdt_offer_price : $pdt_price) * $ItemAmount;
            endif;
        endforeach;

        $CartPrice = (empty($_SESSION['wc_cupom']) ? $CartTotal : $CartTotal * ((100 - $_SESSION['wc_cupom']) / 100));
        $jSON['cart_total'] = number_format($CartTotal, '2', ',', '.');
        $jSON['cart_price'] = number_format($CartPrice, '2', ',', '.');
        break;

    //CART CHANGE
    case 'wc_cart_change':
        $_SESSION['wc_order'][$POST['pdt_id']] = intval($POST['item_amount']);

        $Read->FullRead("SELECT pdt_price, pdt_offer_price, pdt_offer_start, pdt_offer_end FROM " . DB_PDT . " WHERE pdt_id = :id", "id={$POST['pdt_id']}");
        if ($Read->getResult()):
            $ItemData = $Read->getResult()[0];
            $ItemDataPrice = ($ItemData['pdt_offer_price'] && $ItemData['pdt_offer_start'] <= date('Y-m-d H:i:s') && $ItemData['pdt_offer_end'] >= date('Y-m-d H:i:s') ? $ItemData['pdt_offer_price'] : $ItemData['pdt_price']);
            $jSON['cart_item'] = "R$ " . number_format($ItemDataPrice * intval($POST['item_amount']), '2', ',', '.');
        endif;

        $CartTotal = 0;
        foreach ($_SESSION['wc_order'] as $ItemId => $ItemAmount):
            $Read->FullRead("SELECT pdt_price, pdt_offer_price, pdt_offer_start, pdt_offer_end FROM " . DB_PDT . " WHERE pdt_id = :id", "id={$ItemId}");
            if (!$Read->getResult()):
                unset($_SESSION['wc_order'][$ItemId]);
            else:
                extract($Read->getResult()[0]);
                $CartTotal += ($pdt_offer_price && $pdt_offer_start <= date('Y-m-d H:i:s') && $pdt_offer_end >= date('Y-m-d H:i:s') ? $pdt_offer_price : $pdt_price) * $ItemAmount;
            endif;
        endforeach;

        $CartPrice = (empty($_SESSION['wc_cupom']) ? $CartTotal : $CartTotal * ((100 - $_SESSION['wc_cupom']) / 100));
        $jSON['cart_total'] = number_format($CartTotal, '2', ',', '.');
        $jSON['cart_price'] = number_format($CartPrice, '2', ',', '.');
        break;

    //ADD CUPOM
    case 'cart_cupom':
        $Read->FullRead("SELECT cp_id, cp_title, cp_discount, cp_hits FROM " . DB_PDT_COUPONS . " WHERE cp_start <= NOW() AND cp_end >= NOW() AND cp_coupon = :cp", "cp={$POST['cupom_id']}");
        if (!$Read->getResult()):
            unset($_SESSION['wc_cupom'], $_SESSION['wc_cupom_code']);
            $jSON['trigger'] = AjaxErro("<b>OPPSSS:</b> Desculpe mas o cupom <b>{$POST['cupom_id']}</b> não existe ou está com sua oferta inativa hoje :(", E_USER_WARNING);
        else:
            $Coupon = $Read->getResult()[0];
            $_SESSION['wc_cupom'] = $Coupon['cp_discount'];
            $_SESSION['wc_cupom_code'] = $POST['cupom_id'];
            $UpdateCupom = ['cp_hits' => $Coupon['cp_hits'] + 1];
            $Update->ExeUpdate(DB_PDT_COUPONS, $UpdateCupom, "WHERE cp_id = :cp", "cp={$Coupon['cp_id']}");
            $jSON['trigger'] = AjaxErro("Parabéns, o seu cupom <b>{$Coupon['cp_title']}</b> com <b>{$Coupon['cp_discount']}% de desconto</b> foi aplicado com sucesso :)");
        endif;

        $CartTotal = 0;
        foreach ($_SESSION['wc_order'] as $ItemId => $ItemAmount):
            $Read->FullRead("SELECT pdt_price, pdt_offer_price, pdt_offer_start, pdt_offer_end FROM " . DB_PDT . " WHERE pdt_id = :id", "id={$ItemId}");
            if (!$Read->getResult()):
                unset($_SESSION['wc_order'][$ItemId]);
            else:
                extract($Read->getResult()[0]);
                $CartTotal += ($pdt_offer_price && $pdt_offer_start <= date('Y-m-d H:i:s') && $pdt_offer_end >= date('Y-m-d H:i:s') ? $pdt_offer_price : $pdt_price) * $ItemAmount;
            endif;
        endforeach;

        $CartPrice = (empty($_SESSION['wc_cupom']) ? $CartTotal : $CartTotal * ((100 - $_SESSION['wc_cupom']) / 100));
        $jSON['cart_cupom'] = (!empty($_SESSION['wc_cupom']) ? $_SESSION['wc_cupom'] : 0);
        $jSON['cart_price'] = number_format($CartPrice, '2', ',', '.');
        break;

    //CART SHIPMENT
    case 'cart_shipment':
        $CartTotal = 0;
        $HeightTotal = 0;
        $WidthTotal = 0;
        $DepthTotal = 0;
        $WeightTotal = 0;
        foreach ($_SESSION['wc_order'] as $ItemId => $ItemAmount):
            $Read->ExeRead(DB_PDT, "WHERE pdt_id = :id", "id={$ItemId}");
            if (!$Read->getResult()):
                unset($_SESSION['wc_order'][$ItemId]);
            else:
                extract($Read->getResult()[0]);
                $CartTotal += ($pdt_offer_price && $pdt_offer_start <= date('Y-m-d H:i:s') && $pdt_offer_end >= date('Y-m-d H:i:s') ? $pdt_offer_price : $pdt_price) * $ItemAmount;
                $HeightTotal = ($HeightTotal < $pdt_dimension_heigth ? $pdt_dimension_heigth : $HeightTotal);
                $WidthTotal += $pdt_dimension_width * $ItemAmount;
                $DepthTotal += $pdt_dimension_depth * $ItemAmount;
                $WeightTotal += $pdt_dimension_weight * $ItemAmount;
            endif;
        endforeach;

        $CartTotalShip = number_format($CartTotal, '2', ',', '');
        $WeightTotalShip = floatval($WeightTotal / 1000);
        $HeightTotalShip = ($HeightTotal >= 2 ? $HeightTotal : 2);
        $WidthTotalShip = ($WidthTotal >= 11 ? $WidthTotal : 11);
        $DepthTotalShip = ($DepthTotal >= 16 ? $DepthTotal : 16);

        $jSON['m'] = $HeightTotal;


        $data['nCdEmpresa'] = ECOMMERCE_SHIPMENT_CDEMPRESA;
        $data['sDsSenha'] = ECOMMERCE_SHIPMENT_CDSENHA;
        $data['sCepOrigem'] = str_replace('-', '', SITE_ADDR_ZIP);
        $data['sCepDestino'] = str_replace('-', '', $POST['zipcode']);
        $data['nVlPeso'] = $WeightTotalShip;
        $data['nCdFormato'] = ECOMMERCE_SHIPMENT_FORMAT;
        $data['nVlComprimento'] = $DepthTotalShip;
        $data['nVlAltura'] = $HeightTotalShip;
        $data['nVlLargura'] = $WidthTotalShip;
        $data['nVlDiametro'] = '0';
        $data['sCdMaoPropria'] = 's';
        $data['nVlValorDeclarado'] = (ECOMMERCE_SHIPMENT_DECLARE ? $CartTotalShip : '0');
        $data['sCdAvisoRecebimento'] = (ECOMMERCE_SHIPMENT_ALERT ? 's' : 'n');
        $data['StrRetorno'] = 'xml';
        $data['nCdServico'] = ECOMMERCE_SHIPMENT_SERVICE;
        $data = http_build_query($data);

        $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';
        $curl = curl_init($url . '?' . $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $resultXml = simplexml_load_string($result);

        $jSON['cart_shipment'] = null;
        foreach ($resultXml->cServico as $row) :
            if ($row->Erro == 0) :
                $jSON['cart_shipment'] .= "<label class='shiptag'><input required class='wc_shipment' name='shipment' value='" . str_replace(',', '.', $row->Valor) . "' type='radio' id='{$row->Codigo}'/> " . getShipmentTag("{$row->Codigo}") . ": 01 a " . str_pad($row->PrazoEntrega + ECOMMERCE_SHIPMENT_DELAY, 2, 0, 0) . " dias úteis - R$ {$row->Valor}</label>";
            endif;
        endforeach;

        if (!empty($row->Erro) && $row->Erro == '-3'):
            $jSON['trigger'] = AjaxErro("<b>OPPSSS:</b> O CEP digitado não foi encontrado na base dos correios. Confira isso :)", E_USER_WARNING);
            $jSON['reset'] = true;
            $ErroZip = true;
            unset($_SESSION['wc_shipment'], $_SESSION['wc_shipment_zip']);
        endif;

        $CompanyPrice = $CartTotal * (ECOMMERCE_SHIPMENT_COMPANY_VAL / 100);
        if (ECOMMERCE_SHIPMENT_COMPANY && $CompanyPrice >= ECOMMERCE_SHIPMENT_COMPANY_PRICE && empty($ErroZip)):
            $jSON['cart_shipment'] .= "<label class='shiptag'><input required class='wc_shipment' name='shipment' value='{$CompanyPrice}' type='radio' id='10001'/> Envio Padrão: 01 a " . str_pad(ECOMMERCE_SHIPMENT_DELAY + ECOMMERCE_SHIPMENT_COMPANY_DAYS, 2, 0, 0) . " dias úteis - R$ " . number_format($CompanyPrice, '2', ',', '.') . "</label>";
        endif;

        $CartPrice = (empty($_SESSION['wc_cupom']) ? $CartTotal : $CartTotal * ((100 - $_SESSION['wc_cupom']) / 100));
        if (ECOMMERCE_SHIPMENT_FREE && $CartPrice > ECOMMERCE_SHIPMENT_FREE && empty($ErroZip)):
            $jSON['cart_shipment'] .= "<label class='shiptag'><input required class='wc_shipment' name='shipment' value='0' type='radio' id='10002'/> Gratuito: 01 a " . str_pad(ECOMMERCE_SHIPMENT_DELAY + ECOMMERCE_SHIPMENT_FREE_DAYS, 2, 0, 0) . " dias úteis - R$ 0,00</label>";
        endif;

        if (ECOMMERCE_SHIPMENT_FIXED):
            $jSON['cart_shipment'] .= "<label class='shiptag'><input required class='wc_shipment' name='shipment' value='" . ECOMMERCE_SHIPMENT_FIXED_PRICE . "' type='radio' id='10003'/> Frete Fixo: 01 a " . str_pad(ECOMMERCE_SHIPMENT_DELAY + ECOMMERCE_SHIPMENT_FIXED_DAYS, 2, 0, 0) . " dias úteis - R$ " . number_format(ECOMMERCE_SHIPMENT_FIXED_PRICE, 2, ',', '.') . "</label>";
        endif;

        if (ECOMMERCE_SHIPMENT_LOCAL):
            $City = json_decode(file_get_contents("https://viacep.com.br/ws/" . str_replace('-', '', $POST['zipcode']) . "/json/"));
            if (!empty($City) && !empty($City->localidade) && $City->localidade == ECOMMERCE_SHIPMENT_LOCAL):
                $jSON['cart_shipment'] = "<label class='shiptag'><input required class='wc_shipment' name='shipment' value='" . ECOMMERCE_SHIPMENT_LOCAL_PRICE . "' type='radio' id='10004'/> Taxa de entrega: R$ " . number_format(ECOMMERCE_SHIPMENT_LOCAL_PRICE, 2, ',', '.') . "</label>";
            endif;
        endif;

        if (empty($jSON['cart_shipment']) && empty($ErroZip)):
            $jSON['trigger'] = AjaxErro("<b>OPPSSS:</b> Não existem opções de entrega para o pedido autal. Você pode remover ou adicionar alguns produtos para tentar novamente!<p>Ou caso queira, entre em contato para que possamos te ajudar!</p><p>Fone: " . SITE_ADDR_PHONE_A . "<br>E-mail: " . SITE_ADDR_EMAIL . "</p>", E_USER_WARNING);
        elseif (empty($ErroZip)):
            $_SESSION['wc_shipment_zip'] = $POST['zipcode'];
        endif;
        break;

    //SHIPMENT SELECT
    case 'cart_shipment_select':
        $_SESSION['wc_shipment'] = $POST;

        $CartTotal = 0;
        foreach ($_SESSION['wc_order'] as $ItemId => $ItemAmount):
            $Read->FullRead("SELECT pdt_price, pdt_offer_price, pdt_offer_start, pdt_offer_end FROM " . DB_PDT . " WHERE pdt_id = :id", "id={$ItemId}");
            if (!$Read->getResult()):
                unset($_SESSION['wc_order'][$ItemId]);
            else:
                extract($Read->getResult()[0]);
                $CartTotal += ($pdt_offer_price && $pdt_offer_start <= date('Y-m-d H:i:s') && $pdt_offer_end >= date('Y-m-d H:i:s') ? $pdt_offer_price : $pdt_price) * $ItemAmount;
            endif;
        endforeach;

        $CartPrice = (empty($_SESSION['wc_cupom']) ? $CartTotal : $CartTotal * ((100 - $_SESSION['wc_cupom']) / 100));
        $jSON['cart_total'] = number_format($CartTotal, '2', ',', '.');
        $jSON['cart_ship'] = number_format($_SESSION['wc_shipment']['wc_shipprice'], '2', ',', '.');
        $jSON['cart_price'] = number_format($CartPrice + $_SESSION['wc_shipment']['wc_shipprice'], '2', ',', '.');
        break;

    //LOOK USER E-MAIL
    case 'wc_order_email':
        if (empty($POST['user_email'])):
            $jSON['error'] = "<p class='wc_order_error'>&#10008; Informe seu e-mail!</p>";
        elseif (!Check::Email($POST['user_email']) || !filter_var($POST['user_email'])):
            $jSON['error'] = "<p class='wc_order_error'>&#10008; Este não é um e-mail válido!</p>";
        else:
            $Read->FullRead("SELECT user_name, user_lastname, user_document, user_cell FROM " . DB_USERS . " WHERE user_email = :mm", "mm={$POST['user_email']}");
            if ($Read->getResult()):
                $jSON = $Read->getResult()[0];
                $jSON['user'] = true;
            else:
                $jSON['user'] = null;
            endif;
        endif;
        break;

    //USER AUTENTICATION
    case 'wc_order_user':
        if (in_array('', $POST)):
            $jSON['error'] = "<p class='wc_order_error'>&#10008; Preencha esse campo!</p>";
        elseif (!Check::Email($POST['user_email']) || !filter_var($POST['user_email'], FILTER_VALIDATE_EMAIL)):
            $jSON['field'] = 'user_email';
            $jSON['error'] = "<p class='wc_order_error'>&#10008; Este não é um e-mail válido!</p>";
        elseif (!empty($POST['user_document']) && !Check::CPF($POST['user_document'])):
            $jSON['field'] = 'user_document';
            $jSON['error'] = "<p class='wc_order_error'>&#10008; Este não é um CPF válido!</p>";
        elseif (strlen($POST['user_password']) < 5 || strlen($POST['user_password']) > 11):
            $jSON['field'] = 'user_password';
            $jSON['error'] = "<p class='wc_order_error'>&#10008; A senha deve ter entre 5 e 11 caracteres!</p>";
        else:
            $Read->FullRead("SELECT user_id FROM " . DB_USERS . " WHERE user_email = :mm", "mm={$POST['user_email']}");
            if (!$Read->getResult()):
                $Read->FullRead("SELECT user_email FROM " . DB_USERS . " WHERE user_document = :dc", "dc={$POST['user_document']}");
                if ($Read->getResult()):
                    $jSON['field'] = 'user_document';
                    $jSON['error'] = "<p class='wc_order_error'>&#10008; CPF já cadastrado em <b>{$Read->getResult()[0]['user_email']}</b>!</p>";
                else:
                    //CREATE NEWU USER
                    $UserPassBook = str_repeat("*", strlen($POST['user_password']) - 4) . substr($POST['user_password'], strlen($POST['user_password']) - 4);
                    $POST['user_password'] = hash('sha512', $POST['user_password']);
                    $POST['user_channel'] = 'Novo pedido';
                    $POST['user_registration'] = date('Y-m-d H:i:s');
                    $POST['user_level'] = 1;

                    $Create->ExeCreate(DB_USERS, $POST);
                    $POST['user_id'] = $Create->getResult();
                    $_SESSION['userLogin'] = $POST;

                    //SEND CREATE ACCOUNT
                    require_once 'cart.email.php';
                    $BodyMail = "
                        <p style='font-size: 1.3em'>Caro(a) {$POST['user_name']},</p>
                        <p>Este e-mail é para dar a você as boas vindas a nosso site!</p>
                        <p>Uma nova conta foi criada para que você possa ter mais comodidade e agilidade ao interagir conosco. Ao logar-se em sua conta você pode:</p>
                        <p>
                        ✓ Atualizar seus dados pessoais!<br>
                        ✓ Acompanhar o andamento dos seus pedidos!<br>
                        ✓ Realizar novos pedidos com mais agilidade!<br>
                        ✓ Ter acesso a ofertas exclusivas do site por e-mail!
                        </p>
                        <p>Confira abaixo os dados de acesso a sua conta:</p>
                        <p style='font-size: 1.1em'>
                            Login: {$POST['user_email']}<br>
                            Senha: {$UserPassBook}<br>
                        </p>
                        <p><a title='Minha Conta' target='_blank' href='" . BASE . "/conta/login'>Acessar Minha Conta!</a></p>
                        <p>Ao acessar nosso site você pode usar esses dados para identificar-se, e assim ter acesso ao melhor do nosso conteúdo...</p>
                        <p><b>Seja muito bem-vindo(a) {$POST['user_name']}...</b></p>
                        <p><i>Atenciosamente, " . SITE_NAME . "!</i></p>
                    ";
                    $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                    $SendEmail = new Email;
                    $SendEmail->EnviarMontando("Seja bem-vindo(a) {$POST['user_name']}", $Mensagem, SITE_NAME, MAIL_USER, "{$POST['user_name']} {$POST['user_lastname']}", $POST['user_email']);
                    $jSON['success'] = BASE . '/pedido/endereco?vend=' . $separado_pagina[1] . '#cart';
                endif;
            else:
                //LOGIN ACTUAL USER
                $UserEmail = $POST['user_email'];
                $UserPass = hash("sha512", $POST['user_password']);
                $Read->ExeRead(DB_USERS, "WHERE user_email = :em AND user_password = :ps", "em={$UserEmail}&ps={$UserPass}");
                if ($Read->getResult()):
                    unset($POST['user_email'], $POST['user_password']);
                    $Update->ExeUpdate(DB_USERS, $POST, "WHERE user_id = :id", "id={$Read->getResult()[0]['user_id']}");
                    $_SESSION['userLogin'] = $Read->getResult()[0];
                    $jSON['success'] = BASE . '/pedido/endereco' . $compleURL . '#cart';
                else:
                    $jSON['field'] = 'user_password';
                    $jSON['error'] = "<p class='wc_order_error'>&#10008; A senha informada não confere! <a title='Recuperar Senha!' href='" . BASE . "/conta/recuperar'>[ Esqueceu sua senha? ]</a></p>";
                endif;
            endif;
        endif;
        break;

    //WORK CONTROL ADDR SELECT
    case 'wc_addr_select':
        $_SESSION['wc_order_addr'] = $POST['addr_id'];
        $jSON['addr'] = $POST['addr_id'];
        break;

    //WORK CONTRL ORDER CREATE
    case 'wc_order_create':
        //ERROR KEY
        $CartError = null;

        if (empty($_SESSION['userLogin'])):
            $jSON['trigger'] = AjaxErro("<b>Erro:</b> Desculpe! Mas não foi possível obter seus dados pessoais para o pedido!<p><b>Atualize a página para tentar novamente!</b></p>", E_USER_ERROR);
        else:
            //NOVO ENDEREÇO
            if (!empty($POST['addr_name'])):
                $UpdateAddr = ['addr_key' => null];
                $Update->ExeUpdate(DB_USERS_ADDR, $UpdateAddr, "WHERE user_id = :id", "id={$_SESSION['userLogin']['user_id']}");

                $AddrCheck = $POST;
                unset($AddrCheck['addr_complement']);
                if (in_array('', $AddrCheck)):
                    $jSON['form_error'] = "<p class='wc_order_error'>&#10008; Preencha esse campo!</p>";
                    $CartError = true;
                else:
                    $NewAddr = [
                        'user_id' => $_SESSION['userLogin']['user_id'],
                        'addr_key' => 1,
                        'addr_name' => $POST['addr_name'],
                        'addr_zipcode' => $POST['addr_zipcode'],
                        'addr_street' => $POST['addr_street'],
                        'addr_number' => $POST['addr_number'],
                        'addr_complement' => (!empty($POST['addr_complement']) ? $POST['addr_complement'] : null),
                        'addr_district' => $POST['addr_district'],
                        'addr_city' => $POST['addr_city'],
                        'addr_state' => $POST['addr_state'],
                        'addr_country' => 'Brasil'
                    ];
                    $Create->ExeCreate(DB_USERS_ADDR, $NewAddr);
                    $_SESSION['wc_order_addr'] = $Create->getResult();
                endif;
            endif;

            if (!$CartError):
                //ORDER MOUNT
                $CartTotal = 0;
                foreach ($_SESSION['wc_order'] as $ItemId => $ItemAmount):
                    $Read->FullRead("SELECT pdt_title, pdt_id, pdt_price, pdt_offer_price, pdt_offer_start, pdt_offer_end FROM " . DB_PDT . " WHERE pdt_id = :id", "id={$ItemId}");
                    if ($Read->getResult()):
                        extract($Read->getResult()[0]);
                        $CartTotal += ($pdt_offer_price && $pdt_offer_start <= date('Y-m-d H:i:s') && $pdt_offer_end >= date('Y-m-d H:i:s') ? $pdt_offer_price : $pdt_price) * $ItemAmount;

                        $CartOrdeItens[] = [
                            'pdt_id' => $pdt_id,
                            'item_name' => $pdt_title,
                            'item_price' => ($pdt_offer_price && $pdt_offer_start <= date('Y-m-d H:i:s') && $pdt_offer_end >= date('Y-m-d H:i:s') ? $pdt_offer_price : $pdt_price),
                            'item_amount' => $ItemAmount,
                            'idvendedor' => isset($_POST["codvendedor"]) ? $_POST["codvendedor"] : 0
                        ];
                    endif;
                endforeach;
                $CartPrice = (empty($_SESSION['wc_cupom']) ? $CartTotal : $CartTotal * ((100 - $_SESSION['wc_cupom']) / 100));
                $CartTotalPrice = (empty($_SESSION['wc_shipment']['wc_shipprice']) ? $CartPrice : $CartPrice + $_SESSION['wc_shipment']['wc_shipprice']);

                //ORDER CREATE
                $NewOrder = [
                    'user_id' => $_SESSION['userLogin']['user_id'],
                    'order_status' => 3,
                    'order_coupon' => (!empty($_SESSION['wc_cupom']) ? $_SESSION['wc_cupom'] : null),
                    'order_price' => $CartTotalPrice,
                    'order_payment' => 1,
                    'order_addr' => $_SESSION['wc_order_addr'],
                    'order_shipcode' => (!empty($_SESSION['wc_shipment']['wc_shipcode']) ? $_SESSION['wc_shipment']['wc_shipcode'] : null),
                    'order_shipprice' => (!empty($_SESSION['wc_shipment']['wc_shipprice']) ? $_SESSION['wc_shipment']['wc_shipprice'] : null),
                    'order_date' => date('Y-m-d H:i:s')
                ];
                $Create->ExeCreate(DB_ORDERS, $NewOrder);
                $OrderCreateId = $Create->getResult();

                //ORDER ITENS CREATE
                foreach ($CartOrdeItens as $Key => $Value):
                    $CartOrdeItens[$Key]['order_id'] = $OrderCreateId;
                endforeach;
                $Create->ExeCreateMulti(DB_ORDERS_ITEMS, $CartOrdeItens);

                if (ECOMMERCE_STOCK == 'cart'):
                    $Read->FullRead("SELECT pdt_id, pdt_inventory, pdt_delivered FROM " . DB_PDT . " WHERE pdt_id IN(SELECT pdt_id FROM " . DB_ORDERS_ITEMS . " WHERE order_id = :ord AND pdt_id IS NOT NULL)", "ord={$OrderCreateId}");
                    if ($Read->getResult()):
                        foreach ($Read->getResult() as $InventoryPdt):
                            $Read->FullRead("SELECT item_amount FROM " . DB_ORDERS_ITEMS . " WHERE order_id = :ord AND pdt_id = :pdt", "ord={$OrderCreateId}&pdt={$InventoryPdt['pdt_id']}");
                            if ($Read->getResult()):
                                $UpdateProduct = [
                                    'pdt_inventory' => ($InventoryPdt['pdt_inventory'] ? $InventoryPdt['pdt_inventory'] - $Read->getResult()[0]['item_amount'] : null),
                                    'pdt_delivered' => $InventoryPdt['pdt_delivered'] + $Read->getResult()[0]['item_amount']
                                ];
                                $Update->ExeUpdate(DB_PDT, $UpdateProduct, "WHERE pdt_id = :pdt", "pdt={$InventoryPdt['pdt_id']}");
                            endif;
                        endforeach;
                    endif;
                endif;

                //ENVIA E-MAIL DE PEDIDO CONCLUÍDO
                $BodyMail = "<p style='font-size: 1.2em;'>Caro(a) {$_SESSION['userLogin']['user_name']},</p>";
                $BodyMail .= "<p>Obrigado pela preferência. informamos que seu pedido #" . str_pad($OrderCreateId, 7, 0, 0) . " foi registrado com sucesso em nosso site.</p>";
                $BodyMail .= "<p>Neste momento estamos apenas esperando a confirmação do pagamento para envia-lo a você...</p>";
                $BodyMail .= "<p>Ainda não pagou? <a href='" . BASE . "/pedido/pagamento/" . base64_encode($OrderCreateId) . "#cart' title=''>PAGAR AGORA!</p></p>";
                $BodyMail .= "<p style='font-size: 1.4em;'>Confira os detalhes do seu pedido:</p>";
                $BodyMail .= "<p>Pedido: <a href='" . BASE . "/conta/pedido/{$OrderCreateId}' title='Ver pedido' target=''>" . str_pad($OrderCreateId, 7, 0, STR_PAD_LEFT) . "</a><br>Data: " . date('d/m/Y H\hi', strtotime($NewOrder['order_date'])) . "<br>Valor: R$ " . number_format($NewOrder['order_price'], '2', ',', '.') . "</p>";
                $BodyMail .= "<hr><table style='width: 100%'><tr><td>STATUS:</td><td style='color: #00AD8E; text-align: center;'>✓ Aguardando Pagamento</td><td style='color: #888888;  text-align: center;'>✓ Processando</td><td style='color: #888888; text-align: right;'>✓ Concluído</td></tr></table><hr>";
                $Read->ExeRead(DB_ORDERS_ITEMS, "WHERE order_id = :order", "order={$OrderCreateId}");
                if ($Read->getResult()):
                    $i = 0;
                    $ItemsPrice = 0;
                    $ItemsAmount = 0;
                    $BodyMail .= "<p style='font-size: 1.4em;'>Produtos:</p>";
                    $BodyMail .= "<p>Abaixo você pode conferir os detalhes, quantidades e valores de cada produto adquirido em seu pedido. Confira:</p>";
                    $BodyMail .= "<table style='width: 100%' border='0' cellspacing='0' cellpadding='0'>";
                    foreach ($Read->getResult() as $Item):
                        $i++;
                        $ItemsAmount += $Item['item_amount'];
                        $ItemsPrice += $Item['item_amount'] * $Item['item_price'];
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>" . str_pad($i, 5, 0, STR_PAD_LEFT) . " - " . Check::Words($Item['item_name'], 5) . "</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($Item['item_price'], '2', ',', '.') . " * <b>{$Item['item_amount']}</b></td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($Item['item_amount'] * $Item['item_price'], '2', ',', '.') . "</td></tr>";
                    endforeach;
                    if (!empty($NewOrder['order_coupon'])):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Cupom:</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>{$NewOrder['order_coupon']}% de desconto</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>- <strike>R$ " . number_format($ItemsPrice * ($NewOrder['order_coupon'] / 100), '2', ',', '.') . "</strike></td></tr>";
                    endif;
                    if (!empty($NewOrder['order_shipcode'])):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Frete via " . getShipmentTag($NewOrder['order_shipcode']) . "</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($NewOrder['order_shipprice'], '2', ',', '.') . " <b>* 1</b></td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($NewOrder['order_shipprice'], '2', ',', '.') . "</td></tr>";
                    endif;
                    $BodyMail .= "<tr style='background: #cccccc;'><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px;'>{$i} produto(s) no pedido</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px; text-align: right;'>{$ItemsAmount} Itens</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px; text-align: right;'>R$ " . number_format($NewOrder['order_price'], '2', ',', '.') . "</td></tr>";
                    $BodyMail .= "</table>";
                endif;
                $BodyMail .= "<p>Qualquer dúvida não deixe de entrar em contato {$_SESSION['userLogin']['user_name']}. Obrigado por sua preferência mais uma vez...</p>";
                $BodyMail .= "<p><i>Atenciosamente " . SITE_NAME . "!</i></p>";

                require 'cart.email.php';
                $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                $Email = new Email;
                $Email->EnviarMontando("Recebemos seu pedido #" . str_pad($OrderCreateId, 7, 0, 0) . "!", $Mensagem, SITE_NAME, MAIL_USER, "{$_SESSION['userLogin']['user_name']} {$_SESSION['userLogin']['user_lastname']}", $_SESSION['userLogin']['user_email']);

                //PAYMENT REDIRECT
                $jSON['redirect'] = BASE . "/pedido/pagamento/" . base64_encode($OrderCreateId) . "#cart";
            endif;
        endif;
        break;
endswitch;

echo json_encode($jSON);
