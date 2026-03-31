<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="">
<meta name="keywords" content="">
<base href="<?=base_url()?>admin/">
<meta name="author" content="Sylvio Leonel - ">
<title>Betina - <?=$template['title']?></title>
<link href="../lib/css/admin.css" rel="stylesheet" type="text/css" />
<script src="../lib/js/jquery.pack.js" type="text/javascript"></script>
<script src="../lib/js/jquery.validate.pack.js" type="text/javascript"></script>
<?=$template['metadata']?>
</head>

<body>
	<div class="duas-colunas">
        <div class="left">
            <ul>
                <li><a href="assinantes">Assinantes</a></li>
                <li><a href="assuntos">Assuntos</a></li>
                <li><a href="dicas">Dicas e novidades</a></li>
                <li><a href="produtos">Produtos</a></li>
                <li><a href="usuarios">Usuários</a></li>
                <li><a href="../cadastro/sair">Sair</a></li>
            </ul>
        </div>
        <div class="right"><?=$template['body']?></div>
    </div>

</body>
</html>
