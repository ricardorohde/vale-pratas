<div class="main_sign" id="sign-newslatter">
    <span class="wc_goto goto_home"><span class="fa fa-angle-double-up "></span></span>

    <article class="content">
        <header>
            <h1>Assine nossa Newsletter e receba promoções</h1>
            <p>Deixe seu nome e seu e-mail e fique por dentro!</p>
        </header>
        <?php
        $PostNews = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (!empty($PostNews['sendNews'])):
            if (in_array('', $PostNews)):
                Erro("<b>Erro:</b> Preencha todos os campos!", E_USER_WARNING);
            elseif (!Check::Email($PostNews['user_email'])):
                Erro("<b>Erro:</b> Informe um e-mail válido!", E_USER_WARNING);
            else:
                $MessageSignNews = "<p><b>Newsletter:</b></p>";
                $MessageSignNews .= "<p><b>Nome</b>: {$PostNews['user_name']}</p>";
                $MessageSignNews .= "<p><b>E-mail</b>: {$PostNews['user_email']}</p>";
                $EmailSignNews = new Email;
                $EmailSignNews->EnviarMontando("Novo E-mail de Newsletter", $MessageSignNews, SITE_NAME, MAIL_USER, SITE_NAME, 'contato@valepratas.com.br ');
                Erro("<b>Sucesso:</b> Seu e-mail foi enviado para nossa lista de novidades!");
                $PostNews = null;
            endif;
        endif;
        ?>
        <form class="box box3" name="signin" action="<?= BASE; ?>/index#sign-newslatter" method="post">
            <label>
                <span>Nome:</span>
                <input type="text" name="user_name" placeholder="Informe seu Nome:" value="<?php if (!empty($PostNews['user_name'])) echo $PostNews['user_name']; ?>"/>
            </label>
            <label>
                <span>E-mail:</span>
                <input type="text" name="user_email" placeholder="Informe seu E-mail:" value="<?php if (!empty($PostNews['user_email'])) echo $PostNews['user_email']; ?>"/>
            </label>
            <label class="box">
                <button name="sendNews" value="true" class="btn btn_green box"><span class="fa fa-paper-plane" style="display: inline-block;"></span> Cadastre-se!</button>
            </label>
        </form>
        <div class="clear"></div>
    </article>
</div>

<div class="main_footer">
    <footer class="content">

        <div class="box box4">
            <p class="main_footer_block">
                <span class="fa fa-home"></span>

                <?= SITE_ADDR_ADDR; ?>
            </p>
        </div><div class="box box4">
            <p class="main_footer_block">
                <span class="fa fa-phone"></span>
                <?= SITE_ADDR_PHONE_A; ?><br>
                <?= SITE_ADDR_PHONE_B; ?>
            </p>
        </div><div class="box box4">
            <p class="main_footer_block">
                <span class="fa fa-envelope"></span>
                <a style="color: white" href="mailto: <?= SITE_ADDR_EMAIL; ?>"><?= SITE_ADDR_EMAIL; ?></a>
            </p>
        </div><div class="box box4">
            <a href="#" style="text-decoration: none;color: #fff;">
                <p class="main_footer_block">
                    <span class="fa fa-envelope-open-o"></span>
                    Assine nossa Newslatter 
                    e receba promoções
                </p>
            </a>
        </div>

        <div style="margin: 0 auto;padding: 30px 0">
            <div class="box box2">
                <a href="#" style="text-decoration: none;color: #fff;">
                    <p class="main_footer_block">
                        <img src="<?= INCLUDE_PATH; ?>/images/cart.png" />
                    </p>
                    <p class="main_footer_block"> 
                        Em até 12x - Parcela mínima de R$ 100,00
                    </p>
                </a>
            </div><div class="box box2">
                <a href="#" style="text-decoration: none;color: #fff;">
                    <p class="main_footer_block">
                        <img src="<?= INCLUDE_PATH; ?>/images/blind.png" />
                    </p>
                    <p class="main_footer_block"> 
                        Compra totalmente segura e criptografadas
                    </p>
                </a>
            </div>
        </div>

        <p class="main_footer_copy">
            Todos os direitos reservados a empresa <b><?= SITE_ADDR_NAME; ?></b> 
        </p>

        <?php
        if (!empty($URL[1]) && $URL[1] == 'dev'):
            $CookieDevONline = filter_input(INPUT_COOKIE, 'developer_devonline', FILTER_DEFAULT);
            if (!empty($CookieDevONline)):
                echo "<p  style='font-size: 0.8em;text-align: center;position: relative;top: 28px;'>";
                echo "Desenvolvido por <a href='//www.devonline.com.br' target='_blank' style='color: #fff;text-decoration: none;'>DevOnline</a>";
            else:
                setcookie('developer_devonline', Check::Name(SITE_NAME), time() + 86400, '/');
            endif;
        endif;
        ?>

        <div class="clear"></div>
    </footer>
</div>