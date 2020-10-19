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
                <form id="fbanner" name="fbanner" method="post" action="../control/SalvarBanner.php">
                    <input type="hidden" name="codbanner" id="codbanner" value="<?=$bannerp["codbanner"]?>"/>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="arquivo">Arquivo</label>
                            <input type='file' class="form-control" name="arquivo" id="arquivo">
                            <?php
                                if(isset($bannerp["arquivo"]) && $bannerp["arquivo"] != NULL && $bannerp["arquivo"] != ""){
                                    echo '<a target="_blank" href="../arquivos/',$bannerp["arquivo"],'">Visualiza imagem</a>';
                                }
                            ?>
                        </div>
                    </div>                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php 
                            if($nivelp["inserir"] == 1 || $nivelp["atualizar"] == 1){
                                echo '<input type="submit" name="submit" id="submit" value="Salvar" class="btn btn-primary"/> ';
                            }
                            if($nivelp["excluir"] == 1 && isset($_GET["codbanner"])){
                                echo '<button class="btn btn-primary" id="btexcluirBanner" onclick="excluirBanner()">Excluir</button>  ';
                            }
                            echo '<button class="btn btn-primary" id="btNovoBanner"   onclick="novoBanner()">Novo</button> ';                            
                            ?>
                        </div>                                        
                    </div>    
                    <div class="col-md-12">
                        *Banner deve ser inserido com tamanho de w:1161px por h:109px
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