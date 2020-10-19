<?php

/**
 * Session.class [ HELPER ]
 * Responsável pelas estatísticas, sessões e atualizações de tráfego do sistema!
 * 
 * @copyright (c) 2016, Robson V. Leite UPINSIDE TECNOLOGIA
 */
class Session {

    private $Cache;
    private $Session;

    public function __construct($Cache = null) {
        $this->Cache = ($Cache ? $Cache : 20);

        //IF NOT BOT START
        if (!strstr($_SERVER['HTTP_USER_AGENT'], 'bot') || !strstr($_SERVER['HTTP_USER_AGENT'], 'Bot')):
            $this->setSession();
        endif;

        //REMOVE EXPIRED SESSIONS
        $this->sessionClear();
    }

    //Controla a classe para iniciar a sessão ou atualizar, gerencia o tráfego do site!
    private function setSession() {
        $this->Session = (!empty($_SESSION['userOnline']) ? $_SESSION['userOnline'] : null);
        if (!$this->Session):
            $this->sessionStart();
        else:
            $this->sessionUpdate();
        endif;

        $this->viewsStart();
    }

    //Inicia a sessão do ususário quando ela não existir!
    private function sessionStart() {
        $this->Session = array();
        $this->Session['online_startview'] = date('Y-m-d H:i:s');
        $this->Session['online_endview'] = date('Y-m-d H:i:s', strtotime("+{$this->Cache}minutes"));
        $this->Session['online_ip'] = $_SERVER['REMOTE_ADDR'];
        $this->Session['online_url'] = trim(strip_tags(filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
        $this->Session['online_agent'] = $_SERVER['HTTP_USER_AGENT'];

        if (!empty($_SESSION['userLogin'])):
            $this->Session['online_user'] = $_SESSION['userLogin']['user_id'];
            $this->Session['online_name'] = "{$_SESSION['userLogin']['user_name']} {$_SESSION['userLogin']['user_lastname']}";
        endif;

        $Create = new Create;
        $Create->ExeCreate(DB_VIEWS_ONLINE, $this->Session);
        $_SESSION['userOnline'] = $Create->getResult();
    }

    //Atualiza a sessão do usuário de acordo com sua navegação!Ï
    private function sessionUpdate() {
        $Read = new Read;
        $Read->ExeRead(DB_VIEWS_ONLINE, "WHERE online_id = :ses", "ses={$_SESSION['userOnline']}");
        if (!$Read->getResult()):
            $this->sessionStart();
        else:
            $this->Session = $Read->getResult()[0];
            $this->Session['online_url'] = trim(strip_tags(filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
            $this->Session['online_endview'] = date('Y-m-d H:i:s', strtotime("+{$this->Cache}minutes"));

            if (!empty($_SESSION['userLogin'])):
                $this->Session['online_user'] = $_SESSION['userLogin']['user_id'];
                $this->Session['online_name'] = "{$_SESSION['userLogin']['user_name']} {$_SESSION['userLogin']['user_lastname']}";
            else:
                $this->Session['online_user'] = null;
                $this->Session['online_name'] = null;
            endif;

            $Update = new Update;
            $Update->ExeUpdate(DB_VIEWS_ONLINE, $this->Session, "WHERE online_id = :id", "id={$this->Session['online_id']}");
        endif;
    }

    private function sessionClear() {
        $Delete = new Delete;
        $Delete->ExeDelete(DB_VIEWS_ONLINE, "WHERE (online_endview < NOW() OR online_startview IS NULL) AND online_id >= :id", "id=1");
    }

    /*
     * CONTROLA O TRÁFEGO DO SITE
     * Ao primeiro acesso do dia, armazena os dados de tráfego.
     * Atualiza o views_pages a cada load de página
     * Atualiza o views_views a cada nova sessão do site
     * Atualiza o views_users a cada visita única de um dispositivo
     */

    private function viewsStart() {
        $Read = new Read;
        $Read->ExeRead(DB_VIEWS_VIEWS, "WHERE views_date = date(NOW())");
        if ($Read->getResult()):
            $UserCookie = filter_input(INPUT_COOKIE, 'userView');
            $View = $Read->getResult()[0];

            $UpdateView = array();
            $UpdateView['views_pages'] = $View['views_pages'] + 1;
            $UpdateView['views_views'] = (empty($this->Session['online_id']) ? $View['views_views'] + 1 : $View['views_views']);
            $UpdateView['views_users'] = (empty($UserCookie) ? $View['views_users'] + 1 : $View['views_users']);

            $Update = new Update;
            $Update->ExeUpdate(DB_VIEWS_VIEWS, $UpdateView, "WHERE views_date = date(NOW()) AND views_id >= :id", "id=1");

            //24 HORS TO NEW USER
            setcookie('userView', Check::Name(SITE_NAME), time() + 86400, '/');
        else:
            $CreateView = ['views_date' => date('Y-m-d'), 'views_users' => 1, 'views_views' => 1, 'views_pages' => 1];
            $Create = new Create;
            $Create->ExeCreate(DB_VIEWS_VIEWS, $CreateView);
        endif;
    }

}
