function inserirProduto() {
    $.ajax({
        url: "../control/SalvarProduto.php",
        type: "POST",
        data: $("#fproduto").serialize(),
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            if (data.situacao === true) {
                swal("Produto cadastrada", data.mensagem, "success");
                procurarProduto(true);
            } else if (data.situacao === false) {
                swal("Erro ao cadastrar", data.mensagem, "error");
            }
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro ao cadastrar", "Erro causado por:" + errorThrown, "error");
        }
    });
}

function atualizarProduto() {
    $.ajax({
        url: "../control/SalvarProduto.php",
        type: "POST",
        data: $("#fproduto").serialize(),
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            if (data.situacao === true) {
                swal("Produto atualizada", data.mensagem, "success");
                procurarProduto(true);
            } else if (data.situacao === false) {
                swal("Erro ao atualizar", data.mensagem, "error");
            }
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro ao atualizar", "Erro causado por:" + errorThrown, "error");
        }
    });
}

function excluirProduto() {
    if (window.confirm("Deseja realmente excluir esse produto?")) {
        if (document.getElementById("codproduto").value !== null && document.getElementById("codproduto").value !== "") {
            $.ajax({
                url: "../control/ExcluirProduto.php",
                type: "POST",
                data: {codproduto: document.getElementById("codproduto").value},
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    if (data.situacao === true) {
                        swal("Produto excluida", data.mensagem, "success");
                        procurarProduto(true);
                    } else if (data.situacao === false) {
                        swal("Erro ao excluir", data.mensagem, "error");
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

function excluir2Produto(codproduto) {
    swal({
        title: "Confirma exclusão?",
        text: "Você não poderá mais visualizar as informações dessa produto!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sim, exclua ele!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            if (codproduto !== null && codproduto !== "") {
                $.ajax({
                    url: "../control/ExcluirProduto.php",
                    type: "POST",
                    data: {codproduto: codproduto},
                    dataType: 'json',
                    success: function (data, textStatus, jqXHR) {
                        if (data.situacao === true) {
                            swal("Produto excluida", data.mensagem, "success");
                            procurarProduto(true);
                        } else if (data.situacao === false) {
                            swal("Erro ao excluir", data.mensagem, "error");
                        }
                    }, error: function (jqXHR, textStatus, errorThrown) {
                        swal("Erro ao excluir", "Erro causado por:" + errorThrown, "error");
                    }
                });
            } else {
                swal("Campo em branco", "Por favor escolha a produto para excluir!", "error");
            }
        }
    });
}

function procurarProduto(acao) {
    $("#carregando").show();
    $.ajax({
        url: "../control/ProcurarProduto.php",
        type: "POST",
        data: $("#fPproduto").serialize(),
        dataType: 'text',
        success: function (data, textStatus, jqXHR) {
            if (acao == false && data === "") {
                swal("Atenção", "Nada encontrado de produtos!", "error");
            }
            document.getElementById("listagem").innerHTML = data;
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro ao excluir", "Erro causado por:" + errorThrown, "error");
        }
    });
    $("#carregando").hide();
}

function procurarProdutoPaginacao(pagina, codtipo) {
    $("#carregando").show();
    $(".linhaProduto").remove();
    $.ajax({
        url: "../control/PaginacaoProdutos.php",
        type: "POST",
        data: {pagina: pagina, codtipo: codtipo},
        dataType: 'text',
        success: function (data, textStatus, jqXHR) {
            document.getElementById("listagemProdutosPaginacao").innerHTML = data;
            
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro", "Erro causado por:" + errorThrown, "error");
        }
    });
    $("#carregando").hide();
}

function abreRelatorioProduto() {
    document.getElementById("tipo").value = "pdf";
    document.getElementById("fPproduto").submit();
}

function abreRelatorioProduto2() {
    document.getElementById("tipo").value = "xls";
    document.getElementById("fPproduto").submit();
}

$(function () {
   
    $("#fproduto").submit(function () {
        $(".progress").css("visibility", "visible");
    });

    var bar = $('.bar');
    var percent = $('.percent');
    var status = $('#status');

    $('#fproduto').ajaxForm({
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
                if ($("#codproduto").val() !== null && $("#codproduto").val() !== "") {
                    swal("Alteração", data.mensagem, "success");
                    if (data.imagem !== null && data.imagem !== "") {
                        $("#imagemCarregada").html("<img width='150' src='../arquivos/" + data.imagem + "'  alt='Imagem usuário'/>");
                    }

                } else {
                    swal("Cadastro", data.mensagem, "success");
                }
                procurarProduto(true);
            } else if (data.situacao === false) {
                swal("Erro", data.mensagem, "error");
            }
        }
    });
});
