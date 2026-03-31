<pre>
<?php
	require("seguro.php");
	// Abrindo a conexao
	try {
		if(strlen(trim($_POST[porta])) == 0)
			$strpdo = $_POST[tipo].":host=".$_POST[host].";dbname=".$_POST[banco];
		else
			$strpdo = $_POST[tipo].":host=".$_POST[host].";port=".$_POST[porta].";dbname=".$_POST[banco];
		$dbh = new PDO($strpdo, $_POST[login], $_POST[senha]);
		foreach ($dbh->query($_POST[query]) as $row) {
			print_r($row);
		}
		// Fechando a conexao
		$dbh = null;
	} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
	}
?>
</pre>
