<?php
$AdminLevel = 8;
if (!APP_USERS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

$Read = new Read;
$Create = new Create;

$UserId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($UserId):
    $Read->ExeRead(DB_USERS, "WHERE user_id = :id", "id={$UserId}");
    if ($Read->getResult()):
        $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
        extract($FormData);

        if ($user_level > $_SESSION['userLogin']['user_level']):
            $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>. Por questões de segurança, é restrito o acesso a usuário com nível de acesso maior que o seu!";
            header('Location: dashboard.php?wc=users/home');
        endif;
    else:
        $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um usuário que não existe ou que foi removido recentemente!";
        header('Location: dashboard.php?wc=users/home');
    endif;
else:
    $CreateUserDefault = [
        "user_registration" => date('Y-m-d H:i:s'),
        "user_level" => 1
    ];
    $Create->ExeCreate(DB_USERS, $CreateUserDefault);
    header("Location: dashboard.php?wc=users/create&id={$Create->getResult()}");
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-user-plus">Novo Usuário</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=users/home">Usuários</a>
            <span class="crumb">/</span>
            Novo Usuário
        </p>
    </div>

    <div class="dashboard_header_search" style="font-size: 0.875em; margin-top: 16px;" id="<?= $UserId; ?>">
        <span rel='dashboard_header_search' class='j_delete_action icon-warning btn btn_red' id='<?= $UserId; ?>'>Deletar Usuário!</span>
        <span rel='dashboard_header_search' callback='Users' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='<?= $UserId; ?>'>EXCLUIR AGORA!</span>
    </div>
</header>
<div class="dashboard_content">
    <form class="auto_save" class="j_tab_home tab_create" name="user_manager" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="Users"/>
        <input type="hidden" name="callback_action" value="manager"/>
        <input type="hidden" name="user_id" value="<?= $UserId; ?>"/>

        <article class="box box70">
            <div class="box_content">
                <label class="label">
                    <span class="legend">Primeiro nome:</span>
                    <input value="<?= $user_name; ?>" type="text" name="user_name" placeholder="Primeiro Nome:" required />
                </label>

                <label class="label">
                    <span class="legend">Sobrenome:</span>
                    <input value="<?= $user_lastname; ?>" type="text" name="user_lastname" placeholder="Sobrenome:" required />
                </label>

                <label class="label">
                    <span class="legend">CPF:</span>
                    <input value="<?= $user_document; ?>" type="text" name="user_document" class="formCpf" placeholder="CPF:" required />
                </label>

                <div class="label_50">
                    <label class="label">
                        <span class="legend">Telefone:</span>
                        <input value="<?= $user_telephone; ?>" class="formPhone" type="text" name="user_telephone" placeholder="(55) 5555.5555" />
                    </label>

                    <label class="label">
                        <span class="legend">Celular:</span>
                        <input value="<?= $user_cell; ?>" class="formPhone" type="text" name="user_cell" placeholder="(55) 5555.5555" />
                    </label>
                </div>

                <label class="label">
                    <span class="legend">E-mail:</span>
                    <input value="<?= $user_email; ?>" type="email" name="user_email" placeholder="E-mail:" required />
                </label>

                <label class="label">
                    <span class="legend">Senha: (Entre 5 e 11 caracteres)</span>
                    <input value="" type="password" name="user_password" maxlength="11" minlength="5" placeholder="Senha:" readonly onfocus="$(this).removeAttr('readonly');" />
                </label>

                <div class="label_50">
                    <label class="label">
                        <span class="legend">Nível de acesso:</span>
                        <select name="user_level" id="user_level" required>
                            <option selected disabled value="">Selecione o nível de acesso:</option>
                            <?php
                            $NivelDeAcesso = getWcLevel();
                            foreach ($NivelDeAcesso as $Nivel => $Desc):
                                if ($Nivel <= $_SESSION['userLogin']['user_level']):
                                    echo "<option";
                                    if ($Nivel == $user_level):
                                        echo " selected='selected'";
                                    endif;
                                    echo " value='{$Nivel}'>{$Desc}</option>";
                                endif;
                            endforeach;
                            ?>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">Gênero do Usuário:</span>
                        <select name="user_genre" required>
                            <option selected disabled value="">Selecione o Gênero do Usuário:</option>
                            <option value="1" <?= ($user_genre == 1 ? 'selected="selected"' : ''); ?>>Masculino</option>
                            <option value="2" <?= ($user_genre == 2 ? 'selected="selected"' : ''); ?>>Feminino</option>
                        </select>
                    </label>
                </div>
                <div class="clear"></div>
            </div>
            <?php
            if ($user_level == 3) {
                ?>
                <div class="alert alert-info">
                    <strong>Informação!</strong> Pode indicar produtos para venda colocando ao final ?vend=<?= base64_encode("codvendedor=" . $UserId) ?><br>
                    Ex: <a title="clique aqui e anel zirconia será relacionado como sua venda" href="/produto/anel-de-prata-zirconia?vend=<?= base64_encode("codvendedor=" . $UserId) ?>">Anel Zirconia</a>
                </div>        
                <?php
            }
            ?>            
        </article>

        <article class="box box30">
            <?php
            $Image = (file_exists("../uploads/{$user_thumb}") && !is_dir("../uploads/{$user_thumb}") ? "uploads/{$user_thumb}" : 'admin/_img/no_avatar.jpg');
            ?>
            <img class="user_thumb" alt="Foto do usuário" title="Foto do usuário" src="../tim.php?src=<?= $Image; ?>&w=500&h=500" default="../tim.php?src=<?= $Image; ?>&w=500&h=500">
            <div class="box_content">
                <label class="label">
                    <span class="legend">Foto (<?= AVATAR_W; ?>x<?= AVATAR_H; ?>px, JPG ou PNG):</span>
                    <input type="file" name="user_thumb" class="wc_loadimage" />
                </label>

                <div class="upload_bar m_top m_botton"><div class="upload_progress none">0%</div></div>
                <img class="form_load none fl_right" style="margin-left: 10px; margin-top: 2px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
                <button name="public" value="1" class="btn btn_green fl_right icon-share" style="margin-left: 5px;">Atualizar Usuário!</button>
                <div class="clear"></div>
            </div>
        </article>
    </form>

    <article class="box <?= (APP_ORDERS ? 'box50' : 'box100'); ?>">
        <header>
            <h1>Endereços de <?= $user_name; ?> <a href="dashboard.php?wc=users/address&user=<?= $user_id; ?>" class="fl_right icon-plus a" title="Novo Endereço">Cadastrar Novo</a></h1>
        </header>
        <div class="box_content">
            <?php
            //DELETE TRASH ADDR
            if (DB_AUTO_TRASH):
                $Delete = new Delete;
                $Delete->ExeDelete(DB_USERS_ADDR, "WHERE user_id = :id AND addr_street IS NULL AND addr_zipcode IS NULL", "id={$user_id}");
            endif;

            $Read->ExeRead(DB_USERS_ADDR, "WHERE user_id = :user ORDER BY addr_key DESC, addr_name ASC", "user={$user_id}");
            if (!$Read->getResult()):
                Erro("<span class='al_center icon-info'>{$user_name} ainda não possui endereços de entrega cadastrados!</span>", E_USER_NOTICE);
            else:
                foreach ($Read->getResult() as $Addr):
                    $Addr['addr_complement'] = ($Addr['addr_complement'] ? " - {$Addr['addr_complement']}" : null);
                    $Primary = ($Addr['addr_key'] ? ' - Principal' : null);
                    echo "<div class='single_user_addr' id='{$Addr['addr_id']}'>
                            <h1 class='icon-location'>{$Addr['addr_name']}{$Primary}</h1>
                            <p>{$Addr['addr_street']}, {$Addr['addr_number']}{$Addr['addr_complement']}</p>
                            <p>B. {$Addr['addr_district']}, {$Addr['addr_city']}/{$Addr['addr_state']}, {$Addr['addr_country']}</p>
                            <p>CEP: {$Addr['addr_zipcode']}</p>

                            <div class='single_user_addr_actions'>
                                <a title='Editar Artigo' href='dashboard.php?wc=users/address&id={$Addr['addr_id']}' class='post_single_center icon-notext icon-truck btn btn_blue'></a>
                                <span rel='single_user_addr' class='j_delete_action icon-notext icon-cancel-circle btn btn_red' id='{$Addr['addr_id']}'></span>
                                <span rel='single_user_addr' callback='Users' callback_action='addr_delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$Addr['addr_id']}'>Deletar Endereço?</span>
                            </div>
                        </div>";
                endforeach;
            endif;
            ?>
            <div class="clear"></div>
        </div>
    </article>

    <?php if (APP_ORDERS): ?>
        <article class="j_tab_index tab_orders box box50">
            <header>
                <h1>Pedidos de <?= $user_name; ?></h1>
            </header>
            <div class="box_content">
                <?php
                $Read->ExeRead(DB_ORDERS, "WHERE user_id = :user ORDER BY order_status DESC, order_date DESC", "user={$user_id}");
                if (!$Read->getResult()):
                    Erro("<span class='al_center icon-info'>{$user_name} ainda não possui pedidos efetuados!</span>", E_USER_NOTICE);
                else:
                    foreach ($Read->getResult() as $Order):
                        echo "<div class='single_user_order'>
                        <h1 class='icon-cart'>" . str_pad($Order['order_id'], 7, 0, STR_PAD_LEFT) . "</h1>
                        <p class='icon-calendar'>" . date('d/m/Y H\hi', strtotime($Order['order_date'])) . "</p>
                        <p>R$ " . number_format($Order['order_price'], '2', ',', '.') . " via " . getOrderPayment($Order['order_payment']) . "</p>
                        <p>" . getOrderStatus($Order['order_status']) . "</p>
                        <a class='icon-redo2' href='dashboard.php?wc=orders/order&id={$Order['order_id']}' title='Detalhes do Pedido'>Detalhes do Pedido</a>
                    </div>";
                    endforeach;
                endif;
                ?>
                <div class="clear"></div>
            </div>
        </article>
    <?php endif; ?>
</div>