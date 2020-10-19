<?php

/* 
 * @author Thyago Henrique Pacher - thyago.pacher@gmail.com
 */

Class Venda{
    
    public $codvenda;
    public $codcliente;
    public $codvendedor;
    public $dtcadastro;
    public $codproduto;
    public $quantidade;
    public $valor;
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
        if(!isset($this->codstatus) || $this->codstatus == NULL || $this->codstatus == ""){
            $this->codstatus = 1;//vendido mas ainda não pago
        }
        return $this->conexao->inserir('venda', $this);
    }

    public function atualizar(){
        return $this->conexao->atualizar('venda', $this);
    }

    public function procurarCodigo(){
        return $this->conexao->procurarCodigo('venda', $this);
    }
    
    public function excluir(){
        return $this->conexao->excluir('venda', $this);
    }

}