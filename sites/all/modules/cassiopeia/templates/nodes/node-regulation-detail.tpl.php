<?php global $user;?>
<div class="node-regulation-detail">
    <div class="node-container">
        <div class="node-content row">
            <div class="left-block col-xs-12 col-md-8">
                <div class="node-title">
                    <h1><?php echo $node->title; ?></h1>
                </div>
                <div class="items">
                    <?php $items = !empty($node->field_regulation_item['und'])?_cassiopeia_load_collections($node->field_regulation_item['und']):array();  ?>
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
            <div class="right-block col-xs-12 col-md-4 hidden-xs">
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
    <?php $available_point = cassiopeia_guest_post_user_available_point_load($user->uid); ?>
    <?php if($available_point->point<1): ?>
        <div class="button">
            <div class="bg-white">
                <?php
                $cassiopeia_guest_post_regulation_form = drupal_get_form("cassiopeia_guest_post_regulation_form");
                if(!empty($cassiopeia_guest_post_regulation_form)){
                    $cassiopeia_guest_post_regulation_form = drupal_render($cassiopeia_guest_post_regulation_form);
                    echo $cassiopeia_guest_post_regulation_form;
                }
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>