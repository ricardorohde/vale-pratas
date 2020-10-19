<form id="<?= $pdt_id; ?>" class="wc_cart_add" name="cart_add" method="post" enctype="multipart/form-data">
    <input name="pdt_id" type="hidden" value="<?= $pdt_id; ?>"/>
    <?php
        $uri = getenv('REQUEST_URI');
        $separado_pagina = explode('vend=', $uri);
        if(isset($separado_pagina[1]) && $separado_pagina[1] != NULL && $separado_pagina[1] != ""){
            
            $vendedor = base64_decode($separado_pagina[1]);
            $separa_vendedor = explode('=', $vendedor);
            if(is_numeric((int)$separa_vendedor[1])){
                $_GET["idVendedor"] = $separa_vendedor[1];
                echo '<input name="idvendedor" type="hidden" value="',$separa_vendedor[1],'"/>';
            }
        }
    ?>
    <button id="<?= $pdt_id; ?>" class="wc_cart_less cart_more less">-
    </button><input name="item_amount" type="text" value="1" max="<?= $pdt_inventory; ?>"/><button
        id="<?= $pdt_id; ?>" class="wc_cart_plus cart_more plus">+</button>

        <button title="clique para adicionar ao carrinho" class="btn <?= (!empty($CartBtn) ? $CartBtn : 'btn_green'); ?>"> <span class="fa fa-shopping-cart"></span> <?= ECOMMERCE_BUTTON_TAG; ?></button>
</form>