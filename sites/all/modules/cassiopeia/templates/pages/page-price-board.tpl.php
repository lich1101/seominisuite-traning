<?php
global $user;
global $packet;
global $language;
$packet = cassiopeia_get_available_packet_by_uid($user->uid);
$conditions = array();
$conditions['field_weight'] = array(
    "type"      => "fieldOrderBy",
    "column"    => "value",
    "direction" => "ASC"
);
$products = (array)cassiopeia_get_items_by_conditions($conditions,"product","node");
if(!empty(variable_get("metu_plugin"))){
    echo variable_get("metu_plugin");
}
?>

<div class="page-price-board">
    <div class="page-container container">
        <div class="row board-wrap">
            <?php foreach($products as $product): ?>
                <div class="col-md-4">
                    <div class="price-box <?php if(!empty($product->field_featured['und'][0]['value'])) echo "featured"; ?>">
                        <div class="price-top">
                            <div class="price-skin">
                                <?php echo $product->title; ?>
                            </div>
                            <div class="price-cost">
                                <h3>
                                    <?php
                                    if($product->nid==AGENCY){
                                        echo !empty($product->field_price['und'][0]['value'])?number_format($product->field_price['und'][0]['value'],0,",",".")."đ/trọn đời":"đ";
                                    }else{
                                        echo !empty($product->field_price['und'][0]['value'])?number_format($product->field_price['und'][0]['value'],0,",",".")."đ/tháng":"Miễn phí";
                                    }

                                    ?>
                                </h3>
                            </div>
                        </div>
                        <div class="price-content">
                            <div class="price-content__item">
                                <h4>Quản lý thứ hạng từ khoá</h4>
                                <ul>
                                    <li>
                                        <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                        <span>Số lượng dự án: <b><?php echo !empty($product->field_p_key_project['und'][0]['value'])?$product->field_p_key_project['und'][0]['value']:"Không giới hạn"; ?></b></span>
                                    </li>
                                    <li>
                                        <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                        <span>Số lượt kiểm tra từ khoá: <b><?php echo !empty($product->field_p_key_check['und'][0]['value'])?$product->field_p_key_check['und'][0]['value']." lượt/ngày":"Không giới hạn"; ?></b></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="price-content__item">
                                <h4>Quản lý BackLink</h4>
                                <ul>
                                    <li>
                                        <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                        <span>Số lượng dự án: <b><?php echo !empty($product->field_p_backlink_project['und'][0]['value'])?$product->field_p_backlink_project['und'][0]['value']:"Không giới hạn"; ?></b></span>
                                    </li>
                                    <li>
                                        <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                        <span>Số lượt kiểm tra Backlink <b><?php echo !empty($product->field_p_backlink_check['und'][0]['value'])?$product->field_p_backlink_check['und'][0]['value']." lượt/ngày":"Không giới hạn"; ?></b></span>
                                    </li>
                                    <li>
                                        <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                        <span>Số lượt kiểm tra Google Index <b><?php echo !empty($product->field_p_indexed_google['und'][0]['value'])?$product->field_p_indexed_google['und'][0]['value']." lượt/ngày":"Không giới hạn"; ?></b></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="price-content__item">
                                <h4>Hỗ trợ Outline Content</h4>
                                <ul>
                                    <li>
                                        <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                        <span>Số lượng dự án: <b><?php echo !empty($product->field_p_content_project['und'][0]['value'])?$product->field_p_content_project['und'][0]['value']:"Không giới hạn"; ?></b></span>
                                    </li>
                                    <li>
                                        <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                        <span>Số lượng bài viết <b><?php echo !empty($product->field_p_article_content['und'][0]['value'])?$product->field_p_article_content['und'][0]['value']."":"Không giới hạn"; ?></b></span>
                                    </li>
                                    <li>
                                        <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                        <span>Số lượt kiểm tra <b><?php echo !empty($product->field_p_content_check['und'][0]['value'])?$product->field_p_content_check['und'][0]['value']." lượt/ngày":"Không giới hạn"; ?></b></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="price-content__item">
                                <h4>Kiểm tra đạo văn</h4>
                                <ul>
                                    <li>
                                        <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                        <span>Số lượng: <b><?php echo !empty($product->field_p_content_duplicate['und'][0]['value'])?$product->field_p_content_duplicate['und'][0]['value']." lượt/ngày":"Không giới hạn"; ?></b></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="price-content__item">
                                <h4>Số lượng thiết bị cùng sử dụng</h4>
                                <ul>
                                    <li>
                                        <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                        <span>Số lượng: <b><?php echo !empty($product->field_p_device['und'][0]['value'])?$product->field_p_device['und'][0]['value']:"Không giới hạn"; ?></b></span>
                                    </li>
                                </ul>
                            </div>
<!--                            <div class="price-content__item">-->
<!--                                <h4>Tự động giải mã Captcha Coogle</h4>-->
<!--                                <ul>-->
<!--                                    <li>-->
<!--                                        <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>-->
<!--                                        <span><b>Có</b></span>-->
<!--                                    </li>-->
<!--                                </ul>-->
<!--                            </div>-->
                            <div class="price-content__item">
                                <h4>Xuất báo cáo dưới dạng Excel</h4>
                                <ul>
                                    <li>
                                        <?php if(!empty($product->field_p_excel_export['und'][0]['value'])): ?>
                                            <div class="bg-green-light d-flex align-center raddius-3 px-8 py-14">
                                                <span class="icon mr-5"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                                <span>
                                                    <b>Có</b>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <div class="bg-red-light d-flex align-center raddius-3 px-8 py-14">
                                                <span class="icon  mr-5"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-27.png" alt=""></span>
                                                <span>
                                                    <b>Không</b>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="price-content__item">
                                <h4>Quyền đăng bài</h4>
                                <ul>
                                    <li>
                                        <?php if(!empty($product->field_p_guest_post['und'][0]['value'])): ?>
                                            <div class="bg-green-light d-flex align-center raddius-3 px-8 py-14">
                                                <span class="icon mr-5"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-26.png" alt=""></span>
                                                <span>
                                                    <b>Có</b>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <div class="bg-red-light d-flex align-center raddius-3 px-8 py-14">
                                                <span class="icon  mr-5"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-27.png" alt=""></span>
                                                <span>
                                                    <b>Không</b>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            </div>
                            <?php if(!empty($user->uid)): ?>
                                <div class="price-content__button">
                                    <?php if(!empty($packet) && $packet->product==AGENCY): ?>
                                        <a href="/price-confirm/<?php echo $product->nid; ?>" class="btn-disable">Chọn gói này</a>
                                    <?php else: ?>
                                        <?php if($product->nid==BASIC ): ?>
                                            <a href="/price-confirm/<?php echo $product->nid; ?>" class="btn-disable">Chọn gói này</a>
                                        <?php else: ?>
                                            <a href="/price-confirm/<?php echo $product->nid; ?>">Chọn gói này</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>
