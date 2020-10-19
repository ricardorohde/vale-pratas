<?php
ob_start();
session_start();
require '../_app/Config.inc.php';

if (isset($_SESSION['userLogin']) && isset($_SESSION['userLogin']['user_level']) && $_SESSION['userLogin']['user_level'] >= 6):
    header('Location: dashboard.php?wc=home');
endif;

$Cookie = filter_input(INPUT_COOKIE, 'workcontrol', FILTER_VALIDATE_EMAIL);
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="mit" content="0027066">
        <title>Bem-vindo(a) ao <?= ADMIN_NAME; ?> - Entrar!</title>
        <meta name="description" content="<?= ADMIN_DESC; ?>"/>
        <meta name="viewport" content="width=device-width,initial-scale=1"/>
        <meta name="robots" content="noindex, nofollow"/>

        <link rel="shortcut icon" href="_img/favicon.png" />
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Source+Code+Pro:300,500' rel='stylesheet' type='text/css'>
        <link rel="base" href="<?= BASE; ?>/admin/">

        <link rel="stylesheet" href="_css/reset.css"/>        
        <link rel="stylesheet" href="_css/workcontrol.css"/>
    </head>
    <body class="login">
        <div class="container">
            <div class="login_box">
                <img class="login_logo" alt="<?= ADMIN_NAME; ?>" title="<?= ADMIN_NAME; ?>" src="_img/work_icon.png"/>
                <form class="login_form" name="work_login" action="" method="post" enctype="multipart/form-data">
                    <div class="callback_return m_botton">
                        <?php
                        if (!empty($_SESSION['trigger_login'])):
                            echo $_SESSION['trigger_login'];
                            unset($_SESSION['trigger_login']);
                        endif;
                        ?>
                    </div>
                    <input type="hidden" name="callback" value="Login">
                    <input type="hidden" name="callback_action" value="admin_login">

                    <label class="label">
                        <span class="legend">Seu E-mail:</span>
                        <input type="email" name="user_email" value="<?= $Cookie ? $Cookie : ''; ?>" placeholder="E-mail:" required/>
                    </label>

                    <label class="label">
                        <span class="legend">Sua Senha:</span>
                        <input type="password" name="user_password" placeholder="Senha:" required/>
                    </label>

                    <label class="label_check">
                        <input type="checkbox" name="user_remember" value="1" <?= $Cookie ? 'checked' : '' ?> > Lembrar!
                    </label>

                    <img class="form_load none" style="float: right; margin-top: 3px; margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
                    <button class="btn btn_green fl_right">Entrar!</button>
                    <div class="clear"></div>
                </form>
                <p class="login_link"><a href="../">&larrhk; Voltar para <?= SITE_NAME; ?>!</a> &nbsp;&nbsp;&nbsp; <a href="recover.php">Perdeu sua senha?</a></p>
            </div>
        </div>

        <script src="../_cdn/jquery.js"></script>
        <script src="../_cdn/jquery.form.js"></script>
        <script src="_js/workcontrol.js"></script>
    </body>
</html>
<?php
ob_end_flush();
