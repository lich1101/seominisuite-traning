<?php
$id = $variables['id'];
$nid = $variables['nid'];
$query = db_select("tbl_product_price","tbl_product_price");
$query->fields("tbl_product_price");
$query->condition("id",$id);
$price = $query->execute()->fetchObject();
$product = node_load($nid);
?>
<ul>
    <li>
        <span>Gói dịch vụ mua:</span>
        <b class="c-black"><?php echo $product->title; ?></b>
    </li>
    <li>
        <span>Gía gói:</span>
        <b class="c-black">
            <?php if($product->nid==AGENCY): ?>
                <?php echo number_format($product->field_price['und'][0]['value'],0,",","."); ?> đ
            <?php else: ?>
                <?php echo !empty($price->price)?number_format($price->price,0,",",".")."/tháng":"Miễn phí"; ?>
            <?php endif; ?>
        </b>
    </li>
    <li>
        <span>Thời gian dịch vụ:</span>
        <b class="c-black">
            <?php if($product->nid==AGENCY): ?>
                Trọn đời
            <?php else: ?>
                <?php echo $price->month; ?> tháng
            <?php endif; ?>
        </b>
    </li>
    <li>
        <div class="line mg-16"></div>
    </li>
    <li>
        <span>Tổng tiền:</span>
        <?php if($product->nid==AGENCY): ?>
            <b class="c-green"><?php echo number_format($product->field_price['und'][0]['value'],0,",","."); ?> đ</b>
        <?php else: ?>
            <b class="c-green"><?php echo number_format($price->price*$price->month,0,",","."); ?> VND</b>
        <?php endif; ?>
    </li>
</ul>
