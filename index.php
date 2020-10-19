<?php
ob_start();
session_start();

require './_app/Config.inc.php';


//READ CLASS AUTO INSTANCE
if (empty($Read)):
    $Read = new Read;
endif;

$Sesssion = new Session(SIS_CACHE_TIME);

//USER SESSION VALIDATION
if (!empty($_SESSION['userLogin'])):
    if (empty($Read)):
        $Read = new Read;
    endif;
    $Read->ExeRead(DB_USERS, "WHERE user_id = :user_id", "user_id={$_SESSION['userLogin']['user_id']}");
    if ($Read->getResult()):
        $_SESSION['userLogin'] = $Read->getResult()[0];
    else:
        unset($_SESSION['userLogin']);
    endif;
endif;

$getURL = strip_tags(trim(filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
$setURL = (empty($getURL) ? 'index' : $getURL);
$URL = explode('/', $setURL);
$SEO = new Seo($setURL);
?>
<!DOCTYPE html>
<html lang="pt-br" itemscope itemtype="https://schema.org/<?= $SEO->getSchema(); ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="mit" content="0027066">
        <meta name="viewport" content="width=device-width,initial-scale=1">

        <title><?= $SEO->getTitle(); ?></title>
        <meta name="description" content="<?= $SEO->getDescription(); ?>"/>
        <meta name="robots" content="index, follow"/>

        <link rel="base" href="<?= BASE; ?>"/>
        <link rel="canonical" href="<?= BASE; ?>/<?= $getURL; ?>"/>
        <link rel="alternate" type="application/rss+xml" href="<?= BASE; ?>/rss.xml"/>
        <link rel="sitemap" type="application/xml" href="<?= BASE; ?>/sitemap.xml" />
        <?php
        if (SITE_SOCIAL_GOOGLE):
            echo '<link rel="author" href="https://plus.google.com/' . SITE_SOCIAL_GOOGLE_AUTHOR . '/posts"/>' . "\r\n";
            echo '        <link rel="publisher" href="https://plus.google.com/' . SITE_SOCIAL_GOOGLE_PAGE . '"/>' . "\r\n";
        endif;
        ?>

        <meta itemprop="name" content="<?= $SEO->getTitle(); ?>"/>
        <meta itemprop="description" content="<?= $SEO->getDescription(); ?>"/>
        <meta itemprop="image" content="<?= $SEO->getImage(); ?>"/>
        <meta itemprop="url" content="<?= BASE; ?>/<?= $getURL; ?>"/>

        <meta property="og:type" content="article" />
        <meta property="og:title" content="<?= $SEO->getTitle(); ?>" />
        <meta property="og:description" content="<?= $SEO->getDescription(); ?>" />
        <meta property="og:image" content="<?= $SEO->getImage(); ?>" />
        <meta property="og:url" content="<?= BASE; ?>/<?= $getURL; ?>" />
        <meta property="og:site_name" content="<?= SITE_NAME; ?>" />
        <meta property="og:locale" content="pt_BR" />
        <?php
        if (SITE_SOCIAL_FB):
            if (SITE_SOCIAL_FB_APP):
                echo '<meta property="og:app_id" content="' . SITE_SOCIAL_FB_APP . '" />' . "\r\n";
            endif;
            echo '        <meta property="article:author" content="https://www.facebook.com/' . SITE_SOCIAL_FB_AUTHOR . '" />' . "\r\n";
            echo '        <meta property="article:publisher" content="https://www.facebook.com/' . SITE_SOCIAL_FB_PAGE . '" />' . "\r\n";
        endif;
        ?>

        <meta property="twitter:card" content="summary_large_image" />
        <?php
        if (SITE_SOCIAL_TWITTER):
            echo '<meta property="twitter:site" content="@' . SITE_SOCIAL_TWITTER . '" />' . "\r\n";
        endif;
        ?>
        <meta property="twitter:domain" content="<?= BASE; ?>" />
        <meta property="twitter:title" content="<?= $SEO->getTitle(); ?>" />
        <meta property="twitter:description" content="<?= $SEO->getDescription(); ?>" />
        <meta property="twitter:image" content="<?= $SEO->getImage(); ?>" />
        <meta property="twitter:url" content="<?= BASE; ?>/<?= $getURL; ?>" />           

        <link rel="shortcut icon" href="<?= INCLUDE_PATH; ?>/images/favicon.ico"/>
        <link href='https://fonts.googleapis.com/css?family=<?= SITE_FONT_NAME; ?>:<?= SITE_FONT_WHIGHT; ?>' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <style>*{font-family: '<?= SITE_FONT_NAME; ?>', sans-serif;}</style>

        <link rel="stylesheet" href="<?= BASE; ?>/_cdn/shadowbox/shadowbox.css"/>
        <link rel="stylesheet" href="<?= BASE; ?>/_cdn/bootcss/reset.css"/>
        <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>/style.css?<?=date("YmdHis")?>"/>

        <!--[if lt IE 9]>
            <script src="<?= BASE; ?>/_cdn/html5shiv.js"></script>
        <![endif]-->

        <script src="<?= BASE; ?>/_cdn/jquery.js"></script>
         <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="<?= BASE; ?>/_cdn/workcontrol.js"></script>
        <script src="<?= BASE; ?>/_cdn/jquery.maskedinput.js"></script>
        <script src="<?= BASE; ?>/_cdn/shadowbox/shadowbox.js"></script>
        <?php
        if (file_exists('themes/' . THEME . '/scripts.js')):
            echo '<script src="' . INCLUDE_PATH . '/scripts.js?123456789"></script>';
        endif;
        ?>

    </head>
    <body>
        <?php
        //PESQUISA
        $Search = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if ($Search && !empty($Search['s'])):
            $Search = urlencode(strip_tags(trim($Search['s'])));
            header('Location: ' . BASE . '/pesquisa/' . $Search);
        endif;

        //HEADER
        if (file_exists(REQUIRE_PATH . "/inc/header.php")):
            require REQUIRE_PATH . "/inc/header.php";
        else:
            trigger_error('Crie um arquivo /inc/header.php na pasta do tema!');
        endif;

        //CONTENT
        $URL[1] = (empty($URL[1]) ? null : $URL[1]);

        $Pages = array();
        $Read->FullRead("SELECT page_name FROM " . DB_PAGES . " WHERE page_status = 1");
        if ($Read->getResult()):
            foreach ($Read->getResult() as $SinglePage):
                $Pages[] = $SinglePage['page_name'];
            endforeach;
        endif;

        if (in_array($URL[0], $Pages) && file_exists(REQUIRE_PATH . '/pagina.php') && empty($URL[1])):
            if (file_exists(REQUIRE_PATH . "/page-{$URL[0]}.php")):
                require REQUIRE_PATH . "/page-{$URL[0]}.php";
            else:
                require REQUIRE_PATH . '/pagina.php';
            endif;
        elseif (file_exists(REQUIRE_PATH . '/' . $URL[0] . '.php')):
            if ($URL[0] == 'artigos' && file_exists(REQUIRE_PATH . "/cat-{$URL[1]}.php")):
                require REQUIRE_PATH . "/cat-{$URL[1]}.php";
            else:
                require REQUIRE_PATH . '/' . $URL[0] . '.php';
            endif;
        elseif (file_exists(REQUIRE_PATH . '/' . $URL[0] . '/' . $URL[1] . '.php')):
            require REQUIRE_PATH . '/' . $URL[0] . '/' . $URL[1] . '.php';
        else:
            if (file_exists(REQUIRE_PATH . "/404.php")):
                require REQUIRE_PATH . '/404.php';
            else:
                trigger_error("Não foi possível incluir o arquivo themes/" . THEME . "/{$getURL}.php <b>(O arquivo 404 também não existe!)</b>");
            endif;
        endif;

        //FOOTER
        if (file_exists(REQUIRE_PATH . "/inc/footer.php")):
            require REQUIRE_PATH . "/inc/footer.php";
        else:
            trigger_error('Crie um arquivo /inc/footer.php na pasta do tema!');
        endif;
        ?>
    </body>
</html>
<?php
ob_end_flush();

if (!file_exists('.htaccess')):
    $htaccesswrite = "RewriteEngine On\r\nOptions All -Indexes\r\n\r\nRewriteCond %{SCRIPT_FILENAME} !-f\r\nRewriteCond %{SCRIPT_FILENAME} !-d\r\nRewriteRule ^(.*)$ index.php?url=$1\r\n\r\n<IfModule mod_expires.c>\r\nExpiresActive On\r\nExpiresByType image/jpg 'access 1 year'\r\nExpiresByType image/jpeg 'access 1 year'\r\nExpiresByType image/gif 'access 1 year'\r\nExpiresByType image/png 'access 1 year'\r\nExpiresByType text/css 'access 1 month'\r\nExpiresByType application/pdf 'access 1 month'\r\nExpiresByType text/x-javascript 'access 1 month'\r\nExpiresByType application/x-shockwave-flash 'access 1 month'\r\nExpiresByType image/x-icon 'access 1 year'\r\nExpiresDefault 'access 2 days'\r\n</IfModule>";
    $htaccess = fopen('.htaccess', "w");
    fwrite($htaccess, str_replace("'", '"', $htaccesswrite));
    fclose($htaccess);
endif;
