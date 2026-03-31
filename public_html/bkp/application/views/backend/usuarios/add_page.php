<h2>Novo usuário</h2>
<fieldset>
	<legend>Usuário</legend>
    <form action="usuarios/cadastrar" method="post">
    	<p><label for="usuario-login">Login:</label><input type="text" id="usuario-login" name="login" maxlength="45" /></p>
        <p><label for="usuario-senha">Senha:</label><input type="password" id="usuario-senha" name="senha" /></p>
        <p><label for="usuario-confirmacao-senha">Confirme sua senha:</label><input type="password" id="usuario-confirmacao-senha" name="confirmacao_senha" /></p>
        <p><label for="usuario-nome">Nome:</label><input type="text" id="usuario-nome" name="nome" maxlength="45" /></p>
        <p><label for="usuario-empresa">Empresa:</label><input type="text" id="usuario-empresa" name="empresa" maxlength="250" /></p>
        <p><label for="usuario-atuacao">Área de atuação:</label><input type="text" id="usuario-atuacao" name="atuacao" maxlength="80" /></p>
        <p><label for="usuario-documento">Documento:</label><input type="text" id="usuario-documento" name="documento" maxlength="15" /></p>
        <p><label for="usuario-cep">Cep:</label><input type="text" id="usuario-cep" name="cep" maxlength="8" /></p>
        <p><label for="usuario-estado">Estado:</label><select id="usuario-estado" name="estado"><option></option><?php foreach($estados as $value => $estado): ?><option value="<?=$value?>"><?=$estado?></option><?php endforeach; ?></select></p>
        <p><label for="usuario-cidade">Cidade:</label><input type="text" id="usuario-cidade" name="cidade" maxlength="45" /></p>
        <p><label for="usuario-bairro">Bairro:</label><input type="text" id="usuario-bairro" name="bairro" maxlength="45" /></p>
        <p><label for="usuario-endereco">Endereço:</label><input type="text" id="usuario-endereco" name="endereco" maxlength="80" /></p>
        <p><label for="usuario-numero">Número:</label><input type="text" id="usuario-numero" name="numero" /></p>
        <p><label for="usuario-complemento">Complemento:</label><input type="text" id="usuario-complemento" name="complemento" maxlength="45" /></p>
        <p><label for="usuario-telefone">Telefone:</label><input type="text" id="usuario-telefone" name="telefone" /></p>
        <p><label for="usuario-celular">Celular:</label><input type="text" id="usuario-celular" name="celular" /></p>
        <p><label for="usuario-nivel-acesso">Nível de acesso:</label><select id="usuario-nivel-acesso" name="nivel-acesso"><?php foreach($niveis_acesso as $value => $nivel): ?><option value="<?=$value?>"><?=$nivel?></option><?php endforeach; ?></select></p>
        
        <p><input type="submit" value="salvar" /></p>
    </form>
</fieldset>