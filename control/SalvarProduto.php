<?php

/* 
 * @author Thyago Henrique Pacher - thyago.pacher@gmail.com
 */

session_start();
if(!isset($_SESSION)){
    die(json_encode(array('mensagem' => 'Sua sessão caiu, por favor logue novamente!!!', 'situacao' => false)));
}  

function __autoload($class_name) {
    if (file_exists("../model/" . $class_name . '.php')) {
        include "../model/" . $class_name . '.php';
    } elseif (file_exists("../visao/" . $class_name . '.php')) {
        include "../visao/" . $class_name . '.php';
    } elseif (file_exists("./" . $class_name . '.php')) {
        include "./" . $class_name . '.php';
    }
}

date_default_timezone_set('America/Sao_Paulo');
$conexao = new Conexao();
$produto  = new Produto($conexao);

$variables = (strtolower($_SERVER['REQUEST_METHOD']) == 'GET') ? $_GET : $_POST;
foreach ($variables as $key => $value) {
    $produto->$key = $value;
}

if (isset($_FILES['foto'])) {
    $upload = new Upload($_FILES['foto']);
    if ($upload->erro == '') {
        $produto->foto = $upload->nome_final;
    }
}
if (isset($_FILES['imagem2'])) {
    $upload = new Upload($_FILES['imagem2']);
    if ($upload->erro == '') {
        $produto->imagem2 = $upload->nome_final;
    }
}
if (isset($_FILES['imagem3'])) {
    $upload = new Upload($_FILES['imagem3']);
    if ($upload->erro == '') {
        $produto->imagem3 = $upload->nome_final;
    }
}
if (isset($_FILES['imagem4'])) {
    $upload = new Upload($_FILES['imagem4']);
    if ($upload->erro == '') {
        $produto->imagem4 = $upload->nome_final;
    }
}

$msg_retorno = '';
$sit_retorno = true;

if(isset($produto->codproduto) && $produto->codproduto != NULL && $produto->codproduto != ""){
    $res = $produto->atualizar();
}else{
    $res = $produto->inserir();
}

if ($res == FALSE) {
    $msg_retorno = 'Erro ao salvar produto! Causado por:' . mysqli_error($conexao->conexao);
    $sit_retorno = false;
} else {
    $msg_retorno = "Produto salvo com sucesso! {$envioEmail}";
}

if (isset($upload->erro) && $upload->erro != NULL && $upload->erro != '') {
    $msg_retorno .= ' Problemas com o envio do arquivo: ' . $upload->erro;
}
echo json_encode(array('mensagem' => $msg_retorno, 'situacao' => $sit_retorno));
