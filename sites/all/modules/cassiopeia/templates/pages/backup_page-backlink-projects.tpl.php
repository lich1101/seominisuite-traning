<?php drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-backlink-projects.js', ['weight' => 1000]); ?>
<?php
global $user;
$packet = cassiopeia_get_available_packet_by_uid($user->uid);
$query = db_select("node","tbl_node");;
$query->fields("tbl_node");
$query->condition("tbl_node.type","project_backlink");
if(!empty($_REQUEST['title'])){
    $query->where("REPLACE(tbl_node.title, ' ', '') LIKE :arg",array(":arg"=>"%".trim(str_replace(" ","",$_REQUEST['title']))."%"));
}
if(isset($_REQUEST['sort']) && isset($_REQUEST['direction'])){
    switch ($_REQUEST['sort']){
        case "name" :
            $query->orderBy("tbl_node.title",$_REQUEST['direction']);
            break;
        case "website" :
            $query->join("field_data_field_domain","field_domain","field_domain.entity_id=tbl_node.nid");
            $query->orderBy("field_domain.field_domain_value",$_REQUEST['direction']);
            break;
        case "created" :
            $query->orderBy("tbl_node.changed",$_REQUEST['direction']);
            break;
    }
}else{
    $query->orderBy("tbl_node.changed","DESC");
}
$query->condition("tbl_node.uid",$user->uid);
$result = $query->execute()->fetchAll();
$nids = array();
if(!empty($result)){
    foreach($result as $_item){
        $nids[] = $_item->nid;
    }
}
if(!empty($nids)){
    $projects = node_load_multiple($nids);
}

