<h2>Alterar assinante</h2>
<fieldset>
	<legend>Assinante</legend>
    <form action="assinantes/alterar" method="post">
    	<p><label for="assinante-email">Email:</label><input type="text" id="assinante-email" name="email" maxlength="45" value="<?=$assinante->getEmail()?>" readonly="readonly" /></p>
        
        <p><input type="submit" value="salvar" /></p>
    </form>
</fieldset>