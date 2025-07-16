<?php
//$image_url = image_style_url($image_style,"public://no-image.png");
$node = $variables['node'];
$image_style = !empty($variables['image_style'])? $variables['image_style'] : 'original';


?>
<div class="product-type-1">
    <div class="product-type-1-container">
        <div class="product-image">
            <?php
            if (!empty($node->field_product_image['und'][0])) {
                $node_img = (array) $node->field_product_image['und'][0];
                $node_img['style_name'] = $image_style;
                $node_img['path'] = $node_img['uri'];
                $node_img = theme('image_style', $node_img);
                print(l($node_img, 'node/'.$node->nid, array('html'=>TRUE)));
            }
            ?>
        </div>
        <div class="product-title">
            <?php print($node->title); ?>
        </div>
        <div class="product-price">
            <?php
                if(!empty($node->field_product_promotion_price['und'][0]['value'])){
                    print(number_format($node->field_product_promotion_price['und'][0]['value']));
                }else{
                    print(number_format($node->field_product_price['und'][0]['value']));
                }
            ?>
        </div>
        <div class="buttons">
            <button class="btn-add-to-cart btn btn-primary" data-nid="<?php print($node->nid); ?>">Thêm vào giỏ hàng</button>
        </div>
    </div>
</div>