<h2>Usuários</h2>
<a href="usuarios/cadastrar">Cadastrar novo usuário</a>
<?php if(count($usuarios) > 0): ?>
<table>
	<tr>
    	<th>Login</th>
        <th>Empresa</th>
        <th>Nome</th>
        <th>Documento</th>
        <th>Cidade</th>
        <th>Telefone</th>
        <th>Nivel de acesso</th>
        <th>Status</th>
        <th>Data de criação</th>
        <th colspan="2"></th>
    </tr>
    <?php foreach($usuarios as $usuario): ?>
    <tr>
    	<td><?=$usuario->getLogin()?></td>
        <td><?=$usuario->getEmpresa()?></td>
        <td><?=$usuario->getNome()?></td>
        <td><?=$usuario->getDocumento()?></td>
        <td><?=($usuario->getCidade() != NULL && $usuario->getEstado() != NULL) ? $usuario->getCidade() .' - '. $usuario->getEstado() : ''?></td>
        <td><?=$usuario->getTelefone()?></td>
        <td><?=$usuario->getNivelAcesso()?></td>
        <td><a href="usuarios/alterar_status/<?=$usuario->getLogin()?>"><?=($usuario->isAtivo()) ? 'desativar' : 'ativar'?></a></td>
        <td><?=$usuario->getDataCriacao()->format('d/m/Y')?></td>
        <td><a href="usuarios/alterar/<?=$usuario->getLogin()?>">alterar</a></td>
        <td><a href="usuarios/excluir/<?=$usuario->getLogin()?>">excluir</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?=$this->pagination->create_links()?>
<?php else: ?>
<p>Nenhum usuário encontrado.</p>
<?php endif; ?>