<?php $backlinks = $variables['backlinks'];

cassiopeia_dump($backlinks);
?>

<?php if(!empty($backlinks)): $stt=1;?>
    <?php foreach($backlinks as $item): ?>
        <?php echo _cassiopeia_render_theme("module","cassiopeia","templates/parts/row-backlink-check.tpl.php",array("item"=>$item,"stt"=>$stt)); ?>
        <?php $stt++; endforeach; ?>
<?php endif; ?>