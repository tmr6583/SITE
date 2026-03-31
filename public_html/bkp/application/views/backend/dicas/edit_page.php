<h2>Alterar dica e novidade</h2>
<fieldset>
	<legend>Dica e novidade</legend>
    <form action="dicas/alterar" method="post">
    	<input type="hidden" name="id" value="<?=$dica->getId()?>" />
    	<p><label for="dica-titulo">Título:</label><input type="text" id="dica-titulo" name="titulo" maxlength="80" value="<?=$dica->getTitulo()?>" /></p>
        <p><label for="dica-texto">Dica e novidade:</label><textarea id="dica-texto" name="dica"><?=$dica->getDica()?></textarea></p>
        
        <p><input type="submit" value="salvar" /></p>
    </form>
</fieldset>