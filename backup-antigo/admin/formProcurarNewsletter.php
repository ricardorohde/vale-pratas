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
                <form name="fpnewsletter" action="../control/ProcurarNewsletterRelatorio.php" id="fpnewsletter" method="post" onsubmit="return false;" target="_blank">
                    <input type="hidden" name="tipo" id="tipo" value="pdf"/>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nome">Sexo</label>
                            <select name="sexo" id="sexo" class="form-control">
                                <option value="m">Masculino</option>
                                <option value="f">Feminimo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome">E-mail</label>
                            <input type="text" class="form-control" name="email" id="email" value="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type="text" class="form-control" name="nome" id="nome" value="">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <button class="btn btn-primary" type="button" onclick="procurarNewsletter(false)">Procurar</button>
                            <button class="btn btn-primary" type="button" onclick="abreRelatorioNewsletter()">Gerar PDF</button>
                            <button class="btn btn-primary" type="button" onclick="abreRelatorioNewsletter2()">Gerar Excel</button>
                        </div>                                        
                    </div>                    
                </form>
            </div>
            
            <div class="row">
                <div class="col-sm-12" id="listagemNewsletter"></div>
            </div>
        </div>
    </div>
    <!--/.col (right) -->
</div>