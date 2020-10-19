<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Nivel{
    
    public $codnivel;
    public $nome;
    private $conexao;
    
    public function __construct($conn) {
        $this->conexao = $conn;
    }
    
    public function __destruct() {
        unset($this);
    }    
    
    public function inserir(){
        if(!isset($this->dtcadastro) || $this->dtcadastro == NULL || $this->dtcadastro == ""){
            $this->dtcadastro = date("Y-m-d H:i:s");
        }        
        if(!isset($this->codempresa) || $this->codempresa == NULL || $this->codempresa == ""){
            $this->codempresa = $_SESSION["codempresa"];
        }        
        return $this->conexao->inserir('nivel', $this);
    }

    public function atualizar(){
        return $this->conexao->atualizar('nivel', $this);
    }

    public function procurarCodigo(){
        return $this->conexao->procurarCodigo('nivel', $this);
    }
    
    public function excluir(){
        return $this->conexao->excluir('nivel', $this);
    }

}