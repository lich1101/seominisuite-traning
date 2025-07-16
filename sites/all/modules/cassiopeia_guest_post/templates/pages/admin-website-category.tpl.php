<?php
$query = db_select("cassiopeia_guest_post_website_category","cassiopeia_guest_post_website_category");
$query->fields("cassiopeia_guest_post_website_category");
$query->orderBy("cassiopeia_guest_post_website_category.created","DESC");
$result = $query->execute()->fetchAll();
?>
<div class="page-admin-manager-website-category">
    <div class="mb-10">
        <a href="/admin/manager/guest-post/website/category/add?destination=admin/manager/guest-post/website/category" class="btn btn-primary">Thêm mới</a>
    </div>
    <table class="table table-hover table-stripped">
        <thead>
            <tr>
                <th>Ngày tạo</th>
                <th>Tiêu đề</th>
                <th width="200px">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($result)): ?>
                <?php foreach($result as $item): ?>
                    <tr>
                        <td><?php echo date("d/m/Y",$item->created); ?></td>
                        <td><?php echo $item->title; ?></td>
                        <td>
                            <a class="btn btn-primary" href="/admin/manager/guest-post/website/category/<?php echo $item->id; ?>/edit?destination=admin/manager/guest-post/website/category"><i class="fa fa-edit"></i></a>
                            <a class="btn btn-danger" href="/admin/manager/guest-post/website/category/<?php echo $item->id; ?>/delete?destination=admin/manager/guest-post/website/category"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>