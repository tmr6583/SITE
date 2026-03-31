<h2>Assuntos</h2>
<a href="assuntos/cadastrar">Cadastrar novo assunto</a>
<?php if(count($assuntos) > 0): ?>
<table>
	<tr>
    	<th>Título</th>
        <th>Status</th>
        <th colspan="2"></th>
    </tr>
    <?php foreach($assuntos as $assunto): ?>
    <tr>
    	<td><?=$assunto->getTitulo()?></td>
        <td><a href="assuntos/alterar_status/<?=$assunto->getId()?>"><?=($assunto->isAtivo()) ? 'desativar' : 'ativar'?></a></td>
        <td><a href="assuntos/alterar/<?=$assunto->getId()?>">alterar</a></td>
        <td><a href="assuntos/excluir/<?=$assunto->getId()?>">excluir</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?=$this->pagination->create_links()?>
<?php else: ?>
<p>Nenhum assunto encontrado.</p>
<?php endif; ?>