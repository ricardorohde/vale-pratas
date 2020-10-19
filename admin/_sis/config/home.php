<?php
$AdminLevel = 10;
if (empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

$Read = new Read;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-cogs">Configurações</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php">Dashboard</a>
            <span class="crumb">/</span>
            Configurações
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Reiniciar Configurações!" href="dashboard.php?wc=config/home&wc_recet_config=true" class="btn btn_yellow icon-warning wc_resetconfig">Resetar Configurações!</a>
    </div>
</header>
<div class="dashboard_content">
    <?php
    $Read->FullRead("SELECT conf_type FROM " . DB_CONF . " GROUP BY conf_type ASC");
    if ($Read->getResult()):

        foreach ($Read->getResult() as $Config):
            echo "<article class='box box100 box_conf'>";
            echo "<header><h1 class='icon-cog'>{$Config['conf_type']}</h1></header>";
            echo "<div class='box_content'>";

            $Read->ExeRead(DB_CONF, "WHERE conf_type = :type", "type={$Config['conf_type']}");
            if (!$Read->getResult()):
                Erro("Não existem configurações do tipo {$Config['conf_type']}.", E_USER_WARNING);
            else:
                foreach ($Read->getResult() as $ConfType):
                    extract($ConfType);
                    echo "<form class='auto_save' name='workcontrol_conf' action='' method='post' enctype='multipart/form-data'>";
                    echo "<input type='hidden' name='callback' value='Config'/>";
                    echo "<input type='hidden' name='callback_action' value='WorkControl'/>";
                    echo "<input type='hidden' name='conf_id' value='{$conf_id}'/>";
                    echo "<label class='label'>";
                    echo "<span class='legend'>{$conf_key}</span>";
                    echo "<input name='conf_value' value='" . ($conf_value ? htmlspecialchars($conf_value, ENT_QUOTES) : 0) . "' type='text'/>";
                    echo "</label>";
                    echo '</form>';
                endforeach;
            endif;
            echo "<div class='clear'></div>";
            echo "</div>";
            echo "</article>";
        endforeach;
    else:
        $StartConfig = true;
    endif;

    $getResetConfig = filter_input(INPUT_GET, 'wc_recet_config', FILTER_VALIDATE_BOOLEAN);
    if ($getResetConfig):
        $Delete = new Delete;
        $Delete->ExeDelete(DB_CONF, "WHERE conf_id >= :conf", "conf=1");
        header("Location: dashboard.php?wc=config/home");
    endif;

    $CreateConfig = (!empty($StartConfig) ? true : false);
    if ($CreateConfig):
        foreach (get_defined_constants(true)['user'] as $Key => $Value):
            $AppType = substr($Key, 0, strpos($Key, '_'));
            $ArrCreateConf = ['conf_key' => $Key, "conf_value" => $Value, 'conf_type' => $AppType];
            $Create = new Create;
            $Create->ExeCreate(DB_CONF, $ArrCreateConf);
        endforeach;

        $Delete = new Delete;
        $Delete->ExeDelete(DB_CONF, "WHERE conf_type = :type1 OR conf_type = :type2 OR conf_type = :type3 OR conf_type = :type4 OR conf_type = :type5 OR conf_key = :type6 OR conf_key = :type7 OR conf_key = :type8", "type1=DB&type2=SIS&type3=REQUIRE&type4=INCLUDE&type5=WORKCONTROL_CONFIG&type6=BASE&type7=THEME&type8=LDEV");

        $Update = new Update;
        $UpdateNull = ['conf_type' => 'ADMIN'];
        $Update->ExeUpdate(DB_CONF, $UpdateNull, "WHERE conf_type = :null", "null=");

        $UpdateE = ['conf_type' => 'ECOMMERCE'];
        $Update->ExeUpdate(DB_CONF, $UpdateE, "WHERE conf_type = :e", "e=E");

        $UpdateImage = ['conf_type' => 'IMAGE'];
        $Update->ExeUpdate(DB_CONF, $UpdateImage, "WHERE conf_type = :t OR conf_type = :a", "t=THUMB&a=AVATAR");

        header("Location: dashboard.php?wc=config/home");
    endif;
    ?>
</div>