function procurarConta(acao) {
    $.ajax({
        url: "../control/ProcurarConta.php",
        type: "POST",
        data: $("#fpconta").serialize(),
        dataType: 'text',
        success: function (data, textStatus, jqXHR) {
            if (acao == false && data == "") {
                swal("Atenção", "Nada encontrado!", "error");
            }
            document.getElementById("listagemConta").innerHTML = data;
        }, error: function (jqXHR, textStatus, errorThrown) {
            swal("Erro", "Erro causado por:" + errorThrown, "error");
        }
    });
}

function abreRelatorioConta(tipo) {
    var tipos = ['pdf', 'xls'];
    document.getElementById("tipo").value = tipos[tipo];
    document.getElementById("fpnewsletter").submit();
}
