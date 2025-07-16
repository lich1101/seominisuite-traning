<?php
ctools_include('modal');
ctools_modal_add_js();
global $user;
$limit = $form['#limit'];
$page = $form['#page'];
$start = ($page-1)*$limit;
$min = $start+1;
$max = $start+$limit;
$nodes = $form['#nodes'];
$total_result = $form['#total_result'];
if($max>$total_result->total){
    $max = $total_result->total;
}
?>
<div class="page-header">
    <div class="page-title">
        <h1 title="">
            <span style="max-width: unset;">Đơn vị nhận viết content</span>
        </h1>
        <?php print(l('<button class="btn btn-green btn-medium"><i class="fa fa-plus"></i> Đăng ký viết content</button>', 'order/content/add/nojs',  array('html'=>true, 'attributes' => array('class' => 'ctools-use-modal btn btn-icon-before no-padding '))));?>
    </div>
    <div class="page-search">
        <div class="input-group-search">
            <?php echo drupal_render($form['title']); ?>
            <button type="button" class="btn-clear-text"><span class="fa fa-times" aria-hidden="true"></span></button>
            <?php echo drupal_render($form['search']); ?>
        </div>
    </div>
</div>
<div class="page-container">
    <div class="page-main result">
        <div class="t-body">
            <table class="table table-striped table-div-responsive table-type-2 table-responsive">
                <thead>
                <tr>
                    <th class="w-3">
                        STT
                    </th>
                    <th class="sort text-left" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="title">Tên đơn vị</th>
                    <th class="sort text-left" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="website">Website/profile</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="price">Giá niêm yết</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="discount">Khuyến mại</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="promotion_price">Giá ưu đãi</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="tel">Số điện thoại</th>
                    <th class="sort" data-direction="<?php echo $form['sort_direction']['#value'] ?>" data-sort="email">Email</th>
                </tr>
                <tr class="">
                    <th colspan="10" class="th-progress">
                        <div class="progress-bar-block modal-custom">
                            <progress id="file" value="0" max="2180"> 32% </progress>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($nodes)): $stt=1;?>
                    <?php foreach($nodes as $node): ?>
                        <?php  ?>
                        <tr>
                            <td><?php echo $stt; ?></td>
                            <td class="text-left"><?php echo $node->title; ?></td>
                            <td class="text-left"><?php echo $node->website; ?></td>
                            <td><b class="price"><?php echo !empty($node->price)?number_format($node->price,0,",","."):""; ?>đ</b><span class="color-blur">/1000 words</span></td>
                            <td><?php echo !empty($node->discount)?$node->discount:"0"; ?>%</td>
                            <td>
                                <?php if(!empty($node->discount)&&!empty($node->price)): ?>
                                    <?php
                                    $price = $node->price-$node->price*$node->discount/100;
                                    echo "<b class='price'>".number_format($price,0,",",".")."đ</b><span class='color-blur'>/1000 words</span>";
                                    ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo !empty($node->tel)?$node->tel:"0"; ?></td>
                            <td><?php echo !empty($node->email)?$node->email:"0"; ?></td>
                        </tr>
                        <?php $stt++; endforeach; ?>
                <?php else: ?>
                    <?php for($i=1;$i<=5;$i++): ?>
                        <tr>
                            <td width="50px;" data-title="select">
                                <label class="mask-chekbox">
                                    <input name="nid[]" type="checkbox" value="156128" class="" disabled readonly>
                                    <i class="fa fa-square-o"></i>
                                </label>
                            </td>
                            <td width="50px;" data-title="Stt">...</td>
                            <td data-key="title" data-title="title" class="text-green text-left title">...</td>
                            <td data-key="totalDomain">...</td>
                            <td data-value="61" data-key="googleIndex">...</td>
                            <td data-value="100" data-key="dofollow">...</td>
                            <td data-value="1648399487" data-key="created">...</td>
                            <td data-value="1648399487" data-key="created">...</td>
                        </tr>
                    <?php endfor; ?>
                <?php endif;?>
                </tbody>
            </table>
        </div>
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
<div class="hidden">
    <?php echo drupal_render_children($form); ?>
</div>