<?php
session_start();
//validação caso a sessão caia
if (!isset($_SESSION)) {
    die("<script>alert('Sua sessão caiu, por favor logue novamente!!!');window.close();</script>");
}
include "../model/Conexao.php";
$conexao = new Conexao();
$and = "";
$innerJoin = "";
$campos = "";


if (isset($_POST["nome"]) && $_POST["nome"] != NULL && $_POST["nome"] != "") {
    $and .= " and categoria.nome like '%{$_POST["nome"]}%'";
}
if (isset($_POST["data1"]) && $_POST["data1"] != NULL && $_POST["data1"] != "") {
    if (strpos($_POST["data1"], "/")) {
        $data1 = implode("-", array_reverse(explode("/", $_POST["data1"])));
    } else {
        $data1 = $_POST["data1"];
    }
    $and .= " and categoria.dtcadastro >= '{$data1}'";
}
if (isset($_POST["data2"]) && $_POST["data2"] != NULL && $_POST["data2"] != "") {
    if (strpos($_POST["data2"], "/")) {
        $data1 = implode("-", array_reverse(explode("/", $_POST["data2"])));
    } else {
        $data1 = $_POST["data2"];
    }
    $and .= " and categoria.dtcadastro <= '{$_POST["data2"]}'";
}


$sql = 'select categoria.codcategoria, categoria.nome, DATE_FORMAT(categoria.dtcadastro, "%d/%m/%Y") as dtcadastro2,
    categoria.desconto, categoria.imagem
    from categoriaproduto as categoria 
    where 1 = 1 ' . $and. ' order by nome';
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
                                    Nome
                                </th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-label="Browser: activate to sort column ascending">
                                    Imagem
                                </th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-label="Engine version: activate to sort column ascending">
                                    Opções
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while ($categoria = $conexao->resultadoArray($res)) { 
                                ?>
                                <tr role="row" class="<?= $classe_linha ?>">
                                    <td class="sorting_1">
                                        <?= $categoria["dtcadastro2"] ?>
                                    </td>
                                    <td>
                                        <?= $categoria["nome"] ?>
                                    </td>
                                    <td>
                                        <a target="_blank" href="../arquivos/<?= $categoria["imagem"] ?>" title="clique aqui para ver a imagem da categoria">Abrir Imagem</a>
                                    </td>
                                    <td>
                                        <?php
                                        $vlDesconto = number_format($categoria["desconto"], 2, ',', '');
                                        $arrayJavascript = "new Array('{$categoria["codcategoria"]}', '{$categoria["nome"]}', '{$vlDesconto}', '{$categoria["imagem"]}')";
                                        echo '<a href="javascript:setaEditarCategoriaProduto(',$arrayJavascript,')" title="Clique aqui para editar"><img style="width: 20px;" src="./recursos/img/editar.png" alt="botão editar"/></a>';
                                        echo '<a href="#" onclick="excluir2CategoriaProduto(', $categoria["codcategoria"], ')" title="Clique aqui para excluir"><img style="width: 20px;" src="./recursos/img/excluir.png" alt="botão excluir"/></a>';
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