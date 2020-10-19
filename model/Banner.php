<?php

/* 
 * @author Thyago Henrique Pacher - thyago.pacher@gmail.com
 */


Class Banner{
    
    public $codbanner;
    public $arquivo;
    public $titulo;
    public $dtcadastro;
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
        return $this->conexao->inserir('banner', $this);
    }

    public function atualizar(){
        return $this->conexao->atualizar('banner', $this);
    }

    public function procurarCodigo(){
        return $this->conexao->procurarCodigo('banner', $this);
    }
    
    public function excluir(){
        return $this->conexao->excluir('banner', $this);
    }

}