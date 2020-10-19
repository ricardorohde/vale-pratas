<!--Autor: @johnsonvieira / https://github.com/johnsonvieira1-->
<?php
include './model/Conexao.php';
$conexao = new Conexao();
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
        

        <!-- título -->
        <title>Vale Pratas</title>

        <!-- meta -->
        <meta name="description" content="Jóias para todos os gostos">
        <meta name="keywords" content="jioas,folheados,outro,prata">
        <meta name="author" content="Johnson Vieira - @johnsonvieira">

        <!-- fonte -->
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,300' rel='stylesheet' type='text/css'>

        <!-- bootstrap -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" media="all">

        <!-- favicon -->
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>

        <!-- css -->
        <link rel="stylesheet" href="http://valepratas.com.br/loja/assets/css/style.css">

        <!-- script -->

    </head>
    <body class="fundo-inicio">
        <div class="inicio">
            <a href="index.php"><img src="loja/assets/images/logo/logo-2.png" alt=""></a>
            <h1>Escolha seu produto</h1>
            <?php
            $rescategoria = $conexao->comando('select codcategoria, nome from categoriaproduto');
            $qtdcategoria = $conexao->qtdResultado($rescategoria);
            if ($qtdcategoria > 0) {
                while ($categoria = $conexao->resultadoArray($rescategoria)) {
                    $nomeCategoria = ucfirst(strtolower($categoria["nome"]));
                    $restoCategoria = str_replace($nomeCategoria{0}, '', $nomeCategoria);
                    echo '<a href="/loja/', strtolower($categoria["nome"]), '"><strong>' . $nomeCategoria{0} . '</strong>' . $restoCategoria . '</a> <span class="barra-inicio">/</span> ';
                }
            } else {
                echo 'Sem categoria!!!';
            }
            ?>
            <a href="loja/index.php"><strong>LOJA</strong></a>
        </div>
        <!-- Bootstrap -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        <script src="/admin/recursos/js/ajax/Venda.js"></script>
    </body>
</html>