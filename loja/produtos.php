<?php
require ("inc/header.inc.php");
$and = '';
if (isset($_GET["codcategoria"]) && $_GET["codcategoria"] != NULL && $_GET["codcategoria"] != "") {
    $and .= ' and produto.codcategoria = ' . $_GET["codcategoria"];
    $categoriap = $conexao->comandoArray('select nome from categoriaproduto where codcategoria = ' . $_GET["codcategoria"]);
    $tituloH2 = 'Todos os ' . $categoriap["nome"];
} else {
    $tituloH2 = 'Todos os produtos';
}
$sql = 'select * from produto where 1 = 1' . $and. ' limit 12';
$resproduto = $conexao->comando($sql);
$qtdproduto = $conexao->qtdResultado($resproduto);
?>

<div class="container">
    <h2><?= $tituloH2 ?></h2>
    <div class="produtos-destaques" align="center">
        <div class="row" id="listagemProdutosPaginacao">
            <?php
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
        <div class="row col-md-5">
            <?php
            $totalProdutos = $conexao->comandoArray("select count(1) as qtd from produto where 1 = 1");
            $totalPaginas = $totalProdutos["qtd"] / $qtdproduto;
            echo '<input type="hidden" name="totalPaginaProdutos" id="totalPaginaProdutos" value="', $totalPaginas, '"/>';
            echo "<ul class='pagination pagination-sm no-margin pull-right' id='paginacaoDepoimentos'>";
            echo "<li><a href='javascript: procurarProdutoPaginacao(0)'><i class='fa fa-fast-backward' aria-hidden='true'></i></a></li>";
            for ($i = 1; $i <= $totalPaginas; $i++) {
                echo "<li><a href='javascript: procurarProdutoPaginacao($i)'>$i</a></li>";
            }
            if ($totalPaginas > 10) {
                echo "<li><a href='javascript: procurarProdutoPaginacao($totalPaginas)'><i class='fa fa-fast-forward' aria-hidden='true'></i></a></li>";
            }
            echo '</ul>';
            ?>            
        </div>        
        <br>
        <br>

    </div>
</div>
</div>

<?php require ("inc/footer.inc.php"); ?>