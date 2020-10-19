/* 
 * Biblioteca de evendos jQuery do Painel Work Control Pro Content Manager
 * Created on : 06/01/2016, 11:15:16
 * Author     : UpInside Treinamentos
 */

$(function () {
    //MOBILE MENU CONTROL
    $('.mobile_menu').click(function () {
        if ($('.dashboard_nav').css('left') !== '-220px') {
            $('.dashboard_nav').animate({left: '-220px'}, 300);
            $('.dashboard_fix').animate({'margin-left': '0px'}, 300);
        } else {
            $('.dashboard_nav').animate({left: '0px'}, 300);
            $('.dashboard_fix').animate({'margin-left': '220px'}, 300);
        }
    });

    //NEW LINE ACTION
    $('textarea').keypress(function (event) {
        if (event.which === 13) {
            var s = $(this).val();
            $(this).val(s + "\n");
        }
    });

    //############## GET CEP
    $('.wc_getCep').change(function () {
        var cep = $(this).val().replace('-', '').replace('.', '');
        if (cep.length === 8) {
            $.get("https://viacep.com.br/ws/" + cep + "/json", function (data) {
                if (!data.erro) {
                    $('.wc_bairro').val(data.bairro);
                    $('.wc_complemento').val(data.complemento);
                    $('.wc_localidade').val(data.localidade);
                    $('.wc_logradouro').val(data.logradouro);
                    $('.wc_uf').val(data.uf);
                }
            }, 'json');
        }
    });

    //AUTOSAVE ACTION
    $('form.auto_save').change(function () {
        var form = $(this);
        var callback = form.find('input[name="callback"]').val();
        var callback_action = form.find('input[name="callback_action"]').val();

        if (typeof tinyMCE !== 'undefined') {
            tinyMCE.triggerSave();
        }

        form.ajaxSubmit({
            url: '_ajax/' + callback + '.ajax.php',
            data: {callback_action: callback_action},
            dataType: 'json',
            success: function (data) {
                //CLEAR INPUT FILE
                form.find('input[type="file"]').val('');

                if (data.name) {
                    var input = form.find('.wc_name');
                    if (!input.val() || input.val() != data.name) {
                        input.val(data.name);
                    }
                }

                if (data.gallery) {
                    form.find('.gallery').fadeTo('300', '0.5', function () {
                        $(this).html($(this).html() + data.gallery).fadeTo('300', '1');
                    });
                }

                if (data.view) {
                    $('.wc_view').attr('href', data.view);
                }
            }
        });
    });

    //Coloca todos os formulários em AJAX mode e inicia LOAD ao submeter!
    $('form').not('.ajax_off').submit(function () {
        var form = $(this);
        var callback = form.find('input[name="callback"]').val();
        var callback_action = form.find('input[name="callback_action"]').val();

        if (typeof tinyMCE !== 'undefined') {
            tinyMCE.triggerSave();
        }

        form.ajaxSubmit({
            url: '_ajax/' + callback + '.ajax.php',
            data: {callback_action: callback_action},
            dataType: 'json',
            beforeSubmit: function () {
                form.find('.form_load').fadeIn('fast');
                $('.trigger_ajax').fadeOut('fast');
            },
            uploadProgress: function (evento, posicao, total, completo) {
                var porcento = completo + '%';
                form.find('.upload_progress').text(porcento).width(porcento);

                if (completo <= '50') {
                    form.find('.upload_progress').fadeIn('fast');
                }
                if (completo >= '100') {
                    form.find('.upload_progress').fadeOut('slow', function () {
                        $(this).text('0%');
                    });
                }
            },
            success: function (data) {
                //REMOVE LOAD
                form.find('.form_load').fadeOut('slow', function () {
                    //EXIBE CALLBACKS
                    if (data.trigger) {
                        var CallBackPresent = form.find('.callback_return');
                        if (CallBackPresent.length) {
                            CallBackPresent.html(data.trigger);
                            $('.trigger_ajax').fadeIn('slow');
                        } else {
                            Trigger(data.trigger);
                        }
                    }

                    //REDIRECIONA
                    if (data.redirect) {
                        window.setTimeout(function () {
                            window.location.href = data.redirect;
                        }, 1500);
                    }

                    //INTERAGE COM TINYMCE
                    if (data.tinyMCE) {
                        tinyMCE.activeEditor.insertContent(data.tinyMCE);
                        $('.workcontrol_imageupload').fadeOut('slow', function () {
                            $('.workcontrol_imageupload .image_default').attr('src', '../tim.php?src=admin/_img/no_image.jpg&w=500&h=300');
                        });
                    }

                    if (data.gallery) {
                        form.find('.gallery').fadeTo('300', '0.5', function () {
                            $(this).html($(this).html() + data.gallery).fadeTo('300', '1');
                        });
                    }

                    if (data.content) {
                        form.find('.j_content').fadeTo('300', '0.5', function () {
                            $(this).html(data.content).fadeTo('300', '1');
                        });
                    }

                    if (data.view) {
                        $('.wc_view').attr('href', data.view);
                    }

                    //CLEAR INPUT FILE
                    form.find('input[type="file"]').val('');
                });
            }
        });
        return false;
    });

    //Ocultra Trigger clicada
    $('html').on('click', '.trigger_ajax, .trigger_modal', function () {
        $(this).fadeOut('slow', function () {
            $(this).remove();
        });
    });

    //############# POSTS
    //CAPA VIEW
    $('.wc_loadimage').change(function () {
        var input = $(this);
        var target = $('.' + input.attr('name'));
        var fileDefault = target.attr('default');

        if (!input.val()) {
            target.fadeOut('fast', function () {
                $(this).attr('src', fileDefault).fadeIn('slow');
            });
            return false;
        }

        if (this.files && this.files[0].type.match('image.*')) {
            var reader = new FileReader();
            reader.onload = function (e) {
                target.fadeOut('fast', function () {
                    $(this).attr('src', e.target.result).width('100%').height('100%').fadeIn('fast');
                });
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            Trigger('<div class="trigger trigger_alert trigger_ajax"><b class="icon-warning">ERRO AO SELECIONAR:</b> O arquivo <b>' + this.files[0].name + '</b> não é válido! <b>Selecione uma imagem JPG ou PNG!</b></div>');
            target.fadeOut('fast', function () {
                $(this).attr('src', fileDefault).fadeIn('slow');
            });
            input.val('');
            return false;
        }
    });

    //############# PRODUTOS
    //GALLERY IMAGE REMOVE
    $('.pdt_single_image').on('click', 'img', function () {
        var imgDef = $(this);
        var imgDel = $(this).attr('id');
        var Delete = confirm('Deseja DELETAR essa imagem?');
        if (Delete === true) {
            $.post('_ajax/Products.ajax.php', {callback: 'Products', callback_action: 'gbremove', img: imgDel}, function (data) {
                imgDef.fadeOut('fast', function () {
                    $(this).remove();
                });
            });
        }
    });

    //############# GERAIS
    //DELETE CONFIRM
    $('.j_delete_action').click(function () {
        var RelTo = $(this).attr('rel');
        $(this).fadeOut('fast', function () {
            $('.' + RelTo + '[id="' + $(this).attr('id') + '"] .j_delete_action_confirm:eq(0)').fadeIn('fast');
            ;
        });
    });

    //DELETE CONFIRM ACTION
    $('.j_delete_action_confirm').click(function () {
        var Prevent = $(this);
        var DelId = $(this).attr('id');
        var RelTo = $(this).attr('rel');
        var Callback = $(this).attr('callback');
        var Callback_action = $(this).attr('callback_action');
        $.post('_ajax/' + Callback + '.ajax.php', {callback: Callback, callback_action: Callback_action, del_id: DelId}, function (data) {
            if (data.trigger) {
                Trigger(data.trigger);
                $('.' + RelTo + '[id="' + Prevent.attr('id') + '"] .j_delete_action_confirm:eq(0)').fadeOut('fast', function () {
                    $('.' + RelTo + '[id="' + Prevent.attr('id') + '"] .j_delete_action:eq(0)').fadeIn('slow');
                });
            } else {
                $('.' + RelTo + '[id="' + DelId + '"]').fadeOut('slow');
            }

            //REDIRECIONA
            if (data.redirect) {
                window.setTimeout(function () {
                    window.location.href = data.redirect;
                }, 1500);
            }
        }, 'json');
    });

    //MODAL UPLOAD
    $('.workcontrol_imageupload_close').click(function () {
        $("div#" + $(this).attr("id")).fadeOut("fast");
    });

    //SEARCH REMOVE
    $(".wc_delete_search").click(function () {
        var DeleteSearch = confirm("Ao continuar todos os dados de pesquisa seram removidos!");
        if (DeleteSearch !== true) {
            return false;
        }
    });

    //CONFIG CLEAR
    $(".wc_resetconfig").click(function () {
        var DeleteSearch = confirm("Ao continuar todas as configurações serão setadas com o valor das constantes!");
        if (DeleteSearch !== true) {
            return false;
        }
    });
});

//jQUERY BEFORE LOAD
$(window).load(function () {
    //Controla Contents
    $('.dashboard_nav').css('height', $('.dashboard').outerHeight());
    $(window).scroll(function () {
        $('.dashboard_nav').css('height', $('.dashboard').outerHeight());
    });
});

//FUNÇÕES
//############## DASHBOARD STATS
function Dashboard() {
    $.post('_ajax/Dashboard.ajax.php', {callback_action: 'siteviews'}, function (data) {
        $('.wc_useronline').text(data.useron);
        $('.wc_viewsusers b').text(data.users);
        $('.wc_viewsviews b').text(data.views);
        $('.wc_viewspages b').text(data.pages);
        $('.wc_viewsstats b').text(data.stats);
    }, 'json');
}

function OnlineNow() {
    $.post('_ajax/Dashboard.ajax.php', {callback_action: 'onlinenow'}, function (data) {
        $('.wc_onlinenow').html(data.data);
    }, 'json');
}

//############## MODAL MESSAGE
function Trigger(Message) {
    $('.trigger_ajax').fadeOut('fast', function () {
        $(this).remove();
    });

    $('.dashboard_main').before("<div class='trigger_modal'>" + Message + "</div>");
    $('.trigger_ajax').fadeIn('slow');
}

