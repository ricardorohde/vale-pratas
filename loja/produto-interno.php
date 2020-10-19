<?php

require ("inc/header.inc.php");
if (isset($_GET["codproduto"]) && $_GET["codproduto"] != NULL && $_GET["codproduto"] != "") {
    $and .= ' and codproduto = ' . (int)$_GET["codproduto"];
}
$sql = 'select * from produto where 1 = 1 ' . $and . ' order by nome';
$produtop = $conexao->comandoArray($sql);
?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div id="carousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="item active">
                        <img width="200" src="/arquivos/<?= $produtop["foto"] ?>">
                    </div>
                    <?php

                    for ($i = 2; $i <= 4; $i++) {

                        if (isset($produtop["imagem" . $i]) && $produtop["imagem" . $i] != NULL && $produtop["imagem" . $i] != "") {

                            echo '<div class="item">';

                            echo '<img src="/arquivos/', $produtop["imagem" . $i], '">';

                            echo '</div>';

                        }

                    }

                    ?>



                </div>

            </div> 

            <div class="clearfix">

                <div id="thumbcarousel" class="carousel slide" data-interval="false">

                    <div class="carousel-inner">

                        <div class="item active">

                            <div data-target="#carousel" data-slide-to="2" class="thumb">

                                <img src="/arquivos/<?= $produtop["foto"] ?>">

                            </div>                                            

                            <?php

                            for ($i = 2; $i <= 4; $i++) {

                                if (isset($produtop["imagem" . $i]) && $produtop["imagem" . $i] != NULL && $produtop["imagem" . $i] != "") {

                                    echo '<div data-target="#carousel" data-slide-to="', $i, '" class="thumb">';

                                    echo '<img src="/arquivos/', $produtop["imagem$i"], '">';

                                    echo '</div>';

                                }

                            }

                            ?>

                        </div>

                    </div>

                    <a class="left carousel-control" href="#thumbcarousel" role="button" data-slide="prev">

                        <span class="glyphicon glyphicon-chevron-left"></span>

                    </a>

                    <a class="right carousel-control" href="#thumbcarousel" role="button" data-slide="next">

                        <span class="glyphicon glyphicon-chevron-right"></span>

                    </a>

                </div>

            </div>

        </div>

        <div class="row">

        <div class="col-md-3">

            <h3><?= $produtop["nome"] ?></h3>

            <p>R$ <?= number_format($produtop["valor"], 2, ',', '') ?></p>



            <p class="obs-produto hidden-xs hidden-sm"><?= $produtop["desconto"] ?>% de desconto no boleto</p>

            <form id='fvenda' target="pagseguro" method="post" action="https://pagseguro.uol.com.br/v2/checkout/cart.html?action=add">    



                <input type="hidden" name="receiverEmail" value="<?= $empresap["emailpagseguro"] ?>">

                <input type="hidden" name="currency" value="BRL">  

                <input type="hidden" name="itemId" value="0001">  

                <input type="hidden" name="itemDescription" value="<?= $produtop["nome"] ?>">  

                <input type="hidden" name="itemAmount" value="<?= number_format($produtop["valor"], 2, '.', '') ?>">  

                <input type="hidden" name="itemQuantity" value="1">  

                <input type="hidden" name="codproduto" id="codproduto" value="<?=  base64_encode($produtop["codproduto"])?>"/>
                <input type="hidden" name="codvendedor" id="codvendedor" value="<?=  $_GET["codvendedor"]?>"/>


                <input type="hidden" name="codproduto" id="codproduto" value="<?= base64_encode($produtop["codproduto"]) ?>"/>

                <input type="hidden" name="codvendedor" id="codvendedor" value="<?= $_GET["codvendedor"] ?>"/>



                <?php if (isset($_SESSION["codpessoa"])) { ?>

                    <input type="submit" class="btn btn-default" value="Comprar" onclick='salvarVenda();'/>

                <?php } else { ?>

                    <input type="button" class="btn btn-default" value="Comprar" onclick='swal("Atenção", "Precisa entrar no sistema antes de comprar!", "info")'/>

                <?php } ?>

            </form>
            </div>
            <div class="row">
 <div class="col-md-7">

            <h3>Descrição</h3>

            <p>

                <?= $produtop["descricao"] ?>

            </p>

            <hr>

        </div>
        </div>

    </div>

</div>
</div>



<br>
<br>

<div class="container">

    <div class="row">

        <div class="col-md-12">

            <h3>Veja também</h3>

            <div class="row" id="listagemProdutosPaginacao">

                <?php

                $resproduto = $conexao->comando('select * from produto where codtipo = ' . $produtop["codtipo"] . ' order by nome limit 4');

                $qtdproduto = $conexao->qtdResultado($resproduto);                

                if ($qtdproduto > 0) {

                    $linhaProduto = 0;

                    while ($produto = $conexao->resultadoArray($resproduto)) {

                        if ($linhaProduto >= 4) {

                            echo '</div><br><br>';

                            echo '<div class="row linhaProduto">';

                            $linhaProduto = 0;

                        }

                        ?> 

                        <div class="col-md-3 col-xs-4 produtos-destaques-center">

                            <a style="color: black;" href="/loja/produto/<?= str_replace(' ', '-', $produto["nome"]) ?>/<?= $produto["codproduto"] ?>">

                                <img style="width: 110px;height: 110px;" src="/arquivos/<?= $produto["foto"] ?>" class="img-responsive" alt=""/>

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

        </div>

    </div>

</div>
<br>
<br>





<?php require ("inc/footer.inc.php"); ?>