<?php require ("inc/header.inc.php"); ?>

<div class="container">

    <h2>Fale Conosco</h2>
    <div class="fale-conosco">
        <div class="row">
            <form role="form" id='fcontato' name='fcontato' onsubmit="return false;">
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" class="form-control nome" name="nome" id="contato-nome" placeholder="NOME">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control email" name="email" id="contato-email" placeholder="EMAIL">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control telefone" name="telefone" id="contato-telefone" placeholder="TELEFONE">
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="form-group">
                        <textarea class="form-control mensagem" rows="8" name="mensagem" id="contato-mensagem" placeholder="MENSAGEM"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group pull-right">
                                <button type="button" onclick="enviaContato();" class="btn btn-default contato-enviar pull-left">ENVIAR</button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>                 
        </div>
    </div>
</div>
<br>
<br>
<br>


<?php require ("inc/footer.inc.php"); ?>