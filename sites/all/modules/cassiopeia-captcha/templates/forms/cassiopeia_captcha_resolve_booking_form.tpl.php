<?php
$price = cassiopeia_captcha_resolve_booking_price_load($form['price']['#value']);
?>
    <div class="page-title mb-24">
        <h1>Mua thêm lượt giải mã Captcha</h1>
    </div>
    <div class="bg-white pd-24 mb-24 radius-5 box-shadow">
        <!--    <div class="note">-->
        <!--        <div class="block-title">Một số chú ý khi giao dịch</div>-->
        <!--        <div>-->
        <!--            --><?php
        //            $content = variable_get('domain_booking_price_content', array(
        //                'value' => '',
        //                'format' => 'full_html'
        //            ));
        //            if(!empty($content['value'])){
        //                echo $content['value'];
        //            }
        //            ?>
        <!--        </div>-->
        <!--    </div>-->
        <div class="prices">
            <?php echo drupal_render($form['price']); ?>
        </div>
    </div>
    <div class="bg-white pd-24 radius-5 box-shadow payment-info mb-24">
        <label for="">Thông tin thanh toán</label>
        <div class="d-flex justify-between price">
            <span>Giá gói</span>
            <span><b><?php echo number_format($price->price,0,",","."); ?>đ</b>/1k lượt</span>
        </div>
        <div class="d-flex justify-between quantity">
            <span>Số lượt giải mã captcha</span>
            <span><?php echo $price->quantity; ?> lượt</span>
        </div>
        <div class="d-flex justify-between total-price">
            <label for="">Tổng tiền</label>
            <b class="color-green"><?php echo number_format($price->quantity*$price->price/1000,0,",","."); ?>VND</b>
        </div>
    </div>
    <div class="d-flex justify-end">
        <?php echo drupal_render($form['submit']); ?>
    </div>
<?php echo drupal_render_children($form); ?>