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
                <form name="fpvenda" id="fpvenda" method="post" onsubmit="return false;" target="_blank">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nome">Dt. Inicio</label>
                            <input type="date" class="form-control" name="data1" id="data1" title="Digite data de inicio onde foi feito o cadastro" value="<?php if(!isset($_GET["data1"])){echo date("Y-m-d");}?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nome">Dt. Fim</label>
                            <input type="date" class="form-control" name="data2" id="data2" title="Digite data de fim onde foi feito o cadastro" value="<?php if(!isset($_GET["data2"])){echo date("Y-m-d");}?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <button class="btn btn-primary" type="button" onclick="procurarPagSeguro(false)">
                                <i class="fa fa-search"></i>
                                Procurar PagSeguro
                            </button>
                            <button class="btn btn-primary" type="button" onclick="procurarVenda(false)">
                                <i class="fa fa-search"></i>
                                Procurar Venda
                            </button>
                        </div>                                        
                    </div>                    
                </form>
            </div>
            
            <div class="row">
                <div class="col-sm-12" id="listagemVenda"></div>
            </div>
        </div>
    </div>
    <!--/.col (right) -->
</div>