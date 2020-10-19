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
$empresa  = new Empresa($conexao);

$variables = (strtolower($_SERVER['REQUEST_METHOD']) == 'GET') ? $_GET : $_POST;
foreach ($variables as $key => $value) {
    $empresa->$key = $value;
}
if (isset($_FILES['imagem'])) {
    $upload = new Upload($_FILES['imagem']);
    if ($upload->erro == '') {
        $empresa->logo = $upload->nome_final;
    }
}
if (isset($_FILES['cartoes'])) {
    $upload = new Upload($_FILES['cartoes']);
    if ($upload->erro == '') {
        $empresa->cartoes = $upload->nome_final;
    }
}
if (isset($_FILES['imgseguranca'])) {
    $upload = new Upload($_FILES['imgseguranca']);
    if ($upload->erro == '') {
        $empresa->imgseguranca = $upload->nome_final;
    }
}

$msg_retorno = '';
$sit_retorno = true;

if(isset($empresa->codempresa) && $empresa->codempresa != NULL && $empresa->codempresa != ""){
    $res = $empresa->atualizar();
}else{
    $res = $empresa->inserir();
}

if ($res === FALSE) {
    $msg_retorno = 'Erro ao salvar empresa! Causado por:' . mysqli_error($conexao->conexao);
    $sit_retorno = false;
} else {
    $msg_retorno = "Empresa salva com sucesso! {$envioEmail}";
}

if (isset($upload->erro) && $upload->erro != NULL && $upload->erro != '') {
    $msg_retorno .= ' Problemas com o envio do arquivo: ' . $upload->erro;
}
echo json_encode(array('mensagem' => $msg_retorno, 'situacao' => $sit_retorno));