try{

}catch (Exception $e){
//    cassiopeia_dump($e);
}
//cassiopeia_dump($result);
?>
<div class="page page-backlink">
    <div class="page-header">
        <div class="page-title">
            <h1>Tất cả dự án Backlink</h1>
            <button type="button" class="btn-green btn-add-backlink-project">
                <span class="fa fa-plus"></span>
                Thêm dự án
            </button>
            <a class="btn btn-export btn-green btn-search-project <?php echo (empty($projects))?'btn-disable':'' ?> <?php echo !empty($packet->excel)?"excel":""; ?>" href="#">
                <span class="fa fa-file-excel-o fs-16 mr-5" aria-hidden="true"></span>
                Xuất báo cáo
            </a>
        </div>

        <div class="page-search">
            <form action="">
                <div class="input-group-search">
                    <input name="title" type="text" placeholder="Tìm dự án..." value="<?php if(!empty($_REQUEST['title'])) echo $_REQUEST['title']; ?>">
                    <button type="button" class="btn-clear-text"><span class="fa fa-times" aria-hidden="true"></span></button>
                    <button type="button" class="btn-type-1 btn-submit btn-search-project">
                        <i class="fas fa-magnifying-glass fa-fw"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="page-utilities d-none">
        <div class="page-utilities-left">
            <form action="#">
                <!--                <select name="" id="" class="btn-gray btn-type-1">-->
                <!--                    <option value="">Bulk Action</option>-->
                <!--                    <option value="">item 1</option>-->
                <!--                    <option value="">item 2</option>-->
                <!--                </select>-->
                <!--                <button type="submit" class="btn-submit btn-type-1">Áp dụng</button>-->
            </form>
        </div>
        <div class="page-utilities-right">

        </div>
    </div>
    <form action="" class="form-sort">
        <input type="hidden" name="sort" value="">
        <input type="hidden" name="direction" value="">
    </form>
    <div class="page-container">
        <div class="page-main">
            <form class="form-export" action="quan-ly-backlink/export">
                <div class="result table-responsive">
                    <table class="table table-striped table-div-responsive table-type-2">
                        <thead>
                        <tr>
                            <th width="50px;" data-title="select">
                                <label class="mask-chekbox" >
                                    <input type="checkbox" name="select" class="selectAll" <?php echo empty($projects)?"readonly disabled":""; ?>>
                                    <i class="fa fa-square-o"></i>
                                </label>
                            </th>
                            <th width="50px;" data-title=Stt>
                                STT
                            </th>
                            <th data-sort="title" data-title="title"  data-direction="ASC"  class="text-left sort sorting_desc <?php if((isset($_REQUEST['sort']) && $_REQUEST['sort']=="name")&&isset($_REQUEST['direction']) && $_REQUEST['direction']=="ASC") echo "sorting_asc"; ?>">Tên dự án</th>
                            <th data-sort="domain" data-direction="ASC"  class="text-left sort sorting_desc <?php if(isset($_REQUEST['direction']) && $_REQUEST['direction']=="ASC") echo "sorting_asc"; ?>">Website</th>
                            <th data-sort="totalBacklink" data-direction="ASC"  class="number w-8   sort sorting_desc <?php if(isset($_REQUEST['direction']) && $_REQUEST['direction']=="ASC") echo "sorting_asc"; ?>   ">Số lượng Backlink</th>
                            <th data-sort="totalDomain" data-direction="ASC"  class="number w-8      sort sorting_desc <?php if(isset($_REQUEST['direction']) && $_REQUEST['direction']=="ASC") echo "sorting_asc"; ?>  ">Số lượng Domain</th>
                            <th data-sort="googleIndex" data-direction="ASC"  class="w-8      sort sorting_desc <?php if(isset($_REQUEST['direction']) && $_REQUEST['direction']=="ASC") echo "sorting_asc"; ?>  ">Tỷ lệ Google Index</th>
                            <th data-sort="dofollow" data-direction="ASC"  class="w-7     sort sorting_desc <?php if(isset($_REQUEST['direction']) && $_REQUEST['direction']=="ASC") echo "sorting_asc"; ?> ">Tỷ lệ dofollow</th>
                            <th data-sort="created" data-direction="ASC"  class="sort sorting_desc <?php if(isset($_REQUEST['direction']) && $_REQUEST['direction']=="ASC") echo "sorting_asc"; ?>">Ngày cập nhật</th>
                            <th data-sort="" data-direction="" data-title="actions"  class="w-12">Action</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        <?php if(!empty($projects)): $stt=1;?>
                            <?php foreach($projects as $item):  ?>
                                <?php
                                $result = null;
                                try{
                                    $query = db_select("tbl_backlink","tbl_backlink");
                                    $query->fields("tbl_backlink");

                                    $query->leftJoin("tbl_backlink_detail","tbl_backlink_detail","tbl_backlink_detail.nid=tbl_backlink.id");
                                    $query->addExpression("SUM((CASE WHEN tbl_backlink_detail.rel='dofollow' THEN 1 ELSE 0 END))","total_dofollow");

                                    $query->addExpression("SUM(DISTINCT(CASE WHEN tbl_backlink.indexed=1 THEN 1 ELSE 0 END))","total_indexed");

                                    $query->addExpression("COUNT(DISTINCT(tbl_backlink_detail.id))","totalBacklink");
                                    $query->addExpression("COUNT(DISTINCT(tbl_backlink.id))","totalSource");

                                    $query->condition("tbl_backlink.pid",$item->nid);

                                    $query->groupBy("tbl_backlink.id");
                                    $sub = db_select($query,"tbl");

                                    $sub->addExpression("SUM(tbl.totalBacklink)","totalBacklink");
                                    $sub->addExpression("SUM(tbl.total_indexed)","totalIndex");
                                    $sub->addExpression("SUM(tbl.total_dofollow)","totalDofollow");
                                    $sub->addExpression("SUM(tbl.totalSource)","totalSource");
                                    $result = $sub->execute()->fetchObject();

                                    $query = db_select("tbl_backlink","tbl_backlink");
                                    $query->fields("tbl_backlink");
                                    $query->groupBy("tbl_backlink.domain");
                                    $query->condition("pid",$item->nid);
                                    $domainResult = $query->execute()->fetchAll();

                                    $squery = db_select("tbl_backlink","tbl_backlink");
                                    $squery->fields("tbl_backlink",array("id"));
                                    $squery->orderBy("tbl_backlink.created","DESC");

                                    $squery->groupBy("tbl_backlink.domain");
                                    $squery->condition("tbl_backlink.pid",$item->nid);

                                    $domain = $squery->execute()->fetchAll();
                                }catch (Exception $e){
                                    cassiopeia_dump($e);
                                }
                                $_domain = str_replace("http://","",$item->field_domain['und'][0]['value']);
                                $_domain = str_replace("https://","",$_domain);
                                $_domain = str_replace("www","",$_domain);
                                ?>
                                <tr>
                                    <?php if(!empty($projects)): ?>
                                        <td width="50px;" data-title="select">
                                            <label class="mask-chekbox" >
                                                <input name="nid[]" type="checkbox" value="<?php echo($item->nid); ?>" class="" >
                                                <i class="fa fa-square-o"></i>
                                            </label>
                                        </td>
                                    <?php endif; ?>

                                    <td width="50px;" data-title=Stt>
                                        <?php echo($stt); ?>
                                    </td>
                                    <td data-key="title" data-title="title" class="text-green text-left title">
                                        <div class="d-flex space-between">
                                            <a href="/quan-ly-backlink/du-an/<?php echo($item->nid); ?>" title="<?php echo($item->title); ?>" class="project-title sort-text">
                                                <?php echo($item->title); ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td data-key="domain" class="text-left ">
                                        <div class="d-flex space-between">
                                            <span class="domain sort-text" target="_blank" rel="\" href="//<?php echo $_domain; ?>" title="<?php echo $item->field_domain['und'][0]['value']; ?>">
                                                <?php echo $item->field_domain['und'][0]['value']; ?>
                                            </span>
                                            <a class="" href="//<?php echo $_domain; ?>" title="<?php echo $item->field_domain['und'][0]['value']; ?>" target="_blank">
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td data-key="totalBacklink" ><?php echo !empty($result->totalBacklink)?$result->totalBacklink:"-"; ?></td>
                                    <td data-key="totalDomain" ><?php echo count($domainResult); ?></td>
                                    <td data-value="<?php if(!empty($result->totalSource)) echo !empty($result->totalIndex)?round(100*$result->totalIndex/$result->totalSource):0; ?>" data-key="googleIndex" >
                                        <?php
                                        if(!empty($result->totalSource)) {
                                            echo !empty($result->totalIndex)?round(100*$result->totalIndex/$result->totalSource)."%":"-";
                                        }else{
                                            echo "-";
                                        }
                                        ?>
                                    </td>
                                    <td data-value="<?php echo !empty($result->totalBacklink)?round(100*$result->totalDofollow/$result->totalBacklink):0; ?>" data-key="dofollow" >
                                        <?php
                                        if(!empty($result->totalDofollow)){
                                            echo !empty($result->totalBacklink)?round(100*$result->totalDofollow/$result->totalBacklink)."%":"-";
                                        }else{
                                            echo "-";
                                        }

                                        ?>
                                    </td>
                                    <td data-value="<?php echo $item->changed ?>" data-key="created" ><?php echo date("d/m/Y",$item->changed); ?></td>
                                    <td data-title="actions">
                                        <button type="button" class="btn-edit-project" data-id="<?php echo($item->nid); ?>" title="Sửa dự án"><i class="far fa-pen-to-square fa-fw"></i></button>
                                        <button type="button" class="btn-delete-project" data-id="<?php echo($item->nid); ?>" data-name="<?php echo $item->title; ?>" title="Xóa"><i class="far fa-trash-can fa-fw"></i></button>
                                    </td>
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
                                    <td data-key="domain" class="text-left ">...</td>
                                    <td data-key="totalBacklink">...</td>
                                    <td data-key="totalDomain">...</td>
                                    <td data-value="61" data-key="googleIndex">...</td>
                                    <td data-value="100" data-key="dofollow">...</td>
                                    <td data-value="1648399487" data-key="created">...</td>
                                    <td data-title="actions">
                                        <button type="button" class="btn-edit-project btn-disable" data-id="156128" title="Sửa dự án"><i class="far fa-pen-to-square fa-fw"></i></button>
                                        <button type="button" class="btn-delete-project btn-disable" data-id="156128" title="Xóa"><i class="far fa-trash-can fa-fw"></i></button>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Modal BackLink -->
<div class="modal fade Modal-addNew backlink-addNew" id="backlinkModal" tabindex="-1" role="dialog" aria-labelledby="backlinkModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-content-contaienr">
                <form action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Tạo dự án mới</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-body-content">
                            <h3>Nhập Trang Web Của Bạn</h3>
                            <span>LinkAssistant sẽ giúp bạn tìm cơ hội đặt backlink cho trang web dựa trên chủ đề của nó và đối thủ cạnh tranh SEO</span>
                            <div class="website-name">
                                <input type="text" placeholder="Nhập tên website...">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="modal-footer-content">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Huỷ</button>
                            <button type="submit" class="btn btn-success">Tạo mới</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

