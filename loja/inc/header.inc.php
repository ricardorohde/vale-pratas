<?php
ob_start("ob_gzhandler");

session_start();

include '../model/Conexao.php';

$conexao = new Conexao();



$empresap = $conexao->comandoArray('select * from empresa where codempresa = 1');

function mask($val, $mask = "(##) ####-####") {

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

function arrumaTelefone($telefone) {

    $telefone = str_replace("(", "", str_replace(")", "", str_replace("-", "", str_replace(" ", "", str_replace("_", "", $telefone)))));

    if (strlen($telefone) == 10) {

        $telefone = mask($telefone);
    } elseif (strlen($telefone > 10)) {

        $telefone = mask($telefone, "(##) #####-####");
    }

    return $telefone;
}
?>

<!DOCTYPE html>

<html lang="pt-br">

    <head>

        <meta charset="UTF-8">



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



        <link rel="stylesheet" href="http://valepratas.com.br/admin/recursos/css/sweet-alert.min.css">

        <!-- script -->



    </head>

    <body>

        <header>

            <div class="faixa-topo">

                <div class="container-fluid">

                    <div class="row">

                        <div class="contato-topo">

                            <div class="col-md-3 col-md-offset-2 col-xs-6 hidden-xs">

                                <img src="/loja/assets/images/icones/fone-2.png" class="pull-left" alt=""><p>+55 <?= arrumaTelefone($empresap["telefone1"]) ?></p>

                            </div>

                            <div class="col-md-3 hidden-xs">

                                <img src="/loja/assets/images/icones/email-2.png" class="pull-left" alt=""><p><?= $empresap["email1"] ?></p>

                            </div>

                        </div>

                        <div class="acesso-rapido">

                            <div class="col-md-2 col-xs-6">

                                <a  href="https://pagseguro.uol.com.br/v2/checkout/cart.html?action=view">  

                                    <img src="/loja/assets/images/icones/sacola.png" class="pull-left" alt=""><p>Pedidos</p>

                                </a>

                            </div>

                            <div class="col-md-2 col-xs-5">

                                <img src="/loja/assets/images/icones/sair.png" class="pull-left"alt="">

                                <a href="#signup" data-toggle="modal" data-target=".bs-modal-sm"><p>Entrar</p></a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="navbar navbar-inverse navbar-fixed" align="center">

                <div class="container-fluid">

                    <div class="navbar-header">

                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">

                            <span class="sr-only">Toggle navigation</span>

                            <span class="icon-bar"></span>

                            <span class="icon-bar"></span>

                            <span class="icon-bar"></span>

                        </button>

                        <div class="visible-xs visible-sm">

                            <a href="/loja/index.php"><img src="/loja/assets/images/logo/logo-3.png" class="logo-2 img-responsive" alt="Logo vale pratas"></a>

                        </div>

                    </div>

                    <div class="row">

                        <div id="navbar-brand" class="linha" >

                            <a href="/loja/index.php"><img src="/loja/assets/images/logo/logo-3.png" class="visible-lg logo visible-md img-responsive " alt="Logo vale pratas"></a>         

                        </div>

                        <div id="navbar" class="topo  navbar-collapse collapse">

                            <div class="container">

                                <div class="row">

                                    <ul class="nav">

                                        <li><a href="/loja/aneis">Anéis</a></li>

                                        <li><a href="/loja/brincos">Brincos</a></li>

                                        <li><a href="/loja/colares">Correntes</a></li>

                                        <li><a href="/loja/pulseiras">Pulseiras</a></li>

                                        <!-- <li><a href="acessorios">Acessórios</a></li> -->

                                        <li><a href="/loja/categorias">Todas Categorias</a></li>

                                        <li><a href="/loja/produtos">Todos Produtos</a></li>

                                        <li><a href="/loja/fale-conosco">Fale Conosco</a></li>
                                        <br>
                                        <br>
                                        <div id="custom-search-input">
                                            <form action="" method="post">    
                                                <div class="input-group col-md-12">

                                                    <input name="q" type="text" class="nav-buscar form-control input-lg" placeholder="Busque seu produto" />
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-info btn-lg" type="submit">
                                                            <i class="glyphicon glyphicon-search"></i>
                                                        </button>
                                                    </span>

                                                </div>
                                            </form>
                                        </div>

                                    </ul>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- Modal -->

            <div class="modal fade bs-modal-sm" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">

                <div class="modal-dialog modal-sm">

                    <div class="modal-content">

                        <br>

                        <div class="popup">

                            <div class="bs-example bs-example-tabs">

                                <ul id="myTab" class="nav nav-tabs">

                                    <li class="active"><a href="#signin" data-toggle="tab">Cliente</a></li>

                                    <li class=""><a href="#signup" data-toggle="tab">Registrar</a></li>

                                    <li class=""><a href="#why" data-toggle="tab">Vendedor</a></li>

                                </ul>

                            </div>

                        </div>

                        <div class="modal-body">

                            <div id="myTabContent" class="tab-content">

                                <div class="tab-pane fade in" id="why">

                                    <p>Para cadastro como vendedor você deve entrar em contato com o administrado da plataforma para validação do seu registro.</p>

                                    <p></p><br> Entre em contato no email <a href="mailto: <?= $empresap["email1"] ?>"></a><?= $empresap["email1"] ?></a> expecificando detalhadamente seu interesse.</p>

                                </div>

                                <div class="tab-pane fade active in" id="signin">

                                    <form class="form-horizontal" id="loginTopo" name="loginTopo" method="post" onsubmit="return false;">

                                        <fieldset>

                                            <!-- Sign In Form -->

                                            <!-- Text input-->

                                            <div class="control-group">

                                                <label class="control-label" for="userid">E-mail:</label>

                                                <div class="controls">

                                                    <input required id="email" name="email" type="email" class="form-control" placeholder="e-mail" class="input-medium">

                                                </div>

                                            </div>



                                            <!-- Password input-->

                                            <div class="control-group">

                                                <label class="control-label" for="passwordinput">Senha:</label>

                                                <div class="controls">

                                                    <input required id="senha" name="senha" class="form-control" type="password" placeholder="********" class="input-medium">

                                                </div>

                                            </div>



                                            <!-- Multiple Checkboxes (inline) -->

                                            <div class="control-group">

                                                <label class="control-label" for="rememberme"> <input type="checkbox" id="lembreme" value="s"> Lembra-me</label>

                                            </div>



                                            <!-- Button -->

                                            <div class="control-group">

                                                <label class="control-label" for="signin"></label>

                                                <div class="controls">

                                                    <button id="signin" name="signin" class="btn btn-success" onclick="loginCliente()">Entrar</button>

                                                </div>

                                            </div>

                                        </fieldset>

                                    </form>

                                </div>

                                <div class="tab-pane fade" id="signup">

                                    <form class="form-horizontal" name="fpessoa" id="fpessoa" onsubmit="return false;">

                                        <fieldset>

                                            <!-- Sign Up Form -->

                                            <!-- Text input-->

                                            <div class="control-group">

                                                <label class="control-label" for="Email">Email:</label>

                                                <div class="controls">

                                                    <input name="email" id="emailFPessoa" class="form-control" type="text" placeholder="email@email.com.br" class="input-large" required>

                                                </div>

                                            </div>



                                            <!-- Text input-->

                                            <div class="control-group">

                                                <label class="control-label" for="userid">Nome:</label>

                                                <div class="controls">

                                                    <input id="nomeFPessoa" name="nome" class="form-control" type="text" placeholder="Digite seu nome" class="input-large" required>

                                                </div>

                                            </div>



                                            <!-- Password input-->

                                            <div class="control-group">

                                                <label class="control-label" for="password">Senha:</label>

                                                <div class="controls">

                                                    <input id="senhaFPessoa" name="senha" class="form-control" type="password" placeholder="********" class="input-large" required="">

                                                    <em>1 a 8 caracteres</em>

                                                </div>

                                            </div>



                                            <!-- Text input-->

                                            <div class="control-group">

                                                <label class="control-label" for="reenterpassword">Repetir senha:</label>

                                                <div class="controls">

                                                    <input id="senha2FPessoa" class="form-control" name="senha2" type="password" placeholder="********" class="input-large" required="">

                                                </div>

                                            </div>



                                            <!-- Button -->

                                            <div class="control-group">

                                                <label class="control-label" for="confirmsignup"></label>

                                                <div class="controls">

                                                    <button onclick="inserirPessoa();" id="confirmsignup" name="confirmsignup" class="btn popup btn-success">Criar</button>

                                                </div>

                                            </div>

                                        </fieldset>

                                    </form>

                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <center>

                                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>

                            </center>

                        </div>

                    </div>

                </div>

            </div>

        </header>

