<?php
$products = cassiopeia_get_nodes_by_category("product");
?>
<div class="page-products">
    <div class="page-container container">
        <div class="row">
            <?php foreach($products as $product): ?>
                <div class="col-md-4">
                    <button class="btn btn-primary btn-buy-product" data-nid="<?php echo($product->nid); ?>">Mua gói <?php echo($product->title); ?></button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
