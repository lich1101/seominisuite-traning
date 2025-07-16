<?php
drupal_add_js("https://code.jquery.com/ui/1.13.0/jquery-ui.js");
drupal_add_css("https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css");
ctools_include('modal');
ctools_modal_add_js();
drupal_add_js(drupal_get_path("module","cassiopeia_guest_post")."/js/admin-website.js");
global $user;
?>
<div class="page page-admin-manager-guest-post-website">
    <?php
    $cache = !empty($_REQUEST['data'])?$_REQUEST['data']:null;
    try{
        $query = db_select("cassiopeia_guest_post_website","cassiopeia_guest_post_website");
        $query->fields("cassiopeia_guest_post_website");
        $query->orderBy("cassiopeia_guest_post_website.created","DESC");
        if(!empty($cache['domain'])){
            $or = db_or();
            $or->condition("cassiopeia_guest_post_website.domain","%".$cache['domain']."%","LIKE");
            $query->condition($or);
        }
        if(!empty($cache['category'])&&$cache['category']!="all"){
            $query->join("cassiopeia_guest_post_website_field_tx_category","field_tx_category","field_tx_category.entity_id=cassiopeia_guest_post_website.id");
            $query->condition("field_tx_category.tid",$cache['category']);
        }
        if(isset($cache['status'])&&$cache['status']!="all"){
            $query->condition("cassiopeia_guest_post_website.status",$cache['status']);
        }
        $websites = $query->execute()->fetchAll();
    }catch (Exception $e){

    }

    ?>
    <div class="page-top">
        <div class="mb-10">
            <a href="/admin/manager/guest-post/website/add" class="btn btn-primary">Thêm mới</a>
        </div>
        <div class="custom-filter">
            <?php
            $cassiopeia_guest_post_website_category_form = drupal_get_form("cassiopeia_guest_post_website_filter_form",$cache);
            if(!empty($cassiopeia_guest_post_website_category_form)){
                $cassiopeia_guest_post_website_category_form = drupal_render($cassiopeia_guest_post_website_category_form);
                echo $cassiopeia_guest_post_website_category_form;
            }
            ?>
        </div>
    </div>
    <div class="page-container">
        <div class="page-main result">
            <div class="t-body">
                <table class="table table-striped table-div-responsive table-type-2 table-responsive">
                    <thead>
                        <tr>
                            <th><input type="checkbox" value="all"></th>
                            <th class="w-3">
                                STT
                            </th>
                            <th>Tên miền</th>
                            <th width="300px">Lĩnh vực</th>
                            <th>Chủ website</th>
                            <th>Ref domain</th>
                            <th>Organic traffic</th>
                            <th>Bài viết đã đăng</th>
                            <th>Đã đăng trong ngày</th>
                            <th>Ngày đăng gần nhất</th>
                            <th>Tình trạng</th>
                            <th width="100px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($websites)): $stt=1;?>
                        <?php foreach($websites as $_website): ?>
                            <?php
                            $website = cassiopeia_guest_post_website_load($_website->id);
                            $account = user_load($website->uid);
                            ?>
                            <tr>
                                <td>
                                    <input data-domain="<?php echo $website->domain; ?>" type="checkbox" value="<?php echo $_website->id; ?>">
                                </td>
                                <td><?php echo $stt; ?></td>
                                <td class="text-green"><a target="_blank" href="<?php echo $website->domain; ?>"><?php echo $website->domain; ?></a></td>
                                <td><?php echo !empty($website->list_of_category)?$website->list_of_category:""; ?></td>
                                <td><?php echo $account->name; ?></td>
                                <td><?php echo !empty($website->ref_domain)?number_format($website->ref_domain,0,",","."):""; ?></td>
                                <td><?php echo !empty($website->organic_traffic)?number_format($website->organic_traffic,0,",","."):""; ?></td>
                                <td><?php echo !empty($website->posted)?$website->posted:0; ?></td>
                                <td><?php echo !empty($website->posted_of_day)?$website->posted_of_day:0; ?></td>
                                <td><?php echo !empty($website->last_posted_date)?date("d/m/Y",$website->last_posted_date):"-"; ?></td>
                                <td>
                                    <?php if($website->status==0): ?>
                                        <b><span class="color-red">Lỗi</span></b>
                                    <?php else: ?>
                                        <b><span class="color-green">OK</span></b>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/admin/manager/guest-post/website/<?php echo $website->id; ?>/edit?destination=admin/manager/guest-post/website" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                    <a href="/admin/manager/guest-post/website/<?php echo $website->id; ?>/delete?destination=admin/manager/guest-post/website" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php $stt++; endforeach; ?>
                    <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="progress-block">
    <div class="block-container">
        <div class="block-header">Đang quét websites</div>
        <div class="block-body">
            <progress id="file" value="32" max="100"> 32% </progress>
        </div>
    </div>
</div>
