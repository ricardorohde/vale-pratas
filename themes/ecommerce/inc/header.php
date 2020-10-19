<?php require '_cdn/widgets/ecommerce/cart.inc.php'; ?>
<div class="main_header_top container">
    <div class="content">

        <div class="main_header_cart"><?php require '_cdn/widgets/ecommerce/cart.bar.php'; ?></div>
        <div class="main_header_user"><?php require '_cdn/widgets/account/account.bar.php'; ?></div>

        <div class="main_header_bar">
            <div class="main_header_bar_line contact">
                <ul class="main_header_bar_social">
                    <?php
                    if (SITE_SOCIAL_FB):
                        echo '<li><a class="facebook" target="_blank" href="https://www.facebook.com/' . SITE_SOCIAL_FB_PAGE . '" title="No Facebook"><span class="fa fa-facebook-square"></span></a></li>';
                    endif;

                    if (SITE_SOCIAL_TWITTER):
                        echo '<li><a class="twitter" target="_blank" href="https://www.twitter.com/' . SITE_SOCIAL_TWITTER . '" title="No Twitter"><span class="fa fa-twitter-square"></span></a></li>';
                    endif;

                    if (SITE_SOCIAL_GOOGLE):
                        echo '<li><a class="google" target="_blank" href="https://plus.google.com/' . SITE_SOCIAL_GOOGLE_PAGE . '" title="No Google Plus"><span class="fa fa-google-plus-square"></span></a></li>';
                    endif;

                    if (SITE_SOCIAL_YOUTUBE):
                        echo '<li><a class="youtube" target="_blank" href="https://www.youtube.com/user/' . SITE_SOCIAL_YOUTUBE . '" title="No YouTube"><span class="fa fa-youtube-square"></span></a></li>';
                    endif;
                    ?>
                </ul>
                <div class="main_header_bar_contact"><a href="#"><span class="fa fa-phone"></span><?= SITE_ADDR_PHONE_A; ?></a>  <a href="mailto:" title="Enviar E-mail"> <span class="fa fa-envelope"></span> <?= SITE_ADDR_EMAIL; ?></a></div>

            </div>

            <div class="clear"></div>
        </div>
    </div>
</div>

<div class="main_header container">
    <div class="content">
        <header>
            <h1 style="font-size: 0em;"><?= SITE_NAME; ?></h1>
            <a href=""><img class="header_logo" src="<?= INCLUDE_PATH; ?>/images/logo.png" /></a>
            <div class="clear"></div>
        </header>
        <div class="clear"></div>
    </div>
    <?php
    $dbh = Conn::getConn();
    $categorias = $dbh->query("SELECT cat_title, cat_id, cat_name FROM `ws_products_categories` WHERE cat_parent is null order by ordem");
    ?>
    <div class="content main_nav">
        <nav>
            <h1 class="main_nav_mobile_menu">&#9776; MENU</h1>
            <ul>
                <li><a title="<?= SITE_NAME; ?>" href="<?= BASE; ?>">Home</a></li>
                <?php
                if ($categorias != false) {
                    foreach ($categorias as $key => $categoria) {
                        $sth = $dbh->prepare("SELECT cat_title, cat_id, cat_name FROM `ws_products_categories` WHERE cat_parent = " . $categoria["cat_id"]);
                        $sth->execute();
                        $subcategorias = $sth->fetchAll();
                        $qtdsub = count($subcategorias);
                        if ($qtdsub == 0) {
                            echo '<li>';
                            echo '<a href="/produtos/', $categoria["cat_name"], '" title="', $categoria["cat_title"], '">', $categoria["cat_title"], '</a> ';
                            echo '</li>';
                        } else {
                            echo '<li class="dropdown">';
                            echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#">', $categoria["cat_title"], ' <span class="caret"></span></a> ';
                            if ($subcategorias != false) {
                                echo '<ul class="dropdown-menu">';
                                foreach ($subcategorias as $key => $subcategoria) {
                                    echo '<li><a href="/produtos/', $subcategoria["cat_name"], '">',$subcategoria["cat_title"],'</a></li>';
                                }
                                echo '</ul>';
                            }
                            echo '</li> ';
                        }
                    }
                }
                ?>
                <!--                <li> <a href="/produtos/aneis" title="Anéis">Anéis</a> </li>  
                                <li> <a href="/produtos/corrente" title="Correntes">Correntes</a> </li>
                                <li> <a href="/produtos/pingentes" title="Pingentes">Pingentes</a> </li>
                                <li> <a href="/produtos/brinco" title="brincos">Brincos</a> </li>
                                <li> <a href="/produtos/pulseiras" title="Pulseiras">Pulseiras</a> </li>
                                <li> <a href="/produtos/tornozeleiras" title="Tornozeleiras">Tornozeleiras</a> </li>
                                <li> <a href="/produtos/acessorios" title="Acessórios">Acessórios</a> </li>-->

            </ul>
        </nav>


        <div class="main_header_bar_line search">
            <form class="search_form" name="search" action="" method="post" enctype="multipart/form-data">
                <input class="input" type="search" name="s" placeholder="Busque seu Produtos:" required/><button class="btn btn_blue"><span class="fa fa-search"></span></button>
            </form>
        </div>

        <div class="clear"></div>
    </div>
</div>