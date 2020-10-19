function inserir(){
    $.ajax({
        url : "../control/InserirEmpresa.php",
        type: "POST",
        data : $("#fempresa").serialize(),
        dataType: 'json',
        success: function(data, textStatus, jqXHR){
            if(data.situacao === true){
                swal("Empresa cadastrada", data.mensagem, "success");
            }else if(data.situacao === false){
                swal("Erro ao cadastrar", data.mensagem, "error");
            }
        },error: function (jqXHR, textStatus, errorThrown){
            swal("Erro ao cadastrar", "Erro causado por:" + errorThrown, "error");
        }
    });  
}

function atualizar(){
    $.ajax({
        url : "../control/AtualizarEmpresa.php",
        type: "POST",
        data : $("#fempresa").serialize(),
        dataType: 'json',
        success: function(data, textStatus, jqXHR){
            if(data.situacao === true){
                swal("Empresa atualizada", data.mensagem, "success");
            }else if(data.situacao === false){
                swal("Erro ao atualizar", data.mensagem, "error");
            }
        },error: function (jqXHR, textStatus, errorThrown){
            swal("Erro ao atualizar", "Erro causado por:" + errorThrown, "error");
        }
    });      
}

function excluir(){
    if (window.confirm("Deseja realmente excluir esse empresa?")) {
        if(document.getElementById("codempresa").value !== null && document.getElementById("codempresa").value !== ""){
            $.ajax({
                url : "../control/ExcluirEmpresa.php",
                type: "POST",
                data : {codempresa: document.getElementById("codempresa").value},
                dataType: 'json',
                success: function(data, textStatus, jqXHR){
                    if(data.situacao === true){
                        swal("Empresa excluida", data.mensagem, "success");
                    }else if(data.situacao === false){
                        swal("Erro ao excluir", data.mensagem, "error");
                    }
                },error: function (jqXHR, textStatus, errorThrown){
                    swal("Erro ao excluir", "Erro causado por:" + errorThrown, "error");
                }
            });          
        }else{
            swal("Campo em branco", "Por favor escolha uma empresa para excluir!", "error");
        }
    }
}

function procurarEmpresa(acao){
    $("#carregando").show();
    $.ajax({
        url : "../control/ProcurarEmpresa2.php",
        type: "POST",
        data : $("#fpempresa").serialize(),
        dataType: 'text',
        success: function(data, textStatus, jqXHR){
            if(acao === false && data === ""){
                swal("Atenção", "Nada encontrado!", "error");
            }
            if(document.getElementById("listagemEmpresa") != null){
                document.getElementById("listagemEmpresa").innerHTML = data;
            }
        },error: function (jqXHR, textStatus, errorThrown){
            swal("Erro ao procurar", "Erro causado por:" + errorThrown, "error");
        }
    });  
    $("#carregando").hide();
}

function excluir2Empresa(codempresa){
    if (window.confirm("Deseja realmente excluir esse empresa?")) {
        if(codempresa !== null && codempresa !== ""){
            $.ajax({
                url : "../control/ExcluirEmpresa.php",
                type: "POST",
                data : {codempresa: codempresa},
                dataType: 'json',
                success: function(data, textStatus, jqXHR){
                    if(data.situacao === true){
                        swal("Empresa excluida", data.mensagem, "success");
                    }else if(data.situacao === false){
                        swal("Erro ao excluir", data.mensagem, "error");
                    }
                },error: function (jqXHR, textStatus, errorThrown){
                    swal("Erro ao excluir", "Erro causado por:" + errorThrown, "error");
                }
            });          
        }else{
            swal("Campo em branco", "Por favor escolha uma empresa para excluir!", "error");
        }
    }
}

function btNovo(){
    location.href="Empresa.php";
}

function abreRelatorio(){
    document.getElementById("tipoRel").value = "pdf";
    document.getElementById("fpempresa").submit();
}

function abreRelatorio2(){
    document.getElementById("tipoRel").value = "xls";
    document.getElementById("fpempresa").submit();
}


/**daqui para baixa responsável pelo ajax de inserir ou atualizar empresa e também pelo upload sem redirecionar página*/
(function () {
    var bar = $('.bar');
    var percent = $('.percent');
    var status = $('#status');
    
    $("#fempresa").submit(function(){
        $(".progress").css("visibility", "visible");
    });
    
    $('#fempresa').ajaxForm({
        beforeSend: function () {
            status.empty();
            bar.width('0%');
            percent.html('0%');
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
            if(data.situacao === true){
                if(data.codempresa != null && data.codempresa != ""){
                    swal("Cadastro", data.mensagem, "success");
                }else{
                    swal("Alteração", data.mensagem, "success");
                }
                if(data.imagem !== null && data.imagem !== ""){
                    $("#imagemCarregada").html("<img width='150' src='../arquivos/"+data.imagem+"'  alt='Imagem usuário'/>");
                }
                if(data.codempresa != null && data.codempresa != ""){
                    var urlEmpresa = "?codempresa=" + data.codempresa;
                    if(data.codramo != null && data.codramo != ""){
                        urlEmpresa += "&codramo=" + data.codramo;
                    }
                    if($("#tipo").val() != null && $("#tipo").val() != ""){
                        urlEmpresa += "&tipo=" + $("#tipo").val();
                    }
                    setTimeout('location.href="Empresa.php'+urlEmpresa+'";', 1500);
                }
            }else if(data.situacao === false){
                swal("Erro", data.mensagem, "error");
            }
        }
    });

})();