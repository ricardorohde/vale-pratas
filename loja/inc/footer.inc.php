<!--Autor: @johnsonvieira / https://github.com/johnsonvieira1-->
<footer>
    <div class="rodape">

        <div class="container">
            <div class="row">
                <!-- 					<div class="atendimento">
                                                                <div class="col-md-12">
                                                                        <p align="center">Atendimento de <strong>segunda a sexta-feira das 09 as 19h</strong></p>
                                                                </div>
                                                        </div> -->
                <div class="contato">
                    <div class="row">
                        <div class="col-md-3 hidden-xs" align="center">
                            <img src="/loja/assets/images/icones/home.png" alt="">
                            <p><?= $empresap["horariofuncionamento"] ?></p>
                        </div>

                        <div class="col-xs-12 hidden-md hidden-lg">
                            <div class="col-xs-2 col-xs-offset-1">
                                <img src="/loja/assets/images/icones/home.png" alt="">
                            </div>
                            <div class="col-xs-7">
                                <p>
                                    <?= $empresap["horariofuncionamento"] ?>
                                </p>
                                <br>
                            </div>
                        </div>

                        <div class="col-md-3 hidden-xs" align="center">
                            <img src="/loja/assets/images/icones/fone.png" alt="">
                            <p>+55 <?= arrumaTelefone($empresap["telefone1"]) ?><br />+55 <?= arrumaTelefone($empresap["telefone2"]) ?></p>
                        </div>

                        <div class="col-xs-12 hidden-md hidden-lg">
                            <div class="col-xs-2 col-xs-offset-1">
                                <img src="/loja/assets/images/icones/fone.png" alt="">
                            </div>
                            <div class="col-xs-7">
                                <p>+55 <?= arrumaTelefone($empresap["telefone1"]) ?><br />+55 <?= arrumaTelefone($empresap["telefone2"]) ?></p>
                                <br>
                            </div>
                        </div>

                        <div class="col-md-3 hidden-xs" align="center">
                            <img src="/loja/assets/images/icones/email.png" alt="">
                            <p>
                                <a style="color: white;" href="mailto:<?= $empresap["email1"] ?>"><?= $empresap["email1"] ?></a>
                                <br />
                                <a style="color: white;" href="mailto:<?= $empresap["email2"] ?>"><?= $empresap["email2"] ?></a>
                            </p>	
                        </div>

                        <div class="col-xs-12 hidden-md hidden-lg">
                            <div class="col-xs-2 col-xs-offset-1">
                                <img src="/loja/assets/images/icones/email.png" alt="">
                            </div>
                            <div class="col-xs-7">
                                <a style="color: white;" href="mailto:<?= $empresap["email1"] ?>"><?= $empresap["email1"] ?></a>
                                <br />
                                <a style="color: white;" href="mailto:<?= $empresap["email2"] ?>"><?= $empresap["email2"] ?></a>
                            </div>
                        </div>
                        <div class="col-md-3 hidden-xs" align="center">
                            <a href="#" data-toggle="modal" data-target="#login-modal"><img src="assets/images/icones/news.png" width="48" alt="">
                                <p>Assine nossa Newslatter <br> e receba promoções</p></a>	
                        </div>
                    </div>
                </div>
            </div>
        </div>
   
        <!-- Modal -->
        <div id="login-modal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Assine Nossa Newslatter</h4>
                    </div>
                    <div class="modal-body">
                        <form id="fnewsletter" onsubmit="return false;">
                            <div class="form-group">
                                <label>Nome</label>
                                <input type="text" class="form-control" name="nome" placeholder="Nome">
                            </div>
                            <div class="form-group">
                                <label>E-mail</label>
                                <input type="email" class="form-control" name="email" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label>Sexo</label>
                                <select name="sexo" id="sexo" class="form-control">
                                    <option value="m">Masculino</option>
                                    <option value="f">Feminimo</option>
                                </select>                            
                            </div>                            
                        </form>
                    </div>
                    <div class="modal-footer">
                        <input type="button" name="login" class="btn btn-primary btn-block" onclick="salvarNewsletter();" value="Enviar dados">
                    </div>
                </div>

            </div>
        </div>        
        <div class="seguranca">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3 col-md-offset-3" align="center">
                        <img src="/arquivos/<?= $empresap["cartoes"] ?>" alt="">
                        <p>Em até 12x - Parcela mínima de R$ 100,00</p>				
                    </div>
                    <div class="col-xs hidden-md hidden-lg">
                        <br>
                    </div>
                    <div class="col-md-3" align="center">
                        <a href="#"><img src="/arquivos/<?= $empresap["imgseguranca"] ?>" alt=""></a>
                        <p><?= $empresap["fraseseguranca"] ?></strong></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="direitos">
            <div class="container">
                <div class="row">
                    <div class="col-md-11 hidden-xs">
                        <p>Todos os direitos reservados a empresa <strong>Vale Pratas</strong> LTDA.</p>
                    </div>
                    <div class="col-xs-12 hidden-md hidden-lg">
                        <p>Direitos reservados a <strong>Vale Pratas</strong>.</p>
                    </div>
                    <div class="social">
                        <div class="col-md-1" align="center">
                            <?php if (isset($empresap["facebook"]) && $empresap["facebook"] != NULL && $empresap["facebook"] != "") { ?>
                                <a href="<?= $empresap["facebook"] ?>" target="_blank"><img src="/loja/assets/images/social/facebook.png" alt=""></a>
                                <?php
                            }
                            if (isset($empresap["instagram"]) && $empresap["instagram"] != NULL && $empresap["instagram"] != "") {
                                ?>
                                <a href="<?= $empresap["instagram"] ?>" target="_blank"><img src="/loja/assets/images/social/instagram.png" alt=""></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Jquery -->
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>

<script src="/admin/recursos/js/ajax/Login.js"></script>
<script src="/admin/recursos/js/ajax/Pessoa.js"></script>
<script src="/admin/recursos/js/ajax/Venda.js"></script>
<script src="/admin/recursos/js/Geral.js"></script>
<?php if (isset($qtdproduto) && $qtdproduto > 0) { ?>
    <script src="/admin/recursos/js/ajax/Produto.js"></script>
<?php } ?>
<script src="http://valepratas.com.br/admin/recursos/js/sweet-alert.min.js"></script>
<!-- Bootstrap -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</body>
</html>

