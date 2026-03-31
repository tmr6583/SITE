<div id="cesta-cotacoes">
	<h3 class="page-title">Cesta de cotações</h3>
    <?php if(is_array($cesta) && count($cesta) > 0): ?>
    <table width="100%">
    	<thead>
            <tr>
                <th colspan="2">Produto</th>
                <th>Quantidade</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($cesta as $item): ?>
    	<tr>
        	<td><img src="lib/uploads/thumbs/<?=$item['codigo']?>.jpg" alt="<?=$item['titulo']?>" title="<?=$item['titulo']?>" /></td>
            <td align="center" valign="middle"><?=$item['titulo']?></td>
            <td align="center" valign="middle"><form action="cesta/index" method="post"><input type="hidden" name="codigo" value="<?=$item['codigo']?>" /><input type="text" name="quantidade" value="<?=$item['quantidade']?>" size="2" /></form></td>
            <td align="center" valign="middle"><a href="cesta/remover/<?=$item['codigo']?>">Remover</a></td>
        </tr>
    <?php endforeach; ?>
    	</tbody>
    	<tfoot>
        	<tr>
            	<td colspan="2" align="left"><a href="">&laquo; continuar cotando</a></td>
                <td colspan="2" align="right"><a href="cesta/fechar">fechar cotação &raquo;</a></td>
            </tr>
        </tfoot>
    </table>
    <?php else: ?>
    <p>Sua cesta de cotações está vazia.</p>
    <?php endif; ?>
</div>
