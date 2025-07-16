
<?php drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/price-confirm.js', ['weight' => 1000]); ?>
<?php
$arg = arg();
global $language;
$nid = !empty($arg[1])?$arg[1]:-1;
$product = node_load($nid);
_print_r(date("d-m-Y H:i",1642492116));
if(empty($product)){
//    drupal_set_message("Không tìm thấy gói dịch vụ!");
    drupal_goto("/price-board");
}
if(!empty(variable_get("metu_plugin"))){
    echo variable_get("metu_plugin");
}
?>
<input type="hidden" id="product" value="<?php echo $product->nid; ?>">
<div class="page-price-confirm">
    <div class="container">
        <div class="page-header">
            <div class="page-title">
                <h1>Thanh toán</h1>
            </div>
        </div>

        <form action="#">
            <div>
                <div class="row">
                    <div class="col-md-12">
                        <div>
                            <div class="bg-white pd-24 mb-24">
                                <div class="event-order-items">
                                    <h3><?php echo $product->title; ?></h3>
                                    <div class="event-order-item-broad">
                                        <?php
                                            $query = db_select("tbl_product_price","tbl_product_price");
                                            $query->fields("tbl_product_price");
                                            $query->condition("nid",$product->nid);
                                            $prices = $query->execute()->fetchAll();
                                        ?>
                                        <?php if($product->nid==AGENCY): ?>
                                            <label class="radio-mask">
                                                <?php
                                                $__price = number_format($product->field_price['und'][0]['value'],0,",",".")." đ";
                                                ?>
                                                <input type="radio" name="package-price" checked value="">
                                                <span class="mask-checked"></span>
                                                <div class="event-order-item">
                                                    <h2>Trọn đời</h2>
                                                    <div class="event-order-item-price"><b><?php echo $__price; ?></b></div>
                                                </div>
                                            </label>
                                            
                                        <?php else: ?>
                                            <?php foreach((array)$prices as $price): ?>
                                                <label class="radio-mask">
                                                    <input type="radio" name="package-price" <?php echo ($price->month == 12)?'checked':'' ?> value="<?php echo $price->id; ?>">
                                                    <span class="mask-checked"></span>
                                                    <div class="event-order-item">
                                                        <?php if($product->nid==AGENCY): ?>
                                                                <h2>Trọn đời</h2>
                                                                <?php
                                                                $__price = number_format($product->field_price['und'][0]['value'],0,",",".")." đ";
                                                                ?>
                                                            <?php else: ?>
                                                                <h2>
                                                                    <?php echo $price->month; ?>
                                                                    tháng
                                                                </h2>
                                                                <?php $__price = number_format($price->price,0,",",".")." đ/tháng"; ?>
                                                            <?php endif; ?>
                                                        <div class="event-order-item-price"><b><?php echo $__price; ?></b></div>
                                                    </div>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="event-order-info">
                            <h2>Thông tin thanh toán</h2>
                            <div class="event-order-info-content">

                            </div>

                        </div>
                        <div class="mt-10 d-flex event-order-actions">
                            <button type="button" class="btn btn-buy-product text-uppercase lts-1-25 bg-green c-white fs-16 px-16 py-12">Thanh toán</button>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </form>
    </div>
</div>