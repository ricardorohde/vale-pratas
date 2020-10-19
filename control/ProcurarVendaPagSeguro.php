<?php
session_start();
//validação caso a sessão caia
if (!isset($_SESSION)) {
    die("<script>alert('Sua sessão caiu, por favor logue novamente!!!');window.close();</script>");
}
include "../model/Conexao.php";
include "../model/PagSeguro.php";
$conexao = new Conexao();
$pagseguro = new PagSeguro($conexao);
$data1 = '';
$data2 = '';

if (isset($_POST["data1"]) && $_POST["data1"] != NULL && $_POST["data1"] != "") {
    if (strpos($_POST["data1"], "/")) {
        $data1 = implode("-", array_reverse(explode("/", $_POST["data1"])));
    } else {
        $data1 = $_POST["data1"];
    }
}
if (isset($_POST["data2"]) && $_POST["data2"] != NULL && $_POST["data2"] != "") {
    if (strpos($_POST["data2"], "/")) {
        $data2 = implode("-", array_reverse(explode("/", $_POST["data2"])));
    } else {
        $data2 = $_POST["data2"];
    }
}

$res    = $pagseguro->consultaData($data1, $data2);
$vendas = simplexml_load_string($res);    

//$vendas = simplexml_load_file('retornoConsultaPagSeguro.xml');
if ($vendas->resultsInThisPage > 0) {
    foreach ($vendas->transactions as $key => $transaction) {
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
                                    <th class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                        colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending">
                                        Data Cad.
                                    </th>
                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                        colspan="1" aria-label="Browser: activate to sort column ascending">
                                        Cliente
                                    </th>
                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                        colspan="1" aria-label="Platform(s): activate to sort column ascending">
                                        Produto
                                    </th>
                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                        colspan="1" aria-label="Platform(s): activate to sort column ascending">
                                        Valor
                                    </th>
                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                        colspan="1" aria-label="Platform(s): activate to sort column ascending">
                                        Status PagSeguro
                                    </th>
                                    <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                        colspan="1" aria-label="Platform(s): activate to sort column ascending">
                                        ID transação
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($transaction as $key => $ultima) {
                                    $vendap = $conexao->comandoArray("select venda.codvenda, produto.nome as produto, 
                                    DATE_FORMAT(venda.dtcadastro, '%d/%m/%Y') as dtcadastro2, cliente.nome as cliente, venda.valorpagamento
                                    from venda 
                                    inner join produto on produto.codproduto = venda.codproduto
                                    inner join pessoa as cliente on cliente.codpessoa = venda.codcliente
                                    where 1 = 1 and id_transacaopagseguro = '{$ultima->code}' order by venda.dtcadastro desc");
                                    if (isset($vendap["codvenda"]) && $vendap["codvenda"] != NULL && $vendap["codvenda"] != "") {
                                        echo '<tr role="row">';
                                        echo "<td>{$vendap["dtcadastro2"]}</td>";
                                        echo "<td>{$vendap["cliente"]}</td>";
                                        echo "<td>{$vendap["produto"]}</td>";
                                        echo "<td>{$vendap["valorpagamento"]}</td>";
                                        echo "<td>";
                                        ;
                                        if ($ultima->status == 1) {
                                            echo "Aguardando pagamento";
                                        } elseif ($ultima->status == 2) {
                                            echo "Em análise";
                                        } elseif ($ultima->status == 3) {
                                            echo "Paga";
                                        } elseif ($ultima->status == 4) {
                                            echo "Disponível";
                                        } elseif ($ultima->status == 5) {
                                            echo "Em disputa";
                                        } elseif ($ultima->status == 6) {
                                            echo "Devolvida";
                                        } elseif ($ultima->status == 7) {
                                            echo "Cancelada";
                                        }
                                        echo "</td>";
                                        echo "<td>$ultima->code</td>";
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }
} elseif ($vendas->resultsInThisPage == 0) {
    die("<span style='color: red'>Nenhuma venda encontrada !!!</span>");
}
?>