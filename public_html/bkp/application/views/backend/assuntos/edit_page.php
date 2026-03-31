<h2>Alterar assunto</h2>
<fieldset>
	<legend>Assunto</legend>
    <form action="assuntos/alterar" method="post">
    	<input type="hidden" name="id" value="<?=$assunto->getId()?>" />
    	<p><label for="assunto-titulo">Título:</label><input type="text" id="assunto-titulo" name="titulo" maxlength="80" value="<?=$assunto->getTitulo()?>" /></p>
        
        <p><input type="submit" value="salvar" /></p>
    </form>
</fieldset>