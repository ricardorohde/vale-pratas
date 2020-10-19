<?php require ("inc/header.inc.php"); ?>


<div class="container">
    <h2>Todas as Categorias</h2>
    <div class="row">
        <?php
        $rescategoria = $conexao->comando('select * from categoriaproduto order by nome');
        $qtdcategoria = $conexao->qtdResultado($rescategoria);
        if ($qtdcategoria > 0) {
            while ($categoria = $conexao->resultadoArray($rescategoria)) {
                if ($linhaCategoria >= 4) {
                    echo '</div><br><br>';
                    echo '<div class="row">';
                    $linhaCategoria = 0;
                }
                if (!isset($categoria["imagem"]) || $categoria["imagem"] == NULL || $categoria["imagem"] == "") {
                    $categoria["imagem"] = 'sem_imagem.png';
                }
                ?> 
                <div class="col-md-4">
                    <a href="./<?= strtolower($categoria["nome"]) ?>">
                        <div class="row categorias">
                            <div class="col-md-4" align="center">
                                <img style="width: 110px;height: 110px;" src="../arquivos/<?= $categoria["imagem"] ?>" alt="imagem da categoria">
                            </div> 
                            <div class="col-md-8">
                                <h3><?= $categoria["nome"] ?></h3>
                                <p>Veja todos</p>
                                <p class="obs-produto"><?= $categoria["desconto"] ?>% de desconto no boleto</p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
                $linhaCategoria++;
            }
        }
        ?>

    </div>
    <div class="row">

    </div>
    <div class="row">

    </div>
    <div class="row">

    </div>
</div>


<?php require ("inc/footer.inc.php"); ?>