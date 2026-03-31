<div id="registro">
	<h3 class="page-title">Cadastro</h3>
    <form name="frmCadastro" action="cadastro/salvar" method="post">
    	<table>
        	<tr>
            	<td colspan="2">
                	<p><label for="cadastro-nome">Nome:</label><input type="text" id="cadastro-nome" name="nome" maxlength="45" /></p>
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><label for="cadastro-empresa">Empresa:</label><input type="text" id="cadastro-empresa" name="empresa" maxlength="250" /></p>
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><label for="cadastro-atuacao">Ramo de atuação:</label><input type="text" id="cadastro-atuacao" name="atuacao" maxlength="80" /></p>
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><label for="cadastro-documento">CNPJ:</label><input type="text" id="cadastro-documento" name="documento" maxlength="15" /></p>
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><label for="cadastro-endereco">Endereço para entrega:</label><input type="text" id="cadastro-endereco" name="endereco" maxlength="80" /></p>
                </td>
            </tr>
            <tr>
            	<td>
                	<p><label for="cadastro-numero">Número:</label><input type="text" id="cadastro-numero" name="numero" /></p>
                </td>
                <td>
                	<p><label for="cadastro-complemento">Complemento:</label><input type="text" id="cadastro-complemento" name="complemento" maxlength="45" /></p>
                </td>
            </tr>
            <tr>
            	<td>
                	<p><label for="cadastro-cep">Cep:</label><input type="text" id="cadastro-cep" name="cep" maxlength="8" /></p>
                </td>
                <td>
                	<p><label for="cadastro-bairro">Bairro:</label><input type="text" id="cadastro-bairro" name="bairro" maxlength="45" /></p>
                </td>
            </tr>
            <tr>
            	<td>
                	<p><label for="cadastro-cidade">Cidade:</label><input type="text" id="cadastro-cidade" name="cidade" maxlength="45" /></p>
                </td>
                <td>
                	<p><label for="cadastro-estado">Estado:</label><select id="cadastro-estado" name="estado"><option></option><?php foreach($estados as $value => $estado): ?><option value="<?=$value?>"><?=$estado?></option><?php endforeach; ?></select></p>
                </td>
            </tr>
            <tr>
            	<td>
                	<p><label for="cadastro-telefone">Telefone:</label><input type="text" id="cadastro-telefone" name="telefone" /></p>
                </td>
                <td>
                	<p><label for="cadastro-celular">Celular:</label><input type="text" id="cadastro-celular" name="celular" /></p>
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                	<p><label for="cadastro-login">Login:</label><input type="text" id="cadastro-login" name="login" maxlength="45" /></p>
                </td>
            </tr>
            <tr>
            	<td>
                	<p><label for="cadastro-senha">Senha:</label><input type="password" id="cadastro-senha" name="senha" /></p>
                </td>
                <td>
                	<p><label for="cadastro-confirmacao-senha">Confirme sua senha:</label><input type="password" id="cadastro-confirmacao-senha" name="confirmacao_senha" /></p>
                </td>
            </tr>
        </table>
    	
        <input type="submit" id="frm-submit" style="display:none;" />
        <p style="text-align:right;"><a class="btn-color btn-go" href="javascript:void(0);" onclick="javascript:document.getElementById('frm-submit').click();">Enviar</a></p>
    </form>
</div>
