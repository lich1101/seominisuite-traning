<?php
if(!empty(variable_get("metu_plugin"))){
    echo variable_get("metu_plugin");
}
?>
<div class="page-booking-complete hidden-xs">
    <?php $bank_accounts = (array)cassiopeia_get_items_by_conditions(array(),"bank_account","node"); ?>
    <div class="mt-24">
        <div class="method-option-items">
            <div class="page-header">
                <div class="page-title text-center" style="width: 100%;">
                    <h1>Chuyển khoản ngân hàng</h1>
                </div>
            </div>
            <div class="row">
                <?php foreach($bank_accounts as $bank_account): ?>
                    <?php $bank = taxonomy_term_load($bank_account->field_tx_bank['und'][0]['tid']); ?>
                    <div class="col-md-4">
                        <div class="method-option-item">
                            <?php
                            if (!empty($bank_account->field_image['und'][0])) {
                                $node_img = (array) $bank_account->field_image['und'][0];
                                $node_img['style_name'] = "original";
                                $node_img['path'] = $node_img['uri'];
                                $node_img = theme('image_style', $node_img);
                                print($node_img);
                            }
                            ?>
                            <div class="method-option">
                                <p><?php echo $bank->name; ?></p>
                                <p><span>Số tài khoản:</span> <span id="bank-no" class="bank-no"><?php echo $bank_account->field_bank_account['und'][0]['value']; ?></span></p>
                                <p><span>Tên tài khoản:</span> <?php echo $bank_account->title; ?></p>
                                <p><span>Chi nhánh:</span> <?php echo $bank_account->field_bank_brand['und'][0]['value']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-24">
            <div class="note-transfer bg-white">
                <p>Tổng số tiền: <span style="font-size: 20px;
    font-weight: bold;" class="c-green"><?php echo number_format($booking->total_price,0,",","."); ?> VNĐ</span></p>
                <p>Nội dung chuyển khoản: <b><?php echo $booking->booking_code; ?></b></p>
                <div>
                    <span>Lưu ý:</span> Nếu sau <b style="color: red;">5 phút chưa thấy xác nhận đơn hàng</b>, hãy SMS hoặc Gọi đến số <b style="color: #1F9F4C;;">điện thoại <a href="tel:0865621196" style="color: #1F9F4C; text-decoration: underline">0865621196</a></b> hoặc gửi tin nhắn đến <b style="color: #1F9F4C;;">Fanpage (Bên phải - Phía cuối Màn hình)</b> để bộ phận Kinh doanh xác nhận.

                </div>
            </div>
        </div>

        <div class="mt-24 d-flex jsc-end">
            <a href="/" type="button" class="btn btn-buy-product-complete lts-1-25 bg-green c-white fs-16">Hoàn tất</a>
        </div>
    </div>
</div>

<!--<span class="copied">Copied</span>-->