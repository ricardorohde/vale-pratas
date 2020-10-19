<div class="row">
    <div class="box box-default">
        <div class="box-header with-border">


            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <form method="post" action="../control/ProcurarPessoaRelatorio.php" name="fPpessoa" id="fPpessoa" onsubmit="return false;" target="_blank">
                    <input type="hidden" name="tipo" id="tipo" value="pdf"/>
                    <?php
                    if (isset($_GET["callcenter"]) && $_GET["callcenter"] == "true") {
                        echo '<input type="hidden" name="codcategoria" id="categoria" value="6"/>';
                    } else {
                        echo '<input type="hidden" name="codcategoria" id="categoria" value="1"/>';
                    }
                    ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nome">Dt. Inicio</label>
                            <input type="date" class="form-control" name="data1" id="data1" title="Digite data de inicio onde foi feito o cadastro">
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label for="statusPessoa">Status</label>
                            <select class="form-control" name="status" id="status" title="Escolha aqui se a pessoa está ativa ou não">
                                <option value="">--Selecione--</option>
                                <option value="a">ativo</option>
                                <option value="i">inativo</option>
                            </select>
                        </div>
                        <!-- /.form-group -->
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nome">Dt. Fim</label>
                            <input type="date" class="form-control" name="data2" id="data2" title="Digite data de fim onde foi feito o cadastro">
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group"> 
                            <label for="statusPessoa">CPF</label>
                            <input type='text' class="form-control" name="cpf" id="cpf" placeholder="Digite cpf" title="Digite cpf que busca aqui">
                        </div>
                        <!-- /.form-group -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <select class="form-control" name="sexo" id="sexo" title="Escolha o sexo do cliente aqui">
                                <option value="" title="escolha aqui para voltar masculino e feminino">--Selecione--</option>
                                <option value="m">Masculino</option>
                                <option value="f">Feminino</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="rg">RG</label>
                            <input type='text' class="form-control" name="rg" id="rg" placeholder="Digite rg">
                        </div>  

                    </div><!-- /.col -->


                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cidade">Cidade</label>
                            <input type='text' class="form-control" name="cidade" id="cidade" placeholder="Digite cidade">
                        </div>                             
                    </div><!-- /.col -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" name='email' id="email" placeholder="Digite e-mail">
                        </div>                          
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="email">C/ Telefone</label>
                            <select class="form-control" name="ctelefone" id="ctelefone">
                                <option value="">--Selecione--</option>
                                <option value="s">SIM</option>
                                <option value="n">NÃO</option>
                            </select>
                        </div>                           
                    </div>    

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="email">Categoria</label>
                            <select class="form-control" name="codcategoria" id="codcategoria">
                                <?php
                                    $rescategoria = $conexao->comando('select nome, codcategoria from categoriapessoa order by nome');
                                    $qtdcategoria = $conexao->qtdResultado($rescategoria);
                                    if($qtdcategoria > 0){
                                        echo '<option value="">--Selecione--</option>';
                                        while($categoria = $conexao->resultadoArray($rescategoria)){
                                            echo '<option value="',$categoria["codcategoria"],'">',$categoria["nome"],'</option>';
                                        }
                                    }else{
                                        echo '<option value="">--Nada encontrado--</option>';
                                    }
                                ?>
                            </select>
                        </div>                           
                    </div>    

            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <button class="btn btn-primary" type="button" onclick="procurarPessoa(false)">Procurar</button>
                        <button class="btn btn-primary" type="button" onclick="abreRelatorioPessoa()">Gerar PDF</button>
                        <button class="btn btn-primary" type="button" onclick="abreRelatorioPessoa2()">Gerar Excel</button>
                    </div>                                        
                </div>
            </div>
            </form>
            <div class="row">
                <div class="col-sm-12" id="listagem"></div>
            </div>
        </div>
    </div>
    <!--/.col (right) -->
</div>