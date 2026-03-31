<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="">
<meta name="keywords" content="">
<base href="<?=base_url()?>">
<meta name="author" content="Sylvio Leonel - ">
<title>Betina - <?=$template['title']?></title>
<link href="lib/css/styles.css" rel="stylesheet" type="text/css" />
<script src="lib/js/jquery.pack.js" type="text/javascript"></script>
<script type="text/javascript">function addProduto(codigo,titulo){$.post('cesta/adicionar',{codigo:codigo},function(data){alert(titulo+' adicionado a cesta de cotações com sucesso!');$('#qtd-cesta').html(data+' itens')})};function verProduto(codigo,element){$.post('produtos/ver',{codigo:codigo},function(data){var innerBox=data;$('#cobertor #box').css("margin-top", Math.max(0, (($(window).height()-$(element).outerHeight())/2)+$(window).scrollTop())-200+"px").html(innerBox).parent().fadeIn('fast').css('height', $('body').css('height'))})};function lerDica(codigo){$.post('dicas/ler',{codigo:codigo},function(data){var innerBox=data;$('#cobertor #box').html(innerBox).parent().fadeIn('fast').css('height', $('body').css('height'))})};</script>
<?=$template['metadata']?>
</head>

<body>

	<div id="cobertor" style="position:absolute;width:100%;height:100%;left:0;top:0;background-color:rgba(0,0,0,0.5);display:none;z-index:1;" onclick="javascript:$('#cobertor').fadeOut('fast');"><div id="box" style="margin:2% auto 0;padding:20px;width:60%;background-color:#FFF;border:1px solid #666;text-align:center;"></div></div>

	<div id="header">
    	<div class="wrap duas-colunas">
        	<div class="left">
            	<h1 class="logo"><a href=""><img src="lib/img/logo.png" width="395" height="133" alt="Betina Soluções em Limpeza" title="Betina Soluções em Limpeza" /></a></h1>
            </div>
            <div class="right">
            	<div id="saudacao">
                    <span class="yellow-text">Olá visitante.</span><br />
                    <span class="white-text"><a href="cadastro">Faça seu login ou cadastre-se.</a></span>
                </div>
                <div class="duas-colunas">
                    <div id="busca" class="left">
                        <form action="home/buscar" method="get">
                            <input type="text" name="busca" placeholder="BUSCAR" />
                            <input type="image" src="lib/img/busca.png" />
                        </form>
                    </div>
                    <div id="cesta" class="right">
                    	<span class="gray-text"><a href="cesta">Minha cesta<br />de cotações</a><br /><span id="qtd-cesta"><?=get_quantidade_cesta()?> itens</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="section">
    	<div class="duas-colunas">
        	<div class="left">
            	<h2>PRODUTOS</h2>
                <ul id="menu-assuntos">
                	<?php foreach(list_assuntos() as $assunto): ?>
                    <li><a href="produtos/<?=$assunto['id'] .'/'. clean_string($assunto['titulo'])?>"><?=$assunto['titulo']?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="right">
            	<div class="duas-colunas">
                	<div id="article" class="left">
                    	<?=$template['body']?>
                        <div id="adicionais">
                            <table width="100%">
                                <tr>
                                    <td width="30%"><div id="newsletter-wid">
                                        <h4 class="white-text">Novidades e ofertas. Cadastre-se!</h4>
                                        <form name="frmNewsletter" action="assinantes/cadastrar" method="post">
                                            <input type="text" name="email" placeholder="Email" />
                                            <p><a class="btn-color btn-go" href="javascript:void(0);" onclick="javascript:alert('Obrigado por cadastrar seu email.\nEm breve enviaremos novidades e ofertas especiais para você.');document.frmNewsletter.submit();">Ok</a></p>
                                        </form>
                                        <p>Ao se registrar no nosso mailing, você concorda e autoriza o envio de novidades da Betina para o seu endereço eletrônico.</p>
                                    </div></td>
                                    
                                    <td width="30%"><div id="telefones-wid">
                                        <h4 class="yellow-text">Betina Entrega Rápida</h4>
                                        <table width="100%" border="0" class="white-text" cellspacing="30">
                                            <tr>
                                                <td><big>(24) 2222-6197</big></td><td><big>(24) 2222-6198</big></td>
                                            </tr>
                                            <tr>
                                                <td><big>(24) 2222-6289</big></td><td><big>(24) 2222-3180</big></td>
                                            </tr>
                                        </table>
                                    </div></td>
                                    
                                    <td width="30%"><div id="email-wid">
                                        <h4 class="gray-text">Faça seu orçamento por email:</h4>
                                        <hr />
                                        <a class="blue-text" href="mailto:betina@betinalimpeza.com.br">betina@betinalimpeza.com.br</a>
                                    </div></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="right">
                        <ul id="menu-principal">
                            <li><a href="">Home</a></li>
                            <li><a href="sobre">Sobre a Betina</a></li>
                            <li><a href="dicas">Dicas e Novidades</a></li>
                            <li><a href="cadastro">Cadastro / Login</a></li>
                            <li><a href="cesta">Cesta de Cotações</a></li>
                            <li><a href="localizacao">Localização</a></li>
                            <li><a href="contato">Fale Conosco</a></li>
                        </ul>
                        
                        <h2>Dicas e Novidades</h2>
                        <ul id="dicas-recentes">
                        	<?php foreach(list_dicas() as $dica): ?>
                            <li><a href="javascript:void(0);" onclick="javascript:lerDica(<?=$dica['id']?>);"><?=$dica['titulo']?> - <?=substr($dica['dica'], 0, 50)?>...</a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="footer">
    	<div class="wrap duas-colunas">
        	<div class="left">
            	<span class="white-text">Copyright © 2012 - Betina Limpeza - Todos os direitos reservados</span>
            </div>
            <div class="right">
            	<a href=""><img src="lib/img/logo.png" width="250" height="85" alt="Betina Soluções em Limpeza" title="Betina Soluções em Limpeza" /></a>
            </div>
        </div>
    </div>

</body>
</html>
