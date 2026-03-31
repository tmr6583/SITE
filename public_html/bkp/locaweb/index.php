<?php error_reporting(E_ALL ^ E_NOTICE); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>[PHP] Teste de conex&atilde;o com banco de dados</title>
<style>
.x {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:smaller;}
.y {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:xx-small;}
</style>
</head>
<body>
<form class="x" action="" method="post">
<p class="x">Dados do servidor<br>
<table border="0">
<tr><td class="x">Tipo: </td><td><select class="x" name="tipo" id="tipo">
  <option value="mysql"<?php if((isset($_POST[tipo])) and ($_POST[tipo] == "mysql")) echo(" selected"); ?>>MySQL</option>
  <option value="pgsql"<?php if((isset($_POST[tipo])) and ($_POST[tipo] == "pgsql")) echo(" selected"); ?>>PostgreSQL</option>
  <option value="mssql"<?php if((isset($_POST[tipo])) and ($_POST[tipo] == "mssql")) echo(" selected"); ?>>SQL Server</option>
</select></td></tr>
<tr><td class="x">Servidor: </td><td><input class="x" type="text" name="host" id="host" value="<?php echo($_POST[host]); ?>"></td></tr>
<!-- <tr><td class="x">Porta: </td><td><input class="x" type="text" name="porta" id="porta" value="<?php echo($_POST[porta]); ?>"><span class="y">nulo para padr&atilde;o</span></td></tr> -->
<tr><td class="x">Usuario: </td><td><input class="x" type="text" name="login" id="login" value="<?php echo($_POST[login]); ?>"></td></tr>
<tr><td class="x">Senha: </td><td><input class="x" type="password" name="senha" id="senha" value="<?php echo($_POST[senha]); ?>"></td></tr>
<tr><td class="x">Base: </td><td><input class="x" type="text" name="banco" id="banco" value="<?php echo($_POST[banco]); ?>"></td></tr>
</table>
<p class="x">Informe a query:<br>
<textarea class="x" name="query" id="query" cols="80" rows="12"><?php echo($_POST[query]); ?></textarea>
</p>
<div class="x">Conectar usando:
<input type="radio" name="conectar" value="nativa"<?php if((isset($_POST[conectar])) and ($_POST[conectar] == "nativa")) echo(" checked"); ?>>Fun&ccedil;&atilde;o nativa
<input type="radio" name="conectar" value="pdo"<?php if((isset($_POST[conectar])) and ($_POST[conectar] == "pdo")) echo(" checked"); ?>>PDO
<input type="radio" name="conectar" value="adodb"<?php if((isset($_POST[conectar])) and ($_POST[conectar] == "adodb")) echo(" checked"); ?>>ADODB
<input class="x" type="submit" value="enviar">
</div>
<br>
<div class="x"><b>Query sugerida:</b></div>
<div class="y"><b>MySQL:</b> SHOW TABLES;</div>
<div class="y"><b>SQL Server:</b> sp_help;</div>
<div class="y"><b>PostgreSQL:</b> SELECT schemaname, tablename FROM pg_tables WHERE schemaname NOT LIKE
'information_schema' AND schemaname NOT LIKE 'pg_catalog' ORDER BY
schemaname ASC , tablename ASC;</div>
</form>
<?php
	/* APARTIR DAQUI TEREMOS O CODIGO PHP PARA CONECTAR AOS BANCOS DE DADOS */
	if((isset($_POST[tipo])) and (isset($_POST[conectar]))) {
		switch($_POST[conectar]) {
			case "nativa":
				require($_POST[tipo] . "_nativa.php");
				break;
			case "pdo":
				require("pdo.php");
				break;
			case "adodb":
				require("adodb.php");
				break;
		}
	}
?>
</body>
</html>
