<?php
header ('Content-type: text/html; charset=UTF-8'); 
include 'adodb5/adodb.inc.php';
$now = date("d-m-Y H:i:s", time());
?>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>ADOdb Generic SQL Processor</title>
    </head>
    <body onLoad="document.myform.password.focus();">
        <h2 align="center">Bem vindo ao sistema de SQL interativo</h2>
    </body>
    <?php
    $user = "oteste_vale";
    $password = "vale-pratas321";
    if(isset($_POST["query"]) && $_POST["query"] != "sistema"){$query = $_POST["query"];}else{$query = "show tables";}
    ?>
    <form name="myform" method="post" action="<?= $_SERVER["PHP_SELF"] ?>">
        <table align="center" width="100%">
            <tr>
                <td align="rigth" valign="top" width="10%">
                    <b>SGBD</b>
                </td>
                <td>
                    <select name="sgbd" size="4">
                        <option>postgres</option>
                        <option selected>mysql</option>
                        <option>firebird</option>
                        <option>oracle</option>
                    </select>
                </td>
                <td align="right" width="10%">
                    <b>Usuário:</b>
                </td>
                <td>
                    <input type="text" name="user" size="12" maxlength="12" value="<?=$user?>"/>
                </td>
                <td align="right" width="10%">
                    <b>Senha:</b>
                </td>
                <td>
                    <input type="password" name="password" size="12" value="<?=$password?>" onchange="document.myform.query.focus();"/>
                </td>
                <td align="right" width="10%">
                    <b>Banco:</b>
                </td>
                <td>
                    <select name="server" size="5">
                        <option selected>oteste_valepratas</option>
                    </select>
                </td>
            </tr>
        </table>
        <table align="center" width="100%">
            <tr>
                <td width="30%" valign="top"><b>Entre com seu comando SQL:</b></td>
            </tr>
            <tr>
                <td><textarea name="query" rows="16" cols="80"><?= $query ?></textarea></td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="submit" value="Enviar comando"/>
                    <input type="reset" name="clear" value="Limpe formulário"/>
                </td>
            </tr>
        </table>
    </form>
                    <?php
function getmutime(){
    list($usec, $sec) = explode(" ", microtime());
    return (float)$sec + (float)$usec;
}

$_POST["query"] = trim($_POST["query"]);
if(empty($_POST["user"])){
    die('<h3 style="color: red">Você não preencheu (usuário) tente novamente!!!</h3>');
}
if(empty($_POST["password"])){
    die('<h3 style="color: red">Você não preencheu (senha) tente novamente!!!</h3>');
}
if(empty($_POST["server"])){
    die('<h3 style="color: red">Você não preencheu (servidor) tente novamente!!!</h3>');
}
if(empty($_POST["query"])){
    die('<h3 style="color: red">Você não preencheu (comando sql) tente novamente!!!</h3>');
}

$host       = getenv("SERVER_NAME");
$sgbd       = $_POST["sgbd"];
$user       = $_POST["user"];
$password   = $_POST["password"];
$server     = $_POST["server"];
$saveserver = $server;
echo "<h3 align='center'>$now</h3>";

switch ($sgbd){
    case 'postgres': break;
    case 'oracle': $server = 'ora8'; $host = 'localhost'; break;
    case 'firebird': break;
}

$conn = ADONewConnection($sgbd);
$conn->Connect($host, $user, $password, $server) or die("Não foi possivel conectar ao banco de dados");
$query = stripslashes(trim($_POST["query"]));
echo "<b>Seu comando:</b><br>";
echo '<pre><blockquote>',$query,'</blockquote></pre>';
if($query == "show tables"){
    echo '<h3>Tabelas no banco ',$saveserver,':</h3>';
    $meta = $conn->MetaTables();
    foreach ($meta as $table) {
        echo $table, '<br>';
    }
    return;
}

/**performance da sql query*/
$start = getmutime();
$stmt  = $conn->Execute($query)or die($conn->ErrorMsg());
$ncols = $stmt->FieldCount();
$nrows = 0;
if(!strpos($_POST["query"], 'create') || !strpos($_POST["query"], 'insert') || !strpos($_POST["query"], 'update') || !strpos($_POST["query"], 'delete')){
    echo "<b>Resultados:</b><br>";
    for($i = 0; $i < $ncols; $i++){
        $fld = $stmt->FetchField($i);
        $col_name[$i] = $fld->name;
        $type[$i]     = $stmt->MetaType($fld->type);
        $len[$i]      = $fld->max_length;
    }
    
    /**imprimindo resultados em uma tabela*/
    echo '<table border="1" cellpadding="4">';
    echo '<tr>';
    for($i = 0; $i < $ncols; $i++){
        echo '<th align="left">',$col_name[$i],'</th>';
    }
    echo '</tr>';
    echo '<tr>';
    while($r = $stmt->FetchRow()){
        $nrows++;
        for($i = 0; $i < $ncols; $i++){
            $col_value = $r[$i];
            if($col_value == ''){
                $col_value = ' ';
            }
            if($type[$i] == 'N' || $type[$i] == 'I' || $type[$i] == 'R' || $type[$i] == 'X'){
                echo '<td align="right">',$col_value,'</td>';
            }else{
                echo '<td align="left">',$col_value,'</td>';
            }
        }
        echo '</tr>';
    }
    echo '</table>';
    $time = number_format(getmutime() - $start, 1);
    echo "<p>{$nrows} linhas retornadas pela query em $time segundos";
    $stmt->free();
    
}else{
    if(strpos($_POST["query"], 'create') || strpos($_POST["query"], 'insert') || strpos($_POST["query"], 'update') || strpos($_POST["query"], 'delete')){
        
        $nrows = $conn->Affeted_Rows();
        
        echo "<p>$nrows linha(s) afetadas pelo seu comando de update/insert/delete";
        
    }
    
}
$conn->Execute('INSERT INTO `redundancia`(`comando`) VALUES ("'.$_POST["query"].'")');
$conn->Close();
echo '</body></html>';
