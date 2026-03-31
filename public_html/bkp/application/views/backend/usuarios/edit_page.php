<h2>Alterar usuário</h2>
<fieldset>
	<legend>Usuário</legend>
    <form action="usuarios/alterar" method="post">
    	<p><label for="usuario-login">Login:</label><input type="text" id="usuario-login" name="login" maxlength="45" value="<?=$usuario->getLogin()?>" readonly="readonly" /></p>
        <p><label for="usuario-senha">Senha:</label><input type="password" id="usuario-senha" name="senha" /></p>
        <p><label for="usuario-confirmacao-senha">Confirme sua senha:</label><input type="password" id="usuario-confirmacao-senha" name="confirmacao_senha" /></p>
        <p><label for="usuario-nome">Nome:</label><input type="text" id="usuario-nome" name="nome" maxlength="45" value="<?=$usuario->getNome()?>" /></p>
        <p><label for="usuario-empresa">Empresa:</label><input type="text" id="usuario-empresa" name="empresa" maxlength="250" value="<?=$usuario->getEmpresa()?>" /></p>
        <p><label for="usuario-atuacao">Área de atuação:</label><input type="text" id="usuario-atuacao" name="atuacao" maxlength="80" value="<?=$usuario->getAreaAtuacao()?>" /></p>
        <p><label for="usuario-documento">Documento:</label><input type="text" id="usuario-documento" name="documento" maxlength="15" value="<?=$usuario->getDocumento()?>" /></p>
        <p><label for="usuario-cep">Cep:</label><input type="text" id="usuario-cep" name="cep" maxlength="8" value="<?=$usuario->getCep()?>" /></p>
        <p><label for="usuario-estado">Estado:</label><select id="usuario-estado" name="estado"><option></option><?php foreach($estados as $value => $estado): ?><option value="<?=$value?>"<?=($value === $usuario->getEstado()) ? ' selected="selected"' : ''?>><?=$estado?></option><?php endforeach; ?></select></p>
        <p><label for="usuario-cidade">Cidade:</label><input type="text" id="usuario-cidade" name="cidade" maxlength="45" value="<?=$usuario->getCidade()?>" /></p>
        <p><label for="usuario-bairro">Bairro:</label><input type="text" id="usuario-bairro" name="bairro" maxlength="45" value="<?=$usuario->getBairro()?>" /></p>
        <p><label for="usuario-endereco">Endereço:</label><input type="text" id="usuario-endereco" name="endereco" maxlength="80" value="<?=$usuario->getEndereco()?>" /></p>
        <p><label for="usuario-numero">Número:</label><input type="text" id="usuario-numero" name="numero" value="<?=$usuario->getNumero()?>" /></p>
        <p><label for="usuario-complemento">Complemento:</label><input type="text" id="usuario-complemento" name="complemento" maxlength="45" value="<?=$usuario->getComplemento()?>" /></p>
        <p><label for="usuario-telefone">Telefone:</label><input type="text" id="usuario-telefone" name="telefone" value="<?=$usuario->getTelefone()?>" /></p>
        <p><label for="usuario-celular">Celular:</label><input type="text" id="usuario-celular" name="celular" value="<?=$usuario->getCelular()?>" /></p>
        <p><label for="usuario-nivel-acesso">Nível de acesso:</label><select id="usuario-nivel-acesso" name="nivel-acesso"><?php foreach($niveis_acesso as $value => $nivel): ?><option value="<?=$value?>"<?=($value === $usuario->getNivelAcesso()) ? ' selected="selected"' : ''?>><?=$nivel?></option><?php endforeach; ?></select></p>
        
        <p><input type="submit" value="salvar" /></p>
    </form>
</fieldset>