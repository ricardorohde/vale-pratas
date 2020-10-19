<?php require ("inc/header.inc.php"); ?>

<div class="container hidden-xs">
    <div class="row">
        <div class="col-md-8">
            <div class="publicidade-1">
                <?php
                $bannerp = $conexao->comandoArray('select arquivo, titulo from banner');
                if (isset($bannerp["arquivo"]) && $bannerp["arquivo"] != NULL && $bannerp["arquivo"] != "") {
                    echo '<a href="index.php">';
                    echo '<img src="../arquivos/', $bannerp["arquivo"], '" alt="Banner publicidade">';
                    echo '</a>';
                }
                ?>

            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="destaque-promocao">
            <div class="col-md-4 hidden-xs hidden-sm">
                <div class="destaque-promocao-1"> 
                    <div class="row">
                        <div class="col-md-5">
                            <img src="assets/images/produtos/promocao-1.png" class="img-responsive" alt="">
                        </div>
                        <div class="col-md-7">
                            <p>Lindos colares<br />
                                promoção de<br />
                                <span class="destaque-promocao-span"><strong>10%</strong></span></p>
                        </div>
                    </div>
                </div>				
            </div>

            <div class="col-md-4 hidden-xs hidden-sm">
                <div class="destaque-promocao-1">
                    <div class="row">
                        <div class="col-md-5 col-xs-5">
                            <img src="assets/images/produtos/promocao-2.png" class="img-responsive" alt="">
                        </div>
                        <div class="col-md-7 col-xs-7">
                            <p>Lindos colares<br />
                                promoção de<br />
                                <span class="destaque-promocao-span"><strong>10%</strong></span></p>
                        </div>
                    </div>
                </div>		
            </div>

            <div class="col-md-4 hidden-xs hidden-sm">
                <div class="destaque-promocao-1">
                    <div class="row">
                        <div class="col-md-5">
                            <img src="assets/images/produtos/promocao-1.png" class="img-responsive" alt="">
                        </div>
                        <div class="col-md-7">
                            <p>Lindos colares<br />
                                promoção de<br />
                                <span class="destaque-promocao-span"><strong>10%</strong></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row hidden-md hidden-lg">
                <div class="col-xs-12">
                    <div class="destaque-promocao-1">
                        <div class="row">
                            <div class="col-md-5 col-xs-5">
                                <img src="assets/images/produtos/promocao-2.png" class="img-responsive" alt="">
                            </div>
                            <div class="col-md-7 col-xs-7">
                                <p>Lindos colares<br />
                                    promoção de<br />
                                    <span class="destaque-promocao-span"><strong>10%</strong></span></p>
                            </div>
                        </div>
                    </div>		
                </div>
            </div>

            <div class="row hidden-md hidden-lg">
                <div class="col-xs-12">
                    <div class="destaque-promocao-1">
                        <div class="row">
                            <div class="col-md-5 col-xs-5">
                                <img src="assets/images/produtos/promocao-2.png" class="img-responsive" alt="">
                            </div>
                            <div class="col-md-7 col-xs-7">
                                <p>Lindos colares<br />
                                    promoção de<br />
                                    <span class="destaque-promocao-span"><strong>10%</strong></span></p>
                            </div>
                        </div>
                    </div>		
                </div>
            </div>
        </div>

    </div>
    <br>
    <br>
</div>

<div class="container">
    <div class="produtos-destaques" align="center">
        <div class="row">
            <?php
            $sql = 'select * from produto where 1 = 1 and home = "s" and promocao = "s" limit 16';
            $resproduto = $conexao->comando($sql);
            $qtdproduto = $conexao->qtdResultado($resproduto);            
            if ($qtdproduto > 0) {
                $linhaProduto = 0;
                while ($produto = $conexao->resultadoArray($resproduto)) {
                    if ($linhaProduto >= 4) {
                        echo '</div><br><br>';
                        echo '<div class="row">';
                        $linhaProduto = 0;
                    }
                    ?>
                    <div class="col-md-3 col-xs-4 produtos-destaques-center">
                        <a href="/loja/produto/<?= str_replace(' ', '-', $produto["nome"]) ?>/<?= $produto["codproduto"] ?>">
                            <img style="width: 110px;height: 110px;" src="../arquivos/<?= $produto["foto"] ?>" class="img-responsive" alt=""/>
                            <h3><?= $produto["nome"] ?></h3>
                            <p>R$ <?= number_format($produto["valor"], 2, ',', '.') ?></p>
                            <?php if ($produto["desconto"] > 0) { ?>
                                <p class="obs-produto hidden-xs hidden-sm"><?= number_format($produto["desconto"], 2, ',', '.') ?>% de desconto no boleto</p>
                            <?php } ?>                    
                        </a>
                    </div>
                    <?php
                    $linhaProduto++;
                }
            }
            ?>	
        </div>
        <br>
        <br>

    </div>
</div>

<?php require ("inc/footer.inc.php"); ?>