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
$orderby = '';

if (isset($_POST["email"]) && $_POST["email"] != NULL && $_POST["email"] != "") {
    $and .= " and pessoa.email like '%{$_POST["email"]}%'";
}
if (isset($_POST["data1"]) && $_POST["data1"] != NULL && $_POST["data1"] != "") {
    if (strpos($_POST["data1"], "/")) {
        $data1 = implode("-", array_reverse(explode("/", $_POST["data1"])));
    } else {
        $data1 = $_POST["data1"];
    }
    $and .= " and pessoa.dtcadastro >= '{$data1}'";
}
if (isset($_POST["data2"]) && $_POST["data2"] != NULL && $_POST["data2"] != "") {
    if (strpos($_POST["data2"], "/")) {
        $data1 = implode("-", array_reverse(explode("/", $_POST["data2"])));
    } else {
        $data1 = $_POST["data2"];
    }
    $and .= " and pessoa.dtcadastro <= '{$_POST["data2"]}'";
}
if (isset($_POST["cpf"]) && $_POST["cpf"] != NULL && $_POST["cpf"] != "") {
    $cpf_limpo = str_replace(".", "", str_replace("-", "", $_POST["cpf"]));
    $and .= " and (pessoa.cpf = '{$_POST["cpf"]}' or pessoa.cpf = '{$cpf_limpo}')";
}
if (isset($_POST["nome"]) && $_POST["nome"] != NULL && $_POST["nome"] != "") {
    $and .= " and pessoa.nome like '%{$_POST["nome"]}%'";
}

if (isset($_POST["codstatus"]) && $_POST["codstatus"] != NULL && $_POST["codstatus"] != "") {
    $and .= " and pessoa.codstatus = '{$_POST["codstatus"]}'";
}

if (isset($_POST["codcategoria"]) && $_POST["codcategoria"] != NULL && $_POST["codcategoria"] != "" && $_POST["codcategoria"] != "1" && $_POST["codcategoria"] != "6") {
    $and .= " and pessoa.codcategoria = '{$_POST["codcategoria"]}'";
}

if (isset($_POST["sexo"]) && $_POST["sexo"] != NULL && $_POST["sexo"] != "") {
    $and .= " and pessoa.sexo = '{$_POST["sexo"]}'";
}
if (isset($_POST["rg"]) && $_POST["rg"] != NULL && $_POST["rg"] != "") {
    $and .= " and pessoa.rg = '{$_POST["rg"]}'";
}

if (isset($_POST["estado"]) && $_POST["estado"] != NULL && $_POST["estado"] != "") {
    $and .= " and pessoa.estado = '{$_POST["estado"]}'";
}
if (isset($_POST["email"]) && $_POST["email"] != NULL && $_POST["email"] != "") {
    $and .= " and pessoa.email = '{$_POST["email"]}'";
}

//se com telefone
if (isset($_POST["ctelefone"]) && $_POST["ctelefone"] != NULL && $_POST["ctelefone"] != "" && $_POST["ctelefone"] == "s") {
    $innerJoin .= " inner join telefone on telefone.codpessoa = pessoa.codpessoa";
    $campos .= ", telefone.numero as telefone";
} elseif (isset($_POST["ctelefone"]) && $_POST["ctelefone"] != NULL && $_POST["ctelefone"] != "" && $_POST["ctelefone"] == "n") {
    $and .= " and pessoa.codpessoa not in(select codpessoa from telefone)";
}

//se com endereço
if (isset($_POST["cendereco"]) && $_POST["cendereco"] != NULL && $_POST["cendereco"] != "" && $_POST["cendereco"] == "s") {
    $campos .= ", pessoa.tipologradouro, pessoa.logradouro, pessoa.numero, pessoa.bairro, pessoa.cidade, pessoa.estado";
    $and .= " and pessoa.cidade <> '' and pessoa.estado <> ''";
} elseif (isset($_POST["cendereco"]) && $_POST["cendereco"] != NULL && $_POST["cendereco"] != "" && $_POST["cendereco"] == "n") {
    $and .= " and pessoa.cidade = '' and pessoa.estado = ''";
}

