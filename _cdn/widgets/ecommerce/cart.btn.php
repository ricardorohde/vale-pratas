<form class="wc_cart_add" name="cart_add" method="post" enctype="multipart/form-data">
    <input name="pdt_id" type="hidden" value="<?= $pdt_id; ?>"/>
    <input name="item_amount" type="hidden" value="1"/>
    <button class="btn <?= (!empty($CartBtn) ? $CartBtn : 'btn_green'); ?>"> <span class="fa fa-shopping-cart"></span> <?= ECOMMERCE_BUTTON_TAG; ?></button>
</form>