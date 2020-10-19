
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css"/>
<?php
$AdminLevel = 6;
if (!APP_ORDERS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

$Read = new Read;
$Search = filter_input_array(INPUT_POST);
if ($Search && $Search['s']):
    $s = intval($Search['s']);
    $Read->FullRead("SELECT order_id FROM " . DB_ORDERS . " WHERE order_id = :order", "order={$s}");
    if ($Read->getResult()):
        header("Location: dashboard.php?wc=orders/order&id={$s}");
    else:
        $_SESSION['trigger_controll'] = "Desculpe {$Admin['user_name']}, mas não existe o pedido {$Search['s']}!";
        header('Location: dashboard.php?wc=orders/completed');
    endif;
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-cart">Comissão de Vendedor</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=orders/home">Pedidos</a>
            <span class="crumb">/</span>
            Comissões
        </p>
    </div>

<!--    <div class="dashboard_header_search">
        <form name="searchOrders" action="" method="post" enctype="multipart/form-data" class="ajax_off">
            <input type="search" name="s" placeholder="Pesquisar Pedido:" required/>
            <button class="btn btn_green icon icon-search icon-notext"></button>
        </form>
    </div>-->
</header>
<div class="dashboard_content">
    <?php
    $sql = "select i.*, o.order_date, o.order_status, o.order_payment
    from ws_orders_items as i
    inner join ws_orders as o on o.order_id = i.order_id
    where i.idvendedor > 0 order by o.order_date desc";
    $Read->FullRead($sql);
    if (!$Read->getResult()):
        echo Erro("<span class='al_center icon-notification'>Olá {$Admin['user_name']}. Ainda não existem pedidos com comissão :(</span>", E_USER_NOTICE);
    else:
        echo '<table id="tcomissaoVendedor" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Vendedor</th>
                <th>Data</th>
                <th>Item</th>
                <th>(R$) Valor Item</th>
                <th>(R$) Comissão</th>
                <th>Pagamento</th>
                <th>Status</th>
            </tr>
        </thead>';
        echo '<tbody>';
        $totComissao = 0.0;
        $totItem = 0.0;
        foreach ($Read->getResult() as $Order):
            $Read->FullRead("SELECT user_name, user_lastname FROM " . DB_USERS . " WHERE user_id = :user", "user={$Order['idvendedor']}");
            $Vendedor = ($Read->getResult() ? "{$Read->getResult()[0]['user_name']} {$Read->getResult()[0]['user_lastname']}" : 'N/A');
            
            $comissao = '';
            
            if(isset($Vendedor) && $Vendedor != NULL && $Vendedor != ""){
                $vlComissao = 0.15 * $Order['item_price'];
                $totComissao += $vlComissao;
                $comissao = number_format($vlComissao, '2', ',', '.');
            }
            $totItem += $Order['item_price'];
            echo "<tr>
                        <td><b><a class='order' href='dashboard.php?wc=orders/order&id={$Order['order_id']}' title='Ver Pedido'>" . str_pad($Order['order_id'], 7, 0, STR_PAD_LEFT) . "</a></b></td>
                        <td><a href='dashboard.php?wc=users/create&id={$Order['idvendedor']}' target='_blank' title='Ver ficha de vendedor'>{$Vendedor}</a></td>                        
                        <td data-order='{$Order['order_date']}'>" . date('d/m/Y H\hi', strtotime($Order['order_date'])) . "</td>
                        <td><a href='dashboard.php?wc=products/create&id={$Order['pdt_id']}' target='_blank' title='Ver produto'>{$Order['item_name']}</a></td>       
                        <td data-order='{$Order['item_price']}'>" . number_format($Order['item_price'], '2', ',', '.') . "</td>
                        <td>" . $comissao . "</td>        
                        <td>" . getOrderPayment($Order['order_payment']) . "</td>
                        <td>" . getOrderStatus($Order['order_status']) . "</td>
                </tr>";
        endforeach;
        echo '</tbody>';
        echo '<tfoot>';
        echo '<tr>';
        echo '<td colspan="4">Total:</td>';
        echo '<td>', number_format($totItem, 2, ',', '.'),'</td>';
        echo '<td>', number_format($totComissao, 2, ',', '.'),'</td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '</tr>';
        echo '</tfoot>';
        
        echo '</table>';
    endif;
    ?>
</div>