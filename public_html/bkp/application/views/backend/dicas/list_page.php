<h2>Dicas e novidades</h2>
<a href="dicas/cadastrar">Cadastrar nova dica e novidade</a>
<?php if(count($dicas) > 0): ?>
<table>
	<tr>
    	<th>Título</th>
        <th colspan="2"></th>
    </tr>
    <?php foreach($dicas as $dica): ?>
    <tr>
    	<td><?=$dica->getTitulo()?></td>
        <td><a href="dicas/alterar/<?=$dica->getId()?>">alterar</a></td>
        <td><a href="dicas/excluir/<?=$dica->getId()?>">excluir</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?=$this->pagination->create_links()?>
<?php else: ?>
<p>Nenhuma dica e novidade encontrada.</p>
<?php endif; ?>