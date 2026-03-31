<div id="cadastro-login">
	<h3 class="page-title">Cadastro / Login</h3>
    <div class="duas-colunas">
    	<div class="left">
        	<form name="frmLogin" action="cadastro/login" method="post">
            	<p><label for="login-email">Email:</label><input type="text" id="login-email" name="email" /></p>
                <p><label for="login-senha">Senha:</label><input type="password" id="login-senha" name="senha" /></p>
                <p style="text-align:left;">Esqueceu sua senha? <a onClick="javascript:document.location='cadastro/novaSenha/'+document.getElementById('login-email').value;"><b>Clique aqui</b></a>.</p>
                <p style="text-align:right;"><a class="btn-color btn-go" href="javascript:void(0);" onclick="javascript:document.frmLogin.submit();">Entrar</a></p>
            </form>
        </div>
        <div class="right">
        	<p style="text-align:center;">Ainda não é cadastrado? <a href="cadastro/registro"><b>Cadastre-se</b></a>.</p>
        </div>
    </div>
</div>
