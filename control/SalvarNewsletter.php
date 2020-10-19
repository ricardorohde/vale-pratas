<?php

/* 
 * @author Thyago Henrique Pacher - thyago.pacher@gmail.com
 */

function __autoload($class_name) {
    if (file_exists("../model/" . $class_name . '.php')) {
        include "../model/" . $class_name . '.php';
    } elseif (file_exists("../visao/" . $class_name . '.php')) {
        include "../visao/" . $class_name . '.php';
    } elseif (file_exists("./" . $class_name . '.php')) {
        include "./" . $class_name . '.php';
    }
}

$conexao = new Conexao();
$newsletter  = new Newsletter($conexao);

$variables = (strtolower($_SERVER['REQUEST_METHOD']) == 'GET') ? $_GET : $_POST;
foreach ($variables as $key => $value) {
    $newsletter->$key = $value;
}

$msg_retorno = '';
$sit_retorno = true;

if(isset($newsletter->codnewsletter) && $newsletter->codnewsletter != NULL && $newsletter->codnewsletter != ""){
    $res = $newsletter->atualizar();
}else{
    $res = $newsletter->inserir();
}

if ($res === FALSE) {
    $msg_retorno = 'Erro ao salvar newsletter! Causado por:' . mysqli_error($conexao->conexao);
    $sit_retorno = false;
} else {
    $msg_retorno = "Newsletter salva com sucesso! {$envioEmail}";
}

echo json_encode(array('mensagem' => $msg_retorno, 'situacao' => $sit_retorno));
