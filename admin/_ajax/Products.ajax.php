<?php

session_start();
require '../../_app/Config.inc.php';
$NivelAcess = 7;

if (!APP_PRODUCTS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
    $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
    echo json_encode($jSON);
    die;
endif;

usleep(50000);

//DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Products';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//VALIDA AÇÃO
if ($PostData && $PostData['callback_action'] && $PostData['callback'] = $CallBack):
    //PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    $Read = new Read;
    $Create = new Create;
    $Update = new Update;
    $Delete = new Delete;
    $Upload = new Upload('../../uploads/');

    //SELECIONA AÇÃO
    switch ($Case):
        case 'manager':
            $PdtId = $PostData['pdt_id'];
            $PostData['pdt_status'] = (!empty($PostData['pdt_status']) ? $PostData['pdt_status'] : 0);
            $Read->ExeRead(DB_PDT, "WHERE pdt_id = :id", "id={$PdtId}");
            if (!$Read->getResult()):
                $jSON['trigger'] = AjaxErro("<b class='icon-warning'>Erro ao atualizar:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível consultar o produto. Experimente atualizar a página!", E_USER_WARNING);
            elseif (!empty($PostData['pdt_offer_start']) && (!Check::Data($PostData['pdt_offer_start']) || !Check::Data($PostData['pdt_offer_end']))):
                $jSON['trigger'] = AjaxErro("<b class='icon-warning'>Erro ao atualizar:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas a(s) data(s) de oferta foi informada com erro de calendário. Veja isso!", E_USER_WARNING);
            else:
                $Product = $Read->getResult()[0];
                unset($PostData['pdt_id'], $PostData['pdt_cover'], $PostData['image']);

                $PostData['pdt_price'] = str_replace(',', '.', str_replace('.', '', $PostData['pdt_price']));
                $PostData['pdt_offer_price'] = ($PostData['pdt_offer_price'] ? str_replace(',', '.', str_replace('.', '', $PostData['pdt_offer_price'])) : null);
                $PostData['pdt_name'] = (!empty($PostData['pdt_name']) ? Check::Name($PostData['pdt_name']) : Check::Name($PostData['pdt_title']));

                if (!empty($_FILES['pdt_cover'])):
                    $File = $_FILES['pdt_cover'];

                    if ($Product['pdt_cover'] && file_exists("../../uploads/{$Product['pdt_cover']}") && !is_dir("../../uploads/{$Product['pdt_cover']}")):
                        unlink("../../uploads/{$Product['pdt_cover']}");
                    endif;

                    $Upload->Image($File, "{$PdtId}-{$PostData['pdt_name']}-" . time(), 1000);
                    if ($Upload->getResult()):
                        $PostData['pdt_cover'] = $Upload->getResult();
                    else:
                        $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG de 1000x1000px para a capa!", E_USER_WARNING);
                        echo json_encode($jSON);
                        return;
                    endif;
                endif;

                if (!empty($_FILES['image'])):
                    $File = $_FILES['image'];
                    $gbFile = array();
                    $gbCount = count($File['type']);
                    $gbKeys = array_keys($File);
                    $gbLoop = 0;

                    for ($gb = 0; $gb < $gbCount; $gb++):
                        foreach ($gbKeys as $Keys):
                            $gbFiles[$gb][$Keys] = $File[$Keys][$gb];
                        endforeach;
                    endfor;

                    $jSON['gallery'] = null;
                    foreach ($gbFiles as $UploadFile):
                        $gbLoop ++;
                        $Upload->Image($UploadFile, "{$PdtId}-{$gbLoop}-" . time() . base64_encode(time()), 1000);
                        if ($Upload->getResult()):
                            $gbCreate = ['product_id' => $PdtId, "image" => $Upload->getResult()];
                            $Create->ExeCreate(DB_PDT_GALLERY, $gbCreate);
                            $jSON['gallery'] .= "<img id='{$Create->getResult()}' alt='Imagem em {$PostData['pdt_title']}' title='Imagem em {$PostData['pdt_title']}' src='../uploads/{$Upload->getResult()}'/>";
                        endif;
                    endforeach;
                endif;

                $Read->FullRead("SELECT cat_parent FROM " . DB_PDT_CATS . " WHERE cat_id = :id", "id={$PostData['pdt_subcategory']}");
                $PostData['pdt_category'] = ($Read->getResult() ? $Read->getResult()[0]['cat_parent'] : null);

                $Read->FullRead("SELECT pdt_id FROM " . DB_PDT . " WHERE pdt_name = :nm AND pdt_id != :id", "nm={$PostData['pdt_name']}&id={$PdtId}");
                if ($Read->getResult()):
                    $PostData['pdt_name'] = "{$PostData['pdt_name']}-{$PdtId}";
                endif;

                $jSON['name'] = $PostData['pdt_name'];
                $jSON['trigger'] = AjaxErro("<span class='icon-checkmark'><b>PRODUTO ATUALIZADO:</b> Olá {$_SESSION['userLogin']['user_name']}. O produto {$PostData['pdt_title']} foi atualizado com sucesso!<span>");

                $Read->FullRead("SELECT count(pdt_id) as Total FROM " . DB_PDT . " WHERE pdt_status = :st", "st=1");
                if (E_PDT_LIMIT && $Read->getResult()[0]['Total'] >= E_PDT_LIMIT && $PostData['pdt_status'] == 1):
                    $jSON['trigger'] = AjaxErro("<span class='icon-warning'><b>IMPORTANTE:</b> O produto não foi colocado a venda pois seu limite de produtos (" . E_PDT_LIMIT . ") está ultrapassado. Entre em contato via " . AGENCY_EMAIL . " para alterar seu plano!</span><p class='icon-checkmark'>O produto {$PostData['pdt_title']} foi atualizado com sucesso!</p>");
                    $PostData['pdt_status'] = 0;
                endif;

                $Read->FullRead("SELECT pdt_id FROM " . DB_PDT . " WHERE pdt_code = :code AND pdt_id != :id", "code={$PostData['pdt_code']}&id={$PdtId}");
                if ($Read->getResult()):
                    $jSON['trigger'] = AjaxErro("<span class='icon-warning'><b>OPPSSS:</b> Já existe um produto cadastrado com o código {$PostData['pdt_code']}, favor altere o código deste produto!</span>", E_USER_WARNING);
                    $PostData['pdt_code'] = str_pad($PdtId, 7, 0, STR_PAD_LEFT);
                    $PostData['pdt_status'] = 0;
                endif;

                $PostData['pdt_offer_start'] = (!empty($PostData['pdt_offer_start']) && Check::Data($PostData['pdt_offer_start']) ? Check::Data($PostData['pdt_offer_start']) : null);
                $PostData['pdt_offer_end'] = (!empty($PostData['pdt_offer_end']) && Check::Data($PostData['pdt_offer_end']) ? Check::Data($PostData['pdt_offer_end']) : null);

                $PostData['pdt_status'] = (!empty($PostData['pdt_status']) ? 1 : 0);
                if (!empty($PostData['pdt_inventory']) && $PostData['pdt_inventory'] == '-1'):
                    $PostData['pdt_inventory'] = null;
                elseif (!empty($PostData['pdt_inventory'])):
                    $PostData['pdt_inventory'] = $PostData['pdt_inventory'];
                else:
                    $PostData['pdt_inventory'] = 0;
                endif;
                $Update->ExeUpdate(DB_PDT, $PostData, "WHERE pdt_id = :id", "id={$PdtId}");
                $jSON['view'] = BASE . '/produto/' . $PostData['pdt_name'];
            endif;
            break;

        case 'sendimage':
            $NewImage = $_FILES['image'];
            $Read->FullRead("SELECT pdt_title, pdt_name FROM " . DB_PDT . " WHERE pdt_id = :id", "id={$PostData['pdt_id']}");
            if (!$Read->getResult()):
                $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe {$_SESSION['userLogin']['user_name']}, mas não foi possível identificar o produto vinculado!", E_USER_WARNING);
            else:
                $Upload = new Upload('../../uploads/');
                $Upload->Image($NewImage, $PostData['pdt_id'] . '-' . time(), IMAGE_W);
                if ($Upload->getResult()):
                    $PostData['product_id'] = $PostData['pdt_id'];
                    $PostData['image'] = $Upload->getResult();
                    unset($PostData['pdt_id']);

                    $Create->ExeCreate(DB_PDT_IMAGE, $PostData);
                    $jSON['tinyMCE'] = "<img title='{$Read->getResult()[0]['pdt_title']}' alt='{$Read->getResult()[0]['pdt_title']}' src='../uploads/{$PostData['image']}'/>";
                else:
                    $jSON['trigger'] = AjaxErro("<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá {$_SESSION['userLogin']['user_name']}, selecione uma imagem JPG ou PNG para inserir no produto!", E_USER_WARNING);
                endif;
            endif;
            break;

        case 'delete':
            $PdtId = $PostData['del_id'];
            $Read->ExeRead(DB_PDT, "WHERE pdt_id = :id", "id={$PdtId}");
            if (!$Read->getResult()):
                $jSON['trigger'] = AjaxErro("<b class='icon-warning'>OPSS:</b> Desculpe {$_SESSION['userLogin']['user_name']}. Não foi possível deletar pois o produto não existe ou foi removido recentemente.!", E_USER_WARNING);
            else:
                $Product = $Read->getResult()[0];
                $PdtCover = "../../uploads/{$Product['pdt_cover']}";

                if (file_exists($PdtCover) && !is_dir($PdtCover)):
                    unlink($PdtCover);
                endif;

                $Read->ExeRead(DB_PDT_IMAGE, "WHERE product_id = :id", "id={$Product['pdt_id']}");
                if ($Read->getResult()):
                    foreach ($Read->getResult() as $PdtGB):
                        $PdtGBImage = "../../uploads/{$PdtGB['image']}";
                        if (file_exists($PdtGBImage) && !is_dir($PdtGBImage)):
                            unlink($PdtGBImage);
                        endif;
                    endforeach;
                    $Delete->ExeDelete(DB_PDT_IMAGE, "WHERE product_id = :id", "id={$Product['pdt_id']}");
                endif;

                $Delete->ExeDelete(DB_PDT, "WHERE pdt_id = :id", "id={$Product['pdt_id']}");
                $Delete->ExeDelete(DB_COMMENTS, "WHERE pdt_id = :id", "id={$Product['pdt_id']}");
                $jSON['success'] = true;
            endif;
            break;

        case 'gbremove':
            $Read->FullRead("SELECT image FROM " . DB_PDT_IMAGE . " WHERE id = :id", "id={$PostData['img']}");
            if ($Read->getResult()):
                $ImageRemove = "../../uploads/{$Read->getResult()[0]['image']}";
                if (file_exists($ImageRemove) && !is_dir($ImageRemove)):
                    unlink($ImageRemove);
                endif;
                $Delete->ExeDelete(DB_PDT_GALLERY, "WHERE id = :id", "id={$PostData['img']}");
                $jSON['success'] = true;
            endif;
            break;

        case 'cat_manager':
            $PostData = array_map('strip_tags', $PostData);
            $CatId = $PostData['cat_id'];
            unset($PostData['cat_id']);

            $PostData['cat_name'] = Check::Name($PostData['cat_title']);
            $PostData['cat_parent'] = ($PostData['cat_parent'] ? $PostData['cat_parent'] : null);

            $Read->FullRead("SELECT cat_id FROM " . DB_PDT_CATS . " WHERE cat_name = :cn AND cat_id != :ci", "cn={$PostData['cat_name']}&ci={$CatId}");
            if ($Read->getResult()):
                $PostData['cat_name'] = $PostData['cat_name'] . '-' . $CatId;
            endif;

            $Read->FullRead("SELECT cat_id FROM " . DB_PDT_CATS . " WHERE cat_parent = :ci", "ci={$CatId}");
            if ($Read->getResult() && !empty($PostData['cat_parent'])):
                $jSON['trigger'] = AjaxErro("<b class='icon-warning'>OPPSSS: </b> {$_SESSION['userLogin']['user_name']}, uma categoria PAI (que possui subcategorias) não pode ser atribuida como subcategoria", E_USER_WARNING);
            else:
                $Update->ExeUpdate(DB_PDT_CATS, $PostData, "WHERE cat_id = :id", "id={$CatId}");
                $jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>TUDO CERTO: </b> A categoria <b>{$PostData['cat_title']}</b> foi atualizada com sucesso!");
            endif;
            break;

        case 'cat_delete':
            $CatId = $PostData['del_id'];
            $Read->FullRead("SELECT pdt_id FROM " . DB_PDT . " WHERE pdt_category = :cat OR pdt_subcategory = :cat", "cat={$CatId}");
            if ($Read->getResult()):
                $jSON['trigger'] = AjaxErro("<b class='icon-info'>OPSS: </b>Desculpe {$_SESSION['userLogin']['user_name']}, mas não é possível remover categorias com produtos cadastrados nela!", E_USER_WARNING);
            else:
                $Read->FullRead("SELECT cat_id FROM " . DB_PDT_CATS . " WHERE cat_parent = :cat", "cat={$CatId}");
                if ($Read->getResult()):
                    $jSON['trigger'] = AjaxErro("<b class='icon-info'>OPSS: </b>Desculpe {$_SESSION['userLogin']['user_name']}, mas não é possível remover categorias com subcategorias ligadas a ela!", E_USER_WARNING);
                else:
                    $Delete->ExeDelete(DB_PDT_CATS, "WHERE cat_id = :cat", "cat={$CatId}");
                    $jSON['success'] = true;
                endif;
            endif;
            break;

        case 'brand_manager':
            $BrandId = $PostData['brand_id'];
            $PostData['brand_name'] = Check::Name($PostData['brand_title']);

            $Read->FullRead("SELECT brand_id FROM " . DB_PDT_BRANDS . " WHERE brand_name = :nm AND brand_id != :id", "nm={$PostData['brand_name']}&id={$BrandId}");
            if ($Read->getResult()):
                $PostData['brand_name'] = "{$PostData['brand_name']}-{$BrandId}";
            endif;

            unset($PostData['brand_id']);
            $Update->ExeUpdate(DB_PDT_BRANDS, $PostData, "WHERE brand_id = :id", "id={$BrandId}");
            $jSON['trigger'] = AjaxErro("<b class='icon-checkmark'>TUDO CERTO: </b> A marca ou fabricante <b>{$PostData['brand_title']}</b> foi atualizada com sucesso!");
            break;

        case 'brand_remove':
            $BrandId = $PostData['del_id'];
            $Read->FullRead("SELECT pdt_id FROM " . DB_PDT . " WHERE pdt_brand = :brand", "brand={$BrandId}");
            if ($Read->getResult()):
                $jSON['trigger'] = AjaxErro("<b class='icon-info'>OPSS: </b>Desculpe {$_SESSION['userLogin']['user_name']}, mas não é possível remover uma marca quando existem produtos cadastrados com ela!", E_USER_WARNING);
            else:
                $Delete->ExeDelete(DB_PDT_BRANDS, "WHERE brand_id = :brand", "brand={$BrandId}");
                $jSON['success'] = true;
            endif;
            break;

        case 'cupom_manage':
            if (in_array('', $PostData)):
                $jSON['trigger'] = AjaxErro("<b class='icon-info'>OPSS:</b> Favor, preencha todos os campos para atualizar o cupom de desconto!", E_USER_WARNING);
            elseif (!Check::Data($PostData['cp_start']) || !Check::Data($PostData['cp_end'])):
                $jSON['trigger'] = AjaxErro("<b class='icon-info'>OPSS:</b> A data de início ou de término parecem não estar válidas!", E_USER_WARNING);
            else:
                $CouponId = $PostData['cp_id'];
                unset($PostData['cp_id']);

                $PostData['cp_start'] = (!empty($PostData['cp_start']) ? Check::Data($PostData['cp_start']) : date('Y-m-d H:i:s'));
                $PostData['cp_end'] = (!empty($PostData['cp_end']) ? Check::Data($PostData['cp_end']) : date('Y-m-d H:i:s', strtotime("+30days")));
                $Update->ExeUpdate(DB_PDT_COUPONS, $PostData, "WHERE cp_id = :id", "id={$CouponId}");
                $jSON['trigger'] = AjaxErro("<b class='icon-info'>Tudo pronto:</b> Seu cupom de {$PostData['cp_discount']}% de desconto foi cadastrado com sucesso!");
            endif;
            break;

        case 'coupon_remove':
            $Delete->ExeDelete(DB_PDT_COUPONS, "WHERE cp_id = :del_id", "del_id={$PostData['del_id']}");
            $jSON['success'] = true;
            break;
    endswitch;

    //RETORNA O CALLBACK
    if ($jSON):
        echo json_encode($jSON);
    else:
        $jSON['trigger'] = AjaxErro('<b class="icon-warning">OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_ERROR);
        echo json_encode($jSON);
    endif;
else:
    //ACESSO DIRETO
    die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
endif;
