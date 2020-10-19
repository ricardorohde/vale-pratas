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
    DATE_FORMAT(venda.dtcadastro, "%d/%m/%Y") as dtcadastro2, cliente.nome as cliente
    from venda 
    inner join produto on produto.codproduto = venda.codproduto
    inner join pessoa as cliente on cliente.codcliente = venda.codcliente
    where 1 = 1 ' . $and . ' order by venda.dtcadastro desc';
$res = $conexao->comando($sql)or die('Erro no comando: <pre>' . $sql . '</pre>');
$qtd = $conexao->qtdResultado($res);
if ($qtd > 0) {
    $nome = 'Rel. Venda';
    $html .= '<table class="responstable">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Dt. Cadastro</th>';
    $html .= '<th>Cliente</th>';
    $html .= '<th>Produto</th>';
    $html .= '<th>Valor</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    while ($venda = $conexao->resultadoArray($resvenda)) {
        $html .= '<tr>';
        $html .= '<td style="text-align: left;">' . $venda["dtcadastro2"] . '</td>';
        $html .= '<td style="text-align: left;">' . $venda["cliente"] . '</td>';
        $html .= '<td style="text-align: left;">' . $venda["produto"] . '</td>';
        $html .= '<td style="text-align: left;">' . number_format($venda["valor"], 2, ',', '.') . '</td>';
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
    echo '<script>alert("Sem venda encontrado!");window.close();</script>';
}

