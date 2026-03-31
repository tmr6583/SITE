<h2>Alterar produto</h2>
<fieldset>
	<legend>Produto</legend>
    <form action="produtos/alterar" method="post" enctype="multipart/form-data">
    	<p><label for="produto-codigo">Código:</label><input type="text" id="produto-codigo" name="codigo" value="<?=$produto->getCodigo()?>" readonly="readonly" /></p>
        <p><label for="produto-titulo">Título:</label><input type="text" id="produto-titulo" name="titulo" maxlength="80" value="<?=$produto->getTitulo()?>" /></p>
        <p><label for="produto-descricao">Descrição:</label><textarea id="produto-descricao" name="descricao"><?=$produto->getDescricao()?></textarea></p>
        <p><label for="produto-unidade-venda">Unidade de venda:</label><input type="text" id="produto-unidade-venda" name="unidade_venda" maxlength="45" value="<?=$produto->getUnidadeVenda()?>" /></p>
        <p><label for="produto-composicao">Composição:</label><input type="text" id="produto-composicao" name="composicao" maxlength="45" value="<?=$produto->getComposicao()?>" /></p>
        <p><label><input type="radio" name="destaque" value="0"<?=!$produto->isDestaque() ? ' checked="checked"' : ''?> />Sem destaque</label><label><input type="radio" name="destaque" value="1"<?=$produto->isDestaque() ? ' checked="checked"' : ''?> />Em destaque</label></p>
        <p><label><input type="radio" name="promocao" value="0"<?=!$produto->isPromocao() ? ' checked="checked"' : ''?> />Sem promoção</label><label><input type="radio" name="promocao" value="1"<?=$produto->isPromocao() ? ' checked="checked"' : ''?> />Em promoção</label></p>
        <p><label for="produto-preco">Preço:</label><input type="text" id="produto-preco" name="preco" value="<?=$produto->getPreco()?>" /></p>
        <p><label for="produto-preco_promocional">Preço promocional:</label><input type="text" id="produto-preco_promocional" name="preco_promocional" value="<?=$produto->getPrecoPromocional()?>" /></p>
        <p><label for="produto-assuntos">Assuntos:</label><select id="produto-assuntos" name="assuntos[]" multiple="multiple"><?php foreach($assuntos as $assunto): ?><option value="<?=$assunto['id']?>" <?php foreach($produto->getAssuntos() as $pa) if($assunto['id'] === $pa->getId()) echo ' selected="selected"'; ?>><?=$assunto['titulo']?></option><?php endforeach; ?></select></p>
        <p><label for="produto-foto">Foto:</label><input type="file" id="produto-foto" name="foto" /></p>
        
        <p><input type="submit" value="salvar" /></p>
    </form>
</fieldset>