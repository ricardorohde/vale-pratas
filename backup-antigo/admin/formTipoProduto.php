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
                <form id="ftipo" name="ftipo" method="post" onsubmit="return false;">
                    <input type="hidden" name="codtipo" id="codtipoTipo" value=""/>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type='text' class="form-control" name="nome" id="nomeTipoProduto" placeholder="Digite nome" value="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="desconto">Desconto(%)</label>
                            <input title="preencha para dar desconto no tipo" type='text' class="form-control real" name="desconto" id="descontoTipoProduto" maxlength="8" placeholder="Digite desconto" value=''>
                        </div>
                    </div>                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php 
                            echo '<button class="btn btn-primary" id="btinserirTipoProduto" onclick="inserirTipoProduto()">Salvar</button>  ';
                            echo '<button style="display: none" class="btn btn-primary" id="btatualizarTipoProduto" onclick="atualizarTipoProduto()">Salvar</button>  ';
                            echo '<button style="display: none" class="btn btn-primary" id="btexcluirTipoProduto"   onclick="excluirTipoProduto()">Excluir</button>  ';
                            echo '<button class="btn btn-primary" id="btNovoTipoProduto"   onclick="novoTipoProduto()">Novo</button>  ';
                            ?>
                        </div>                                        
                    </div>                    
                </form>
            </div>
            <div class="row">
                <div id="listagemTipoProduto" class="col-md-12"></div>
            </div>
        </div>
    </div>
    <!--/.col (right) -->
</div>