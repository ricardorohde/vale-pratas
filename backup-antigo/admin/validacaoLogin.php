<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
if(!isset($_SESSION["codpessoa"])){
    echo '<script>alert("Sua sessão caiu por favor faça login novamente!!!"); location.href="./index.php"</script>';
}
include "../model/Conexao.php";
include "../model/Visitante.php";
$conexao = new Conexao();

$sql      = 'select razao from empresa where codempresa = 1';
$empresap = $conexao->comandoArray($sql);

$pagina          = $_SERVER["REQUEST_URI"];
$separado_pagina = explode('/', $pagina);
$pagina          = $separado_pagina[count($separado_pagina) - 1];
$pagina          = explode('?', $pagina);//limpa código página
$pagina          = $pagina[0];
$sql = "select nivelpagina.*, pagina.nome as pagina, modulo.nome as modulo, pagina.link as pagina_link 
            from nivelpagina 
            inner join pagina on pagina.codpagina = nivelpagina.codpagina    
            inner join modulo on modulo.codmodulo = pagina.codmodulo
            where nivelpagina.codnivel = '{$_SESSION["codnivel"]}' and pagina.link = '{$pagina}'";
$nivelp = $conexao->comandoArray($sql);
$_SESSION["codpagina"] = $nivelp["codpagina"];

$visitante = new Visitante();
$visitante->inserir();