function salvarNewsletter() {
    $.ajax({
        url: "../control/SalvarNewsletter.php",
        type: "POST",
        data: $("#fnewsletter").serialize(),
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            if (data.situacao === true) {
                swal("Newsletter cadastrada", data.mensagem, "success");
                procurarNewsletter(true);
            } else if (data.situacao === false) {
                swal("Erro ao cadastrar", data.mensagem, "error");
            }
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro ao cadastrar", "Erro causado por:" + errorThrown, "error");
        }
    });
}

function excluirNewsletter(codigo) {
    if(codigo == undefined){
        codigo = document.getElementById("codnewsletter").value;
    }
    if (window.confirm("Deseja realmente excluir essa newsletter?")) {
        if (codigo != null && codigo != "") {
            $.ajax({
                url: "../control/ExcluirNewsletter.php",
                type: "POST",
                data: {codnewsletter: codigo},
                dataType: 'json',
                success: function (data, textStatus, jqXHR) {
                    if (data.situacao === true) {
                        swal("Newsletter excluida", data.mensagem, "success");
                        procurarNewsletter(true);
                    } else if (data.situacao === false) {
                        swal("Erro", data.mensagem, "error");
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {
                    swal("Erro", "Erro causado por:" + errorThrown, "error");
                }
            });
        } else {
            swal("Campo em branco", "Por favor escolha a página para excluir!", "error");
        }
    }
}

function procurarNewsletter(acao) {
    $("#carregando").show();
    $.ajax({
        url: "../control/ProcurarNewsletter.php",
        type: "POST",
        data: $("#fpnewsletter").serialize(),
        dataType: 'text',
        success: function (data, textStatus, jqXHR) {
            if (acao == false && data == "") {
                swal("Atenção", "Nada encontrado!", "error");
            }
            document.getElementById("listagemNewsletter").innerHTML = data;
            
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro ao procurar", "Erro causado por:" + errorThrown, "error");
        }
    });
    $("#carregando").hide();
}

function abreRelatorioNewsletter() {
    document.getElementById("tipo").value = "pdf";
    document.getElementById("fpnewsletter").submit();
}

function abreRelatorioNewsletter2() {
    document.getElementById("tipo").value = "xls";
    document.getElementById("fpnewsletter").submit();
}