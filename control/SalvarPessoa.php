<?php

/* 
 * @author Thyago Henrique Pacher - thyago.pacher@gmail.com
 */

session_start();
if(!isset($_SESSION)){
    die(json_encode(array('mensagem' => 'Sua sessão caiu, por favor logue novamente!!!', 'situacao' => false)));
}  
$envioEmail = '';
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
$pessoa  = new Pessoa($conexao);

$variables = (strtolower($_SERVER['REQUEST_METHOD']) == 'GET') ? $_GET : $_POST;
foreach ($variables as $key => $value) {
    $pessoa->$key = $value;
}

if(!isset($pessoa->nome) || $pessoa->nome == NULL || $pessoa->nome == ""){
    die(json_encode(array('mensagem' => 'Por favor preencha nome!!!', 'situacao' => false)));
}

if (isset($_FILES['foto'])) {
    $upload = new Upload($_FILES['foto']);
    if ($upload->erro == '') {
        $pessoa->foto = $upload->nome_final;
    }
}

$msg_retorno = '';
$sit_retorno = true;

if(isset($pessoa->codpessoa) && $pessoa->codpessoa != NULL && $pessoa->codpessoa != ""){
    $res = $pessoa->atualizar();
}else{
    $sql = 'select codpessoa from pessoa where email = "'. $pessoa->email.'" and nome = "'.$pessoa->nome.'"';
    $pessoap = $conexao->comandoArray($sql);
    if(isset($pessoap["codpessoa"]) && $pessoap["codpessoa"] != NULL && $pessoap["codpessoa"] != ""){
        die(json_encode(array('mensagem' => 'Pessoa ja inserida!!!', 'situacao' => false)));
    }
    $res     = $pessoa->inserir();
}

if ($res === FALSE) {
    $msg_retorno = 'Erro ao salvar pessoa! Causado por:' . mysqli_error($conexao->conexao);
    $sit_retorno = false;
} else {
    $msg_retorno = "Pessoa salva com sucesso! {$envioEmail}";
}

if (isset($upload->erro) && $upload->erro != NULL && $upload->erro != '') {
    $msg_retorno .= ' Problemas com o envio do arquivo: ' . $upload->erro;
}
echo json_encode(array('mensagem' => $msg_retorno, 'situacao' => $sit_retorno));
