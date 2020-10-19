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
                <form action="../control/SalvarCategoriaProduto.php" id="fcategoria" name="fcategoria" method="post">
                    <input type="hidden" name="codcategoria" id="codcategoriaCategoria" value=""/>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type='text' class="form-control" name="nome" id="nomeCategoriaProduto" placeholder="Digite nome" value="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="desconto">Desconto(%)</label>
                            <input title="preencha para dar desconto na categoria" type='text' class="form-control real" name="desconto" id="descontoCategoriaProduto" maxlength="8" placeholder="Digite desconto" value=''>
                        </div>
                    </div>                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="desconto">Imagem</label>
                            <input title="escolha imagem da categoria" type='file' class="form-control real" name="imagem" id="imagem">
                            <a href="" style="display: none" target="_blank" id="visualiza_imagem_categoria">Veja Imagem</a>
                        </div>
                    </div>                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php 
                            echo '<input type="submit" class="btn btn-primary" id="btinserirCategoriaProduto" value="Salvar"/>  ';
                            echo '<button style="display: none" class="btn btn-primary" id="btexcluirCategoriaProduto"   onclick="excluirCategoriaProduto()">Excluir</button>  ';
                            echo '<button class="btn btn-primary" id="btNovoCategoriaProduto"   onclick="novoCategoriaProduto()">Novo</button>  ';                            
                            ?>
                        </div>                                        
                    </div>                    
                </form>
            </div>
            <div class="row">
                <div id="listagemCategoriaProduto" class="col-md-12"></div>
            </div>
        </div>
    </div>
    <!--/.col (right) -->
</div>