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
                <form action="../control/ProcurarProdutoRelatorio.php" name="fPproduto" id="fPproduto" method="post" onsubmit="return false;" target="_blank">
                    <input type="hidden" name="tipo" id="tipo" value="pdf"/>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nome">Dt. Inicio</label>
                            <input type="date" class="form-control" name="data1" id="data1" title="Digite data de inicio onde foi feito o cadastro">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nome">Dt. Fim</label>
                            <input type="date" class="form-control" name="data2" id="data2" title="Digite data de fim onde foi feito o cadastro">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cidade">Nome</label>
                            <input type='text' class="form-control" name="nome" id="nome" placeholder="Digite nome produto">
                        </div>                             
                    </div><!-- /.col -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cidade">Promoção</label>
                            <select class="form-control" name="promocao" id="promocao">
                                <option value="">--Selecione--</option>
                                <option value="s">Sim</option>
                                <option value="n">Não</option>
                            </select>
                        </div>                             
                    </div><!-- /.col -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <button class="btn btn-primary" type="button" onclick="procurarProduto(false)">Procurar</button>
                        <button class="btn btn-primary" type="button" onclick="abreRelatorioProduto()">Gerar PDF</button>
                        <button class="btn btn-primary" type="button" onclick="abreRelatorioProduto2()">Gerar Excel</button>
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