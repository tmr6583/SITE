<h2>Assinantes</h2>
<a href="assinantes/cadastrar">Cadastrar novo assinante</a>
<?php if(count($assinantes) > 0): ?>
<table>
	<tr>
    	<th>Email</th>
        <th>Data de criação</th>
        <th colspan="2"></th>
    </tr>
    <?php foreach($assinantes as $assinante): ?>
    <tr>
    	<td><?=$assinante->getEmail()?></td>
        <td><?=$assinante->getDataCriacao()->format('d/m/Y H:i')?></td>
        <td><a href="assinantes/alterar/<?=$assinante->getEmail()?>">alterar</a></td>
        <td><a href="assinantes/excluir/<?=$assinante->getEmail()?>">excluir</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?=$this->pagination->create_links()?>
<?php else: ?>
<p>Nenhum assinante encontrado.</p>
<?php endif; ?>