<?php
global $user;
$article_info = !empty($variables['article_info'])?$variables['article_info']:null;
$activeTab = $article_info['activeTab'];
$query = db_select("cassiopeia_content_check","cassiopeia_content_check");
$query->fields("cassiopeia_content_check");
$query->condition("uid",$user->uid);
$query->condition("nid",0);
$query->orderBy("google_index","ASC");
$result = $query->execute()->fetchAll();
if(empty($result)&&!empty($article_info['nid'])){
    $query = db_select("cassiopeia_content_check","cassiopeia_content_check");
    $query->fields("cassiopeia_content_check");
    $query->condition("uid",$user->uid);
    $query->condition("nid",$article_info['nid'],"=");
    $query->orderBy("google_index","ASC");
    $result = $query->execute()->fetchAll();
}
$_result = array();
$min_words = 0;
$max_words = 0;
$h2_min = 0;
$h2_max = 0;
$h3_min = 0;
$h3_max = 0;
$img_min = 0;
$img_max = 0;
$h1_count = $h2_count = $h3_count = 0;

if(!empty($result)){
    $stt = 1;
    foreach($result as $item){
        $data = unserialize($item->data);
        if($data->isArticle==1){
            if(empty($h2_min)){
                $h2_min = count($data->h2);
            }
            if(empty($h2_max)){
                $h2_max = count($data->h2);
            }
            if(empty($h3_min)){
                $h3_min = count($data->h3);
            }
            if(empty($h3_max)){
                $h3_max = count($data->h3);
            }
            if(empty($min_words)){
                $min_words = $data->word_count;
            }
            if(empty($max_words)){
                $max_words = $data->word_count;
            }
            if(empty($img_min)){
                $img_min = $data->img;
            }
            if(empty($img_max)){
                $img_max = $data->img;
            }
            if(!empty($data->word_count)){
                $min_words = $min_words>$data->word_count?$data->word_count:$min_words;
                $max_words = $max_words<$data->word_count?$data->word_count:$max_words;
            }
            if(!empty($data->h2)){
                $h2_min = $h2_min>count($data->h2)?count($data->h2):$h2_min;
                $h2_max = $h2_max<count($data->h2)?count($data->h2):$h2_max;
            }
            if(!empty($data->h3)){
                $h3_min = $h3_min>count($data->h3)?count($data->h3):$h3_min;
                $h3_max = $h3_max<count($data->h3)?count($data->h3):$h3_max;
            }
            if(!empty($data->img)){
                $img_min = $img_min>$data->img?$data->img:$img_min;
                $img_max = $img_max<$data->img?$data->img:$img_max;
            }
            $h1_count += count($data->h1);
            $h2_count += count($data->h2);
            $h3_count += count($data->h3);
            $stt++;
        }
    }
}
if($min_words<1200){
    $min_words = 1200;
}
if($max_words<2000){
    $max_words = 2000;
}
if($h2_min<2){
    $h2_min = 2;
}
if($h2_max<5){
    $h2_max = 5;
}
if($h3_min<2){
    $h3_min = 2;
}
if($h3_max<5){
    $h3_max = 5;
}
if($img_min<2){
    $img_min = 2;
}
if($img_max<5){
    $img_max = 5;
}
//_print_r($img_min);
$current_word_count = !empty($article_info)?$article_info['word_count']:0;
$current_h2_count = !empty($article_info)?$article_info['h2']:0;
$current_h3_count = !empty($article_info)?$article_info['h3']:0;
$current_img_count = !empty($article_info)?$article_info['img']:0;
$point = !empty($article_info)?$article_info['current_point']:0;
if(!empty($result)) $point = 0;
$point += $current_word_count<$min_words?round($current_word_count*70/$min_words):70;
$point += $current_h2_count<$h2_min?round($current_h2_count*15/$h2_min):15;
$point += $current_h3_count<$h3_min?round($current_h3_count*5/$h3_min):5;
$point += $current_img_count<$img_min?round($current_img_count*10/$img_min):10;
?>
<ul class="nav nav-tabs <?php if(!empty($result)) echo "active"; ?>">
    <li data-index="1" class="<?php if(empty($result)) echo "disabled"; ?><?php if(!empty($result)&&$activeTab==1) echo "active"; ?>"><a data-toggle="tab" href="#menu1">Hỗ trợ dàn ý</a></li>
    <li data-index="2" class="<?php if(empty($result)) echo "disabled"; ?>"><a data-toggle="tab" href="#menu2">Chi tiết đối thủ</a></li>
    <li data-index="3" class="<?php if(empty($result)) echo "disabled"; ?><?php if($activeTab==3) echo "active"; ?> tab-3"><a data-toggle="tab" href="#home">Đánh giá bài viết</a></li>
