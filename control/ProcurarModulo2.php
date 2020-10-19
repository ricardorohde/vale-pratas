<?php
    include "../model/Conexao.php";
    $conexao = new Conexao();
    $and = '';
    if(isset($_POST["nome"]) && $_POST["nome"] != NULL && $_POST["nome"] != ""){
        $and .= " and modulo.nome like '%{$_POST["nome"]}%'";
    }
    $res = $conexao->comando('select * from modulo where 1 = 1 '. $and . ' order by nome');
    $qtd = $conexao->qtdResultado($res);
    
    if($qtd > 0){
        ?>
        <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                    <div class="col-sm-6">
                    </div>
                    <div class="col-sm-6">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table id="example2" class="table table-bordered table-striped dataTable"
                               role="grid" aria-describedby="example2_info">
                            <thead>
                                <tr role="row">
                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                        colspan="1" aria-label="Browser: activate to sort column ascending">
                                        Nome
                                    </th>
                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                        colspan="1" aria-label="Platform(s): activate to sort column ascending">
                                        Titulo
                                    </th>
                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                        colspan="1" aria-label="Engine version: activate to sort column ascending">
                                        Opções
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($modulo = $conexao->resultadoArray($res)) {?>
                                <tr role="row" class="<?= $classe_linha ?>">
                                    <td>
                                        <?= $modulo["nome"] ?>
                                    </td>
                                    <td>
                                        <?= $modulo["titulo"] ?>
                                    </td>
                                    <td>
                                        <?php
                                        $arrayJavascript = "new Array('{$modulo["codmodulo"]}', '{$modulo["nome"]}', '{$modulo["titulo"]}')";
                                        echo '<a href="javascript:setaEditarModulo(',$arrayJavascript,')" title="Clique aqui para editar"><img src="./recursos/img/editar.png" alt="botão editar"/></a>';
                                        echo '<a href="#" onclick="excluirModulo2(',$modulo["codmodulo"],')" title="Clique aqui para excluir"><img src="./recursos/img/excluir.png" alt="botão excluir"/></a>';
                                        ?>
                                    </td>
                                </tr>
                                <?php }?>
                            </tbody>
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $classe_linha = "even";
    }

?>