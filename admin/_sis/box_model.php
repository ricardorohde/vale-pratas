<?php
$AdminLevel = 6;
if (!APP_ || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

$Read = new Read;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-home">Dahsboard</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php">Dashboard</a>
            <span class="crumb">/</span>
            Controler
        </p>
    </div>
</header>
<div class="dashboard_content">
    <?php for ($i = 0; $i < 4; $i++): ?>
        <article class="box box25">
            <header>
                <h1>APP 25%</h1>
            </header>
            <div class="box_content">
                Conteúdo!
            </div>
        </article>
    <?php endfor; ?>

    <?php for ($i = 0; $i < 2; $i++): ?>
        <article class="box box50">
            <header>
                <h1>APP 50%</h1>
            </header>
            <div class="box_content">
                Conteúdo!
            </div>
        </article>
    <?php endfor; ?>

    <article class="box box30">
        <header>
            <h1>APP 30%</h1>
        </header>
        <div class="box_content">
            Conteúdo!
        </div>
    </article>

    <article class="box box70">
        <header>
            <h1>APP 70%</h1>
        </header>
        <div class="box_content">
            Conteúdo!
        </div>
    </article>

    <article class="box box100">
        <header>
            <h1>APP 100%</h1>
        </header>
        <div class="box_content">
            Conteúdo!
        </div>
    </article>
</div>