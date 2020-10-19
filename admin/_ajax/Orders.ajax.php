<?php

session_start();
require '../../_app/Config.inc.php';
$NivelAcess = 7;

if (!APP_ORDERS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
    $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
    echo json_encode($jSON);
    die;
endif;

usleep(50000);

//DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Orders';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//VALIDA AÇÃO
if ($PostData && $PostData['callback_action'] && $PostData['callback'] = $CallBack):
    //PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    $Read = new Read;
    $Create = new Create;
    $Update = new Update;
    $Delete = new Delete;
    $Upload = new Upload('../../uploads/');

    //SELECIONA AÇÃO
    switch ($Case):
        case 'manager':
            $OrderId = $PostData['order_id'];
            $OrderMail = (!empty($PostData['post_mail']) ? true : false);
            unset($PostData['order_id'], $PostData['post_mail']);

            $Read->ExeRead(DB_ORDERS, "WHERE order_id = :order", "order={$OrderId}");
            if (!$Read->getResult()):
                $jSON['trigger'] = AjaxErro("<span class='icon-warning'>Opss {$_SESSION['userLogin']['user_name']}. Você está tentando gerenciar um pedido que não existe ou foi removido!</span>", E_USER_WARNING);
            else:
                extract($Read->getResult()[0]);
                $Read->ExeRead(DB_USERS, "WHERE user_id = :user", "user={$user_id}");
                $Client = $Read->getResult()[0];
                $Traking = ($order_shipcode < 40000 ? ECOMMERCE_SHIPMENT_COMPANY_LINK : 'http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=');

                if ($OrderMail):
                    if ($PostData['order_status'] == 6 && !$order_mail_processing):
                        //ENVIA E-MAIL DE PEDIDO EM PROCESSAMENTO
                        $BodyMail = "<p style='font-size: 1.2em;'>Caro(a) {$Client['user_name']},</p>";
                        $BodyMail .= "<p>Este e-mail é para informar que seu pedido #" . str_pad($OrderId, 7, 0, STR_PAD_LEFT) . ", foi processado aqui na " . SITE_NAME . " e que já estamos preparando ele!</p>";
                        $BodyMail .= "<p>Isso significa que já identificamos o pagamento do seu pedido, e o mesmo está sendo preparado para ser enviado para você!</p>";
                        $BodyMail .= "<p style='font-size: 1.4em;'>Detalhes do Pedido:</p>";
                        $BodyMail .= "<p>Pedido: <a href='" . BASE . "/conta/pedido/{$order_id}' title='Ver pedido' target='_blank'>#" . str_pad($OrderId, 7, 0, STR_PAD_LEFT) . "</a><br>Data: " . date('d/m/Y H\hi', strtotime($order_date)) . "<br>Valor: R$ " . number_format($order_price, '2', ',', '.') . "<br>Método de Pagamento: " . getOrderPayment($order_payment) . "</p>";
                        $BodyMail .= "<hr><table style='width: 100%'><tr><td>STATUS:</td><td style='color: #00AD8E; text-align: center;'>✓ Aguardando Pagamento</td><td style='color: #00AD8E; text-align: center;'>✓ Processando</td><td style='color: #888888; text-align: right;'>» Concluído</td></tr></table><hr>";
                        $Read->ExeRead(DB_ORDERS_ITEMS, "WHERE order_id = :order", "order={$OrderId}");
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

                        require '../_tpl/Client.email.php';
                        $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                        $Email = new Email;
                        $Email->EnviarMontando("Identificamos seu pagamento #" . str_pad($order_id, 7, 0, 0), $Mensagem, SITE_NAME, MAIL_USER, "{$Client['user_name']} {$Client['user_lastname']}", $Client['user_email']);

                        //ESTOQUE: Remove produtos do estoque:
                        $Read->FullRead("SELECT pdt_id, item_amount FROM " . DB_ORDERS_ITEMS . " WHERE order_id = :order AND pdt_id IS NOT NULL", "order={$OrderId}");
                        if ($Read->getResult()):
                            foreach ($Read->getResult() as $Inventory):
                                $Read->FullRead("SELECT pdt_inventory, pdt_delivered FROM " . DB_PDT . " WHERE pdt_id = :pdt", "pdt={$Inventory['pdt_id']}");
                                if ($Read->getResult()):
                                    $UpdateInventory = [
                                        'pdt_inventory' => $Read->getResult()[0]['pdt_inventory'] - $Inventory['item_amount'],
                                        'pdt_delivered' => $Read->getResult()[0]['pdt_delivered'] + $Inventory['item_amount']
                                    ];
                                    $Update->ExeUpdate(DB_PDT, $UpdateInventory, "WHERE pdt_id = :pdt", "pdt={$Inventory['pdt_id']}");
                                endif;
                            endforeach;
                        endif;

                        //Impede envio dubplicado de e-mail de processamento
                        $PostData['order_mail_processing'] = 1;
                    endif;

                    if ($PostData['order_status'] == 1 && !$order_mail_completed && $PostData['order_tracking']):
                        //ENVIA E-MAIL DE PEDIDO CONCLUÍDO
                        $BodyMail = "<p style='font-size: 1.2em;'>Caro(a) {$Client['user_name']},</p>";
                        $BodyMail .= "<p>Este e-mail rápido é para informar que seu pedido #" . str_pad($OrderId, 7, 0, STR_PAD_LEFT) . " foi concluído, e que seus produtos estão a caminho!</p>";
                        if ($PostData['order_tracking'] && $PostData['order_tracking'] != 1):
                            $BodyMail .= "<p>Você pode acompanhar o envio <a title='Acompanhar Pedido' href='{$Traking}{$PostData['order_tracking']}' target='_blank'>clicando aqui!</a></p>";
                        endif;
                        $BodyMail .= "<p>A " . SITE_NAME . " gostaria de lhe agradecer mais uma vez pela preferência em adquirir seus produtos em nossa loja.</p>";
                        $BodyMail .= "<p>Esperamos ter proporcionado a melhor experiência!</p>";
                        $BodyMail .= "<p style='font-size: 1.4em;'>Detalhes do Pedido:</p>";
                        $BodyMail .= "<p>Pedido: <a href='" . BASE . "/conta/pedido/{$order_id}' title='Ver pedido' target=''>#" . str_pad($OrderId, 7, 0, STR_PAD_LEFT) . "</a><br>Data: " . date('d/m/Y H\hi', strtotime($order_date)) . "<br>Valor: R$ " . number_format($order_price, '2', ',', '.') . "<br>Método de Pagamento: " . getOrderPayment($order_payment) . (!empty($PostData['order_tracking']) && $PostData['order_tracking'] != 1 ? "<br>Código do Rastreio: <a title='Acompanhar Pedido' href='{$Traking}{$PostData['order_tracking']}' target='_blank'>{$PostData['order_tracking']}</a>" : '') . "</p>";
                        $BodyMail .= "<hr><table style='width: 100%'><tr><td>STATUS:</td><td style='color: #00AD8E; text-align: center;'>✓ Aguardando Pagamento</td><td style='color: #00AD8E; text-align: center;'>✓ Processando</td><td style='color: #00AD8E; text-align: right;'>✓ Concluído</td></tr></table><hr>";
                        $Read->ExeRead(DB_ORDERS_ITEMS, "WHERE order_id = :order", "order={$OrderId}");
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

                        require '../_tpl/Client.email.php';
                        $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                        $Email = new Email;
                        $Email->EnviarMontando("Seu pedido esta a caminho #" . str_pad($order_id, 7, 0, 0), $Mensagem, SITE_NAME, MAIL_USER, "{$Client['user_name']} {$Client['user_lastname']}", $Client['user_email']);

                        //Impede envio dubplicado de e-mail de concluído
                        $PostData['order_mail_completed'] = 1;
                    elseif ($PostData['order_status'] == 1 && !$order_mail_completed && empty($PostData['order_tracking'])):
                        $jSON['trigger'] = AjaxErro("<span class='icon-checkmark'>Pedido Atualizado com Sucesso!</span><p class='icon-warning'>Opss {$Client['user_name']}. <b>Informe o código do RASTREIO</b> para informar o cliente sobre seu pedido!</p>", E_USER_WARNING);
                    endif;
                endif;

                if (!empty($PostData['order_tracking']) && $PostData['order_tracking'] != 1):
                    $jSON['content'] = "<a title='Rastrear Pedido' target='_blanck' href='{$Traking}{$PostData['order_tracking']}'>RASTREIO:</a>";
                else:
                    $jSON['content'] = 'RASTREIO:';
                endif;

                $PostData['order_shipment'] = (empty($PostData['order_shipment']) ? null : $PostData['order_shipment']);
                $Update->ExeUpdate(DB_ORDERS, $PostData, "WHERE order_id = :order", "order={$OrderId}");

                if (empty($jSON['trigger'])):
                    $jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>Pedido Atualizado com Sucesso!</b>");
                endif;
            endif;
            break;

        case 'delete':
            $Delete->ExeDelete(DB_ORDERS, "WHERE order_id = :order", "order={$PostData['del_id']}");
            $Delete->ExeDelete(DB_ORDERS_ITEMS, "WHERE order_id = :order", "order={$PostData['del_id']}");

            $jSON['trigger'] = AjaxErro('<b class="icon-checkmark">PEDIDO REMOVIDO COM SUCESSO!</b> <a style="font-size: 0.8em; margin-left: 10px" class="btn btn_green" href="dashboard.php?wc=orders/home" title="Ver Pedidos">VER PEDIDOS!</a>');
            break;
    endswitch;

    //RETORNA O CALLBACK
    if ($jSON):
        echo json_encode($jSON);
    else:
        $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_ERROR);
        echo json_encode($jSON);
    endif;
else:
    //ACESSO DIRETO
    die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
endif;
