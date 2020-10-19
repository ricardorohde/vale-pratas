<?php

session_start();

$getPost = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (empty($getPost) || empty($getPost['action'])):
    die('Acesso Negado!');
endif;

$strPost = array_map('strip_tags', $getPost);
$POST = array_map('trim', $strPost);

$Action = $POST['action'];
$jSON = null;
unset($POST['action']);

usleep(2000);

require '../../../_app/Config.inc.php';
Conn::Ldev();
$Read = new Read;
$Create = new Create;
$Update = new Update;

switch ($Action):
    case 'wc_login':
        if (in_array('', $POST)):
            $jSON['trigger'] = AjaxErro("Favor informe seu E-mail e Senha para logar!", E_USER_WARNING);
        elseif (!Check::Email($POST['user_email']) || !filter_var($POST['user_email'], FILTER_VALIDATE_EMAIL)):
            $jSON['trigger'] = AjaxErro("O E-mail informado não tem um formato válido!", E_USER_WARNING);
        elseif (strlen($POST['user_password']) < 5 || strlen($POST['user_password']) > 11):
            $jSON['trigger'] = AjaxErro("Sua senha deve conter entre 5 e 11 caracteres!", E_USER_WARNING);
        else:
            $Password = hash("sha512", $POST['user_password']);
            $Read->ExeRead(DB_USERS, "WHERE user_email = :email AND user_password = :pass", "email={$POST['user_email']}&pass={$Password}");
            if (!$Read->getResult()):
                $jSON['trigger'] = AjaxErro("Os dados informados não conferem. Informe seu e-mail e senha!", E_USER_WARNING);
            else:
                $_SESSION['userLogin'] = $Read->getResult()[0];
                $jSON['clear'] = true;
                $jSON['redirect'] = BASE . "/conta/home#acc";
            endif;
        endif;
        break;

    case 'wc_recover':
        if (in_array('', $POST)):
            $jSON['trigger'] = AjaxErro("Favor informe seu E-mail para continuar!", E_USER_WARNING);
        elseif (!Check::Email($POST['user_email']) || !filter_var($POST['user_email'], FILTER_VALIDATE_EMAIL)):
            $jSON['trigger'] = AjaxErro("O E-mail informado não tem um formato válido!", E_USER_WARNING);
        else:
            $Read->ExeRead(DB_USERS, "WHERE user_email = :email", "email={$POST['user_email']}");
            if (!$Read->getResult()):
                $jSON['trigger'] = AjaxErro("O e-mail informado não esta cadastrado em nosso site!", E_USER_WARNING);
            else:
                $AccountUser = $Read->getResult()[0];
                $HashRecover = base64_encode($Read->getResult()[0]['user_email']) . "pass" . $Read->getResult()[0]['user_password'];
                $LinkRecover = BASE . "/conta/nova-senha/{$HashRecover}";

                //SEND MAIL RECOVER
                //SEND CODE TO LOGIN
                require_once './account.email.php';
                $BodyMail = "
                    <p>Olá {$AccountUser['user_name']}, você está recebendo esse e-mail pois solicitou uma nova senha em nosso site.</p>
                    <p>Caso não tenha solicitado essa senha. Por favor nos desculpe pelo incomodo. E apenas ignore este e-mail :)</p>
                    <p>Caso contrário:</p>
                    <p><a title='Recuperar Minha Senha' href='{$LinkRecover}#acc'>RECUPERAR MINHA SENHA AGORA!</a></p>
                    <p>Ao clicar no link acima você será redirecionado para criar uma nova senha, e assim recuperar seu acesso!</p>
                    <p><i>Atenciosamente, " . SITE_NAME . "!</i></p>
                    ";
                $Mensagem = str_replace('#mail_body#', $BodyMail, $MailContent);
                $SendEmail = new Email;
                $SendEmail->EnviarMontando("Recupere sua senha {$AccountUser['user_name']}!", $Mensagem, SITE_NAME, MAIL_USER, "{$AccountUser['user_name']} {$AccountUser['user_lastname']}", $AccountUser['user_email']);
                $jSON['trigger'] = AjaxErro("Olá {$AccountUser['user_name']}. Enviamos os dados de acesso para seu e-mail :)");
                $jSON['clear'] = true;
            endif;
        endif;
        break;

    case 'wc_newpass':
        if (empty($_SESSION['userRecoverId'])):
            $jSON['redirect'] = BASE . "/conta/recuperar#acc";
        elseif (in_array('', $POST)):
            $jSON['trigger'] = AjaxErro("Favor informe e repita sua nova senha!", E_USER_WARNING);
        elseif (strlen($POST['user_password']) < 5 || strlen($POST['user_password']) > 11):
            $jSON['trigger'] = AjaxErro("Sua nova senha deve ter entre 5 e 11 caracteres!", E_USER_WARNING);
        elseif ($POST['user_password'] != $POST['user_password_r']):
            $jSON['trigger'] = AjaxErro("Você informou duas senhas diferentes!", E_USER_WARNING);
        else:
            $UpdatePassword = ['user_password' => hash("sha512", $POST['user_password']), 'user_lastupdate' => date('Y-m-d H:i:s')];
            $Update->ExeUpdate(DB_USERS, $UpdatePassword, "WHERE user_id = :id", "id={$_SESSION['userRecoverId']}");
            $Read->ExeRead(DB_USERS, "WHERE user_id = :id", "id={$_SESSION['userRecoverId']}");
            if ($Read->getResult()):
                $_SESSION['userLogin'] = $Read->getResult()[0];
                if (!empty($_SESSION['userRecoverId'])):
                    unset($_SESSION['userRecoverId']);
                endif;
                $jSON['trigger'] = AjaxErro("Olá {$_SESSION['userLogin']['user_name']}, sua senha foi alterada! <a href='" . BASE . "/conta/home' title='Acessar Minha Conta!'>Acessar Minha Conta!</a>");
                $jSON['clear'] = true;
            else:
                $jSON['trigger'] = AjaxErro("Você informou duas senhas diferentes!", E_USER_WARNING);
            endif;
        endif;
        break;

    case 'wc_user':
        if (empty($_SESSION['userLogin']['user_id']) || empty($_SESSION['userLogin']['user_email'])):
            unset($_SESSION['userLogin']);
            $jSON['redirect'] = BASE . "/conta/sair#acc";
        elseif (empty($POST['user_name']) || empty($POST['user_lastname']) || empty($POST['user_genre'])):
            $jSON['trigger'] = AjaxErro("Opppssss {$_SESSION['userLogin']['user_name']}, você deve preencher os campos obrigatórios (*)!", E_USER_WARNING);
        else:
            $UserId = $_SESSION['userLogin']['user_id'];
            $UserEmail = $_SESSION['userLogin']['user_email'];
            unset($POST['user_thumb']);

            if (!empty($_FILES['user_thumb'])):
                $UserThumb = $_FILES['user_thumb'];
                $Read->FullRead("SELECT user_thumb FROM " . DB_USERS . " WHERE user_id = :id", "id={$UserId}");
                if ($Read->getResult()):
                    if (file_exists("../../../uploads/{$Read->getResult()[0]['user_thumb']}") && !is_dir("../../../uploads/{$Read->getResult()[0]['user_thumb']}")):
                        unlink("../../../uploads/{$Read->getResult()[0]['user_thumb']}");
                    endif;
                endif;

                $Upload = new Upload('../../../uploads/');
                $Upload->Image($UserThumb, $UserId . "-" . Check::Name($POST['user_name'] . $POST['user_lastname']), AVATAR_W);
                if ($Upload->getResult()):
                    $POST['user_thumb'] = $Upload->getResult();
                else:
                    $jSON['trigger'] = AjaxErro("Opppssss {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para enviar sua foto!", E_USER_WARNING);
                    echo json_encode($jSON);
                    return;
                endif;
            endif;

            if (!empty($POST['user_password'])):
                if (strlen($POST['user_password']) >= 5 && strlen($POST['user_password']) <= 11):
                    $POST['user_password'] = hash('sha512', $POST['user_password']);
                else:
                    $jSON['trigger'] = AjaxErro("Oppsss {$_SESSION['userLogin']['user_name']}, sua senha deve ter entre 5 e 11 caracteres para ser redefinida!", E_USER_WARNING);
                    echo json_encode($jSON);
                    return;
                endif;
            else:
                unset($POST['user_password']);
            endif;

            //ATUALIZA USUÁRIO
            $Update->ExeUpdate(DB_USERS, $POST, "WHERE user_id = :id", "id={$UserId}");
            $Read->ExeRead(DB_USERS, "WHERE user_id = :id", "id={$UserId}");
            if ($Read->getResult()):
                $_SESSION['userLogin'] = $Read->getResult()[0];
            endif;

            $jSON['trigger'] = AjaxErro("Olá {$_SESSION['userLogin']['user_name']}, seus dados foram atualizados com sucesso!");
        endif;
        break;
endswitch;

echo json_encode($jSON);
