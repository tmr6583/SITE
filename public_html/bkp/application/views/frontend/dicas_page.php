<div id="dicas-novidades">
	<h3 class="page-title">Dicas e novidades</h3>
    <?php if(count($dicas) > 0): ?>
    <div class="dicas">
		<?php foreach($dicas as $dica): ?>
        <div class="dica">
            <h4 class="blue-text"><?=$dica->getTitulo()?></h4>
            <p><?=substr($dica->getDica(), 0, 150)?>...</p>
            <a href="javascript:void(0);" onclick="javascript:lerDica(<?=$dica->getId()?>);" class="blue-text">Continue lendo</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?=$this->pagination->create_links()?>
    <?php else: ?>
    <p>Nenhuma dica e novidade encontrada.</p>
    <?php endif; ?>
</div>
