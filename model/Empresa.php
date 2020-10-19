<?php

/* 
 * @author Thyago Henrique Pacher - thyago.pacher@gmail.com
 */

Class Empresa{
    
    public $codempresa;
    public $razao;
    public $email1;
    public $email2;
    public $telefone1;
    public $telefone2;
    public $dtcadastro;
    public $logo;
    public $desconto;
    public $horariofuncionamento;
    public $fraseseguranca;
    public $parcelaminima;
    public $cartoes;
    public $imgseguranca;
    public $emailpagseguro;
    public $pagseguroativo;
    public $tokenpagseguro;
    private $conexao;
    
    public function __construct($conexao) {
        $this->conexao = $conexao;
    }
    
    public function __destruct() {
        unset($this);
    }     
    
    public function inserir(){
        if(!isset($this->dtcadastro) || $this->dtcadastro == NULL || $this->dtcadastro == ""){
            $this->dtcadastro = date("Y-m-d H:i:s");
        }        
        return $this->conexao->inserir('empresa', $this);
    }

    public function atualizar(){
        return $this->conexao->atualizar('empresa', $this);
    }

    public function procurarCodigo(){
        return $this->conexao->procurarCodigo('empresa', $this);
    }
    
    public function excluir(){
        return $this->conexao->excluir('empresa', $this);
    }

}