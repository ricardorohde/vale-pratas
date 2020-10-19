<?php
session_start();
//validação caso a sessão caia
if (!isset($_SESSION)) {
    die("<script>alert('Sua sessão caiu, por favor logue novamente!!!');window.close();</script>");
}
include "../model/Conexao.php";
$conexao = new Conexao();
$and     = '';

if (isset($_POST["codbanner"]) && $_POST["codbanner"] != NULL && $_POST["codbanner"] != "") {
    $and .= " and banner.codbanner = '{$_POST["codbanner"]}'";
}

$sql = "select DATE_FORMAT(banner.dtcadastro, '%d/%m/%Y') as dtcadastro2, banner.arquivo, banner.titulo, banner.codbanner 
    from banner 
    order by posicao";
$res = $conexao->comando($sql)or die("<pre>$sql</pre>");
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
                                    Titulo
                                </th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-label="Browser: activate to sort column ascending">
                                    Arquivo
                                </th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-label="Engine version: activate to sort column ascending">
                                    Opções
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($banner = $conexao->resultadoArray($res)) { ?>
                                <tr role="row" class="<?= $classe_linha ?>">
                                    <td class="sorting_1">
                                        <?= $banner["dtcadastro2"] ?>
                                    </td>
                                    <td>
                                        <?= $banner["titulo"] ?>
                                    </td>
                                    <td>
                                        <?= $banner["arquivo"] ?>
                                    </td>
                                   
                                    <td>
                                        <?php
                                        echo '<a href="Banner.php?codbanner=', $banner["codbanner"], '" title="Clique aqui para editar"><img style="width: 20px;" src="./recursos/img/editar.png" alt="botão editar"/></a>';
                                        echo '<a href="javascript: excluir2Banner(', $banner["codbanner"], ')" title="Clique aqui para excluir"><img style="width: 20px;" src="./recursos/img/excluir.png" alt="botão excluir"/></a>';
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