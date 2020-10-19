function salvarVenda() {
    $.ajax({
        url: "/control/SalvarVenda.php",
        type: "POST",
        data: $("#fvenda").serialize(),
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            if (data.situacao === true) {
                swal("Venda salva", data.mensagem, "success");
            } else if (data.situacao === false) {
                swal("Erro", data.mensagem, "error");
            }
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro", "Erro causado por:" + errorThrown, "error");
        }
    });
}

function atualizarVenda() {
    if ($("#nomeVenda").val() !== null && $("#nomeVenda").val() !== "") {
        $.ajax({
            url: "../control/SalvarVenda.php",
            type: "POST",
            data: $("#fvenda").serialize(),
            dataType: 'json',
            success: function (data, textStatus, jqXHR) {
                if (data.situacao === true) {
                    swal("Tipo Produto atualizada", data.mensagem, "success");
                    procurarVenda(true);
                } else if (data.situacao === false) {
                    swal("Erro", data.mensagem, "error");
                }
            }, error: function (jqXHR, textStatus, errorThrown) {
                swal("Erro ao atualizar", "Erro causado por:" + errorThrown, "error");
            }
        });
    } else if ($("#nomeVenda").val() === null || $("#nomeVenda").val() === "") {
        swal("Campo em branco", "Por favor escolha um nome!", "error");
    }
}

function excluirVenda() {
    if (window.confirm("Deseja realmente excluir essa pagina?")) {
        if (document.getElementById("codvenda").value !== null && document.getElementById("codvenda").value !== "") {
            $.ajax({
                url: "../control/ExcluirVenda.php",
                type: "POST",
                data: {codvenda: document.getElementById("codvenda").value},
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    if (data.situacao === true) {
                        swal("Tipo Produto excluida", data.mensagem, "success");
                        procurarVenda(true);
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

function excluir2Venda(codvenda) {
    if (window.confirm("Deseja realmente excluir essa pagina?")) {
        if (codvenda !== null && codvenda !== "") {
            $.ajax({
                url: "../control/ExcluirVenda.php",
                type: "POST",
                data: {codvenda: codvenda},
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    if (data.situacao === true) {
                        swal("Tipo Produto excluido", data.mensagem, "success");
                        procurarVenda(true);
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

function excluirVendas() {
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
                url: "../control/ExcluirVenda.php",
                type: "POST",
                data: $("#fProcurarNivel").serialize(),
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    if (data.situacao == true) {
                        swal("Tipo Produto excluida", data.mensagem, "success");
                        procurarVenda(true);
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

function setaEditarVenda(venda) {
    document.getElementById("codvendaTipo").value = venda[0];
    document.getElementById("nomeVenda").value = venda[1];
    document.getElementById("descontoVenda").value = venda[2];

    $("#btatualizarVenda").css("display", "");
    $("#btexcluirVenda").css("display", "");
    $("#btinserirVenda").css("display", "none");
}

function novoVenda() {
    document.getElementById("codvendaTipo").value = '';
    document.getElementById("nomeVenda").value = '';
    document.getElementById("descontoVenda").value = '';

    $("#btatualizarVenda").css("display", "none");
    $("#btexcluirVenda").css("display", "none");
    $("#btinserirVenda").css("display", "");
}

function procurarVenda(acao) {
    $.ajax({
        url: "../control/ProcurarVenda.php",
        type: "POST",
        dataType: 'text',
        success: function (data, textStatus, jqXHR) {
            if (acao == false && data == "") {
                swal("Atenção", "Nada encontrado!", "error");
            }
            document.getElementById("listagemVenda").innerHTML = data;
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro ao procurar", "Erro causado por:" + errorThrown, "error");
        }
    });
}

function procurarPagSeguro() {
    $.ajax({
        url: "../control/ProcurarVendaPagSeguro.php",
        type: "POST",
        dataType: 'text',
        data: $("#fpvenda").serialize(),
        success: function (data, textStatus, jqXHR) {
            document.getElementById("listagemVenda").innerHTML = data;
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro ao procurar", "Erro causado por:" + errorThrown, "error");
        }
    });
}

function marcarTudoVenda() {
    if ($(".checkbox_pagina").prop("checked") == true) {
        $(".checkbox_pagina").prop("checked", false);
    } else {
        $(".checkbox_pagina").prop("checked", true);
    }
}
