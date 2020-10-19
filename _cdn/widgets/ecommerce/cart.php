<?php

$CartAction = trim(strip_tags($URL[1]));
$CartBaseUI = BASE . '/conta';

$uri = getenv('REQUEST_URI');
$separado_pagina = explode('vend=', $uri);
if (isset($separado_pagina[1]) && $separado_pagina[1] != NULL && $separado_pagina[1] != "") {

    $vendedor = base64_decode($separado_pagina[1]);
    $separa_vendedor = explode('=', $vendedor);
    if (is_numeric((int) $separa_vendedor[1])) {
        $_GET["idVendedor"] = $separa_vendedor[1];
        echo '<script>localStorage.setItem("idvendedor", ',$_GET["idVendedor"],')</script>';
    }
}else{
    $separado_pagina[1] = '';
}

echo '<article class="workcontrol_cart" id="cart">';

//CHECK CART
if (empty($_SESSION['wc_order']) && $CartAction != 'pagamento' && $CartAction != 'obrigado'):
    echo '<header>';
    echo '<h1>' . ECOMMERCE_TAG . '</h1>';
    echo '</header>';
    echo "<div class='workcontrol_cart_clean'>";
    echo "<p class='title'><span>&#10008;</span>Oppsss, sua lista de compras está vazia! :(</p>";
    echo "<p>Para continuar comprando, navegue pelas categorias do site ou faça uma busca pelo seu produto.</p>";
    echo "<a class='btn btn_green' title='Escolher Produtos!' href='" . BASE . "'>ESCOLHER PRODUTOS!</a>";
    echo "</div>";
