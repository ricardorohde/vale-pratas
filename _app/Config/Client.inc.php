<?php

if (!$WorkControlDefineConf):
    /*
     * SITE CONFIG
     */
    define('SITE_NAME', 'Vale Pratas'); //Nome do site do cliente
    define('SITE_SUBNAME', ''); //Nome do site do cliente
    define('SITE_DESC', 'Jóias para todos os gostos'); //Descrição do site do cliente

    define('SITE_FONT_NAME', 'Lato'); //Tipografia do site (https://www.google.com/fonts)
    define('SITE_FONT_WHIGHT', '300,400,600,700,800'); //Tipografia do site (https://www.google.com/fonts)

    /*
     * SHIP CONFIG
     * DADOS DO SEU CLIENTE/DONO DO SITE
     */
    define('SITE_ADDR_NAME', 'Vale Pratas LTDA'); //Nome de remetente
    define('SITE_ADDR_RS', 'Vale Pratas'); //Razão Social
    define('SITE_ADDR_EMAIL', 'contato@valepratas.com.br'); //E-mail de contato
    define('SITE_ADDR_SITE', 'www.valepratas.com.br'); //URL descrita
    define('SITE_ADDR_CNPJ', '00.000.000/0000-00'); //CNPJ da empresa
    define('SITE_ADDR_IE', '000/0000000'); //Inscrição estadual da empresa
    define('SITE_ADDR_PHONE_A', '+55 (12) 3341-6170'); //Telefone 1
    define('SITE_ADDR_PHONE_B', '+55 Whatsapp Em Breve'); //Telefone 2
    define('SITE_ADDR_ADDR', ' Seg. a Sexta 08:00 as 18:00 <br> Sábado 09:00 às 12:00'); //ENDEREÇO: rua, número (complemento)
    define('SITE_ADDR_CITY', 'São Paulo'); //ENDEREÇO: cidade
    define('SITE_ADDR_DISTRICT', 'Centro'); //ENDEREÇO: bairro
    define('SITE_ADDR_UF', 'SP'); //ENDEREÇO: UF do estado
    define('SITE_ADDR_ZIP', '06790-030'); //ENDEREÇO: CEP
    define('SITE_ADDR_COUNTRY', 'Brasil'); //ENDEREÇO: País

    /*
     * SOCIAL CONFIG
     * Google
     */
    define('SITE_SOCIAL_GOOGLE', false);
    define('SITE_SOCIAL_GOOGLE_AUTHOR', ''); //https://plus.google.com/????? (**ID DO USUÁRIO)
    define('SITE_SOCIAL_GOOGLE_PAGE', ''); //https://plus.google.com/???? (**ID DA PÁGINA)

    /*
     * Facebook
     */
    define('SITE_SOCIAL_FB', true);
    define('SITE_SOCIAL_FB_APP', ''); //Opcional APP do facebook
    define('SITE_SOCIAL_FB_AUTHOR', ''); //https://www.facebook.com/?????
    define('SITE_SOCIAL_FB_PAGE', 'facebook'); //https://www.facebook.com/?????

    /*
     * Twitter
     */
    define('SITE_SOCIAL_TWITTER', 'facebook'); //https://www.twitter.com/?????

    /*
     * YouTube Channel
     */
    define('SITE_SOCIAL_YOUTUBE', 'facebook'); //https://www.youtube.com/user/?????
endif;