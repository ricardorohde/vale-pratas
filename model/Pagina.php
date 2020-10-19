<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Pagina{
    public $codpagina;
    public $nome;
    public $email;
    public $senha;
    public $dtcadastro;
    public $foto;
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
        return $this->conexao->inserir('pagina', $this);
    }

    public function atualizar(){
        return $this->conexao->atualizar('pagina', $this);
    }

    public function excluir(){
        return $this->conexao->excluir('pagina', $this);
    }

    public function procurarCodigo(){
        return $this->conexao->procurarCodigo('pagina', $this);
    }

}