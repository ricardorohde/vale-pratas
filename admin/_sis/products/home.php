<?php
$AdminLevel = 7;
if (!APP_PRODUCTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

//AUTO DELETE PRODUCT TRASH
if (DB_AUTO_TRASH):
    $Delete = new Delete;
    $Delete->ExeDelete(DB_PDT, "WHERE pdt_title IS NULL AND pdt_content IS NULL and pdt_status = :st", "st=0");

    //AUTO TRASH IMAGES
    $Read->FullRead("SELECT image FROM " . DB_PDT_IMAGE . " WHERE product_id NOT IN(SELECT pdt_id FROM " . DB_PDT . ")");
    if ($Read->getResult()):
        $Delete->ExeDelete(DB_PDT_IMAGE, "WHERE id >= :id AND product_id NOT IN(SELECT pdt_id FROM " . DB_PDT . ")", "id=1");
        foreach ($Read->getResult() as $ImageRemove):
            if (file_exists("../uploads/{$ImageRemove['image']}") && !is_dir("../uploads/{$ImageRemove['image']}")):
                unlink("../uploads/{$ImageRemove['image']}");
            endif;
        endforeach;
    endif;
endif;

$Read = new Read;
$Search = filter_input_array(INPUT_POST);
if ($Search && $Search['s']):
    $S = urlencode($Search['s']);
    header("Location: dashboard.php?wc=products/search&s={$S}");
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-books">Produtos</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            Produtos
        </p>
    </div>

    <div class="dashboard_header_search">
        <form name="searchPosts" action="" method="post" enctype="multipart/form-data" class="ajax_off">
            <input type="search" name="s" placeholder="Pesquisar Produto:" required/>
            <button class="btn btn_green icon icon-search icon-notext"></button>
        </form>
    </div>

</header>
<div class="dashboard_content">
    <?php
    $Page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    $Pager = new Pager("dashboard.php?wc=products/home&page=", "<<", ">>", 5);
    $Pager->ExePager($Page, 12);
    $Read->ExeRead(DB_PDT, "ORDER BY pdt_status DESC, pdt_created DESC LIMIT :limit OFFSET :offset", "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
    if (!$Read->getResult()):
        $Pager->ReturnPage();
        echo Erro("<span class='al_center icon-notification'>Ainda não existem produtos cadastrados {$Admin['user_name']}. Comece agora mesmo criando seu primeiro produto!</span>", E_USER_NOTICE);
    else:
        foreach ($Read->getResult() as $Products):
            extract($Products);
            $PdtImage = ($pdt_cover && file_exists("../uploads/{$pdt_cover}") && !is_dir("../uploads/{$pdt_cover}") ? "uploads/{$pdt_cover}" : 'admin/_img/no_image.jpg');
            $PdtTitle = ($pdt_title ? Check::Chars($pdt_title, 45) : 'Edite este produto para coloca-lo a venda!');
            $PdtCode = ($pdt_code ? $pdt_code : 'indefinido');
            $PdtClass = ($pdt_status != 1 ? 'inactive' : (is_numeric($pdt_inventory) && $pdt_inventory <= 0 ? 'outsale' : ''));
            echo "<article class='box box25 single_pdt {$PdtClass}' id='{$pdt_id}'>
            <img title='{$PdtTitle}' alt='{$PdtTitle}' src='../tim.php?src={$PdtImage}&w=" . THUMB_W . "&h=" . THUMB_H . "'/>
            <div class='box_content'>
                <header>
                    <h1>{$PdtTitle}</h1>";

            if ($pdt_offer_price && strtotime($pdt_offer_start) <= time() && strtotime($pdt_offer_end) >= time()):
                echo "<p class='tagline'><span class='offer'>de <strike>R$ " . number_format($pdt_price, "2", ",", ".") . "</strike> por</span>R$ " . number_format($pdt_offer_price, "2", ",", ".") . "</p>";
            else:
                echo "<p class='tagline'><span class='offer'>por apenas</span>R$ " . number_format($pdt_price, "2", ",", ".") . "</p>";
            endif;

            $Read->FullRead("SELECT brand_title FROM " . DB_PDT_BRANDS . " WHERE brand_id = :bid", "bid={$pdt_brand}");
            $Brand = ($Read->getResult() ? $Read->getResult()[0]['brand_title'] : 'indefinida');

            $Read->FullRead("SELECT cat_title FROM " . DB_PDT_CATS . " WHERE cat_id = :cat", "cat={$pdt_category}");
            $Category = ($Read->getResult() ? $Read->getResult()[0]['cat_title'] : 'indefinida');

            $Read->FullRead("SELECT cat_title FROM " . DB_PDT_CATS . " WHERE cat_id = :cat", "cat={$pdt_subcategory}");
            $SubCategory = ($Read->getResult() ? $Read->getResult()[0]['cat_title'] : 'indefinida');

            echo " </header>
                <div class='single_pdt_info'>
                    <p>Código: <b>{$PdtCode}</b></p>
                    <p>Vendido: <b>" . str_pad($pdt_delivered, 5, 0, STR_PAD_LEFT) . "</b></p>
                    <p>Estoque: <b>" . (is_numeric($pdt_inventory) ? ($pdt_inventory >= 1 ? str_pad($pdt_inventory, 4, 0, STR_PAD_LEFT) : $pdt_inventory) : "+100") . "</b></p>
                    <p>Fabricante: <b>{$Brand}</b></p>
                    <p>Em: <b>{$Category}</b> &raquo; <b>{$SubCategory}</b></p>
                </div>
                <a title='Ver produto no site' target='_blank' href='" . BASE . "/produto/{$pdt_name}' class='icon-notext icon-eye btn btn_green'></a>
                <a title='Editar produto' href='dashboard.php?wc=products/create&id={$pdt_id}' class='post_single_center icon-notext icon-pencil btn btn_blue'></a>
                <span rel='single_pdt' class='j_delete_action icon-notext icon-cancel-circle btn btn_red' id='{$pdt_id}'></span>
                <span rel='single_pdt' callback='Products' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$pdt_id}'>Remover Produto?</span>
            </div>
        </article>";
        endforeach;

        $Pager->ExePaginator(DB_PDT);
        echo $Pager->getPaginator();

    endif;
    ?>
</div>