<?php
$AdminLevel = 6;
if (empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

$Read = new Read;
?>
<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-earth">Usuários Online Agora</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            Online Agora
        </p>
    </div>
</header>
<div class="dashboard_content">
    <article class="box box100 dashboard_online">
        <header class="header_green">
            <h1 class="icon-earth">ONLINE AGORA:</h1>
        </header>
        <div class="box_content wc_onlinenow">
            <?php
            $Read->ExeRead(DB_VIEWS_ONLINE, "ORDER BY online_endview DESC");
            if (!$Read->getResult()):
                echo Erro('<span class="icon-earth al_center">Não existem usuárion online neste momento!</span>', E_USER_NOTICE);
                echo '<div class="clear"></div>';
            else:
                $i = 0;
                foreach ($Read->getResult() as $Online):
                    $i++;
                    $Name = ($Online['online_name'] ? "<a target='_blank' href='dashboard.php?wc=users/create&id={$Online['online_user']}' title='Ver Cliente'>{$Online['online_name']}</a>" : 'guest user');
                    $Date = date('d/m/Y H\hi', strtotime($Online['online_startview']));

                    echo "<div class='single_onlinenow'>
                    <p>" . str_pad($i, 4, 0, STR_PAD_LEFT) . "</p>
                    <p>{$Name}</p>
                    <p>{$Date}</p>
                    <p>{$Online['online_ip']}</p>
                    <p><a target='_blank' href='" . BASE . "/{$Online['online_url']}' title='Ver Destino'>" . ($Online['online_url'] ? $Online['online_url'] : 'home') . "</a></p>
                    </div>";
                endforeach;
            endif;
            ?>
        </div>
    </article>
</div>

<script>
    //DASHBOARD REALTIME
    setInterval(function () {
        OnlineNow();
    }, 3000);
</script>