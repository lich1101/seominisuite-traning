<?php
$limit = $form['#limit'];
$page = $form['#page'];
$start = ($page-1)*$limit;
$min = $start+1;
$max = $start+$limit;
$nodes = $form['#nodes'];
if(empty($nodes)){$min=0;}
$total_result = $form['#total_result'];
if($max>$total_result->total){
    $max = $total_result->total;
}
?>
<div class="page-point-exchange">
    <div class="page-title mb-24">
        <h1 class="title">Giao dịch điểm</h1>
    </div>
    <div class="page-body mb-24">
        <div class="row">
            <div class="col-md-4">
                <div class="block-container bg-white padding-24 mb-24">
                    <div class="block-title">Chuyển điểm sang tài khoản khác</div>
                    <div class="block-body">
                        <div>
                            <?php echo drupal_render($form['tel']); ?>
                        </div>
                        <div>
                            <?php echo drupal_render($form['email']); ?>
                        </div>
                        <div>
                            <?php echo drupal_render($form['point']); ?>
                        </div>
                        <div>
                            <?php echo drupal_render($form['submit']); ?>
                        </div>
                    </div>
                </div>
                <div class="block-container bg-white padding-24 mb-24 note-block color-co-danger">
                    <div class="">
                        <div class="title">Lưu ý:</div>
                        <div><i class="fa fa-angle-right"></i> Không khuyến khích việc trao đổi điểm với người không quen biết</div>
                        <div><i class="fa fa-angle-right"></i> Không khuyến khích mua bán</div>
                        <div><i class="fa fa-angle-right"></i> Mục đích của giao dịch điểm: Giúp người dùng thuận tiện trong việc trao đổi Guest Post</div>
                    </div>
                </div>
                <div class="block-container bg-white padding-24 note-block color-co-success">
                    <div class="">
                        <div class="title">Một số chú ý khi giao dịch:</div>
                        <div><i class="fa fa-angle-right"></i> Kiểm tra độ tin tưởng của người bán qua <b>Profile cá nhân</b></div>
                        <div><i class="fa fa-angle-right"></i> Mọi vấn đê scam, lừa đảo, thành viên có thể tạo bài viết & thảo luận trong group <b>SeoMinisuite</b></div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="block-container bg-white padding-24 mb-24">
                    <div class="block-title">Quản lý giao dịch</div>
                    <div class="block-body">
                        <table class="table table-striped table-div-responsive table-type-2 table-responsive">
                            <thead>
                            <tr>
                                <th class="w-3">
                                    STT
                                </th>
                                <th class="" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Người gửi</th>
                                <th class="" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Người nhận</th>
                                <th class="" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Point</th>
                                <th class="" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Tình trạng</th>
                                <th class="" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Ngày tạo</th>
                                <th class="" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($nodes)): $stt=1;?>
                                <?php foreach($nodes as $node): ?>
                                    <?php
                                        $sender = user_load($node->uid);
                                        $receiver = !empty($node->receiver)?user_load($node->receiver):null;
                                    ?>
                                    <tr>
                                        <td><?php echo $stt; ?></td>
                                        <td>
                                            <div>
                                                <?php
                                                $sender_name = !empty($sender->field_full_name['und'][0]['value'])?$sender->field_full_name['und'][0]['value']:"";
                                                $sender_name.=!empty($sender->field_tel['und'][0]['value'])?" - ".$sender->field_tel['und'][0]['value']:"";
                                                echo $sender_name;
                                                ?>
                                            </div>
                                            <div><?php echo $sender->mail; ?></div>
                                        </td>
                                        <td>
                                            <?php if(!empty($node->receiver)): ?>
                                                <div>
                                                    <?php
                                                    $receiver_name = !empty($receiver->field_full_name['und'][0]['value'])?$receiver->field_full_name['und'][0]['value']:"";
                                                    $receiver_name.=!empty($receiver->field_tel['und'][0]['value'])?" - ".$receiver->field_tel['und'][0]['value']:"";
                                                    echo $receiver_name;
                                                    ?>
                                                </div>
                                                <div><?php echo $receiver->mail; ?></div>
                                            <?php else: ?>
                                                <?php echo $node->tel; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><b><?php echo $node->point; ?></b></td>
                                        <td>
                                            <?php
                                                switch ($node->status){
                                                    case 0 : echo "<span class='radius-30 status-warning'>Chờ</span>"; break;
                                                    case 1 : echo "<span class='radius-30 status-success'>Thành công</span>"; break;
                                                    case 2 : echo "<span class='radius-30 status-danger'>Không thành công</span>"; break;
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo date("d/m/Y",$node->created); ?></td>
                                        <td>
                                            <?php if($node->status==0): ?>
                                                <select  data-exchange-id="<?php echo $node->id; ?>" name="" id=""
                                                        class="form-control select-exchange-update bg-green color-white">
                                                    <option value="_none" hidden class="bg-green">Action</option>
                                                    <option class="bg-white color-black" value="1">Chuyển điểm</option>
                                                    <option class="bg-white color-black" value="2">Hủy giao dịch</option>
                                                </select>
                                            <?php else: ?>
                                                <select data-exchange-id="<?php echo $node->id; ?>" name="" id=""
                                                        class="form-control" disabled readonly="">
                                                    <option value="_none" hidden>Action</option>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php $stt++; endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                </tr>
                                <tr>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                </tr>
                                <tr>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                </tr>
                                <tr>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                </tr>
                                <tr>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                    <td>...</td>
                                </tr>
                            <?php endif;?>
                            </tbody>
                        </table>
                        <div class="clickon-pagination">
                            <div class="item-per-page">
                                <span>Số bài mỗi trang:</span>
                                <?php echo drupal_render($form['num_per_page']); ?>
                            </div>
                            <div class="current-page">
                                <?php echo ($min); ?>-<?php echo $max; ?> of <?php echo $total_result->total; ?>
                            </div>
                            <div class="nav-buttons">
                                <?php echo drupal_render($form['prev']); ?>
                                <?php echo drupal_render($form['next']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="hidden">
    <?php echo drupal_render_children($form);?>
</div>