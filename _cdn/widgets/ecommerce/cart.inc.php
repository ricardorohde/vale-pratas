<link rel="stylesheet" href="<?= BASE; ?>/_cdn/widgets/ecommerce/cart.css"/>
<script src="<?= BASE; ?>/_cdn/widgets/ecommerce/cart.js"></script>

<div class="wc_cart_callback"></div>
    <?php
        $compleURL = '';
        $uri = getenv('REQUEST_URI');
        $separado_pagina = explode('vend=', $uri);
        if(isset($separado_pagina[1]) && $separado_pagina[1] != NULL && $separado_pagina[1] != ""){
            
            $vendedor = base64_decode($separado_pagina[1]);
            $separa_vendedor = explode('=', $vendedor);
            if(is_numeric((int)$separa_vendedor[1])){
                $_GET["idVendedor"] = $separa_vendedor[1];
                echo '<input name="idvendedor" type="hidden" value="',$separa_vendedor[1],'"/>';
                $compleURL = '?vend='. $separado_pagina[1];
            }
        }
    ?>

<div class="wc_cart_manager">
    <div class="wc_cart_manager_content"> 
        <div class="wc_cart_manager_header"><?= ECOMMERCE_TAG; ?></div>
        <div class="wc_cart_manager_info">Você adicionou <b></b> a sua lista de compras. O que deseja fazer agora ???</div>
        <div class="wc_cart_manager_actions">
            <span class="wc_cart_close btn btn_blue">Continuar Comprando!</span>
            <a class="wc_cart_finish btn btn_green" title="Concluir Compra!" href="<?= BASE; ?>/pedido/home<?=$compleURL?>#cart">Fechar Compra!</a>
            <div class="clear"></div>
        </div>
    </div>
</div>