</ul>

<div class="tab-content">
    <div id="menu1" class="tab-pane <?php if($activeTab==1) echo "in active"; ?> fade tab-2">
        <div class="tab-title">Hỗ trợ dàn ý bài viết</div>
        <div class="tab-body">
            <div class="tab-note">
                <?php if(!empty($result)): ?>
                    Seo Minisuite đưa ra  <?php echo $h1_count; ?> tiêu đề ( H1), <?php echo $h2_count; ?> Heading ( H2) và <?php echo $h3_count; ?> Heading (H3). Kết quả lấy từ <?php echo count($result); ?> đối thủ có thứ hạng cao nhất.
                <?php else: ?>
                    Seo Minisuite đưa ra kết quả dựa trên 10 đối thủ có thứ hạng cao nhất trên Google Search Engine!
                <?php endif; ?>
            </div>
            <div class="tab-detail">
                <div class="">
                    <ul>
                        <li class="expanded active header">
                            <a href="#" class="sm-event-none">
                                <div>#</div>
                                <div>Title</div>
                                <div>Thứ hạng</div>
                                <div><i class="fa fa-angle-up"></i></div>
                            </a>
                            <ul class="mb-none">
                                <?php if(!empty($result)): ?>
                                    <?php foreach($result as $item): ?>
                                        <?php $data = unserialize($item->data); ?>
                                        <?php if(!empty($data->h1)): ?>
                                            <?php foreach($data->h1 as $h1): ?>
                                                <?php if(!empty($h1)): ?>
                                                    <li>
                                                        <div class="heading-h1">H1</div>
                                                        <div class="has-data"><?php echo $h1; ?></div>
                                                        <div><?php echo $item->google_index; ?></div>
                                                        <div><button class="btn-add-heading" data-heading="h1" data-text="<?php echo $h1; ?>"><i class="fa fa-plus"></i> Thêm</button></div>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="disabled">
                                        <div>H1</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div>H1</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div>H1</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div>H1</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div>H1</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div>H1</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div>H1</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div>H1</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div>H1</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="expanded header">
                            <a href="#" class="sm-event-none">
                                <div>#</div>
                                <div>Headings (H2)</div>
                                <div>Thứ hạng</div>
                                <div><i class="fa fa-angle-up"></i></div>
                            </a>
                            <ul class="mb-none">
                                <?php if(!empty($result)): ?>
                                    <?php foreach($result as $item): ?>
                                        <?php $data = unserialize($item->data); ?>
                                        <?php if(!empty($data->h2)): ?>
                                            <?php foreach($data->h2 as $h2): ?>
                                                <?php if(!empty($h2)): ?>
                                                    <li>
                                                        <div class="heading-h2">H2</div>
                                                        <div><?php echo $h2; ?></div>
                                                        <div><?php echo $item->google_index; ?></div>
                                                        <div><button class="btn-add-heading" data-heading="h2" data-text="<?php echo $h2; ?>"><i class="fa fa-plus"></i> Thêm</button></div>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="disabled">
                                        <div class="heading-h2">H2</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h2">H2</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h2">H2</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h2">H2</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h2">H2</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h2">H2</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h2">H2</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h2">H2</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h2">H2</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h2">H2</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="expanded header">
                            <a href="#" class="sm-event-none">
                                <div>#</div>
                                <div class="">Headings (H3)</div>
                                <div>Thứ hạng</div>
                                <div><i class="fa fa-angle-up"></i></div>
                            </a>
                            <ul class="mb-none">
                                <?php if(!empty($result)): ?>
                                    <?php foreach($result as $item): ?>
                                        <?php $data = unserialize($item->data); ?>
                                        <?php if(!empty($data->h3)): ?>
                                            <?php foreach($data->h3 as $h3): ?>
                                                <?php if(!empty($h3)): ?>
                                                    <li>
                                                        <div class="heading-h3">H3</div>
                                                        <div><?php echo $h3; ?></div>
                                                        <div><?php echo $item->google_index; ?></div>
                                                        <div><button class="btn-add-heading" data-heading="h3" data-text="<?php echo $h3; ?>"><i class="fa fa-plus"></i> Thêm</button></div>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="disabled">
                                        <div class="heading-h3">H3</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h3">H3</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h3">H3</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h3">H3</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h3">H3</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h3">H3</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h3">H3</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h3">H3</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h3">H3</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                    <li class="disabled">
                                        <div class="heading-h3">H3</div>
                                        <div>...</div>
                                        <div>...</div>
                                        <div><button class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</button></div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="home" class="tab-pane fade <?php if($activeTab==3) echo "in active"; ?> tab-1">
        <div class="tab-title">Điểm đánh giá</div>
        <div class="tab-note">
            SEO MiniSuite đưa ra kết quả dựa trên <?php echo count($result); ?> đối thủ có thứ hạng cao nhất trên Google Search Engine!
        </div>
        <div class="tab-body">

            <div class="tab-detail">
                <table class="table-hover table-stripped">
                    <tbody>
                    <?php if(!empty($result)): ?>
                        <tr>
                            <td>
                                <div>
                                    <?php echo $current_word_count; ?>
                                    <?php if($current_word_count<$min_words): ?>
                                        <img class="up" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                    <?php elseif($current_word_count>$max_words): ?>
                                        <img class="down" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                    <?php else:  ?>
                                        <img class="" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-check.png" alt="">
                                    <?php endif; ?>
                                </div>
                                <div>Số từ hiện tại</div>
                            </td>
                            <td>
                                <div><?php echo number_format($min_words,0,",","."); ?> - <?php echo number_format($max_words,0,",","."); ?></div>
                                <div>Số từ tiêu chuẩn</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <?php echo $current_h2_count; ?>
                                    <?php if($current_h2_count<$h2_min): ?>
                                        <img class="up" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                    <?php elseif($current_h2_count>$h2_max): ?>
                                        <img class="down" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                    <?php else:  ?>
                                        <img class="" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-check.png" alt="">
                                    <?php endif; ?>
                                </div>
                                <div>Số H2 hiện tại</div>
                            </td>
                            <td>
                                <div><?php echo $h2_min; ?> - <?php echo $h2_max; ?></div>
                                <div>Số H2 tiêu chuẩn</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <?php echo $current_h3_count; ?>
                                    <?php if($current_h3_count<$h3_min): ?>
                                        <img class="up" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                    <?php elseif($current_h3_count>$h3_max): ?>
                                        <img class="down" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                    <?php else: ?>
                                        <img class="" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-check.png" alt="">
                                    <?php endif; ?>
                                </div>
                                <div>Số H3 hiện tại</div>
                            </td>
                            <td>
                                <div><?php echo $h3_min; ?> - <?php echo $h3_max; ?></div>
                                <div>Số H3 tiêu chuẩn</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <?php echo $current_img_count; ?>
                                    <?php if($current_img_count<$img_min): ?>
                                        <img class="up" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                    <?php elseif($current_img_count>$img_max): ?>
                                        <img class="down" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                    <?php else:  ?>
                                        <img class="" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-check.png" alt="">
                                    <?php endif; ?>
                                </div>
                                <div>Số hình ảnh hiện tại</div>
                            </td>
                            <td>
                                <div><?php echo $img_min; ?> - <?php echo $img_max; ?></div>
                                <div>Hình ảnh tiêu chuẩn</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td>
                                <div>
                                    <?php echo $current_word_count; ?>
                                </div>
                                <div>Số từ hiện tại</div>
                            </td>
                            <td>
                                <div>-</div>
                                <div>Số từ tiêu chuẩn</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <?php echo $current_h2_count; ?>
                                </div>
                                <div>Số H2 hiện tại</div>
                            </td>
                            <td>
                                <div>-</div>
                                <div>Số H2 tiêu chuẩn</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <?php echo $current_h3_count; ?>
                                </div>
                                <div>Số H3 hiện tại</div>
                            </td>
                            <td>
                                <div>-</div>
                                <div>Số H3 tiêu chuẩn</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>
                                    <?php echo $current_img_count; ?>
                                </div>
                                <div>Số hình ảnh hiện tại</div>
                            </td>
                            <td>
                                <div>-</div>
                                <div>Hình ảnh tiêu chuẩn</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
