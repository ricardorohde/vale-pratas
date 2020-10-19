<?php
$AdminLevel = 7;
if (!APP_PRODUCTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

$Read = new Read;
$Create = new Create;

$PdtId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($PdtId):
    $Read->ExeRead(DB_PDT, "WHERE pdt_id = :id", "id={$PdtId}");
    if ($Read->getResult()):
        $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
        extract($FormData);
    else:
        $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um produto que não existe ou que foi removido recentemente!";
        header('Location: dashboard.php?wc=products/home');
    endif;
else:
    $Read->FullRead("SELECT count(pdt_id) as Total FROM " . DB_PDT . " WHERE pdt_status = :st", "st=1");
    if (E_PDT_LIMIT && $Read->getResult()[0]['Total'] >= E_PDT_LIMIT):
        $_SESSION['trigger_controll'] = "<b>LIMITE ATINGIDO:</b>, Olá {$Admin['user_name']}, o limite de produtos para sua loja é " . E_PDT_LIMIT . ". Esse limite foi atingido!<p>Para cadastrar mais produtos entre em contato via " . AGENCY_EMAIL . " e solicite alteração de plano!</p><p><b>Atenciosamente " . AGENCY_NAME . "!</b></p>";
        header('Location: dashboard.php?wc=products/home');
    else:
        $PdtCreate = ['pdt_created' => date('Y-m-d H:i:s'), 'pdt_status' => 0];
        $Create->ExeCreate(DB_PDT, $PdtCreate);
        header('Location: dashboard.php?wc=products/create&id=' . $Create->getResult());
    endif;
endif;

$Search = filter_input_array(INPUT_POST);
if ($Search && $Search['s']):
    $S = urlencode($Search['s']);
    header("Location: dashboard.php?wc=posts/search&s={$S}");
endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-new-tab">Cadastrar Novo Produto</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=products/home">Produtos</a>
            <span class="crumb">/</span>
            Novo Produto
        </p>
    </div>

    <div class="dashboard_header_search">
        <a target="_blank" title="Ver no site" href="<?= BASE;?>/produto/<?= $pdt_name;?>" class="wc_view btn btn_green icon-eye">Ver produto no site!</a>
    </div>
</header>

<div class="workcontrol_imageupload none" id="post_control">
    <div class="workcontrol_imageupload_content">
        <form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="callback" value="Products"/>
            <input type="hidden" name="callback_action" value="sendimage"/>
            <input type="hidden" name="pdt_id" value="<?= $PdtId; ?>"/>
            <div class="upload_progress none" style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">0%</div>
            <div style="overflow: auto; max-height: 300px;">
                <img class="image image_default" alt="Nova Imagem" title="Nova Imagem" src="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>" default="../tim.php?src=admin/_img/no_image.jpg&w=<?= IMAGE_W; ?>&h=<?= IMAGE_H; ?>"/>
            </div>
            <div class="workcontrol_imageupload_actions">
                <input class="wc_loadimage" type="file" name="image" required/>
                <span class="workcontrol_imageupload_close icon-cancel-circle btn btn_red" id="post_control" style="margin-right: 8px;">Fechar</span>
                <button class="btn btn_green icon-image">Enviar e Inserir!</button>
                <img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>

<div class="dashboard_content single_pdt_form">
    <form class="auto_save" name="manage_pdt" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="callback" value="Products"/>
        <input type="hidden" name="callback_action" value="manager"/>
        <input type="hidden" name="pdt_id" value="<?= $PdtId; ?>"/>

        <div class="box box70">
            <div class="box_content">
                <label class="label">
                    <span class="legend">Capa (JPG <?= THUMB_W; ?>x<?= THUMB_H; ?>px):</span>
                    <input type="file" class="wc_loadimage" name="pdt_cover"/>
                </label>

                <label class="label">
                    <span class="legend">Produto:</span>
                    <input style="font-size: 1.4em;" type="text" name="pdt_title" value="<?= $pdt_title; ?>" placeholder="Nome do Produto:" required/>
                </label>


                <label class="label">
                    <span class="legend">Breve Descrição:</span>
                    <input style="font-size: 1.1em;" type="text" name="pdt_subtitle" value="<?= $pdt_subtitle; ?>" placeholder="Uma breve descrição do produto:" required/>
                </label>

                <label class="label">
                    <span class="legend">Código:</span>
                    <input type="text" name="pdt_code" value="<?= ($pdt_code ? $pdt_code : str_pad($pdt_id, 7, 0, STR_PAD_LEFT)); ?>"/>
                </label>

                <?php if (APP_LINK_PRODUCTS): ?>
                    <label class="label">
                        <span class="legend">Link Alternativo (Opcional):</span>
                        <input type="text" name="pdt_name" value="<?= $pdt_name; ?>" placeholder="Link do Produto:"/>
                    </label>
                <?php endif; ?>

                <div class="label_50">
                    <label class="label">
                        <span class="legend">Marca/Fabricante:</span>
                        <?php
                        $Read->ExeRead(DB_PDT_BRANDS, "ORDER BY brand_title ASC");
                        if (!$Read->getResult()):
                            echo Erro("<span class='icon-warning'>Cadastre algumas marcas ou fabricantes antes de começar!</span>", E_USER_WARNING);
                        else:
                            echo "<select name='pdt_brand' required>";
                            echo "<option value=''>Selecione um Fabricante</option>";
                            foreach ($Read->getResult() as $Brand):
                                echo "<option";
                                if ($pdt_brand == $Brand['brand_id']):
                                    echo " selected='selected'";
                                endif;
                                echo " value='{$Brand['brand_id']}'>{$Brand['brand_title']}</option>";
                            endforeach;

                            echo "</select>";
                        endif;
                        ?>
                    </label>

                    <label class="label">
                        <span class="legend">Categoria:</span>
                        <?php
                        $Read->ExeRead(DB_PDT_CATS, "WHERE cat_parent IS NULL ORDER BY cat_title ASC");
                        if (!$Read->getResult()):
                            echo Erro("<span class='icon-warning'>Cadastre algumas categorias de produtos antes de começar!</span>", E_USER_WARNING);
                        else:
                            echo "<select name='pdt_subcategory' required>";
                            echo "<option value=''>Selecione uma Categoria</option>";
                            foreach ($Read->getResult() as $Cat):
                                echo "<option disabled='disabled' value='{$Cat['cat_id']}'>{$Cat['cat_title']}</option>";
                                $Read->ExeRead(DB_PDT_CATS, "WHERE cat_parent = :id", "id={$Cat['cat_id']}");
                                if (!$Read->getResult()):
                                    echo "<option disabled='disabled' value=''>&raquo;&raquo; Cadastre uma categoria nessa sessão!</option>";
                                else:
                                    foreach ($Read->getResult() as $SubCat):
                                        echo "<option";
                                        if ($pdt_subcategory == $SubCat['cat_id']):
                                            echo " selected='selected'";
                                        endif;
                                        echo " value='{$SubCat['cat_id']}'>&raquo;&raquo; {$SubCat['cat_title']}</option>";
                                    endforeach;
                                endif;
                            endforeach;
                            echo "</select>";
                        endif;
                        ?>
                    </label>
                </div>

                <label class="label">
                    <span class="legend">Descrição Completa:</span>
                    <textarea name="pdt_content" class="work_mce" rows="10"><?= $pdt_content; ?></textarea>
                </label>

                <div class="label_50">
                    <label class="label">
                        <span class="legend">Preço R$ (1.000,00):</span>
                        <input style="font-size: 1.4em;" type="text" name="pdt_price" value="<?= $pdt_price ? number_format($pdt_price, '2', ',', '.') : "0,00"; ?>" placeholder="Preço do Produto:" required/>
                    </label>

                    <label class="label">
                        <span class="legend">Estoque (-1 para ilimitado):</span>
                        <input style="font-size: 1.4em;" type="number" name="pdt_inventory" value="<?= (!is_numeric($pdt_inventory) ? '-1' : $pdt_inventory); ?>" placeholder="Quantidade em Estoque:" required/>
                    </label>
                    <div class="clear"></div>
                </div>

                <span class="section icon-box-remove">DIMENSÕES DO PRODUTO:</span>
                <div class="label_50">
                    <label class="label">
                        <span class="legend">Altura Em Centímetros:</span>
                        <input type="number" name="pdt_dimension_heigth" value="<?= $pdt_dimension_heigth; ?>" placeholder="Altura em Centímetros:" required/>
                    </label>

                    <label class="label">
                        <span class="legend">Largura Em Centímetros:</span>
                        <input type="number" name="pdt_dimension_width" value="<?= $pdt_dimension_width; ?>" placeholder="Largura em Centímetros:" required/>
                    </label>
                    <div class="clear"></div>
                </div>

                <div class="label_50">
                    <label class="label">
                        <span class="legend">Profundidade Em Centímetros:</span>
                        <input type="number" name="pdt_dimension_depth" value="<?= $pdt_dimension_depth; ?>" placeholder="Profundidade em Centímetros:" required/>
                    </label>

                    <label class="label">
                        <span class="legend">Peso Em Gramas:</span>
                        <input type="number" name="pdt_dimension_weight" value="<?= $pdt_dimension_weight; ?>" placeholder="Peso em Gramas:" required/>
                    </label>
                    <div class="clear"></div>
                </div>

                <div class="clear"></div>
            </div>
        </div>

        <div class="box box30">
            <?php
            $Image = (file_exists("../uploads/{$pdt_cover}") && !is_dir("../uploads/{$pdt_cover}") ? "uploads/{$pdt_cover}" : 'admin/_img/no_image.jpg');
            ?>
            <img class="pdt_cover" alt="Capa do Produto" title="Capa do Produto" src="../tim.php?src=<?= $Image; ?>&w=<?= THUMB_W; ?>&h=<?= THUMB_H; ?>" default="../tim.php?src=<?= $Image; ?>&w=<?= THUMB_W; ?>&h=<?= THUMB_H; ?>">
            <?php
            $Read->ExeRead(DB_PDT_GALLERY, "WHERE product_id = :id", "id={$pdt_id}");
            if ($Read->getResult()):
                echo '<div class="pdt_images gallery pdt_single_image">';
                foreach ($Read->getResult() as $Image):
                    $ImageUrl = ($Image['image'] && file_exists("../uploads/{$Image['image']}") && !is_dir("../uploads/{$Image['image']}") ? "../uploads/{$Image['image']}" : '_img/no_image.jpg');
                    echo "<img id='{$Image['id']}' alt='Imagem em {$pdt_title}' title='Imagem em {$pdt_title}' src='{$ImageUrl}'/>";
                endforeach;
                echo '</div>';
            else:
                echo '<div class="pdt_images gallery pdt_single_image"></div>';
            endif;
            ?>

            <div class="box_content">
                <label class="label">
                    <span class="legend">Fotos Adicionais (JPG <?= THUMB_W; ?>x<?= THUMB_H; ?>px):</span>
                    <input type="file" name="image[]" multiple/>
                </label>

                <p class="section">Oferta:</p>

                <label class="label">
                    <span class="legend">Promoção: (860,00)</span>
                    <input type="text" name="pdt_offer_price" value="<?= $pdt_offer_price ? number_format($pdt_offer_price, '2', ',', '.') : "0,00"; ?>" placeholder="Preço Promocional:"/>
                </label>

                <label class="label">
                    <span class="legend">Início da Promoção:</span>
                    <input type="text" class="formTime" name="pdt_offer_start" value="<?= ($pdt_offer_start ? date('d/m/Y H:i', strtotime($pdt_offer_start)) : null); ?>"/>
                </label>

                <label class="label">
                    <span class="legend">Fim da Promoção:</span>
                    <input type="text" class="formTime" name="pdt_offer_end" value="<?= ($pdt_offer_end ? date('d/m/Y H:i', strtotime($pdt_offer_end)) : null); ?>"/>
                </label>

                <label class="label">
                    <span class="legend">Hotsite (opcional):</span>
                    <input type="url" name="pdt_hotlink" value="<?= $pdt_hotlink; ?>" placeholder="https://"/>
                </label>

                <div class="upload_bar m_top m_botton"><div class="upload_progress none">0%</div></div>
                <img class="form_load none fl_right" style="margin-left: 10px; margin-top: 2px;" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
                <button name="public" value="1" class="btn btn_green fl_right icon-share" style="margin-left: 5px;">Atualizar!</button>
                <label class="label_check fl_right"><input style="margin-top: -1px;" type="checkbox" value="1" name="pdt_status" <?= ($pdt_status == 1 || empty($pdt_title) ? 'checked' : ''); ?>> Publicar Agora!</label>
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>
    </form>
</div>