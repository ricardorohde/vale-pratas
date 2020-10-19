<?php
require ("inc/header.inc.php");
$and = '';
if (isset($_POST["q"]) && $_POST["q"] != NULL && $_POST["q"] != "") {
    $and .= " and (tp.nome like '%{$_POST["q"]}%' or produto.nome like '%{$_POST["q"]}%' or cp.nome like  '%{$_POST["q"]}%')";
}
if (isset($_GET["codtipo"]) && $_GET["codtipo"] != NULL && $_GET["codtipo"] != "") {
    $tipop = $conexao->comandoArray("select nome from tipoproduto where codtipo = {$_GET["codtipo"]}");
    $and .= " and produto.codtipo = {$_GET["codtipo"]}";
} else {
    $tipop["nome"] = "Geral";
}
if (isset($_GET["codcategoria"]) && $_GET["codcategoria"] != NULL && $_GET["codcategoria"] != "") {
    $and .= ' and produto.codcategoria = ' . $_GET["codcategoria"];
    $categoriap = $conexao->comandoArray('select nome from categoriaproduto where codcategoria = ' . $_GET["codcategoria"]);
    $tipop["nome"] = $categoriap["nome"];
}
$sql = 'select produto.* 
from produto 
inner join categoriaproduto as cp on cp.codcategoria = produto.codcategoria
inner join tipoproduto as tp on tp.codtipo = produto.codtipo
where 1 = 1 ' . $and . ' order by produto.nome limit 12';
$resproduto = $conexao->comando($sql)or die("<pre>$sql</pre>");
$qtdproduto = $conexao->qtdResultado($resproduto);
?>

<div class="container">
    <h2>Todos - <?= $tipop["nome"] ?></h2>
    <div class="produtos-destaques" align="center">
  
        <br><br>
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
            $totalProdutos = $conexao->comandoArray("select count(1) as qtd 
            from produto 
            inner join categoriaproduto as cp on cp.codcategoria = produto.codcategoria
            inner join tipoproduto as tp on tp.codtipo = produto.codtipo            
            where 1 = 1 {$and}");

            if ($qtdproduto > 0 && $qtdproduto > $totalProdutos["qtd"]) {
                $totalPaginas = $totalProdutos["qtd"] / $qtdproduto;
                echo '<input type="hidden" name="totalPaginaProdutos" id="totalPaginaProdutos" value="', $totalPaginas, '"/>';
                echo "<ul class='pagination pagination-sm no-margin pull-right' id='paginacaoDepoimentos'>";
                echo "<li><a href='javascript: procurarProdutoPaginacao(0, {$_GET["codtipo"]})'><i class='fa fa-fast-backward' aria-hidden='true'></i></a></li>";
                for ($i = 1; $i <= $totalPaginas; $i++) {
                    echo "<li><a href='javascript: procurarProdutoPaginacao($i, {$_GET["codtipo"]})'>$i</a></li>";
                }
                if ($totalPaginas > 10) {
                    echo "<li><a href='javascript: procurarProdutoPaginacao($totalPaginas, {$_GET["codtipo"]})'><i class='fa fa-fast-forward' aria-hidden='true'></i></a></li>";
                }
                echo '</ul>';
            }
            ?>            
        </div>         
        <br>
        <br>

    </div>
</div>
</div>

<?php require ("inc/footer.inc.php"); ?>