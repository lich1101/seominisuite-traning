<?php
global $user;
$data = $variables['data'];
$page = isset($data['page'])?$data['page']:1;
$query = db_select("tbl_project","tbl_project");
$query->fields("tbl_project");
$query->condition("tbl_project.uid",$user->uid);
$query->condition("tbl_project.type","backlink");
$query->leftJoin("tbl_back_link","tbl_back_link","tbl_back_link.project_id=tbl_project.id");
if(!empty($data['key'])){
    $query->condition("tbl_project.title","%".$data['key']."%","LIKE");
}
$query->addExpression("COUNT(tbl_back_link.id)","backlink");
$query->groupBy("tbl_project.id");
$result = $query->execute()->fetchAll();
$stt=1;
$total_items = count($result);
$limit = 1;
$offset = $limit * ($page-1);
if(!empty($result)){
    $result = array_slice($result, $offset, $limit);
}else{
    $result=null;
}
$page_count = ceil($total_items/$limit);
?>
<table class="table table-striped table-div-responsive table-type-2">
    <thead>
    <tr>
        <th>
            <label class="mask-chekbox">
                <input type="checkbox" name="select" class="selectAll">
                <span class="mask-checked"></span>
            </label>
        </th>
        <th>
            <span class="square">#</span>
        </th>
        <th class="text-left sorting sorting_desc">Tên dự án</th>
        <th class="text-left sorting sorting_desc">Website</th>
        <th class="w-8 sorting sorting_desc">Số lượng Backlink</th>
        <th class="w-8 sorting sorting_desc">Số lượng Domain</th>
        <th class="w-8 sorting sorting_desc">Tỷ lệ Google Index</th>
        <th class="w-7 sorting sorting_desc">Tỷ lệ do follow</th>
        <th class="w-8 sorting sorting_desc">Backlink trong content</th>
        <th class="sorting sorting_desc">Update</th>
        <th class="w-12">Action</th>
    </tr>
    </thead>
    <tbody class="">
    <?php if(!empty($result)): ?>
        <?php foreach($result as $project): ?>
            <tr>
                <td>
                    <label class="mask-chekbox">
                        <input type="checkbox" value="<?php echo($project->id); ?>">
                        <span class="mask-checked"></span>
                    </label>
                </td>
                <td><?php echo($stt); ?></td>
                <td class="text-green text-left"><?php echo($project->title); ?></td>
                <td class="text-left"><a href="<?php echo($project->domain); ?>"><?php echo($project->domain); ?></a></td>
                <td><?php echo $project->backlink; ?></td>
                <td>100</td>
                <td>yes</td>
                <td>70</td>
                <td>90%</td>
                <td>02/01/2021</td>
                <td>
                    <button class="btn-edit-project" data-id="<?php echo($project->id); ?>" title="Cập nhật"> <img src="./sites/all/themes/cassiopeia_theme/img/icons/icon-14.png" class="img-responsive" alt="" ></button>
                    <button class="btn-delete-project" data-id="<?php echo($project->id); ?>" title="Xóa"><img src="./sites/all/themes/cassiopeia_theme/img/icons/icon-15.png" class="img-responsive" alt=""></button>
                </td>
            </tr>
            <?php $stt++; endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<div class="page-footer">
    <div class="page-footer-left">
        <form action="#">
            <select name="" id="" class="btn-gray btn-type-1">
                <option value="">Bulk Action</option>
                <option value="">item 1</option>
                <option value="">item 2</option>
            </select>
            <button type="submit" class="btn-submit btn-type-1">Áp dụng</button>
        </form>
    </div>

    <div class="page-footer-right">
        <div class="ajax-pagination">
            <div class="ajax-pagination-container">
                <ul>
                    <?php if($page_count<=3): ?>
                        <?php for($i=1;$i<=$page_count;$i++): ?>
                            <li><span class="ajax-item <?php if($page==$i) print("active"); ?>" data-page="<?php print($i); ?>"><?php print($i); ?></span></li>
                        <?php endfor; ?>
                    <?php else: ?>
                        <?php if($page<=2): ?>
                            <?php for($i=1;$i<=3;$i++): ?>
                                <li><span class="ajax-item <?php if($page==$i) print("active"); ?>" data-page="<?php print($i); ?>"><?php print($i); ?></span></li>
                            <?php endfor; ?>
                            <li><span class="">...</span></li>
                            <li><span class="ajax-item fa fa-angle-right" data-page="<?php print($page+1); ?>"></span></li>
                            <li><span class="ajax-item fa fa-angle-double-right" data-page="<?php print($page_count); ?>"></span></li>
                        <?php else: ?>
                            <?php if($page>=$page_count-1): ?>
                                <li><span class="ajax-item fa fa-angle-double-left" data-page="<?php print(1); ?>"></span></li>
                                <li><span class="ajax-item fa fa-angle-left" data-page="<?php print($page-1); ?>"></span></li>
                                <li><span class="">...</span></li>
                                <?php for($i=$page_count-2;$i<=$page_count;$i++): ?>
                                    <li><span class="ajax-item <?php if($page==$i) print("active"); ?>" data-page="<?php print($i); ?>"><?php print($i); ?></span></li>
                                <?php endfor; ?>
                            <?php else: ?>
                                <li><span class="ajax-item fa fa-angle-double-left" data-page="<?php print(1); ?>"></span></li>
                                <li><span class="ajax-item fa fa-angle-left" data-page="<?php print($page-1); ?>"></span></li>
                                <li><span class="">...</span></li>
                                <?php for($i=$page-1;$i<=$page+1;$i++): ?>
                                    <li><span class="ajax-item <?php if($page==$i) print("active"); ?>" data-page="<?php print($i); ?>"><?php print($i); ?></span></li>
                                <?php endfor; ?>
                                <li><span class="">...</span></li>
                                <li><span class="ajax-item fa fa-angle-right" data-page="<?php print($page+1); ?>"></span></li>
                                <li><span class="ajax-item fa fa-angle-double-right" data-page="<?php print($page_count); ?>"></span></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>