<!--            --><?php //$point = 90; ?>
            <div class="tab-chart">
                <div class="gauge" data-point="<?php echo $point; ?>">
                    <img src="/sites/all/themes/cassiopeia_theme/img/gauge.png" alt="">
                    <img style="transform: rotate(<?php echo round($point*2.7); ?>deg)" class="vector" src="/sites/all/themes/cassiopeia_theme/img/gauge-vector.png" alt="">
                    <div class="point">
                        <span class="rank-<?php if((int)$point<50){ echo "low";}elseif((int)$point<90){ echo "medium";}else{ echo "high";} ?>"><?php echo $point; ?></span>/100
                    </div>
                    <div class="chart-label">
                        <div class="low">
                            <span></span>
                            <span>0-49</span>
                        </div>
                        <div class="medium">
                            <span></span>
                            <span>50-89</span>
                        </div>
                        <div class="high">
                            <span></span>
                            <span>90-100</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="menu2" class="tab-pane fade tab-3">
        <div class="tab-title">Chi tiết đối thủ</div>
        <div class="tab-body">
            <div class="tab-note">
                SEO MiniSuite đưa ra kết quả dựa trên <?php echo count($result); ?> đối thủ có thứ hạng cao nhất trên Google Search Engine!
            </div>
            <div class="tab-detail">
                <div class="">
                    <ul>
                        <?php if(!empty($result)): ?>
                            <?php foreach($result as $item): ?>
                                <?php $data = unserialize($item->data); ?>
                                <li class="expanded header">
                                    <div href="#" class="">
                                        <div>
                                            <div class="article-title"><?php echo $data->anchorText; ?></div>
                                            <i class="fa fa-angle-up"></i>
                                        </div>
                                        <div>
                                            <span><?php echo $data->href; ?></span> <a  class="btn-goto" href="<?php echo $data->href; ?>" title="<?php echo $data->href; ?>" target="_blank">
                                                <i class="fa fa-external-link" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <ul class="mb-none">
                                        <?php if(!empty(trim($data->h1))): ?>
                                            <?php foreach($data->h1 as $h1): ?>
                                                <?php if(!empty(trim($h1))): ?>
                                                    <li>
                                                        <div class="heading-h1">H1</div>
                                                        <div><?php echo $h1; ?></div>
                                                        <div><button class="btn-add-heading" data-heading="h1" data-text="<?php echo $h1; ?>"><i class="fa fa-plus"></i> Thêm</button></div>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <?php if(!empty($data->h2)): ?>
                                            <?php foreach($data->h2 as $h2): ?>
                                                <?php if(!empty(trim($h2))): ?>
                                                    <li>
                                                        <div class="heading-h2">H2</div>
                                                        <div><?php echo $h2; ?></div>
                                                        <div><button class="btn-add-heading" data-heading="h2" data-text="<?php echo $h2; ?>"><i class="fa fa-plus"></i> Thêm</button></div>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <?php if(!empty($data->h3)): ?>
                                            <?php foreach($data->h3 as $h3): ?>
                                                <?php if(!empty(trim($h3))): ?>
                                                    <li>
                                                        <div class="heading-h3">H3</div>
                                                        <div><?php echo $h3; ?></div>
                                                        <div><button class="btn-add-heading" data-heading="h3" data-text="<?php echo $h3; ?>"><i class="fa fa-plus"></i> Thêm</button></div>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="expanded header">
                                <div href="#" class="">
                                    <div>
                                        <div class="article-title">-</div>
                                        <i class="fa fa-angle-up"></i>
                                    </div>
                                    <div>
                                        <a href="" rel="nofollow" target="_blank">-</a>
                                    </div>
                                </div>
                                <ul class="mb-none">
                                    <li>
                                        <div class="heading-h1">H1</div>
                                        <div>-</div>
                                        <div>-</div>
                                    </li>
                                    <li>
                                        <div class="heading-h2">H2</div>
                                        <div>-</div>
                                        <div>-</div>
                                    </li>
                                    <li>
                                        <div class="heading-h3">H3</div>
                                        <div>-</div>
                                        <div>-</div>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
