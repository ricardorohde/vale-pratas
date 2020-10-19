function inserirBanner() {
    $.ajax({
        url: "../control/SalvarBanner.php",
        type: "POST",
        data: $("#fbanner").serialize(),
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            if (data.situacao === true) {
                swal("Banner cadastrada", data.mensagem, "success");
                procurarBanner(true);
            } else if (data.situacao === false) {
                swal("Erro ao cadastrar", data.mensagem, "error");
            }
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro ao cadastrar", "Erro causado por:" + errorThrown, "error");
        }
    });
}

function atualizarBanner() {
    $.ajax({
        url: "../control/SalvarBanner.php",
        type: "POST",
        data: $("#fbanner").serialize(),
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            if (data.situacao === true) {
                swal("Banner atualizada", data.mensagem, "success");
                procurarBanner(true);
            } else if (data.situacao === false) {
                swal("Erro ao atualizar", data.mensagem, "error");
            }
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro ao atualizar", "Erro causado por:" + errorThrown, "error");
        }
    });
}

function excluirBanner() {
    if (window.confirm("Deseja realmente excluir esse banner?")) {
        if (document.getElementById("codbanner").value !== null && document.getElementById("codbanner").value !== "") {
            $.ajax({
                url: "../control/ExcluirBanner.php",
                type: "POST",
                data: {codbanner: document.getElementById("codbanner").value},
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    if (data.situacao === true) {
                        swal("Banner excluida", data.mensagem, "success");
                        procurarBanner(true);
                    } else if (data.situacao === false) {
                        swal("Erro ao excluir", data.mensagem, "error");
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {
                    swal("Erro ao excluir", "Erro causado por:" + errorThrown, "error");
                }
            });
        } else {
            swal("Campo em branco", "Por favor escolha o banner para excluir!", "error");
        }
    }
}

function excluir2Banner(codbanner) {
    swal({
        title: "Confirma exclusão?",
        text: "Você não poderá mais visualizar as informações dessa banner!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sim, exclua ele!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            if (codbanner !== null && codbanner !== "") {
                $.ajax({
                    url: "../control/ExcluirBanner.php",
                    type: "POST",
                    data: {codbanner: codbanner},
                    dataType: 'json',
                    success: function (data, textStatus, jqXHR) {
                        if (data.situacao === true) {
                            swal("Banner excluida", data.mensagem, "success");
                            procurarBanner(true);
                        } else if (data.situacao === false) {
                            swal("Erro ao excluir", data.mensagem, "error");
                        }
                    }, error: function (jqXHR, textStatus, errorThrown) {
                        swal("Erro ao excluir", "Erro causado por:" + errorThrown, "error");
                    }
                });
            } else {
                swal("Campo em branco", "Por favor escolha o banner para excluir!", "error");
            }
        }
    });
}

function procurarBanner(acao) {
    $("#carregando").show();
    $.ajax({
        url: "../control/ProcurarBanner.php",
        type: "POST",
        data: $("#fPbanner").serialize(),
        dataType: 'text',
        success: function (data, textStatus, jqXHR) {
            if (acao == false && data === "") {
                swal("Atenção", "Nada encontrado de banners!", "error");
            }
            document.getElementById("listagemBanner").innerHTML = data;
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro ao excluir", "Erro causado por:" + errorThrown, "error");
        }
    });
    $("#carregando").hide();
}

function abreRelatorioBanner() {
    document.getElementById("tipo").value = "pdf";
    document.getElementById("fPbanner").submit();
}

function abreRelatorioBanner2() {
    document.getElementById("tipo").value = "xls";
    document.getElementById("fPbanner").submit();
}

$(function () {
   
    $("#fbanner").submit(function () {
        $(".progress").css("visibility", "visible");
    });

    var bar = $('.bar');
    var percent = $('.percent');
    var status = $('#status');

    $('#fbanner').ajaxForm({
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
                if ($("#codbanner").val() !== null && $("#codbanner").val() !== "") {
                    swal("Alteração", data.mensagem, "success");
                    if (data.imagem !== null && data.imagem !== "") {
                        $("#imagemCarregada").html("<img width='150' src='../arquivos/" + data.imagem + "'  alt='Imagem usuário'/>");
                    }

                } else {
                    swal("Cadastro", data.mensagem, "success");
                }
                procurarBanner(true);
            } else if (data.situacao === false) {
                swal("Erro", data.mensagem, "error");
            }
        }
    });
});
