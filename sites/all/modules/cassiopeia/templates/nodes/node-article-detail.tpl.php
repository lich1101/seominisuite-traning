<?php
/**
 * Created by PhpStorm.
 * User: MXP
 * Date: 07-May-19
 * Time: 4:13 PM
 */
$node = $variables['node'];
if(!empty(variable_get("metu_plugin"))){
    echo variable_get("metu_plugin");
}
?>
<?php $items = !empty($node->field_article_item['und'])?_cassiopeia_load_collections($node->field_article_item['und']):array(); ?>


<div class="node-regulation-detail">
    <div class="node-container">
        <div class="node-content row">
            <div class="left-block col-md-8">
                <div class="node-title">
                    <h1><?php echo $node->title; ?></h1>
                </div>
                <div class="items">
                    <?php if(!empty($items)): ?>
                        <?php foreach($items as $item): ?>
                            <div class="item" data-id="<?php echo $item->item_id; ?>">
                                <div class="item-container">
                                    <div class="item-title">
                                        <h2><?php echo !empty($item->field_title['und'][0]['value'])?$item->field_title['und'][0]['value']:""; ?></h2>
                                    </div>
                                    <div class="item-content">
                                        <?php echo !empty($item->field_content['und'][0]['value'])?$item->field_content['und'][0]['value']:""; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="right-block col-md-4">
                <div class="right-nav">
                    <div class="nav-title">Nội dung chính</div>
                    <div class="nav-items">
                        <ul>
                            <?php if(!empty($items)): ?>
                                <?php foreach($items as $item): ?>
                                    <li  data-id="<?php echo $item->item_id; ?>"><?php echo !empty($item->field_title['und'][0]['value'])?$item->field_title['und'][0]['value']:""; ?></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>