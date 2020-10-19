<?php
    session_start();
    include '../model/Conexao.php';
    $conexao    = new Conexao();
    if(isset($_POST['descricao']) && $_POST['descricao'] != NULL && $_POST['descricao'] != ""){
        $and .= " and produto.nome like '%{$_POST['descricao']}%'";
    }
    if(isset($_POST['data1']) && $_POST['data1'] != NULL && $_POST['data1'] != ""){
        if(strpos($_POST['data'], '/')){
            $data1 = implode("-",array_reverse(explode('/', $_POST['data1'])));
            $and .= " and produto.dtcadastro >= '{$data1} 00:00:00'";
        }else{
            $and .= " and produto.dtcadastro >= '{$_POST['data1']} 00:00:00'";
        }          
    }
    if(isset($_POST['data2']) && $_POST['data2'] != NULL && $_POST['data2'] != ""){
        if(strpos($_POST['data2'], '/')){
            $data2 = implode("-",array_reverse(explode('/', $_POST['data2'])));
            $and .= " and produto.dtcadastro <= '{$data2} 23:59:59'";
        }else{
            $and .= " and produto.dtcadastro <= '{$_POST['data2']} 23:59:59'";
        } 
    }    
    $sql = "select DATE_FORMAT(produto.dtcadastro, '%d/%m/%Y') as dtcadastro2, nome, valor, foto, codproduto, promocao 
    from produto where 1 = 1 {$and}";
    $resproduto = $conexao->comando($sql);
    $qtdproduto = $conexao->qtdResultado($resproduto);
    
    if($qtdproduto > 0){
        $nome  = 'Rel. Produto';
        $html .= '<table class="responstable">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Dt. Cadastro</th>';
        $html .= '<th>Nome</th>';
        $html .= '<th>Valor</th>';
        $html .= '<th>Promoção</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';        
        while($produto = $conexao->resultadoArray($resproduto)){
            $html .= '<tr>';
            $html .= '<td style="text-align: left;">'.$produto["dtcadastro2"].'</td>';
            $html .= '<td style="text-align: left;">'.$produto["nome"].'</td>';
            $html .= '<td style="text-align: left;">'.number_format($produto["valor"], 2, ',', '.').'</td>';
            $html .= '<td style="text-align: left;">'.$produto["promocao"].'</td>';         
            $html .= '</tr>';            
        }
        $html .= '</tbody>';
        $html .= '</table>';        
        
        $_POST["html"] = $html;
        $paisagem = "sim";   
        if(isset($_POST["tipo"]) && $_POST["tipo"] == "xls"){
            include "./GeraExcel.php";
        }else{
            include "./GeraPdf.php";
        }            
    }else{
        echo '<script>alert("Sem produto encontrado!");window.close();</script>';
    }

