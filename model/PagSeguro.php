<?php

/*
 * @author Thyago Henrique Pacher - thyago.pacher@gmail.com
 */

/**
 * Description of PagSeguro
 *
 * @author ThyagoHenrique
 */
header('Content-Type: application/x-www-form-urlencoded; charset=utf-8');

class PagSeguro {

    private $urlAssinatura = 'https://ws.pagseguro.uol.com.br/v2/checkout?email={email}&token={token}';
    private $urlConsulta = 'https://ws.pagseguro.uol.com.br/v2/transactions?initialDate={dataInicial}T00:00&finalDate={dataFinal}T00:00&page=1&maxPageResults=100&email={email}&token={token}';
    private $token = '';
    private $email = '';
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
        $configuracaop = $this->conexao->comandoArray('select tokenpagseguro, emailpagseguro from empresa where tokenpagseguro <> ""');
        if (isset($configuracaop["tokenpagseguro"]) && $configuracaop["tokenpagseguro"] != NULL && $configuracaop["tokenpagseguro"] != "") {
            $configuracaop["tokenpagseguro"] = str_replace(' ', '', $configuracaop["tokenpagseguro"]);

            $this->token = $configuracaop["tokenpagseguro"];
            $this->email = $configuracaop["emailpagseguro"];
        }
    }

    function __destruct() {
        unset($this);
    }

    public function consultaData($dataInicial, $dataFim){
        if(!isset($dataInicial) || $dataInicial == NULL || $dataInicial == ""){
            $dataInicial = date("Y-m-d");
        }
        if(!isset($dataFim) || $dataFim == NULL || $dataFim == ""){
            $dataFim = date("Y-m-d");
        }
        $this->urlConsulta = str_replace('{email}', $this->email, $this->urlConsulta);
        $this->urlConsulta = str_replace('{token}', $this->token, $this->urlConsulta);
        $this->urlConsulta = str_replace('{dataInicial}', $dataInicial, $this->urlConsulta);
        $this->urlConsulta = str_replace('{dataFinal}', $dataFim, $this->urlConsulta);
        return $this->AbreSite($this->urlConsulta);
    }
    
    public function assinatura($codpessoa){
        $this->urlAssinatura = str_replace('{email}', $this->email, $this->urlAssinatura);
        $this->urlAssinatura = str_replace('{token}', $this->token, $this->urlAssinatura);
        
        $sql = 'select 
        pessoa.nome, pessoa.email, pessoa.celular, pessoa.telefone,
        plano.nome as plano, plano.valor, venda.diapagamento, plano.meses, pessoa.renovaplano
        from pessoa 
        inner join venda on venda.codcliente  = pessoa.codpessoa
        inner join plano on plano.codplano    = venda.codplano
        where pessoa.codpessoa = '. $codpessoa;
        $clientep = $this->conexao->comandoArray($sql);
        
        $finalVigencia = date('Y-m-d', strtotime('+'.$clientep["meses"].' months'));
        $clientep["valor"] = number_format($clientep["valor"], 2, '.', '');
        $data['email'] = $this->email;
        $data['token'] = $this->token;
        $data['details'] = "Todo dia {$clientep["diapagamento"]} será cobrado o valor de R$ ".  number_format($clientep["valor"], 2, ',', '.') ." referente ao {$clientep["plano"]}";
        $data['finalDate'] = $finalVigencia."T00:00:00";
        if(isset($clientep["renovaplano"]) && $clientep["renovaplano"] != NULL && $clientep["renovaplano"] == "s"){
            $data['charge'] = 'auto';
        }else{
            $data['charge'] = 'manual';
        }
        $data['name'] = "Assinatura plano {$clientep["plano"]}";
        $data['maxTotalAmount'] = $clientep["valor"];
        $data['amountPerPayment'] = $clientep["valor"];
        $data['currency'] = 'BRL';
        $data['itemId1'] = '0001';
        $data['itemQuantity1'] = 1;
        $data['itemAmount1'] = $clientep["valor"];
        $data['itemDescription1'] = "Assinatura plano {$clientep["plano"]}";
        $data['reference'] = "CODPESSOA{$codpessoa}";
        $data['senderName'] = $clientep["nome"];
        $data['senderEmail'] = $clientep["email"];
        if($clientep["meses"] == 1){
            $data['period'] = "MONTHLY";
        }elseif($clientep["meses"] == 2){
            $data['period'] = "BIMONTHLY";
        }elseif($clientep["meses"] == 3){
            $data['period'] = "TRIMONTHLY";
        }elseif($clientep["meses"] == 6){
            $data['period'] = "SEMIANNUALLY";
        }elseif($clientep["meses"] == 12){
            $data['period'] = "YEARLY";
        }
        $data = http_build_query($data);  
        
        return $this->AbreSite($this->urlAssinatura, $data);
    }
    
    private function AbreSite($url, $dados = NULL) {
        $site_url = $url;
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $site_url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        
        if(isset($dados) && $dados != NULL){
            //parametros em post
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
        }
        ob_start();
        curl_exec($ch);
        curl_close($ch);
        $file_contents = ob_get_contents();
        ob_end_clean();
        return $file_contents;
    }

}
