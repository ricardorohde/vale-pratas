<div class="row">

    <!-- general form elements -->
    <div class="box box-primary">
        <div class="box-header with-border">

        </div><!-- /.box-header -->
        <!-- form start -->
        <form id="fempresa" action="../control/SalvarEmpresa.php" method="post">
            <input type="hidden" name="codempresa" id="codempresa" value="<?= $empresaf["codempresa"] ?>"/>
            <div class="box-body">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nome">Razão</label>
                        <input type='text' class="form-control" name="razao" id="razao" placeholder="Digite razão social"  value='<?php if (isset($empresaf["razao"])) {echo $empresaf["razao"];} ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="localnascimento">Desconto(%)</label>
                        <input title="deixe em branco para cobrar valor cheio na loja" type='text' class="form-control real" name="desconto" id="desconto" maxlength="8" placeholder="Digite desconto" value='<?php if (isset($empresaf["desconto"])) {
                                echo number_format($empresaf["desconto"], 2, ',', '');
                            } ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="localnascimento">E-mail PagSeguro</label>
                        <input type='email' class="form-control" name="emailpagseguro" id="emailpagseguro" placeholder="Digite email" value='<?php if (isset($empresaf["emailpagseguro"])) {
                                echo $empresaf["emailpagseguro"];
                            } ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tokenpagseguro">Token PagSeguro</label>
                        <input type='text' class="form-control" name="tokenpagseguro" id="tokenpagseguro" placeholder="Digite transação" value='<?php if (isset($empresaf["tokenpagseguro"])) {
                                echo $empresaf["tokenpagseguro"];
                            } ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tokenpagseguro">Código transação</label>
                        <input type='text' class="form-control" name="transaction_id" id="transaction_id" placeholder="Digite código transação" value='<?php if (isset($empresaf["transaction_id"])) {
                                echo $empresaf["transaction_id"];
                            } ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="localnascimento">PagSeguro Ativo</label>
                        <select class="form-control" name="pagseguroativo" id="pagseguroativo">
                            <option value="">--Selecione--</option>
                            <option value="s" <?php if (isset($empresaf["pagseguroativo"]) && $empresaf["pagseguroativo"] == "s") {echo 'selected';} ?>>Sim</option>
                            <option value="n" <?php if (isset($empresaf["pagseguroativo"]) && $empresaf["pagseguroativo"] == "n") {echo 'selected';} ?>>Não</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="localnascimento">CEP</label>
                        <input type='text' class="form-control" name="cep" id="cep" maxlength="8" placeholder="Digite cep ele busca endereço" value='<?php if (isset($empresaf["cep"])) {
                                echo $empresaf["cep"];
                            } ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="localnascimento">Tip. Logradouro</label>
                        <input type='text' class="form-control" name="tipologradouro" id="tipologradouro" maxlength="8" placeholder="Digite tipo logradouro" value='<?php if (isset($empresaf["tipologradouro"])) {
                                echo $empresaf["tipologradouro"];
                            } ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="localnascimento">Logradouro</label>
                        <input type='text' class="form-control" name="logradouro" id="logradouro" placeholder="Digite logradouro" value='<?php if (isset($empresaf["logradouro"])) {
                                echo $empresaf["logradouro"];
                            } ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="numero">Número</label>
                        <input type='text' class="form-control" name="numero" id="numero" placeholder="Digite numero" value='<?php if (isset($empresaf["numero"])) {
                                echo $empresaf["numero"];
                            } ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bairro">Bairro</label>
                        <input type='text' class="form-control" name="bairro" id="bairro" placeholder="Digite bairro" value='<?php if (isset($empresaf["bairro"])) {
                                echo $empresaf["bairro"];
                            } ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cidade">Cidade</label>
                        <input type='text' class="form-control" name="cidade" id="cidade" placeholder="Digite cidade" value='<?php if (isset($empresaf["cidade"])) {
                                echo $empresaf["cidade"];
                            } ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <input type='text' class="form-control" name="estado" id="estado" placeholder="Digite estado" value='<?php if (isset($empresaf["estado"])) {
                                echo $empresaf["estado"];
                            } ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="estadocivil">Tel. 1</label>
                        <input type='text' class="form-control" name="telefone1" id="telefone1" placeholder="Digite telefone fixo" value='<?php if (isset($empresaf["telefone1"])) {echo $empresaf["telefone1"];} ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="estadocivil">Tel. 2</label>
                        <input type='text' class="form-control" name="telefone2" id="telefone2" placeholder="Digite telefone 2" value='<?php if (isset($empresaf["telefone2"])) {echo $empresaf["telefone2"];} ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">E-mail - 1</label>
                        <input type="email" class="form-control" name='email1' id="email1" placeholder="Digite e-mail 1" value='<?php if (isset($empresaf["email1"])) {echo $empresaf["email1"];} ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">E-mail - 2</label>
                        <input type="email" class="form-control" name='email2' id="email2" placeholder="Digite e-mail 2" value='<?php if (isset($empresaf["email2"])) {echo $empresaf["email2"];} ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Facebook</label>
                        <input type="url" class="form-control" name='facebook' id="facebook" placeholder="Digite facebook" value='<?php if (isset($empresaf["facebook"])) {echo $empresaf["facebook"];} ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Instagram</label>
                        <input type="url" class="form-control" name='instagram' id="instagram" placeholder="Digite instagram" value='<?php if (isset($empresaf["instagram"])) {echo $empresaf["instagram"];} ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">H. Funcionamento</label>
                        <input type="text" class="form-control" name='horariofuncionamento' id="horariofuncionamento" placeholder="Digite horário funcionamento" value='<?php if (isset($empresaf["horariofuncionamento"])) {echo $empresaf["horariofuncionamento"];} ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Frase Segurança</label>
                        <input type="text" class="form-control" name='fraseseguranca' id="fraseseguranca" placeholder="Digite frase segurança" value='<?php if (isset($empresaf["fraseseguranca"])) {echo $empresaf["fraseseguranca"];} ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Parcela minima</label>
                        <input type="text" class="form-control" name='parcelaminima' id="parcelaminima" placeholder="Digite parcela minima" value='<?php if (isset($empresaf["parcelaminima"])) {echo $empresaf["parcelaminima"];} ?>'>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputFile">Img. Cartões</label>
                        <input type="file" id="cartoes" name="cartoes">
                        <p class="help-block">Escolha uma imagem aqui para a Cartões</p>
                        <?php
                            if(isset($empresaf["cartoes"]) && $empresaf["cartoes"] != NULL && $empresaf["cartoes"] != ""){
                                echo '<a target="_blank" href="../arquivos/',$empresaf["cartoes"],'">Link cartões</a>';
                            }
                        ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="imgseguranca">Img. segurança</label>
                        <input type="file" id="imgseguranca" name="imgseguranca">
                        <p class="help-block">Escolha uma imagem aqui para Segurança</p>
                        <?php
                            if(isset($empresaf["imgseguranca"]) && $empresaf["imgseguranca"] != NULL && $empresaf["imgseguranca"] != ""){
                                echo '<a target="_blank" href="../arquivos/',$empresaf["imgseguranca"],'">Link img segurança</a>';
                            }
                        ?>
                    </div>
                </div>
               
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputFile">Logo</label>
                        <input type="file" id="logo" name="logo">
                        <p class="help-block">Escolha uma imagem aqui para a Logo</p>
                        <?php
                            if(isset($empresaf["logo"]) && $empresaf["logo"] != NULL && $empresaf["logo"] != ""){
                                echo '<a target="_blank" href="../arquivos/',$empresaf["logo"],'">Link logo</a>';
                            }
                        ?>
                    </div>
                </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <button type="submit"  class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div><!-- /.box -->
    <!--/.col (right) -->
</div>   <!-- /.row -->