<h2>Novo produto</h2>
<fieldset>
	<legend>Produto</legend>
    <form action="produtos/cadastrar" method="post" enctype="multipart/form-data">
    	<p><label for="produto-codigo">Código:</label><input type="text" id="produto-codigo" name="codigo" /></p>
        <p><label for="produto-titulo">Título:</label><input type="text" id="produto-titulo" name="titulo" maxlength="80" /></p>
        <p><label for="produto-descricao">Descrição:</label><textarea id="produto-descricao" name="descricao"></textarea></p>
        <p><label for="produto-unidade-venda">Unidade de venda:</label><input type="text" id="produto-unidade-venda" name="unidade_venda" maxlength="45" /></p>
        <p><label for="produto-composicao">Composição:</label><input type="text" id="produto-composicao" name="composicao" maxlength="45" /></p>
        <p><label><input type="radio" name="destaque" value="0" checked="checked" />Sem destaque</label><label><input type="radio" name="destaque" value="1" />Em destaque</label></p>
        <p><label><input type="radio" name="promocao" value="0" checked="checked" />Sem promoção</label><label><input type="radio" name="promocao" value="1" />Em promoção</label></p>
        <p><label for="produto-preco">Preço:</label><input type="text" id="produto-preco" name="preco" /></p>
        <p><label for="produto-preco_promocional">Preço promocional:</label><input type="text" id="produto-preco_promocional" name="preco_promocional" /></p>
        <p><label for="produto-assuntos">Assuntos:</label><select id="produto-assuntos" name="assuntos[]" multiple="multiple"><?php foreach($assuntos as $assunto): ?><option value="<?=$assunto['id']?>"><?=$assunto['titulo']?></option><?php endforeach; ?></select></p>
        <p><label for="produto-foto">Foto:</label><input type="file" id="produto-foto" name="foto" /></p>
        
        <p><input type="submit" value="salvar" /></p>
    </form>
</fieldset>