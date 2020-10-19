<?php
$AdminLevel = 7;
if (!APP_PRODUCTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

$Read = new Read;
$Create = new Create;

$CatId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($CatId):
    $Read->ExeRead(DB_PDT_CATS, "WHERE cat_id = :id", "id={$CatId}");
    if ($Read->getResult()):
        $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
        extract($FormData);
    else:
        $_SESSION['trigger_controll'] = Erro("<b>OPPSS {$Admin['user_name']}</b>, você tentou editar uma categoria que não existe ou que foi removida recentemente!", E_USER_NOTICE);
        header('Location: dashboard.php?wc=products/categories');
    endif;
else:
    $Date = date('Y-m-d H:i:s');
    $Title = "Nova Categoria - {$Date}";
    $Name = Check::Name($Title);
    $CarCreate = ['cat_name' => $Name, 'cat_created' => $Date];
    $Create->ExeCreate(DB_PDT_CATS, $CarCreate);
    header('Location: dashboard.php?wc=products/category&id=' . $Create->getResult());
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-price-tags">Adicionar Categoria</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/categories">Categorias</a>
            <span class="crumb">/</span>
            Nova Categoria
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Ver Categorias!" href="dashboard.php?wc=products/categories" class="btn btn_blue icon-eye">Ver Categorias!</a>
        <a title="Nova Categoria" href="dashboard.php?wc=products/category" class="btn btn_green icon-plus">Adicionar Categoria!</a>
    </div>

</header>
<div class="dashboard_content">
    <article class="box box100">
        <header>
            <h1>Cadastrar Categoria:</h1>
        </header>
        <div class="box_content">
            <form class="auto_save" name="category_add" action="" method="post" enctype="multipart/form-data">
                <div class="callback_return"></div>
                <input type="hidden" name="callback" value="Products"/>
                <input type="hidden" name="callback_action" value="cat_manager"/>
                <input type="hidden" name="cat_id" value="<?= $CatId; ?>"/>
                <label class="label">
                    <span class="legend">Nome:</span>
                    <input style="font-size: 1.5em;" type="text" name="cat_title" value="<?= $cat_title; ?>" placeholder="Título da Categoria:" required/>
                </label>

                <label class="label">
                    <span class="legend">Setor:</span>
                    <select name="cat_parent">
                        <option value="">Esse é um setor!</option>
                        <?php
                        $Read->FullRead("SELECT cat_id, cat_title FROM " . DB_PDT_CATS . " WHERE cat_parent IS NULL AND cat_id != :ci ORDER BY cat_title ASC", "ci={$CatId}");
                        if ($Read->getResult()):
                            foreach ($Read->getResult() as $Sector):
                                echo "<option";
                                if ($Sector['cat_id'] == $cat_parent):
                                    echo " selected='selected'";
                                endif;
                                echo " value='{$Sector['cat_id']}'>&raquo;{$Sector['cat_title']}</option>";
                            endforeach;
                        endif;
                        ?>
                    </select>
                </label>

                <div class="m_top">&nbsp;</div>
                <img class="form_load fl_right none" style="margin-left: 10px; margin-top: 2px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
                <button class="btn btn_green icon-price-tags fl_right">Atualizar Categoria!</button>
                <div class="clear"></div>
            </form>
        </div>
    </article>
</div>