<?php
/*
 * @author Thyago Henrique Pacher - thyago.pacher@gmail.com
 */

function __autoload($class_name) {
    if (file_exists("../model/" . $class_name . '.php')) {
        include "../model/" . $class_name . '.php';
    } elseif (file_exists("../visao/" . $class_name . '.php')) {
        include "../visao/" . $class_name . '.php';
    } elseif (file_exists("./" . $class_name . '.php')) {
        include "./" . $class_name . '.php';
    }
}

$conexao    = new Conexao();

$qtdPagina  = 12;
if(!isset($_POST["pagina"])){
    $_POST["pagina"] = 1;
}
$inicio     = $qtdPagina * ($_POST["pagina"] - 1);
$fim        = $inicio + $qtdPagina;

if(isset($_POST["codtipo"]) && $_POST["codtipo"] != NULL && $_POST["codtipo"] != ""){
    $and .= " and codtipo = {$_POST["codtipo"]}";
}

$sql = "select codproduto, nome, foto, valor, desconto from produto where 1 = 1 {$and} order by nome limit $inicio, $fim";
$resproduto = $conexao->comando($sql);
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