else:


    //CART CLEAR
    if ($CartAction == 'clear'):
        unset($_SESSION['wc_order']);
        header('Location: ' . BASE . '/pedido/home');
    endif;

    //CART FRONT-CONTROLER
    if ($CartAction == 'home'):
        //CART HOME
        echo '<header>';
        echo '<h1>' . ECOMMERCE_TAG . '</h1>';
        echo '</header>';
        echo "<div class='workcontrol_cart_list'>";
        echo "<div class='workcontrol_cart_list_header'><p class='item'>-</p><p class='item'>Produto</p><p>Preço</p><p>Quantidade</p><p>Total</p><p>-</p></div>";
        $CartTotal = 0;
        foreach ($_SESSION['wc_order'] as $ItemId => $ItemAmount):
            $Read->ExeRead(DB_PDT, "WHERE pdt_status = 1 AND (pdt_inventory IS NULL OR pdt_inventory >= 1) AND pdt_id = :id", "id={$ItemId}");
            if ($Read->getResult()):
                extract($Read->getResult()[0]);
                $ItemPrice = ($pdt_offer_price && $pdt_offer_start <= date('Y-m-d H:i:s') && $pdt_offer_end >= date('Y-m-d H:i:s') ? $pdt_offer_price : $pdt_price);
                $CartTotal += $ItemPrice * $ItemAmount;
                echo "<div class='workcontrol_cart_list_item workcontrol_cart_list_item_{$pdt_id}'>";
                echo "<p><img title='{$pdt_title}' alt='{$pdt_title}' src='" . BASE . "/tim.php?src=uploads/{$pdt_cover}&w=" . THUMB_W / 5 . "&h=" . THUMB_H / 5 . "'/></p>";
                echo "<p class='item'><a href='" . BASE . "/produto/{$pdt_name}' title='Ver detalhes de {$pdt_title}'>";
                echo "{$pdt_title}";
                if (isset($_GET["idVendedor"]) && $_GET["idVendedor"] != NULL && $_GET["idVendedor"] != "") {
                    $_SESSION['wc_order']["idvendedor"][] = array('idproduto' => $pdt_id, 'codvendedor' => $_GET["idVendedor"]);
                }
                if (isset($_SESSION['wc_order']["idvendedor"]) && $_SESSION['wc_order']["idvendedor"] != NULL) {
                    $array_vendedor = $_SESSION['wc_order']["idvendedor"];
                    foreach ($array_vendedor as $key => $vendedor) {
                        if ($vendedor["idproduto"] == $pdt_id) {
                            $idVendedor = $vendedor["codvendedor"];
                        }
                    }

                    $sql = "select user_name from ws_users where user_id = {$idVendedor}";
                    $Read->FullRead($sql);
                    $vendedorp = $Read->getResult()[0];
                    if (isset($vendedorp["user_name"]) && $vendedorp["user_name"] != NULL && $vendedorp["user_name"] != "") {
                        echo '<br> Vendedor: ' . $vendedorp["user_name"];
                    }
                }
                echo "</a></p>";
                echo "<p>" . ($pdt_price != $ItemPrice ? "<span class='discount'>De R$ <strike>" . number_format($pdt_price, '2', ',', '.') . "</strike></span>Por " : '') . "R$ " . number_format($ItemPrice, '2', ',', '.') . "</p>";

                echo "<p><button id='{$pdt_id}' class='change wc_cart_change_less'>-</button><input id='{$pdt_id}' class='wc_cart_change' type='text' value='{$ItemAmount}' max='{$pdt_inventory}'><button id='{$pdt_id}' class='change wc_cart_change_plus'>+</button><span class='stock'>" . ($pdt_inventory ? str_pad($pdt_inventory, 3, 0, STR_PAD_LEFT) : "+100") . " em estoque!</span></p>";
                echo "<p class='wc_item_price_{$pdt_id}'>R$ " . number_format($ItemAmount * $ItemPrice, '2', ',', '.') . "</p>";
                echo "<p><span class='wc_cart_remove' id='{$pdt_id}'>X</span></p>";
                echo "</div>";
            else:
                unset($_SESSION['wc_order'][$ItemId]);
            endif;
        endforeach;
        echo "</div>";

        $CartCupom = (!empty($_SESSION['wc_cupom']) ? intval($_SESSION['wc_cupom']) : 0);
        $CartPrice = (empty($_SESSION['wc_cupom']) ? $CartTotal : $CartTotal * ((100 - $_SESSION['wc_cupom']) / 100));

        echo "<div class='wc_cart_total_forms'>";
        echo "<div class='wc_cart_total_cupom'>";
        echo "<p>Cupom:</p><input type='text' value='" . (!empty($_SESSION['wc_cupom_code']) ? $_SESSION['wc_cupom_code'] : '') . "' class='wc_cart_cupom_val'/><button class='wc_cart_cupom'>Aplicar</button><img alt='Calculando Desconto!' title='Calculando Desconto!' src='" . BASE . "/_cdn/widgets/ecommerce/load_g.gif'/>";
        echo "</div>";
        echo "<div class='wc_cart_total_shipment'>";
        echo "<p>Frete:</p><input type='text' value='" . (!empty($_SESSION['wc_shipment_zip']) ? $_SESSION['wc_shipment_zip'] : '') . "' class='formCep wc_cart_ship_val'/><button class='wc_cart_ship'>Calcular</button><img alt='Calculando Frete!' title='Calculando Frete!' src='" . BASE . "/_cdn/widgets/ecommerce/load_g.gif'/>";
        echo "<div class='wc_cart_total_shipment_result'></div>";
        echo "</div>";
        echo "</div>";
        echo "<div class='wc_cart_total_price'>";
        echo "<p class='wc_cart_total'><b>Sub-total:</b> R$ <span>" . number_format($CartTotal, '2', ',', '.') . "</span></p>";
        echo "<p class='wc_cart_discount'><b>Cupom:</b> <span>{$CartCupom}</span>%</p>";
        echo "<p class='wc_cart_price'><b>Total:</b> R$ <span>" . number_format($CartPrice, '2', ',', '.') . "</span></p>";
        echo "</div>";
        echo "<div class='wc_cart_actions'>";
        echo "<a class='btn btn_blue' href='" . BASE . "' title='Escolher Mais Produtos!'>Escolher Mais Produtos!</a>";
        echo "<a class='btn btn_green' href='" . BASE . "/pedido/login?vend={$separado_pagina[1]}#cart' title='Fechar Pedido!'>Fechar Pedido!</a>";
        echo "</div>";

    elseif ($CartAction == 'login'):
        //CART LOGIN
        if (!empty($_SESSION['userLogin']) && !empty($_SESSION['userLogin']['user_cell']) && !empty($_SESSION['userLogin']['user_document'])):
            header('Location: ' . BASE . '/pedido/endereco');
        endif;
        echo '<header>';
        echo '<h1>Dados Pessoais:</h1>';
        echo '</header>';
        echo "<div class='workcontrol_order'>";
        echo "<div class='workcontrol_order_forms'>";
        echo "<form autocomplete='off' class='wc_order_login' method='post' action=''>";
        echo "<label><span>E-mail:</span><input class='wc_order_email' type='email' name='user_email' placeholder='Seu E-mail:' required/></label>";
        echo "<div class='label50'><label><span>Nome:</span><input type='text' name='user_name' placeholder='Seu Primeiro Nome:' required/></label></div>";
        echo "<div class='label50'><label><span>Sobrenome:</span><input type='text' name='user_lastname' placeholder='Seu Último Nome:' required/></label></div>";
        echo "<div class='label50'><label><span>Celular:</span><input class='formPhone' type='text' name='user_cell' placeholder='Seu Telefone:' required/></label></div>";
        echo "<div class='label50 labeldocument'><label><span>CPF:</span><input class='formCpf' type='text' name='user_document' placeholder='Seu CPF:' required/></label></div>";
        echo "<label><span>Senha (de 5 a 11 caracteres):</span><input type='password' name='user_password' placeholder='Sua Senha:' required/></label>";
        echo "<div class='workcontrol_order_forms_actions'>";
        echo "<button title='clique para continuar' class='btn btn_green wc_button_cart'>CONTINUAR!</button>";
        echo "<img alt='Processando Dados!' title='Processando Dados!' src='" . BASE . "/_cdn/widgets/ecommerce/load_g.gif'/>";
        echo "</div>";
        echo "</form>";
        echo "</div>";
        require 'cart.sidebar.php';
        echo "<div class='workcontrol_order_back'>";
        echo "<a href='" . BASE . "/pedido/home#cart' title='Voltar a minha lista de compras!'>Voltar!</a>";
        echo "</div>";
        echo "</div>";
    elseif ($CartAction == 'endereco'):
        //CART ADDR
        if (empty($_SESSION['userLogin'])):
            header('Location: ' . BASE . '/pedido/login');
        endif;
        echo '<header>';
        echo '<h1>Endereço e entrega:</h1>';
        echo '</header>';
        echo "<div class='workcontrol_order'>";
        echo "<div class='workcontrol_order_forms'>";
        echo "<form autocomplete='off' class='wc_order_create' method='post' action=''>";

        $Read->ExeRead(DB_USERS_ADDR, "WHERE user_id = :id ORDER BY addr_key DESC, addr_name ASC", "id={$_SESSION['userLogin']['user_id']}");
        if ($Read->getResult()):
            echo "<div class='workcontrol_order_addrs'>";
            echo "<p class='workcontrol_order_newaddr'><span class='btn btn_blue wc_addr_form_open'>Cadastrar Novo Endereço!</span></p>";
            foreach ($Read->getResult() as $Addr):
                echo "<label class='worcontrol_useraddr'><input class='wc_order_user_addr' required type='radio' value='{$Addr['addr_id']}' name='wc_order_addr' id='{$Addr['addr_zipcode']}'/><div><p class='title'>{$Addr['addr_name']}: </p><p>{$Addr['addr_street']}, {$Addr['addr_number']}</p><p>B. {$Addr['addr_district']}, {$Addr['addr_city']}/{$Addr['addr_state']}</p><p>{$Addr['addr_zipcode']}</p></div></label>";
            endforeach;
            echo "</div>";
            ?>
            <script>
                if(!$("#codvendedor").length){
                    var codvendedor = localStorage.getItem('idvendedor');
                    $(".wc_order_create").append('<input type="hidden" name="codvendedor" id="codvendedor" value="'+codvendedor+'"/>');
                }
            </script>
            <?php
            echo "<div class='workcontrol_order_newaddr_form'>";
            echo "<p class='workcontrol_order_newaddr'><span class='btn btn_yellow wc_addr_form_close'>Selecionar Um Endereço!</span></p>";
        endif;

        echo "<div class='label50'><label><span>Nome:</span><input type='text' name='addr_name' placeholder='Ex: Minha Casa' required/></label></div>";
        echo "<div class='label50'><label><span>CEP:</span><input class='wc_getCep formCep wc_order_zipcode wc_cart_ship_val' type='text' name='addr_zipcode' placeholder='CEP:' required/></label></div>";
        echo "<div class='label50'><label><span>Logradouro:</span><input class='wc_logradouro' type='text' name='addr_street' placeholder='Nome da Rua:' required/></label></div>";
        echo "<div class='label50'><label><span>Número:</span><input type='text' name='addr_number' placeholder='Informe o número:' required/></label></div>";
        echo "<div class='label50'><label><span>Complemento:</span><input class='wc_complemento' type='text' name='addr_complement' placeholder='Ex: Casa B, Ap101'/></label></div>";
        echo "<div class='label50'><label><span>Bairro:</span><input class='wc_bairro' type='text' name='addr_district' placeholder='Bairro:' required/></label></div>";
        echo "<div class='label50'><label><span>Cidade:</span><input class='wc_localidade' type='text' name='addr_city' placeholder='Cidade:' required/></label></div>";
        echo "<div class='label50'><label><span>Estado:</span><input class='wc_uf' type='text' name='addr_state' placeholder='UF do estado:' required/></label></div>";

        if ($Read->getResult()):
            echo "</div>";
        endif;

        echo "<p class='wc_cart_total_shipment_tag'>Selecione o frete:</p>";
        echo "<div class='workcontrol_shipment wc_cart_total_shipment_result'></div>";
        echo "<div class='workcontrol_order_forms_actions'>";
        echo "<button title='clique aqui para continuar' class='btn btn_green wc_button_cart'>CONTINUAR!</button>";
        echo "<img alt='Processando Dados!' title='Processando Dados!' src='" . BASE . "/_cdn/widgets/ecommerce/load_g.gif'/>";
        echo "</div>";
        echo "</form>";
        echo "</div>";
        require 'cart.sidebar.php';
        echo "<div class='workcontrol_order_back'>";
        echo "<a href='" . BASE . "/pedido/home#cart' title='Voltar a minha lista de compras!'>Voltar!</a>";
        echo "</div>";
        echo "</div>";

    elseif ($CartAction == 'pagamento'):
        //CART CLEAR
        unset($_SESSION['wc_order'], $_SESSION['wc_cupom'], $_SESSION['wc_cupom_code'], $_SESSION['wc_shipment_zip'], $_SESSION['wc_shipment_item'], $_SESSION['wc_order_addr']);

        //CART PAY
        echo '<header>';
        echo '<h1>Pagamento:</h1>';
        echo '</header>';

        $OrderId = filter_var(base64_decode($URL[2]), FILTER_VALIDATE_INT);
        $Read->ExeRead(DB_ORDERS, "WHERE order_id = :od", "od={$OrderId}");

        if (!$OrderId):
            echo "<div class='workcontrol_cart_clean'>";
            echo "<p class='title'><span>&#10008;</span>Oppsss, não foi possível acessar o pedido! :(</p>";
            echo "<p>Desculpe mas o pedido que você está tentando pagar não existe. Por favor, confira o link de pagamento!</p>";
            echo "<a class='btn btn_green' title='Escolher Produtos!' href='" . BASE . "'>ESCOLHER PRODUTOS!</a>";
            echo "</div>";
        elseif (!$Read->getResult()):
            echo "<div class='workcontrol_cart_clean'>";
            echo "<p class='title'><span>&#10008;</span>Oppsss, pedido indisponível para pagamento! :(</p>";
            echo "<p>Você tentou acessar o pedido <b>#" . str_pad($OrderId, 7, 0, 0) . "</b>. O mesmo não existe ou está indisponível para pagamento!</p>";
            echo "<a class='btn btn_green' title='Escolher Produtos!' href='" . BASE . "'>ESCOLHER PRODUTOS!</a>";
            echo "</div>";
        else:
            $CartOrder = $Read->getResult()[0];
            extract($CartOrder);
            if ($order_status == 1 || $order_status == 6 || date('Y-m-d H:i:s', strtotime($order_date . "+" . E_ORDER_DAYS . "days")) < date('Y-m-d H:i:s')):
                echo "<div class='workcontrol_cart_clean'>";
                echo "<p class='title'><span>&#10008;</span>O pedido #" . str_pad($order_id, 7, 0, 0) . " não pode ser pago!</p>";
                echo "<p>O status deste pedido é <b>" . getOrderStatus($order_status) . "</b>, pedidos cancelados ou concluídos não podem ser pagos!</p>";
                echo "<a class='btn btn_green' title='Escolher Produtos!' href='" . BASE . "'>Escolha produtos para um novo pedido!</a>";
                echo "</div>";
            else:
                $_SESSION['wc_payorder'] = $CartOrder;
                echo "<div class='workcontrol_order'>";
                echo "<div class='workcontrol_order_forms'>";
                require 'PagSeguro/Payment.workcontrol.php';
                echo "</div>";
                require 'cart.order.php';
                echo "</div>";
            endif;
        endif;
    elseif ($CartAction == 'obrigado'):
        //CART PAY
        if (empty($_SESSION['wc_payorder'])):
            echo '<header>';
            echo '<h1>Detalhes do pedido:</h1>';
            echo '</header>';
            echo "<div class='workcontrol_cart_clean'>";
            echo "<p class='title'><span>&#10008;</span>Oppsss, não foi possível acessar o pedido! :(</p>";
            echo "<p>Desculpe mas o pedido que você está tentando acessar não existe. Por favor, confira o link ou crie um novo pedido!</p>";
            echo "<a class='btn btn_green' title='Escolher Produtos!' href='" . BASE . "'>ESCOLHER PRODUTOS!</a>";
            echo "</div>";
        else:
            $Read = new Read;
            $Read->ExeRead(DB_ORDERS, "WHERE order_id = :orid", "orid={$_SESSION['wc_payorder']['order_id']}");
            if (!$Read->getResult()):
                echo '<header>';
                echo '<h1>Detalhes do pedido:</h1>';
                echo '</header>';
                echo "<div class='workcontrol_cart_clean'>";
                echo "<p class='title'><span>&#10008;</span>Oppsss, não foi possível acessar o pedido! :(</p>";
                echo "<p>Desculpe mas o pedido que você está tentando acessar não existe. Por favor, confira o link ou crie um novo pedido!</p>";
                echo "<a class='btn btn_green' title='Escolher Produtos!' href='" . BASE . "'>ESCOLHER PRODUTOS!</a>";
                echo "</div>";
            else:
                extract($Read->getResult()[0]);
                $Read->FullRead("SELECT user_name, user_email FROM " . DB_USERS . " WHERE user_id = :oruser", "oruser={$user_id}");
                $UserOrder = $Read->getResult()[0];

                echo '<header>';
                echo '<h1>&#10003 Pedido Confirmado <span>#' . str_pad($order_id, 7, 0, 0) . '</span></h1>';
                echo '</header>';
                echo "<div class='workcontrol_order'>";

                echo "<div class='trigger trigger_success workcontrol_trigger_order'>";
                echo "<b>Caro(a) {$UserOrder['user_name']},</b>";
                echo "<p>Você recebeu em seu endereço <b>{$UserOrder['user_email']}</b> um e-mail com todos os detalhes do seu pedido. Que foi pago via " . getOrderPayment($order_payment) . " e encontra-se aguardando a confirmação do pagamento!</p>";
                echo "<p>Assim que o pagamento for compensado enviaremos seu pedido!</p>";
                echo "</div>";

                echo "<article class='workcontrol_order_completed'>";
                echo "<header>";
                echo "<h1><span>Compra realizada em " . date("d/m/Y H\hi", strtotime($order_date)) . " via " . getOrderPayment($order_payment) . "</span>";
                if ($order_billet):
                    echo "<a class='btn btn_green fl_right' title='Imprimir Boleto' target='_blanck' href='{$order_billet}'>&#x274F; Imprimir Boleto!</a>";
                endif;
                echo "</h1><div class='clear'></div></header>";

                echo "<div class='workcontrol_order_completed_card'><p class='product'>Produto</p><p>Preço</p><p>Quantidade</p><p>Total</p></div>";
                $SideTotalCart = 0;
                $SideTotalExtra = 0;
                $SideTotalPrice = 0;
                $Read->ExeRead(DB_ORDERS_ITEMS, "WHERE order_id = :orid", "orid={$order_id}");
                if ($Read->getResult()):
                    foreach ($Read->getResult() as $SideProduct):
                        if ($SideProduct['pdt_id']):
                            echo "<div class='workcontrol_order_completed_card items'>";
                            $Read->FullRead("SELECT pdt_cover FROM " . DB_PDT . " WHERE pdt_id = :pid", "pid={$SideProduct['pdt_id']}");
                            echo "<p class='product'><img title='{$SideProduct['item_name']}' alt='{$SideProduct['item_name']}' src='" . BASE . "/tim.php?src=uploads/{$Read->getResult()[0]['pdt_cover']}&w=" . THUMB_W / 5 . "&h=" . THUMB_H / 5 . "'/><span>" . Check::Chars($SideProduct['item_name'], 42) . "</span></p>";
                            echo "<p>R$ " . number_format($SideProduct['item_price'], '2', ',', '.') . "</p>";
                            echo "<p>{$SideProduct['item_amount']}</p>";
                            echo "<p>R$ " . number_format($SideProduct['item_price'] * $SideProduct['item_amount'], '2', ',', '.') . "</p>";
                            $SideTotalCart += $SideProduct['item_price'] * $SideProduct['item_amount'];
                            echo "</div>";
                        else:
                            $SideTotalExtra += $SideProduct['item_price'] * $SideProduct['item_amount'];
                        endif;
                    endforeach;
                endif;

                $TotalCart = $SideTotalCart;
                $TotalExtra = $SideTotalExtra;
                echo "<div class='workcontrol_order_completed_card total'>";
                echo "<div class='wc_cart_total'>Sub-total: <b>R$ <span>" . number_format($TotalCart, '2', ',', '.') . "</span></b></div>";
                if ($order_coupon):
                    echo "<div class='wc_cart_discount'>Desconto: <b><strike>R$ <span>" . number_format($SideTotalCart * ($order_coupon / 100), '2', ',', '.') . "</span></strike></b></div>";
                endif;
                echo "<div>Frete: <b>R$ <span>" . number_format($order_shipprice, '2', ',', '.') . "</span></b></div>";
                if ($order_installments > 1):
                    echo "<div>Total : <b>R$ <span>" . number_format($order_price, '2', ',', '.') . "</span></b></div>";
                    echo "<div class='wc_cart_price'><small><sup>{$order_installments}x</sup> R$ {$order_installment} : </small><b>R$ <span>" . number_format($order_installments * $order_installment, '2', ',', '.') . "</span></b></div>";
                else:
                    echo "<div class='wc_cart_price'>Total : <b>R$ <span>" . number_format($order_price, '2', ',', '.') . "</span></b></div>";
                endif;
                echo "</div>";
                echo "</article>";

                echo "</div>";
            endif;
        endif;
    else:
        header("Location: " . BASE . "/pedido/home");
    endif;
endif;
echo '</article>';
