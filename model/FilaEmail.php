<?php

/* 
 * @author Thyago Henrique Pacher - thyago.pacher@gmail.com
 */

Class FilaEmail{
    
    public $codfila;
    public $codpessoa;
    public $assunto;
    public $texto;
    public $situacao;
    public $codstatus;
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
        if(!isset($this->codstatus) || $this->codstatus == NULL || $this->codstatus == ""){
            $this->codstatus = 3;//não enviado
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