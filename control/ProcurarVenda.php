<?php
session_start();
//validação caso a sessão caia
if (!isset($_SESSION)) {
    die("<script>alert('Sua sessão caiu, por favor logue novamente!!!');window.close();</script>");
}
include "../model/Conexao.php";
$conexao = new Conexao();
$and = "";

if (isset($_POST["codcliente"]) && $_POST["codcliente"] != NULL && $_POST["codcliente"] != "") {
    $and .= " and venda.codcliente = '{$_POST["codcliente"]}'";
}
if (isset($_POST["codvendedor"]) && $_POST["codvendedor"] != NULL && $_POST["codvendedor"] != "") {
    $and .= " and venda.codvendedor = '{$_POST["codvendedor"]}'";
}
if (isset($_POST["codproduto"]) && $_POST["codproduto"] != NULL && $_POST["codproduto"] != "") {
    $and .= " and venda.codproduto = '{$_POST["codproduto"]}'";
}
if (isset($_POST["data1"]) && $_POST["data1"] != NULL && $_POST["data1"] != "") {
    if (strpos($_POST["data1"], "/")) {
        $data1 = implode("-", array_reverse(explode("/", $_POST["data1"])));
    } else {
        $data1 = $_POST["data1"];
    }
    $and .= " and venda.dtcadastro >= '{$data1}'";
}
if (isset($_POST["data2"]) && $_POST["data2"] != NULL && $_POST["data2"] != "") {
    if (strpos($_POST["data2"], "/")) {
        $data1 = implode("-", array_reverse(explode("/", $_POST["data2"])));
    } else {
        $data1 = $_POST["data2"];
    }
    $and .= " and venda.dtcadastro <= '{$_POST["data2"]}'";
}

$sql = 'select venda.codvenda, produto.nome as produto, venda.quantidade,
    DATE_FORMAT(venda.dtcadastro, "%d/%m/%Y") as dtcadastro2, cliente.nome as cliente, venda.valor
    from venda 
    inner join produto on produto.codproduto = venda.codproduto
    inner join pessoa as cliente on cliente.codpessoa = venda.codcliente
    where 1 = 1 ' . $and. ' order by venda.dtcadastro desc';
$res = $conexao->comando($sql)or die('Erro no comando: <pre>'.$sql.'</pre>');
$qtd = $conexao->qtdResultado($res);
if ($qtd > 0) {
    ?>
    <div class="box-body">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table id="example2" class="table table-bordered table-striped dataTable"
                           role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr role="row">
                                <th class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">
                                    Data Cad.
                                </th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-label="Browser: activate to sort column ascending">
                                    Cliente
                                </th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-label="Platform(s): activate to sort column ascending">
                                    Produto
                                </th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-label="Platform(s): activate to sort column ascending">
                                    Valor
                                </th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-label="Engine version: activate to sort column ascending">
                                    Opções
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($venda = $conexao->resultadoArray($res)) { ?>
                                <tr role="row" class="<?= $classe_linha ?>">
                                    <td class="sorting_1">
                                        <?= $venda["dtcadastro2"] ?>
                                    </td>
                                    <td>
                                        <?= $venda["cliente"] ?>
                                    </td>
                                    <td>
                                        <?= $venda["produto"]?>                                      
                                    </td>                                    
                                    <td>
                                        <?= number_format($venda["valor"], 2, ',', '.') ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo '<a href="Venda.php?codvenda=', $venda["codvenda"],'" title="Clique aqui para editar"><img style="width: 20px;" src="./recursos/img/editar.png" alt="botão editar"/></a>';
                                        echo '<a href="#" onclick="excluir2Venda(', $venda["codvenda"], ')" title="Clique aqui para excluir"><img style="width: 20px;" src="./recursos/img/excluir.png" alt="botão excluir"/></a>';
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>
          
        </div>
    </div>
    <?php
    $classe_linha = "even";
}
?>