<h2>Produtos</h2>
<a href="produtos/cadastrar">Cadastrar novo produto</a>
<?php if(count($produtos) > 0): ?>
<form action="produtos" method="post">
	<input type="text" name="pesquisa" />
    <input type="submit" value="buscar" />
</form>
<table>
	<tr>
    	<th>Código</th>
        <th>Título</th>
        <th>Destaque</th>
        <th>Status</th>
        <th>Data de criação</th>
        <th colspan="2"></th>
    </tr>
    <?php foreach($produtos as $produto): ?>
    <tr>
    	<td><?=$produto->getCodigo()?></td>
        <td><?=$produto->getTitulo()?></td>
        <td><a href="produtos/alterar_destaque/<?=$produto->getCodigo()?>"><?=($produto->isDestaque()) ? 'retirar destaque' : 'colocar como destaque'?></a></td>
        <td><a href="produtos/alterar_status/<?=$produto->getCodigo()?>"><?=($produto->isAtivo()) ? 'desativar' : 'ativar'?></a></td>
        <td><?=$produto->getDataCriacao()->format('d/m/Y')?></td>
        <td><a href="produtos/alterar/<?=$produto->getCodigo()?>">alterar</a></td>
        <td><a href="produtos/excluir/<?=$produto->getCodigo()?>">excluir</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?=$this->pagination->create_links()?>
<?php else: ?>
<p>Nenhum produto encontrado.</p>
<?php endif; ?>