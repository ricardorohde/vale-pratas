<?php

session_start();
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
    where 1 = 1 ' . $and . $orderby;

$res = $conexao->comando($sql)or die('Erro no comando: <pre>' . $sql . '</pre>');
$qtd = $conexao->qtdResultado($res);
if ($qtd > 0) {
    $nome = 'Rel. Pessoa';
    $html  = '<table class="responstable">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Dt. Cadastro</th>';
    $html .= '<th>Nome</th>';
    $html .= '<th>CPF</th>';
    $html .= '<th>Status</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    while ($pessoa = $conexao->resultadoArray($res)) {
        $html .= '<tr>';
        $html .= '<td style="text-align: left;">' . $pessoa["data"] . '</td>';
        $html .= '<td style="text-align: left;">' . $pessoa["nome"] . '</td>';
        $html .= '<td style="text-align: left;">' . $pessoa["cpf"] . '</td>';
        $html .= '<td style="text-align: left;">' . $conexao->trocaStatus($pessoa["status"]) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';

    $_POST["html"] = $html;
    $paisagem = "sim";
    if (isset($_POST["tipo"]) && $_POST["tipo"] == "xls") {
        include "./GeraExcel.php";
    } else {
        include "./GeraPdf.php";
    }
} else {
    echo '<script>alert("Sem pessoa encontrada!");window.close();</script>';
}

