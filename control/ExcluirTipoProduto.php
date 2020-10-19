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
$tipo    = new TipoProduto($conexao);

$variables = (strtolower($_SERVER['REQUEST_METHOD']) == 'GET') ? $_GET : $_POST;
foreach ($variables as $key => $value) {
    $tipo->$key = $value;
}

$msg_retorno = '';
$sit_retorno = true;

$qtdTipo = $conexao->comandoArray('select count(1) as qtd from produto where codtipo = '. $tipo->codtipo);
if(isset($qtdTipo["qtd"]) && $qtdTipo["qtd"] != NULL && $qtdTipo["qtd"] > 0){
    die(json_encode(array('mensagem' => "Não pode excluir tipo pois tem {$qtdTipo["qtd"]} usando ele!!!", 'situacao' => false)));
}
$res = $tipo->excluir();

if ($res === FALSE) {
    $msg_retorno = 'Erro ao excluir tipo produto! Causado por:' . mysqli_error($conexao->conexao);
    $sit_retorno = false;
} else {
    $msg_retorno = "Tipo Produto excluido com sucesso!";
}

echo json_encode(array('mensagem' => $msg_retorno, 'situacao' => $sit_retorno));
