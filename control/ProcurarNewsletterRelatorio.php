<?php
session_start();
//validação caso a sessão caia
if (!isset($_SESSION)) {
    die("<script>alert('Sua sessão caiu, por favor logue novamente!!!');window.close();</script>");
}
include "../model/Conexao.php";
$conexao = new Conexao();
$and = "";

if (isset($_POST["nome"]) && $_POST["nome"] != NULL && $_POST["nome"] != "") {
    $and .= " and newsletter.nome = '{$_POST["nome"]}'";
}
if (isset($_POST["email"]) && $_POST["email"] != NULL && $_POST["email"] != "") {
    $and .= " and newsletter.email = '{$_POST["email"]}'";
}
if (isset($_POST["sexo"]) && $_POST["sexo"] != NULL && $_POST["sexo"] != "") {
    $and .= " and newsletter.sexo = '{$_POST["sexo"]}'";
}
if (isset($_POST["data1"]) && $_POST["data1"] != NULL && $_POST["data1"] != "") {
    if (strpos($_POST["data1"], "/")) {
        $data1 = implode("-", array_reverse(explode("/", $_POST["data1"])));
    } else {
        $data1 = $_POST["data1"];
    }
    $and .= " and newsletter.dtcadastro >= '{$data1} 00:00:00'";
}
if (isset($_POST["data2"]) && $_POST["data2"] != NULL && $_POST["data2"] != "") {
    if (strpos($_POST["data2"], "/")) {
        $data1 = implode("-", array_reverse(explode("/", $_POST["data2"])));
    } else {
        $data1 = $_POST["data2"];
    }
    $and .= " and newsletter.dtcadastro <= '{$_POST["data2"]} 23:59:59'";
}

$sql = 'select newsletter.codnewsletter, nome, email, sexo,
    DATE_FORMAT(newsletter.dtcadastro, "%d/%m/%Y") as dtcadastro2
    from newsletter 
    where 1 = 1 ' . $and . ' order by newsletter.dtcadastro desc';

$res = $conexao->comando($sql)or die('Erro no comando: <pre>' . $sql . '</pre>');
$qtd = $conexao->qtdResultado($res);
if ($qtd > 0) {
    
    $nome = 'Rel. Newsletter';
    $html .= '<table class="responstable">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Dt. Cadastro</th>';
    $html .= '<th>Nome</th>';
    $html .= '<th>E-mail</th>';
    $html .= '<th>Sexo</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    while ($newsletter = $conexao->resultadoArray($res)) {
        $html .= '<tr>';
        $html .= '<td style="text-align: left;">' . $newsletter["dtcadastro2"] . '</td>';
        $html .= '<td style="text-align: left;">' . $newsletter["nome"] . '</td>';
        $html .= '<td style="text-align: left;">' . $newsletter["email"] . '</td>';
        $html .= '<td style="text-align: left;">' . $newsletter["sexo"] . '</td>';
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
    echo '<script>alert("Sem newsletter encontrado!");window.close();</script>';
}

