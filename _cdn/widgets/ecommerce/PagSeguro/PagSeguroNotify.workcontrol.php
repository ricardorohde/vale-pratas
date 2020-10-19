<?php

header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");

$notificationCode = filter_input_array(INPUT_POST, FILTER_DEFAULT);
if (!$notificationCode):
    die('Acesso Negado!');
else:
    require '../../../../_app/Config.inc.php';
    require 'PagSeguroLibrary.php';

    $Read = new Read;
    $Update = new Update;

    try {
        $credentials = PagSeguroConfig::getAccountCredentials();
        $response = PagSeguroNotificationService::checkTransaction($credentials, $notificationCode['notificationCode']);

        $PaymentOrder = $response->getReference();
        $PaymentStatus = $response->getStatus()->getValue();

        $Read->ExeRead(DB_ORDERS, "WHERE order_id = :orid", "orid={$PaymentOrder}");
        if ($Read->getResult()):
            extract($Read->getResult()[0]);

            $Read->ExeRead(DB_USERS, "WHERE user_id = :usr", "usr={$user_id}");
            $Client = $Read->getResult()[0];

            $Email = new Email;

            if ($PaymentStatus == 1):
                /*
                 * AGUARDANDO PAGAMENTO
                 */
                $BodyMail = "<p style='font-size: 1.2em;'>Caro(a) {$Client['user_name']},</p>";
                $BodyMail .= "<p>Primeiramente gostaríamos de agradecer por você escolher a nossa loja para adquirir seus produtos.</p>";
                $BodyMail .= "<p>Seu pedido #" . str_pad($order_id, 7, 0, 0) . " foi concluído com sucesso!</p>";
                $BodyMail .= "<p>E neste momento estamos apenas <b>aguardando a confirmação do pagamento</b> para envia-lo a você!</p>";
                $BodyMail .= "<p style='font-size: 1.4em;'>Detalhes do Pedido:</p>";
                $BodyMail .= "<p>Pedido: <a href='" . BASE . "/conta/pedido/{$order_id}' title='Ver pedido' target='_blank'>#" . str_pad($order_id, 7, 0, STR_PAD_LEFT) . "</a><br>Data: " . date('d/m/Y H\hi', strtotime($order_date)) . "<br>Valor: R$ " . number_format($order_price, '2', ',', '.') . "<br>Método de Pagamento: " . getOrderPayment($order_payment) . ($order_billet ? " - <a title='Imprimir Boleto!' href='{$order_billet}'>Imprimir Boleto!</a>" : "") . "</p>";
                $BodyMail .= "<hr><table style='width: 100%'><tr><td>STATUS:</td><td style='color: #00AD8E; text-align: center;'>✓ Aguardando Pagamento</td><td style='color: #888888; text-align: center;'>» Processando</td><td style='color: #888888; text-align: right;'>✓ Concluído</td></tr></table><hr>";
                $Read->ExeRead(DB_ORDERS_ITEMS, "WHERE order_id = :order", "order={$order_id}");
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
                    if (!empty($order_coupon)):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Cupom:</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>{$order_coupon}% de desconto</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>- <strike>R$ " . number_format($ItemsPrice * ($order_coupon / 100), '2', ',', '.') . "</strike></td></tr>";
                    endif;
                    if (!empty($order_shipcode)):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Frete via " . getShipmentTag($order_shipcode) . "</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_shipprice, '2', ',', '.') . " <b>* 1</b></td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_shipprice, '2', ',', '.') . "</td></tr>";
                    endif;
                    $BodyMail .= "<tr style='background: #cccccc;'><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px;'>{$i} produto(s) no pedido</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px; text-align: right;'>{$ItemsAmount} Itens</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px; text-align: right;'>R$ " . number_format($order_price, '2', ',', '.') . "</td></tr>";

                    if (!empty($order_installments) && $order_installments > 1):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Pago em {$order_installments}x de R$ " . number_format($order_installment, '2', ',', '.') . "</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>Total: </td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_installments * $order_installment, '2', ',', '.') . "</td></tr>";
                    endif;
                    $BodyMail .= "</table>";
                endif;
                $BodyMail .= "<p>Qualquer dúvida não deixe de entrar em contato {$Client['user_name']}. Obrigado por sua preferência mais uma vez...</p>";
                $BodyMail .= "<p><i>Atenciosamente " . SITE_NAME . "!</i></p>";

                require '../cart.email.php';
                $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                $Email->EnviarMontando("Aguardando pagamento #" . str_pad($order_id, 7, 0, 0), $Mensagem, SITE_NAME, MAIL_USER, "{$Client['user_name']} {$Client['user_lastname']}", $Client['user_email']);
            elseif ($PaymentStatus == 2):
                /*
                 * EM ANÁLISE
                 */
                $BodyMail = "<p style='font-size: 1.2em;'>Caro(a) {$Client['user_name']},</p>";
                $BodyMail .= "<p>Primeiramente gostaríamos de agradecer por você escolher a nossa loja para adquirir seus produtos.</p>";
                $BodyMail .= "<p>Seu pedido #" . str_pad($order_id, 7, 0, 0) . " foi concluído com sucesso!</p>";
                $BodyMail .= "<p>Informamos que seu <b>pagamento está em análise pela operadora</b>. E assim que aprovado, enviaremos seu pedido!</p>";
                $BodyMail .= "<p style='font-size: 1.4em;'>Detalhes do Pedido:</p>";
                $BodyMail .= "<p>Pedido: <a href='" . BASE . "/conta/pedido/{$order_id}' title='Ver pedido' target='_blank'>#" . str_pad($order_id, 7, 0, STR_PAD_LEFT) . "</a><br>Data: " . date('d/m/Y H\hi', strtotime($order_date)) . "<br>Valor: R$ " . number_format($order_price, '2', ',', '.') . "<br>Método de Pagamento: " . getOrderPayment($order_payment) . ($order_billet ? " - <a title='Imprimir Boleto!' href='{$order_billet}'>Imprimir Boleto!</a>" : "") . "</p>";
                $BodyMail .= "<hr><table style='width: 100%'><tr><td>STATUS:</td><td style='color: #00AD8E; text-align: center;'>✓ Aguardando Pagamento</td><td style='color: #888888; text-align: center;'>✓ Processando</td><td style='color: #888888; text-align: right;'>✓ Concluído</td></tr></table><hr>";
                $Read->ExeRead(DB_ORDERS_ITEMS, "WHERE order_id = :order", "order={$order_id}");
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
                    if (!empty($order_coupon)):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Cupom:</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>{$order_coupon}% de desconto</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>- <strike>R$ " . number_format($ItemsPrice * ($order_coupon / 100), '2', ',', '.') . "</strike></td></tr>";
                    endif;
                    if (!empty($order_shipcode)):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Frete via " . getShipmentTag($order_shipcode) . "</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_shipprice, '2', ',', '.') . " <b>* 1</b></td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_shipprice, '2', ',', '.') . "</td></tr>";
                    endif;
                    $BodyMail .= "<tr style='background: #cccccc;'><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px;'>{$i} produto(s) no pedido</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px; text-align: right;'>{$ItemsAmount} Itens</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px; text-align: right;'>R$ " . number_format($order_price, '2', ',', '.') . "</td></tr>";

                    if (!empty($order_installments) && $order_installments > 1):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Pago em {$order_installments}x de R$ " . number_format($order_installment, '2', ',', '.') . "</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>Total: </td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_installments * $order_installment, '2', ',', '.') . "</td></tr>";
                    endif;
                    $BodyMail .= "</table>";
                endif;
                $BodyMail .= "<p>Qualquer dúvida não deixe de entrar em contato {$Client['user_name']}. Obrigado por sua preferência mais uma vez...</p>";
                $BodyMail .= "<p><i>Atenciosamente " . SITE_NAME . "!</i></p>";

                require '../cart.email.php';
                $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                $Email->EnviarMontando("Pagamento em análise #" . str_pad($order_id, 7, 0, 0), $Mensagem, SITE_NAME, MAIL_USER, "{$Client['user_name']} {$Client['user_lastname']}", $Client['user_email']);
            elseif ($PaymentStatus == 3):
                /*
                 * PAGO
                 */
                $BodyMail = "<p style='font-size: 1.2em;'>Caro(a) {$Client['user_name']},</p>";
                $BodyMail .= "<p>Seu pagamento para o pedido #" . str_pad($order_id, 7, 0, 0) . " foi aprovado, e já estamos preparando tudo por aqui!</p>";
                $BodyMail .= "<p>Assim que o pedido for postado, <b>enviaremos outro e-mail com os detalhes do envio</b> para que você possa acompanhar sua encomenda!</p>";
                $BodyMail .= "<p style='font-size: 1.4em;'>Detalhes do Pedido:</p>";
                $BodyMail .= "<p>Pedido: <a href='" . BASE . "/conta/pedido/{$order_id}' title='Ver pedido' target='_blank'>#" . str_pad($order_id, 7, 0, STR_PAD_LEFT) . "</a><br>Data: " . date('d/m/Y H\hi', strtotime($order_date)) . "<br>Valor: R$ " . number_format($order_price, '2', ',', '.') . "<br>Método de Pagamento: " . getOrderPayment($order_payment) . "</p>";
                $BodyMail .= "<hr><table style='width: 100%'><tr><td>STATUS:</td><td style='color: #00AD8E; text-align: center;'>✓ Aguardando Pagamento</td><td style='color: #00AD8E; text-align: center;'>✓ Processando</td><td style='color: #888888; text-align: right;'>✓ Concluído</td></tr></table><hr>";
                $Read->ExeRead(DB_ORDERS_ITEMS, "WHERE order_id = :order", "order={$order_id}");
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
                    if (!empty($order_coupon)):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Cupom:</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>{$order_coupon}% de desconto</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>- <strike>R$ " . number_format($ItemsPrice * ($order_coupon / 100), '2', ',', '.') . "</strike></td></tr>";
                    endif;
                    if (!empty($order_shipcode)):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Frete via " . getShipmentTag($order_shipcode) . "</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_shipprice, '2', ',', '.') . " <b>* 1</b></td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_shipprice, '2', ',', '.') . "</td></tr>";
                    endif;
                    $BodyMail .= "<tr style='background: #cccccc;'><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px;'>{$i} produto(s) no pedido</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px; text-align: right;'>{$ItemsAmount} Itens</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px; text-align: right;'>R$ " . number_format($order_price, '2', ',', '.') . "</td></tr>";

                    if (!empty($order_installments) && $order_installments > 1):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Pago em {$order_installments}x de R$ " . number_format($order_installment, '2', ',', '.') . "</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>Total: </td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_installments * $order_installment, '2', ',', '.') . "</td></tr>";
                    endif;
                    $BodyMail .= "</table>";
                endif;
                $BodyMail .= "<p>Qualquer dúvida não deixe de entrar em contato {$Client['user_name']}. Obrigado por sua preferência mais uma vez...</p>";
                $BodyMail .= "<p><i>Atenciosamente " . SITE_NAME . "!</i></p>";

                require '../cart.email.php';
                $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                $Email->EnviarMontando("Seu pagamento foi aprovado #" . str_pad($order_id, 7, 0, 0), $Mensagem, SITE_NAME, MAIL_USER, "{$Client['user_name']} {$Client['user_lastname']}", $Client['user_email']);

                $UpdateOrder = ['order_status' => 6, 'order_update' => date('Y-m-d H:i:s'), 'order_mail_processing' => 1];
                $Update->ExeUpdate(DB_ORDERS, $UpdateOrder, "WHERE order_id = :orid", "orid={$order_id}");

                //NOTIFICAÇÃO DE ENVIO
                require '../../../../admin/_tpl/Mail.email.php';
                $NotifyMail = "<p style='font-size: 1.4em;'>Pagamento aprovado em " . date('d/m/Y H\hi') . "!</p>";
                $NotifyMail .= "<p>";
                $NotifyMail .= "Pedido: <a href='" . BASE . "/admin/dashboard.php?wc=orders/order&id={$order_id}' title='Detalhes do pedido'>#" . str_pad($order_id, 7, 0, 0) . "</a><br>";
                $NotifyMail .= "Data: " . date("d/m/Y \a\s H\hi", strtotime($order_date)) . "<br>";
                $NotifyMail .= "Valor: R$ " . number_format($order_price, '2', ',', '.') . "<br><br>";
                $NotifyMail .= "Código PagSeguro: {$order_code}<br><br>";
                $NotifyMail .= "Cliente: <a href='" . BASE . "/admin/dashboard.php?wc=users/create&id={$Client['user_id']}' title='{$Client['user_name']} {$Client['user_lastname']}'>{$Client['user_name']} {$Client['user_lastname']}</a><br>";
                $NotifyMail .= "Telefone: {$Client['user_cell']}<br>";
                $NotifyMail .= "E-mail: {$Client['user_email']}<br>";
                $NotifyMail .= "</p>";
                $NotifyMail .= "<p><b>Dica:</b> O pedido já pode ser enviado ao cliente. E o quanto mais rápido postar, maior será a satisfação do mesmo!</p>";
                $NotifyMail .= "<p style='font-size: 1.2em;'>√ Concluir Pedido:</p>";
                $NotifyMail .= "<ol><li>Realize o envio e anote o rastreio!</li><li>Acesse o pedido em seu painel!</li><li>Marque o <b>STATUS DO PEDIDO</b> como concluído!</li><li>Informe o código de rastreio!</li><li>Clique em atualizar pedido!</li></ol>";
                $NotifyMail .= "<p>Ao concluir esse processo seu cliente receberá um e-mail com o link de rastreio junto aos detalhes do pedido!</p>";
                $NotifyMail .= "<p><i>Atenciosamente " . ADMIN_NAME . "!</i></p>";
                $Notify = str_replace('#mail_body#', $NotifyMail, $MailContent);
                $Email->EnviarMontando("[#{$order_id}] Pagamento Aprovado!", $Notify, SITE_NAME, MAIL_USER, MAIL_SENDER, PAGSEGURO_NOTIFICATION_EMAIL);
            elseif ($PaymentStatus == 4):
            //DISPONÍVEL
            elseif ($PaymentStatus == 5):
                require '../../../../admin/_tpl/Mail.email.php';
                $NotifyMail = "<p style='font-size: 1.4em;'>O pedido #" . str_pad($order_id, 7, 0, 0) . " requer sua atenção!</p>";
                $NotifyMail .= "<p>Uma disputa é aberta quando o cliente entende que não recebeu o produto. É importante resolver a questão o quanto antes!</p>";
                $NotifyMail .= "<p>";
                $NotifyMail .= "Abertura da disputa em " . date('d/m/Y H\hi') . "<br><br>";
                $NotifyMail .= "Pedido: <a href='" . BASE . "/admin/dashboard.php?wc=orders/order&id={$order_id}' title='Detalhes do pedido'>#" . str_pad($order_id, 7, 0, 0) . "</a><br>";
                $NotifyMail .= "Data: " . date("d/m/Y \a\s H\hi", strtotime($order_date)) . "<br>";
                $NotifyMail .= "Valor: R$ " . number_format($order_price, '2', ',', '.') . "<br><br>";
                $NotifyMail .= "Código PagSeguro: {$order_code}<br><br>";
                $NotifyMail .= "Cliente: <a href='" . BASE . "/admin/dashboard.php?wc=users/create&id={$Client['user_id']}' title='{$Client['user_name']} {$Client['user_lastname']}'>{$Client['user_name']} {$Client['user_lastname']}</a><br>";
                $NotifyMail .= "Telefone: {$Client['user_cell']}<br>";
                $NotifyMail .= "E-mail: {$Client['user_email']}<br>";
                $NotifyMail .= "</p>";
                $NotifyMail .= "<p><b>Dica:</b> Ligue para o cliente para resolver o caso. Se não conseguir, acesse sua conta PagSeguro e envie os comprovantes de entrega do mesmo!</p>";
                $NotifyMail .= "<p><b>Importante:</b> Pedidos com disputas que não são resolvidas, são devolvidos pela PagSeguro. Não deixe de resolver para não perder o pagamento!</p>";
                $NotifyMail .= "<p><i>Atenciosamente " . ADMIN_NAME . "!</i></p>";
                $Notify = str_replace('#mail_body#', $NotifyMail, $MailContent);
                $Email->EnviarMontando("[#{$order_id}] Pagamento em disputa!", $Notify, SITE_NAME, MAIL_USER, MAIL_SENDER, PAGSEGURO_NOTIFICATION_EMAIL);
            elseif ($PaymentStatus == 6):
            //DEVOLVIDA
            elseif ($PaymentStatus == 7):
                /*
                 * CANCELADO
                 */
                $BodyMail = "<p style='font-size: 1.2em;'>Caro(a) {$Client['user_name']},</p>";
                $BodyMail .= "<p>Este e-mail é para informar que o seu pedido #" . str_pad($order_id, 7, 0, 0) . " foi cancelado.</p>";
                $BodyMail .= "<p>Isso ocorre quando o pagamento não é identificado no prazo, ou quando a operadora (em compras com cartão) nega o pagamento!</p>";

                //VOCÊ AINDA PODE PAGAR
                if (date('Y-m-d H:i:s', strtotime($order_date . "+" . E_ORDER_DAYS . "days")) > date('Y-m-d H:i:s')):
                    $BodyMail .= "<p><b>Seu pedido ainda é válido {$Client['user_name']}...</b>";
                    $BodyMail .= "...e você pode efetuar o pagamento até dia " . date('d/m/Y \a\s H\hi', strtotime($order_date . "+" . E_ORDER_DAYS . "days")) . " utilizando qualquer forma de pagamento disponível!</p>";
                    $BodyMail .= "<p><a href='" . BASE . "/pedido/pagamento/" . base64_encode($order_id) . "#cart' title='Pagar Mue Pedido Agora!' target='_blank'>Clique aqui para pagar agora!</a></p>";
                endif;

                $BodyMail .= "<p>Caso tenha qualquer dúvida por favor, entre em contato respondendo este e-mail ou pelo telefone " . SITE_ADDR_PHONE_A . ".</p>";
                $BodyMail .= "<p>Fique a vontade para escolher novos produtos e realizar um novo pedido em nossa loja! <a href='" . BASE . "' title='Produtos " . SITE_NAME . "'>Confira aqui nossos produtos!</a></p>";
                $BodyMail .= "<p style='font-size: 1.4em;'>Detalhes do Pedido:</p>";
                $BodyMail .= "<p>Pedido: <a href='" . BASE . "/conta/pedido/{$order_id}' title='Ver pedido' target='_blank'>#" . str_pad($order_id, 7, 0, STR_PAD_LEFT) . "</a><br>Data: " . date('d/m/Y H\hi', strtotime($order_date)) . "<br>Valor: R$ " . number_format($order_price, '2', ',', '.') . "<br>Método de Pagamento: " . getOrderPayment($order_payment) . "</p>";
                $BodyMail .= "<hr><table style='width: 100%'><tr><td>STATUS:</td><td style='color: #00AD8E; text-align: center;'>✓ Aguardando Pagamento</td><td style='color: #888888; text-align: center;'>✓ Processando</td><td style='color: #CC4E4F; text-align: right;'>✓ Cancelado</td></tr></table><hr>";
                $Read->ExeRead(DB_ORDERS_ITEMS, "WHERE order_id = :order", "order={$order_id}");
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
                    if (!empty($order_coupon)):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Cupom:</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>{$order_coupon}% de desconto</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>- <strike>R$ " . number_format($ItemsPrice * ($order_coupon / 100), '2', ',', '.') . "</strike></td></tr>";
                    endif;
                    if (!empty($order_shipcode)):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Frete via " . getShipmentTag($order_shipcode) . "</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_shipprice, '2', ',', '.') . " <b>* 1</b></td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_shipprice, '2', ',', '.') . "</td></tr>";
                    endif;
                    $BodyMail .= "<tr style='background: #cccccc;'><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px;'>{$i} produto(s) no pedido</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px; text-align: right;'>{$ItemsAmount} Itens</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 10px 10px 10px; text-align: right;'>R$ " . number_format($order_price, '2', ',', '.') . "</td></tr>";

                    if (!empty($order_installments) && $order_installments > 1):
                        $BodyMail .= "<tr><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0;'>Pago em {$order_installments}x de R$ " . number_format($order_installment, '2', ',', '.') . "</td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>Total: </td><td style='border-bottom: 1px solid #cccccc; padding: 10px 0 10px 0; text-align: right;'>R$ " . number_format($order_installments * $order_installment, '2', ',', '.') . "</td></tr>";
                    endif;
                    $BodyMail .= "</table>";
                endif;
                $BodyMail .= "<p>Qualquer dúvida não deixe de entrar em contato {$Client['user_name']}. Obrigado por sua preferência mais uma vez...</p>";
                $BodyMail .= "<p><i>Atenciosamente " . SITE_NAME . "!</i></p>";

                require '../cart.email.php';
                $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                $Email->EnviarMontando("Pedido cancelado #" . str_pad($order_id, 7, 0, 0), $Mensagem, SITE_NAME, MAIL_USER, "{$Client['user_name']} {$Client['user_lastname']}", $Client['user_email']);

                //ORDER CANCEL
                if ($order_status != 2):
                    $UpdateOrder = ['order_status' => 2, 'order_update' => date('Y-m-d H:i:s')];
                    $Update->ExeUpdate(DB_ORDERS, $UpdateOrder, "WHERE order_id = :orid", "orid={$order_id}");

                    //STOCK MANAGER
                    $Read->FullRead("SELECT pdt_id, item_amount FROM " . DB_ORDERS_ITEMS . " WHERE order_id = :orid", "orid={$order_id}");
                    if ($Read->getResult()):
                        foreach ($Read->getResult() as $OrderItem):
                            $Read->FullRead("SELECT pdt_title, pdt_inventory, pdt_delivered FROM " . DB_PDT . " WHERE pdt_id = :pid", "pid={$OrderItem['pdt_id']}");
                            if ($Read->getResult()):
                                $UpdateInventory = [
                                    'pdt_inventory' => (ECOMMERCE_STOCK && $Read->getResult()[0]['pdt_inventory'] !== null ? $Read->getResult()[0]['pdt_inventory'] + $OrderItem['item_amount'] : null),
                                    'pdt_delivered' => ($Read->getResult()[0]['pdt_delivered'] - $OrderItem['item_amount'] >= 1 ? $Read->getResult()[0]['pdt_delivered'] - $OrderItem['item_amount'] : 0)
                                ];
                                $Update->ExeUpdate(DB_PDT, $UpdateInventory, "WHERE pdt_id = :pid", "pid={$OrderItem['pdt_id']}");
                            endif;
                        endforeach;
                    endif;
                endif;

                //NOTIFY
                require '../../../../admin/_tpl/Mail.email.php';
                $NotifyMail = "<p style='font-size: 1.4em;'>O pedido #" . str_pad($order_id, 7, 0, 0) . " foi cancelado!</p>";
                $NotifyMail .= "<p>O cancelamento ocorre quando o prazo de pagamento não é atendido na operadora. Seu cliente também foi notificado!</p>";
                $NotifyMail .= "<p>";
                $NotifyMail .= "Cancelamento em " . date('d/m/Y H\hi') . "<br><br>";
                $NotifyMail .= "Pedido: <a href='" . BASE . "/admin/dashboard.php?wc=orders/order&id={$order_id}' title='Detalhes do pedido'>#" . str_pad($order_id, 7, 0, 0) . "</a><br>";
                $NotifyMail .= "Data: " . date("d/m/Y \a\s H\hi", strtotime($order_date)) . "<br>";
                $NotifyMail .= "Valor: R$ " . number_format($order_price, '2', ',', '.') . "<br><br>";
                $NotifyMail .= "Código PagSeguro: {$order_code}<br><br>";
                $NotifyMail .= "Cliente: <a href='" . BASE . "/admin/dashboard.php?wc=users/create&id={$Client['user_id']}' title='{$Client['user_name']} {$Client['user_lastname']}'>{$Client['user_name']} {$Client['user_lastname']}</a><br>";
                $NotifyMail .= "Telefone: {$Client['user_cell']}<br>";
                $NotifyMail .= "E-mail: {$Client['user_email']}<br>";
                $NotifyMail .= "</p>";
                $NotifyMail .= "<p><b>Dica:</b> Experimente ligar para o cliente para falar sobre o pedido ainda hoje. Com isso você aumenta as chances de um novo pedido com sucesso!</p>";
                $NotifyMail .= "<p><b>Estoque:</b> Os produtos deste pedido tiveram seu estoque reposto automaticamente, e se ativos estão a venda em sua loja online!</p>";
                $NotifyMail .= "<p><i>Atenciosamente " . ADMIN_NAME . "!</i></p>";
                $Notify = str_replace('#mail_body#', $NotifyMail, $MailContent);
                $Email->EnviarMontando("[#{$order_id}] Pedido cancelado!", $Notify, SITE_NAME, MAIL_USER, MAIL_SENDER, PAGSEGURO_NOTIFICATION_EMAIL);
            endif;
        endif;
    } catch (PagSeguroServiceException $e) {
        die($e->getMessage());
    }  
endif;