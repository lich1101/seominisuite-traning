<?php
global $user;
$data = $variables['data'];
$page = isset($data['page'])?$data['page']:1;
$query = db_select("tbl_back_link","tbl_back_link");
$query->fields("tbl_back_link");
$query->condition("tbl_back_link.project_id",$data['pid']);
$result = $query->execute()->fetchAll();
$stt=1;
$total_items = count($result);
$limit = 10;
$offset = $limit * ($page-1);
if(!empty($result)){
    $result = array_slice($result, $offset, $limit);
}else{
    $result=null;
}
$page_count = ceil($total_items/$limit);
?>
<table class="table table-hover table-stripped">
    <thead>
    <tr>
        <th><input type="checkbox" name="all"></th>
        <th>STT</th>
        <th>Nguồn đặt Backlink</th>
        <th>Vị trí</th>
        <th>Thuộc tính</th>
        <th>AnchorText</th>
        <th>URL SEO</th>
        <th>GG Indexed</th>
        <th>Tag</th>
        <th>Updated</th>
<!--        <th>Action</th>-->
    </tr>
    </thead>
    <tbody class="result">
    <?php if(!empty($result)): ?>
        <?php foreach($result as $project): ?>
            <tr>
            <tr>
                <td>
                    <label class="mask-chekbox">
                        <input type="checkbox" name="select" class="selectAll">
                        <span class="mask-checked"></span>
                    </label>
                </td>
                <td>1</td>
                <td class="text-left">
                    <a href="<?php echo($project->refer_page); ?>"><?php echo($project->refer_page); ?></a>
                </td>
                <td></td>
                <td>
                    <?php if(!empty($project->dofollow)): ?>

                    <?php else: ?>

                    <?php endif; ?>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php echo(date("d/m/Y h:i",$project->updated)); ?></td>
<!--                <td data-type="actions">-->
<!--                    <a href="#" class="table-action">-->
<!--                        <img src="./img/icons/icon-12.png" class="img-responsive" alt="">-->
<!--                    </a>-->
<!--                    <a href="#" class="table-action">-->
<!--                        <img src="./img/icons/icon-13.png" class="img-responsive" alt="">-->
<!--                    </a>-->
<!--                    <a href="#" class="table-action">-->
<!--                        <img src="./img/icons/icon-14.png" class="img-responsive" alt="">-->
<!--                    </a>-->
<!--                    <a href="#" class="table-action">-->
<!--                        <img src="./img/icons/icon-15.png" class="img-responsive" alt="">-->
<!--                    </a>-->
<!--                </td>-->
            </tr>
            <?php $stt++; endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>