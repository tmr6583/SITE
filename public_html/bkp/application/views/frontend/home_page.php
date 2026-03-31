<div id="banner"><img src="lib/img/banner1.png" width="912" height="341" alt=""/></div>
<br />
<div id="produtos-em-oferta">
  <h3>PROMOÇÕES</h3>
    <img id="promocoes-icon" src="lib/img/icopromo.png" width="80" height="80" alt="" />
    <div class="center">
    	<?php if(count($promocionais) > 0): ?>
        <?php foreach($promocionais as $produto): ?>
        <?php $img_version = '';
		if(!is_null($produto->getDataAlteracao())) {
			$data = $produto->getDataAlteracao();
			$img_version = '?v='. $data->getTimestamp();
		} ?>
    	<div class="produto">
        	<img src="lib/uploads/thumbs/<?=$produto->getCodigo()?>.jpg<?=$img_version?>" alt="<?=$produto->getTitulo()?>" title="<?=$produto->getTitulo()?>" />
            <span class="titulo"><?=$produto->getTitulo()?></span>
            <?php if($produto->isPromocao()): ?>
            <span class="preco">De: R$ <?=format_to_real($produto->getPreco())?> por R$ <?=format_to_real($produto->getPrecoPromocional())?></span>
            <?php endif; ?>
            <p><a class="btn-color btn-cotacao" href="javascript:void(0);" onclick="javascript:addProduto(<?=$produto->getCodigo()?>,'<?=$produto->getTitulo()?>');"><img src="lib/img/btncota.png" alt=""/></a></p>
            <p></p><a class="btn-color btn-detalhes" href="javascript:void(0);" onclick="javascript:verProduto(<?=$produto->getCodigo()?>,this);">+detalhes</a></p>
        </div>
        <?php endforeach; ?>
    	<?php endif; ?>
    </div>
</div>

<div id="produtos-em-destaque">
	<h3 style="padding-left:60px">Confira os destaques do mês</h3>
  <img id="destaque-icon" src="lib/img/icodest.png" width="68" height="82" alt="" />
    <div class="center">
    	<?php if(count($destaques) > 0): ?>
        <?php foreach($destaques as $produto): ?>
        <?php $img_version = '';
		if(!is_null($produto->getDataAlteracao())) {
			$data = $produto->getDataAlteracao();
			$img_version = '?v='. $data->getTimestamp();
		} ?>
    	<div class="produto">
        	<img src="lib/uploads/thumbs/<?=$produto->getCodigo()?>.jpg<?=$img_version?>" alt="<?=$produto->getTitulo()?>" title="<?=$produto->getTitulo()?>" />
            <span class="titulo"><?=$produto->getTitulo()?></span>
            <?php if($produto->isPromocao()): ?>
            <span class="preco">De: R$ <?=format_to_real($produto->getPreco())?> por R$ <?=format_to_real($produto->getPrecoPromocional())?></span>
            <?php endif; ?>
            <p><a class="btn-color btn-cotacao" href="javascript:void(0);" onclick="javascript:addProduto(<?=$produto->getCodigo()?>,'<?=$produto->getTitulo()?>');"><img src="lib/img/btncota.png" alt=""/></a></p>
            <p></p><a class="btn-color btn-detalhes" href="javascript:void(0);" onclick="javascript:verProduto(<?=$produto->getCodigo()?>,this);">+detalhes</a></p>
        </div>
        <?php endforeach; ?>
    	<?php endif; ?>
    </div>
</div>
