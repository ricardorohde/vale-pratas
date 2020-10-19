<?php

/* 
 * @author Thyago Henrique Pacher - thyago.pacher@gmail.com
 */

Class Produto{
    
    public $codproduto;
    public $nome;
    public $promocao;
    public $valor;
    public $dtcadastro;
    public $foto;
    public $desconto;
    public $home;
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
        return $this->conexao->inserir('produto', $this);
    }

    public function atualizar(){
        return $this->conexao->atualizar('produto', $this);
    }

    public function procurarCodigo(){
        return $this->conexao->procurarCodigo('produto', $this);
    }
    
    public function excluir(){
        return $this->conexao->excluir('produto', $this);
    }

}