//se com beneficio
if (isset($_POST["cbeneficio"]) && $_POST["cbeneficio"] != NULL && $_POST["cbeneficio"] != "" && $_POST["cbeneficio"] == "s") {
    $innerJoin .= " inner join beneficiocliente as beneficio on beneficio.codpessoa = pessoa.codpessoa and beneficio.codempresa = pessoa.codempresa";
    $innerJoin .= " inner join especie on especie.codespecie = beneficio.codespecie";
    $campos .= ", beneficio.numbeneficio, beneficio.salariobase, beneficio.margem, especie.nome as especie";
} elseif (isset($_POST["cbeneficio"]) && $_POST["cbeneficio"] != NULL && $_POST["cbeneficio"] != "" && $_POST["cbeneficio"] == "n") {
    $and .= " and pessoa.codpessoa not in(select codpessoa from beneficiocliente)";
}

//se com empréstimo
if (isset($_POST["cemprestimo"]) && $_POST["cemprestimo"] != NULL && $_POST["cemprestimo"] != "" && $_POST["cemprestimo"] == "s") {
    $innerJoin .= " inner join emprestimo on emprestimo.codpessoa = pessoa.codpessoa";
    $campos .= ", emprestimo.prazo, emprestimo.quitacao, emprestimo.vlparcela, emprestimo.meio, emprestimo.situacao, emprestimo.parcelas_aberto";
} elseif (isset($_POST["cemprestimo"]) && $_POST["cemprestimo"] != NULL && $_POST["cemprestimo"] != "" && $_POST["cemprestimo"] == "n") {
    $and .= " and pessoa.codpessoa not in(select codpessoa from emprestimo)";
}

if (isset($_POST["ordem"])) {
    if ($_POST["ordem"] == "1") {
        $orderby = " order by pessoa.nome";
    } elseif ($_POST["ordem"] == "2") {
        $orderby = " order by pessoa.dtcadastro";
    }
}

$sql = 'select pessoa.codpessoa, pessoa.nome, pessoa.codcategoria, pessoa.cpf, 
    pessoa.email, DATE_FORMAT(pessoa.dtcadastro, "%d/%m/%Y") as data, pessoa.senha, categoria.nome as categoria, pessoa.status,
    nivel.nome as nivel ' . $campos . '
    from pessoa 
    left join categoriapessoa as categoria on categoria.codcategoria = pessoa.codcategoria 
    left join nivel on nivel.codnivel = pessoa.codnivel ' . $innerJoin . '
    where 1 = 1 ' . $and. $orderby;

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
                                    colspan="1" aria-label="Platform(s): activate to sort column ascending">
                                    CPF
                                </th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-label="Platform(s): activate to sort column ascending">
                                    Status
                                </th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-label="Engine version: activate to sort column ascending">
                                    Opções
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($pessoa = $conexao->resultadoArray($res)) { ?>
                                <tr role="row" class="<?= $classe_linha ?>">
                                    <td class="sorting_1">
                                        <?= $pessoa["data"] ?>
                                    </td>
                                    <td>
                                        <?= $pessoa["nome"] ?>
                                    </td>
                                    <td>
                                        <?= $pessoa["cpf"] ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($pessoa["status"] == "i") {
                                            echo "Inativo";
                                        } else {
                                            echo "Ativo";
                                        }
                                        ?>                                      
                                    </td>
                                    <td>
                                        <?php
                                        if ($pessoa["codcategoria"] == 1 || $pessoa["codcategoria"] == 6) {
                                            $caminhoTelaPessoa = "Cliente";
                                        } else {
                                            $caminhoTelaPessoa = "Pessoa";
                                        }
                                        echo '<a href="', $caminhoTelaPessoa, '.php?codpessoa=', $pessoa["codpessoa"], $complementoCaminho, '" title="Clique aqui para editar"><img style="width: 20px;" src="./recursos/img/editar.png" alt="botão editar"/></a>';
                                        echo '<a href="#" onclick="excluir2(', $pessoa["codpessoa"], ')" title="Clique aqui para excluir"><img style="width: 20px;" src="./recursos/img/excluir.png" alt="botão excluir"/></a>';
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