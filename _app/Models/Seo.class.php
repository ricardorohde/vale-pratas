<?php

/**
 * Seo [ MODEL ]
 * Classe de apoio para o modelo LINK. Pode ser utilizada para gerar SSEO para as páginas do sistema!
 * 
 * @copyright (c) 2014, Robson V. Leite UPINSIDE TECNOLOGIA
 */
class Seo {

    private $Pach;
    private $File;
    private $Link;
    private $Key;
    private $Schema;
    private $Title;
    private $Description;
    private $Image;

    public function __construct($Pach) {
        $this->Pach = explode('/', strip_tags(trim($Pach)));
        $this->File = (!empty($this->Pach[0]) ? $this->Pach[0] : null);
        $this->Link = (!empty($this->Pach[1]) ? $this->Pach[1] : null);
        $this->Key = (!empty($this->Pach[2]) ? $this->Pach[2] : null);

        $this->setPach();
        //var_dump($this);
    }

    public function getSchema() {
        return $this->Schema;
    }

    public function getTitle() {
        return $this->Title;
    }

    public function getDescription() {
        return $this->Description;
    }

    public function getImage() {
        return $this->Image;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    private function setPach() {
        if (empty($Read)):
            $Read = new Read;
        endif;

        $Pages = array();
        $Read->FullRead("SELECT page_name FROM " . DB_PAGES . " WHERE page_status = 1");
        if ($Read->getResult()):
            foreach ($Read->getResult() as $SinglePage):
                $Pages[] = $SinglePage['page_name'];
            endforeach;
        endif;

        if (in_array($this->File, $Pages) && empty($this->Link)):
            //PÁGINAS 
            $Read->FullRead("SELECT page_title, page_subtitle FROM " . DB_PAGES . " WHERE page_name = :nm AND page_status = 1", "nm={$this->File}");
            if ($Read->getResult()):
                $Page = $Read->getResult()[0];
                $this->Schema = 'WebSite';
                $this->Title = $Page['page_title'] . " - " . SITE_NAME;
                $this->Description = $Page['page_subtitle'];
                $this->Image = INCLUDE_PATH . '/images/default.jpg';
            else:
                $this->set404();
            endif;
        elseif ($this->File == 'index'):
            //INDEX
            $this->Schema = 'WebSite';
            $this->Title = SITE_NAME;
            $this->Description = SITE_DESC;
            $this->Image = INCLUDE_PATH . '/images/default.jpg';
        elseif ($this->File == 'artigo'):
            //ARTIGO 
            $Read->FullRead("SELECT post_title, post_subtitle, post_cover FROM " . DB_POSTS . " WHERE post_name = :nm AND post_status = 1 AND post_date <= NOW()", "nm={$this->Link}");
            if ($Read->getResult()):
                $Post = $Read->getResult()[0];
                $this->Schema = 'WebSite';
                $this->Title = $Post['post_title'] . " - " . SITE_NAME;
                $this->Description = $Post['post_subtitle'];
                $this->Image = BASE . "/uploads/{$Post['post_cover']}";
            else:
                $this->set404();
            endif;
        elseif ($this->File == 'artigos'):
            //ARTIGOS
            $Read->FullRead("SELECT category_title, category_content FROM " . DB_CATEGORIES . " WHERE category_name = :nm", "nm={$this->Link}");
            if ($Read->getResult()):
                $Category = $Read->getResult()[0];
                $this->Schema = 'WebSite';
                $this->Title = $Category['category_title'] . " - " . SITE_NAME;
                $this->Description = $Category['category_content'];
                $this->Image = INCLUDE_PATH . '/images/default.jpg';
            else:
                $this->set404();
            endif;
        elseif ($this->File == 'pesquisa'):
            //PESQUISA
            $this->Schema = 'WebSite';
            $this->Title = "Pesquisa por {$this->Link} em " . SITE_NAME;
            $this->Description = SITE_DESC;
            $this->Image = INCLUDE_PATH . '/images/default.jpg';
        elseif ($this->File == 'conta'):
            //CONTA
            if (ACC_MANAGER):
                $ArrAccountApp = [
                    '' => 'Entrar!',
                    'login' => 'Entrar!',
                    'recuperar' => 'Recuperar Senha!',
                    'nova-senha' => 'Criar Nova Senha!',
                    'sair' => 'Sair!',
                    'home' => 'Minha Conta!',
                    'restrito' => 'Acesso Restrito!',
                    'enderecos' => 'Meus Endereços!',
                    'pedidos' => 'Meus Pedidos!',
                    'pedido' => 'Pedido #' . str_pad($this->Key, 7, 0, STR_PAD_LEFT)
                ];

                $this->Schema = 'WebSite';
                $this->Title = (!empty($ArrAccountApp[$this->Link]) ? SITE_NAME . " - " . $ArrAccountApp[$this->Link] : 'OPPPSSS!');
                $this->Description = SITE_DESC;
                $this->Image = INCLUDE_PATH . '/images/default.jpg';
            else:
                $this->set404();
            endif;
        elseif ($this->File == 'produto'):
            //PRODUTO 
            $Read->FullRead("SELECT pdt_title, pdt_subtitle, pdt_cover FROM " . DB_PDT . " WHERE pdt_name = :nm AND (pdt_inventory >= 1 OR pdt_inventory IS NULL) AND pdt_status = 1", "nm={$this->Link}");
            if ($Read->getResult()):
                $Pdt = $Read->getResult()[0];
                $this->Schema = 'Product';
                $this->Title = $Pdt['pdt_title'] . " - " . SITE_NAME;
                $this->Description = $Pdt['pdt_subtitle'];
                $this->Image = BASE . "/uploads/{$Pdt['pdt_cover']}";
            else:
                $this->set404();
            endif;
        elseif ($this->File == 'produtos'):
            //PRODUTOS
            $Read->FullRead("SELECT cat_title FROM " . DB_PDT_CATS . " WHERE cat_name = :nm", "nm={$this->Link}");
            if ($Read->getResult()):
                $Category = $Read->getResult()[0];
                $this->Schema = 'WebSite';
                $this->Title = $Category['cat_title'] . " - " . SITE_NAME;
                $this->Description = SITE_DESC;
                $this->Image = INCLUDE_PATH . '/images/default.jpg';
            else:
                $this->set404();
            endif;
        elseif ($this->File == 'marca'):
            //MARCAS
            $Read->FullRead("SELECT brand_title FROM " . DB_PDT_BRANDS . " WHERE brand_name = :nm", "nm={$this->Link}");
            if ($Read->getResult()):
                $Brand = $Read->getResult()[0];
                $this->Schema = 'WebSite';
                $this->Title = $Brand['brand_title'] . " - " . SITE_NAME;
                $this->Description = SITE_DESC;
                $this->Image = INCLUDE_PATH . '/images/default.jpg';
            else:
                $this->set404();
            endif;
        elseif ($this->File == 'pedido'):
            //PEDIDO
            $this->Schema = 'WebSite';
            $this->Title = SITE_NAME . " - " . ECOMMERCE_TAG;
            $this->Description = SITE_DESC;
            $this->Image = INCLUDE_PATH . '/images/default.jpg';
        else:
            //404
            $this->set404();
        endif;
    }

    private function set404() {
        $this->Schema = 'WebSite';
        $this->Title = "Oppsss, nada encontrado! - " . SITE_NAME;
        $this->Description = SITE_DESC;
        $this->Image = INCLUDE_PATH . '/images/default.jpg';
    }

}
