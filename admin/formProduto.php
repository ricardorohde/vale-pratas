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
                <form action="<?= $action ?>" id="fproduto" name="fproduto" method="post">
                    <input type="hidden" name="codproduto" id="codprodutoProduto" value="<?php if(isset($produtop["codproduto"])){echo $produtop["codproduto"];}?>"/>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Categoria</label>
                            <select class="form-control" name="codcategoria" id="codcategoria">
                                <?php
                                    $rescategoria = $conexao->comando('select * from categoriaproduto as categoria order by nome');
                                    $qtdcategoria = $conexao->qtdResultado($rescategoria);
                                    if($qtdcategoria > 0){
                                        echo '<option value="">--Selecione--</option>';
                                        while($categoria = $conexao->resultadoArray($rescategoria)){
                                            if(isset($produtop["codcategoria"]) && $produtop["codcategoria"] != NULL && $produtop["codcategoria"] == $categoria["codcategoria"]){
                                                echo '<option selected value="',$categoria["codcategoria"],'">',$categoria["nome"],'</option>';
                                            }else{
                                                echo '<option value="',$categoria["codcategoria"],'">',$categoria["nome"],'</option>';
                                            }
                                        }
                                    }else{
                                        echo '<option value="">--Nada encontrado--</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Tipo</label>
                            <select class="form-control" name="codtipo" id="codtipo">
                                <?php
                                    $restipo = $conexao->comando('select codtipo, nome from tipoproduto as tipo order by nome');
                                    $qtdtipo = $conexao->qtdResultado($restipo);
                                    if($qtdtipo > 0){
                                        echo '<option value="">--Selecione--</option>';
                                        while($tipo = $conexao->resultadoArray($restipo)){
                                            if(isset($produtop["codtipo"]) && $produtop["codtipo"] != NULL && $produtop["codtipo"] == $tipo["codtipo"]){
                                                echo '<option selected value="',$tipo["codtipo"],'">',$tipo["nome"],'</option>';
                                            }else{
                                                echo '<option value="',$tipo["codtipo"],'">',$tipo["nome"],'</option>';
                                            }
                                        }
                                    }else{
                                        echo '<option value="">--Nada encontrado--</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type='text' class="form-control" name="nome" id="nomeProduto" placeholder="Digite nome" value="<?php if (isset($produtop["nome"]) && $produtop["nome"] != NULL && $produtop["nome"] != "") {echo $produtop["nome"];} ?>">
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Descrição</label>
                            <input type='text' class="form-control" name="descricao" id="descricao" placeholder="Digite descrição" value="<?php if (isset($produtop["descricao"]) && $produtop["descricao"] != NULL && $produtop["descricao"] != "") {echo $produtop["descricao"];} ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="desconto">Desconto(%)</label>
                            <input title="preencha para dar desconto no produto, esse sobrepoe outros descontos" type='text' class="form-control real" name="desconto" id="desconto" maxlength="8" placeholder="Digite desconto" value='<?php if (isset($produtop["desconto"])) {
                                    echo number_format($produtop["desconto"], 2, ',', '');
                                } ?>'>
                        </div>
                    </div>                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome">Valor</label>
                            <input type='text' class="form-control real" name="valor" id="valor" placeholder="Digite valor" value="<?php if (isset($produtop["valor"]) && $produtop["valor"] != NULL && $produtop["valor"] != "") {echo $produtop["valor"];} ?>">
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Foto</label>
                            <input type='file' class="form-control" name="foto" id="foto">
                            <?php
                                if(isset($produtop["foto"]) && $produtop["foto"] != NULL && $produtop["foto"] != ""){
                                    echo '<a target="_blank" href="../arquivos/',$produtop["foto"],'">Link arquivo</a>';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Img 2</label>
                            <input type='file' class="form-control" name="imagem2" id="imagem2">
                            <?php
                                if(isset($produtop["imagem2"]) && $produtop["imagem2"] != NULL && $produtop["imagem2"] != ""){
                                    echo '<a target="_blank" href="../arquivos/',$produtop["imagem2"],'">Link arquivo</a>';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Img 3</label>
                            <input type='file' class="form-control" name="imagem3" id="imagem3">
                            <?php
                                if(isset($produtop["imagem3"]) && $produtop["imagem3"] != NULL && $produtop["imagem3"] != ""){
                                    echo '<a target="_blank" href="../arquivos/',$produtop["imagem3"],'">Link arquivo</a>';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Img 4</label>
                            <input type='file' class="form-control" name="imagem4" id="imagem4">
                            <?php
                                if(isset($produtop["foto"]) && $produtop["foto"] != NULL && $produtop["imagem4"] != ""){
                                    echo '<a target="_blank" href="../arquivos/',$produtop["imagem4"],'">Link arquivo</a>';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Promoção</label>
                            <select class="form-control" name="promocao" id="promocao">
                                <option value="">--Selecione--</option>
                                <option value="s" <?php if (isset($produtop["promocao"]) && $produtop["promocao"] != NULL && $produtop["promocao"] == "s") {echo 'selected';} ?>>Sim</option>
                                <option value="n" <?php if (isset($produtop["promocao"]) && $produtop["promocao"] != NULL && $produtop["promocao"] == "n") {echo 'selected';} ?>>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Aparece Home</label>
                            <select class="form-control" name="home" id="home">
                                <option value="">--Selecione--</option>
                                <option value="s" <?php if (isset($produtop["home"]) && $produtop["home"] != NULL && $produtop["home"] == "s") {echo 'selected';} ?>>Sim</option>
                                <option value="n" <?php if (isset($produtop["home"]) && $produtop["home"] != NULL && $produtop["home"] == "n") {echo 'selected';} ?>>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php if ($nivelp["inserir"] == 1 || $nivelp["atualizar"] == 1) { ?>
                                <input type="submit" name="btSalvar" class="btn btn-primary" id="btinserirPagamento" value="Salvar"/>
                                <?php
                            }
                            if (isset($nivelp["excluir"]) && $nivelp["excluir"] != NULL && $nivelp["excluir"] == 1) {
                                ?>
                                <button class="btn btn-primary" id="btexcluirPagamento" onclick="excluirProduto()">Excluir</button>  
                            <?php } ?>
                        </div>                                        
                    </div>                    
                </form>
            </div>

        </div>
    </div>
    <!--/.col (right) -->
</div>