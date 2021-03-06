<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Pessoa{
    public $codpessoa;
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
        return $this->conexao->inserir('pessoa', $this);
    }

    public function atualizar(){
        return $this->conexao->atualizar('pessoa', $this);
    }

    public function excluir(){
        return $this->conexao->excluir('pessoa', $this);
    }

    public function procurarCodigo(){
        return $this->conexao->procurarCodigo('pessoa', $this);
    }

    public function login(){
        $this->email = addslashes($this->email);
        $sql = 'select codpessoa, nome, foto, dtcadastro, codnivel from pessoa where email = "'. $this->email. '" and senha = "'. base64_encode($this->senha). '"';
//        echo "<pre>$sql</pre>";
        return $this->conexao->comandoArray($sql);
    }
    

}