<form name="account_form" action="" method="post" enctype="multipart/form-data">
    <div class="account_form_fields">
        <div class="account_form_callback"></div>
        <label>
            <span>E-mail:</span>
            <input name="user_email" type="email" placeholder="Informe seu E-mail:" required/>
        </label>

        <label>
            <span>Senha:</span>
            <input name="user_password" type="password" placeholder="Informe sua Senha:" required/>
        </label>
    </div>

    <input type="hidden" name="action" value="wc_login"/>

    <div class="account_form_actions">
        <button class="btn btn_blue">Iniciar Sessão!</button>
        <img alt="Efetuando Login!" title="Efetuando Login!" src="<?= BASE; ?>/_cdn/widgets/account/load.gif"/>
        <a title="Recuperar Senha!" href="<?= $AccountBaseUI; ?>/recuperar#acc">Esqueci Minha Senha!</a>
    </div>
</form>