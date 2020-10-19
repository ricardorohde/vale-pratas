<article class="box <?= empty($PdtBox) ? 'box4' : $PdtBox; ?>">
    <div class="single_pdt">
        <div class="single_pdt_cover">
            <a href="<?= BASE; ?>/produto/<?= $pdt_name; ?>" title="Ver detalhes de <?= $pdt_title; ?>">
                <img alt="Detalhes do produto <?= $pdt_title; ?>" title="Detalhes do produto <?= $pdt_title; ?>" src="<?= BASE; ?>/tim.php?src=uploads/<?= $pdt_cover; ?>&w=<?= THUMB_W / 2; ?>&h=<?= THUMB_H / 2; ?>"/>
            </a>
            <?php
            $PdtStockeOut = ($pdt_inventory && $pdt_inventory <= 2 ? true : false);

            if ($pdt_offer_price && $pdt_offer_start <= date('Y-m-d H:i:s') && $pdt_offer_end >= date('Y-m-d H:i:s')):
                $PdtDiscount = number_format(100 - ((100 / $pdt_price) * $pdt_offer_price), '0', '', '');
                $PdtIdentBox = (!empty($PdtStockeOut) ? ' single_pdt_offer_ident' : null);
                echo "<span class='single_pdt_offer{$PdtIdentBox}'>{$PdtDiscount}% OFF</span>";
            endif;

            if ($PdtStockeOut):
                echo "<span class='single_pdt_stock'>Últimas Unidades</span>";
            endif;
            ?>
        </div>

        <header>
            <h1><a href="<?= BASE; ?>/produto/<?= $pdt_name; ?>" title="Ver detalhes de <?= $pdt_title; ?>"><?= $pdt_title; ?></a></h1>
            <p><?= Check::Words($pdt_subtitle, 10); ?></p>
        </header>

        <div class="single_pdt_price">
            <?php
            $PdtPrice = null;
            if ($pdt_offer_price && $pdt_offer_start <= date('Y-m-d H:i:s') && $pdt_offer_end >= date('Y-m-d H:i:s')):
                $PdtPrice = $pdt_offer_price;
                echo "<span>de R$ <strike>" . number_format($pdt_price, '2', ',', '.') . "</strike> por</span>Apenas R$ " . number_format($pdt_offer_price, '2', ',', '.');
            else:
                $PdtPrice = $pdt_price;
                echo "<span class='font_white'>-</span>Apenas R$ " . number_format($pdt_price, '2', ',', '.');
            endif;

            if (ECOMMERCE_PAY_SPLIT):
                $MakeSplit = intval($PdtPrice / ECOMMERCE_PAY_SPLIT_MIN);
                $NumSplit = (!$MakeSplit ? 1 : ($MakeSplit && $MakeSplit <= ECOMMERCE_PAY_SPLIT_NUM ? $MakeSplit : ECOMMERCE_PAY_SPLIT_NUM));
                $SplitPrice = number_format(($PdtPrice * (pow(1 + (ECOMMERCE_PAY_SPLIT_ACM / 100), $NumSplit - ECOMMERCE_PAY_SPLIT_ACN)) / $NumSplit), '2', ',', '.');
                echo "<p class='pdt_single_split'>Em até {$NumSplit}x de R$ {$SplitPrice}</p>";
            endif;
            ?>
        </div>

        <div class="single_pdt_btn">
            <?php require '_cdn/widgets/ecommerce/cart.btn.php'; ?>
        </div>
    </div>
</article>