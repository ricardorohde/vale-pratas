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

$conexao = new Conexao();
$venda    = new Venda($conexao);

$variables = (strtolower($_SERVER['REQUEST_METHOD']) == 'GET') ? $_GET : $_POST;
foreach ($variables as $key => $value) {
    $venda->$key = $value;
}

$msg_retorno = '';
$sit_retorno = true;

if(!isset($_POST["codproduto"]) || $_POST["codproduto"] == NULL || $_POST["codproduto"] == ""){
    die(json_encode(array('mensagem' => 'Produto não escolhido!', 'situacao' => false)));
}else{
    $venda->codproduto = base64_decode($venda->codproduto);
}

$venda->codcliente = $_SESSION["codpessoa"];

if(isset($venda->codvendedor) && $venda->codvendedor != NULL && $venda->codvendedor != ""){
    $venda->codvendedor = base64_decode($venda->codvendedor);
}

$empresap = $conexao->comandoArray('select razao, email1 from empresa where codempresa = 1');
$produtop = $conexao->comandoArray('select nome, valor from produto where codproduto = '. $venda->codproduto);

if(isset($venda->codvenda) && $venda->codvenda != NULL && $venda->codvenda != ""){
    $assuntoEmail = "Venda atualizada: ". date("d/m/Y H:i:s"). ' - '. $empresap["razao"];
    $res = $venda->atualizar();
}else{
    if(!isset($venda->codvendedor) || $venda->codvendedor == NULL || $venda->codvendedor == ""){
        $vendedorAdm = $conexao->comandoArray('select codpessoa from pessoa where codnivel in(select nome from nivel where nome = "Administrador")');
        $venda->codvendedor = $vendedorAdm["codpessoa"];
    }
    $assuntoEmail = "Venda inserida: ". date("d/m/Y H:i:s"). ' - '. $empresap["razao"];
    $res = $venda->inserir();
}

if ($res == FALSE) {
    $msg_retorno = 'Erro ao salvar venda! Causado por:' . mysqli_error($conexao->conexao);
    $sit_retorno = false;
} else {
    $msg_retorno = "Venda salva com sucesso!";
    $clientep = $conexao->comandoArray("select nome, email from pessoa where codpessoa = {$_SESSION["codpessoa"]}");

    include 'Email.php';
    $email = new Email();
    $email->assunto         = $assuntoEmail;
    $email->mensagem        = "Comprado itens:<br>";
    $email->mensagem       .= "Quantidade: {$venda->quantidade}<br>";
    $email->mensagem       .= "Produto: {$produtop["nome"]}<br>";
    $email->mensagem       .= "Preço Unitário: ".  number_format($produtop["valor"], 2, ',', '')."<br>";
    $email->mensagem       .= 'Total Gasto: '. number_format(($produtop["valor"]  * $venda->quantidade), 2, ',', '')."<br>";
    $email->para            = $clientep["nome"];
    $email->para_email      = $clientep["email"];
    $email->copia           = $empresap["razao"];
    $email->copia_email     = $empresap["email"];
    
    $resInserirFila       = $email->envia();
    if($email->erro != ""){
        die(json_encode(array('mensagem' => 'Problemas ao enviar e-mail causado por:'. $email->erro, 'situacao' => false)));
    }
}

echo json_encode(array('mensagem' => $msg_retorno, 'situacao' => $sit_retorno));
