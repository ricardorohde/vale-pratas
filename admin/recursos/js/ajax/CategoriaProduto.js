function inserirCategoriaProduto() {
    if ($("#nomeCategoriaProduto").val() !== null && $("#nomeCategoriaProduto").val() !== "") {
        $.ajax({
            url: "../control/SalvarCategoriaProduto.php",
            type: "POST",
            data: $("#fcategoria").serialize(),
            dataType: 'json',
            success: function (data, textStatus, jqXHR) {
                if (data.situacao === true) {
                    swal("Cat. Produto cadastrada", data.mensagem, "success");
                    procurarCategoriaProduto(true);
                } else if (data.situacao === false) {
                    swal("Erro", data.mensagem, "error");
                }
            }, error: function (jqXHR, textStatus, errorThrown) {
                swal("Erro", "Erro causado por:" + errorThrown, "error");
            }
        });
    } else if ($("#nomeCategoriaProduto").val() === null || $("#nomeCategoriaProduto").val() === "") {
        swal("Campo em branco", "Por favor escolha um nome!", "error");
    }
}

function atualizarCategoriaProduto() {
    if ($("#nomeCategoriaProduto").val() !== null && $("#nomeCategoriaProduto").val() !== "") {
        $.ajax({
            url: "../control/SalvarCategoriaProduto.php",
            type: "POST",
            data: $("#fcategoria").serialize(),
            dataType: 'json',
            success: function (data, textStatus, jqXHR) {
                if (data.situacao === true) {
                    swal("Cat. Produto atualizada", data.mensagem, "success");
                    procurarCategoriaProduto(true);
                } else if (data.situacao === false) {
                    swal("Erro", data.mensagem, "error");
                }
            }, error: function (jqXHR, textStatus, errorThrown) {
                swal("Erro ao atualizar", "Erro causado por:" + errorThrown, "error");
            }
        });
    } else if ($("#nomeCategoriaProduto").val() === null || $("#nomeCategoriaProduto").val() === "") {
        swal("Campo em branco", "Por favor escolha um nome!", "error");
    }
}

function excluirCategoriaProduto() {
    if (window.confirm("Deseja realmente excluir essa pagina?")) {
        if (document.getElementById("codcategoria").value !== null && document.getElementById("codcategoria").value !== "") {
            $.ajax({
                url: "../control/ExcluirCategoriaProduto.php",
                type: "POST",
                data: {codcategoria: document.getElementById("codcategoria").value},
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    if (data.situacao === true) {
                        swal("Cat. Produto excluida", data.mensagem, "success");
                        procurarCategoriaProduto(true);
                    } else if (data.situacao === false) {
                        swal("Erro", data.mensagem, "error");
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {
                    swal("Erro ao excluir", "Erro causado por:" + errorThrown, "error");
                }
            });
        } else {
            swal("Campo em branco", "Por favor escolha a produto para excluir!", "error");
        }
    }
}

function excluir2CategoriaProduto(codcategoria) {
    if (window.confirm("Deseja realmente excluir essa pagina?")) {
        if (codcategoria !== null && codcategoria !== "") {
            $.ajax({
                url: "../control/ExcluirCategoriaProduto.php",
                type: "POST",
                data: {codcategoria: codcategoria},
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    if (data.situacao === true) {
                        swal("Cat. Produto excluida", data.mensagem, "success");
                        procurarCategoriaProduto(true);
                    } else if (data.situacao === false) {
                        swal("Erro", data.mensagem, "error");
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {
                    swal("Erro ao excluir", "Erro causado por:" + errorThrown, "error");
                }
            });
        } else {
            swal("Campo em branco", "Por favor escolha a produto para excluir!", "error");
        }
    }
}

function excluirCategoriaProdutos() {
    swal({
        title: "Confirma exclusão?",
        text: "Você não poderá mais visualizar as informações dessa cat. produto!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sim, exclua ele!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: "../control/ExcluirCategoriaProduto.php",
                type: "POST",
                data: $("#fProcurarNivel").serialize(),
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    if (data.situacao == true) {
                        swal("Categoria Produto excluida", data.mensagem, "success");
                        procurarCategoriaProduto(true);
                    } else if (data.situacao == false) {
                        swal("Erro ao excluir", data.mensagem, "error");
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {
                    swal("Erro ao excluir", "Erro causado por:" + errorThrown, "error");
                }
            });
        }
    });
}

function setaEditarCategoriaProduto(categoria) {
    document.getElementById("codcategoriaCategoria").value = categoria[0];
    document.getElementById("nomeCategoriaProduto").value = categoria[1];
    document.getElementById("descontoCategoriaProduto").value = categoria[2];

    document.getElementById("visualiza_imagem_categoria").href = "../arquivos/" + categoria[3];
    $("#visualiza_imagem_categoria").css("display", "");
    $("#btexcluirCategoriaProduto").css("display", "");
}

function novoCategoriaProduto(){
    document.getElementById("codcategoriaCategoria").value = '';
    document.getElementById("nomeCategoriaProduto").value = '';
    document.getElementById("descontoCategoriaProduto").value = '';

    $("#btexcluirCategoriaProduto").css("display", "none");
}

function procurarCategoriaProduto(acao) {
    $("#carregando").show();
    $.ajax({
        url: "../control/ProcurarCategoriaProduto.php",
        type: "POST",
        dataType: 'text',
        success: function (data, textStatus, jqXHR) {
            if (acao == false && data == "") {
                swal("Atenção", "Nada encontrado!", "error");
            }
            document.getElementById("listagemCategoriaProduto").innerHTML = data;
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro ao procurar", "Erro causado por:" + errorThrown, "error");
        }
    });
    $("#carregando").hide();
}

function marcarTudoCategoriaProduto() {
    if ($(".checkbox_pagina").prop("checked") == true) {
        $(".checkbox_pagina").prop("checked", false);
    } else {
        $(".checkbox_pagina").prop("checked", true);
    }
}

$(document).ready(function () {
    procurarCategoriaProduto();
    
    $("#fcategoria").submit(function () {
        $(".progress").css("visibility", "visible");
    });

    var bar = $('.bar');
    var percent = $('.percent');
    var status = $('#status');

    $('#fcategoria').ajaxForm({
        beforeSend: function () {
            status.empty();
            var percentVal = '0%';
            bar.width(percentVal)
            percent.html(percentVal);
        },
        uploadProgress: function (event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            bar.width(percentVal)
            percent.html(percentVal);
        },
        success: function () {
            var percentVal = '100%';
            bar.width(percentVal)
            percent.html(percentVal);
        },
        complete: function (xhr) {
            var data = JSON.parse(xhr.responseText);
            if (data.situacao === true) {
                if ($("#codcategoria").val() !== null && $("#codcategoria").val() !== "") {
                    swal("Alteração", data.mensagem, "success");
                } else {
                    swal("Cadastro", data.mensagem, "success");
                }
                procurarCategoriaProduto();
            } else if (data.situacao === false) {
                swal("Erro", data.mensagem, "error");
            }
        }
    });    
});