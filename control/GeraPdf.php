<?php

    set_time_limit(0);
    include("../control/mpdf/mpdf.php");
    define('MPDF_PATH', '../control/mpdf/');
    if(isset($paisagem) && $paisagem != NULL && $paisagem != ''){
        $mpdf=new mPDF('utf-8', 'A4-L');
    }else{
        $mpdf = new mPDF();
    }
    
    $mpdf->useSubstitutions = false;
    $mpdf->simpleTables = true;
    $empresa    = $conexao->comandoArray("select logo, razao from empresa where codempresa = 1");
    $cabecalho  = '<img width="150" style="margin-left: 40%" src="../arquivos/'.$empresa["logo"].'" alt="Logo condominio" title="Logo"/><h3 style="width: 220px; margin: 0 auto;">'.$nome.'</h3>';    
    $mpdf->SetDisplayMode("fullpage");
    $mpdf->WriteHTML('<link rel="stylesheet" href="https://gestccon.com.br/sistema/visao/recursos/css/tabela.min.css" type="text/css"><style>.nrelatorio{display: none}</style>'.$cabecalho.$html, 0, true, false);
    $mpdf->SetHTMLFooter('<p style="float: left; color: black;width: 180px;text-align: left; font-size: 12px;">Data: '.date('d/m/Y').'</p><p style="float: right; color: grey;width: 10%;text-align: right;">@ '.$empresa["razao"].'</p>');
    $mpdf->Output();
    
