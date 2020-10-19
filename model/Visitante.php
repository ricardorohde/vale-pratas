<?php

/* 
 * @author Thyago Henrique Pacher - thyago.pacher@gmail.com
 */

Class Visitante{
    
    public $codvisitante;
    public $enderecoip;
    public $dtcadastro;
    public $pagina;
    public $navegador;
    private $conexao;
    
    public function __construct() {
        $this->conexao = new Conexao();
    }
    
    public function __destruct() {
        unset($this);
    }     
    
    public function inserir(){
        if(!isset($this->dtcadastro) || $this->dtcadastro == NULL || $this->dtcadastro == ""){
            $this->dtcadastro = date("Y-m-d H:i:s");
        }
        if(!isset($this->enderecoip) || $this->enderecoip == NULL || $this->enderecoip == ""){
            $this->enderecoip = $this->get_client_ip();
        }
        if(!isset($this->acessode) || $this->acessode == NULL || $this->acessode == ""){
            $this->acessode = $this->acessoDe();
        }
        if(!isset($this->pagina) || $this->pagina == NULL || $this->pagina == ""){
            $this->pagina = $_SERVER['REQUEST_URI'];
        }
        return $this->conexao->inserir('visitante', $this);
    }

    public function atualizar(){
        return $this->conexao->atualizar('visitante', $this);
    }

    public function procurarCodigo(){
        return $this->conexao->procurarCodigo('visitante', $this);
    }
    
    public function excluir(){
        return $this->conexao->excluir('visitante', $this);
    }

    public function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    public function acessoDe(){
        if(strpos($_SERVER['HTTP_USER_AGENT'],"iPhone")){
            return "iPhone";
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'],"iPad")){
            return "iPad";
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'],"Android")){
            return "Android";
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'],"webOS")){
            return "webOS";
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry")){
            return "BlackBerry";
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'],"iPod")){
            return "iPod";
        }elseif(strpos($_SERVER['HTTP_USER_AGENT'],"Symbian")){
            return "Symbian";
        }else{
            return "computador";
        }
    }
    

    
}