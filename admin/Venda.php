<?php
include "validacaoLogin.php";
if (isset($_GET["codvenda"]) && $_GET["codvenda"] != NULL && trim($_GET["codvenda"]) != "") {
    $sql = "select *
        from venda where codvenda = '{$_GET["codvenda"]}' $andPessoa";
    $vendap = $conexao->comandoArray($sql);
}
?>  
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?= $empresap["razao"] ?> | Vendas</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.5 -->
        <link rel="stylesheet" href="./recursos/css/jquery-ui.min.css">
        <link rel="stylesheet" href="./recursos/css/sweet-alert.min.css">
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            body{
                color: #585858;
            }
        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">

        <div class="wrapper">

            <?php include "header.php"; ?>
            <?php include "menu.php"; ?>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <i class="fa fa-money"></i>
                        Rel. Vendas
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li><a href="#"><?= $nivelp["modulo"] ?></a></li>
                        <li class="active"><?= $nivelp["pagina"] ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div id="tabs">
                        <ul>
                            <?php if ($nivelp["procurar"] == 1 || $_SESSION["codnivel"] == '1') { ?>
                                <li><a href="#tabs-2">Procurar</a></li>
                            <?php } ?>

                        </ul>   
                        <?php if ($nivelp["procurar"] == 1 || $_SESSION["codnivel"] == '1') { ?>
                            <div id="tabs-2">
                                <?php include("formProcurarVenda.php"); ?>
                            </div>
                        <?php } ?>

                        <span style="float: right; color: grey;width: 100%;text-align: right;">@ <?= $empresap["razao"]?></span>                            
                    </div>

                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->
            <?php include "footer.php" ?>

            <!-- Control Sidebar -->
            
            <!-- Add the sidebar's background. This div must be placed
                 immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>
        </div><!-- ./wrapper -->

        <!-- jQuery-->
        <script type="text/javascript" src="./recursos/js/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="./recursos/js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="./recursos/js/jquery.form.min.js"></script>
        <script type="text/javascript" src="./recursos/js/Geral.js"></script>
        <script type="text/javascript" src="./recursos/js/ajax/Venda.js?1235463"></script>

        <!-- Bootstrap 3.3.5 -->
        <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
        <!-- FastClick -->
        <script type="text/javascript" src="plugins/fastclick/fastclick.min.js"></script>
        <!-- AdminLTE App -->
        <script type="text/javascript" src="dist/js/app.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script type="text/javascript" src="dist/js/demo.js"></script>

        <script type="text/javascript" src="./recursos/js/jquery.mask.min.js"></script>
        <script type="text/javascript" src="./recursos/js/jquery.maskMoney.min.js"></script>
        <script type="text/javascript" src="./recursos/js/sweet-alert.min.js"></script>

    </body>
</html>
<?php
/* * mascara para inputs html */

function mask($val, $mask = "(##)####-####") {
    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; $i++) {
        if ($mask[$i] == '#') {
            if (isset($val[$k])) {
                $maskared .= $val[$k++];
            }
        } else {
            if (isset($mask[$i])) {
                $maskared .= $mask[$i];
            }
        }
    }
    return $maskared;
}

/* * sintaxe para corrigir valor de mascara de acordo com o tamanho */

function reestruturandoTelefone($telefonevenda2) {
    $telefone = str_replace("-", "", str_replace("(", "", str_replace(")", "", str_replace('.', '', $telefonevenda2))));
    $telefonevenda = trim($telefone);
    if (strlen($telefonevenda) == 10) {
        $mascaraTelefone = "(##)####-####";
    } else {
        $mascaraTelefone = "(##)#####-####";
    }
    if (strlen($telefonevenda) > 8 && $telefonevenda{0} == "0") {
        $ddd = substr($telefonevenda, 0, 3);
        if ($ddd !== "021") {
            $telefone = mask($telefonevenda, $mascaraTelefone);
        } else {
            $telefone = mask($telefonevenda, $mascaraTelefone);
        }
    } elseif (strlen($telefonevenda) > 8 && $telefonevenda{0} != "0") {
        $ddd = substr($telefonevenda, 0, 2);
        if ($ddd !== "21") {
            $telefone = mask($telefonevenda, $mascaraTelefone);
        } else {
            $telefone = mask($telefonevenda, $mascaraTelefone);
        }
    } elseif (strlen($telefonevenda) == 8) {
        $telefone = mask("21" . $telefonevenda, $mascaraTelefone);
    }
    return $telefone;
}
