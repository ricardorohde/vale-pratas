<?php
$AdminLevel = 6;
if (!APP_SEARCH || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

$Read = new Read;

$ClearSearch = filter_input(INPUT_GET, 'clear', FILTER_VALIDATE_BOOLEAN);
if ($ClearSearch):
    $Delete = new Delete;
    $Delete->ExeDelete(DB_SEARCH, "WHERE search_id >= :id", "id=1");
    header('Location: dashboard.php?wc=searchnow');
endif;
?>
<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-search">Relatório de Pesquisas no Site</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            Pesquisas no Site
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Limpar Pesquisas!" href="dashboard.php?wc=searchnow&clear=true" class="btn btn_yellow icon-warning wc_delete_search">Limpar Dados de Pesquisas!</a>
    </div>
</header>
<div class="dashboard_content">
    <article class="box box100 dashboard_search">
        <header class="header_yellow">
            <h1 class="icon-search">Últimas Pesquisas:</h1>
        </header>
        <div class="box_content wc_onlinenow">
            <?php
            $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
            $Page = ($getPage ? $getPage : 1);
            $Pager = new Pager("dashboard.php?wc=searchnow&page=", "<<", ">>", 1);
            $Pager->ExePager($Page, 15);
            $Read->ExeRead(DB_SEARCH, "ORDER BY search_commit DESC, search_count DESC LIMIT :limit OFFSET :offset", "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
            if (!$Read->getResult()):
                $Pager->ReturnPage();
                echo Erro("<span class='icon-info al_center'>Seus usuários ainda não pesquisaram em seu site. Assim que isso acontecer você poderá receber dicas de conteúdo pelas pesquisas realizadas!</span>", E_USER_NOTICE);
            else:
                foreach ($Read->getResult() as $Search):
                    extract($Search);
                    $Read->FullRead("SELECT post_id FROM " . DB_POSTS . " WHERE post_status = 1 AND post_date <= NOW() AND (post_title LIKE '%' :s '%' OR post_subtitle LIKE '%' :s '%')", "s={$search_key}");
                    $ResultPosts = $Read->getRowCount();

                    $Read->FullRead("SELECT pdt_id FROM " . DB_PDT . " WHERE pdt_status = 1 AND (pdt_title LIKE '%' :s '%' OR pdt_subtitle LIKE '%' :s '%')", "s={$search_key}");
                    $ResultPdts = $Read->getRowCount();
                    echo "
                            <article>
                               <h1 class='icon-search'><a href='dashboard.php?wc=posts/search&s=" . urlencode($search_key) . "' title='Ver resultados'>{$search_key}</a></h1>
                               <p>DIA " . date('d/m/Y H\hi', strtotime($search_date)) . "</p>
                               <p>" . str_pad($search_count, 4, 0, STR_PAD_LEFT) . " VEZES</p>
                               <p>" . str_pad($ResultPosts + $ResultPdts, 4, 0, STR_PAD_LEFT) . " RESULTADOS</p>
                            </article>
                        ";
                endforeach;
            endif;
            ?>
            <div class="clear"></div>
        </div>
    </article>

    <?php
    $Pager->ExePaginator(DB_SEARCH);
    echo $Pager->getPaginator();
    ?>
</div>