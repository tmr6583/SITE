<h2>Nova dica e novidade</h2>
<fieldset>
	<legend>Dica e novidade</legend>
    <form action="dicas/cadastrar" method="post">
    	<p><label for="dica-titulo">Título:</label><input type="text" id="dica-titulo" name="titulo" maxlength="80" /></p>
        <p><label for="dica-texto">Dica e novidade:</label><textarea id="dica-texto" name="dica"></textarea></p>
        
        <p><input type="submit" value="salvar" /></p>
    </form>
</fieldset>