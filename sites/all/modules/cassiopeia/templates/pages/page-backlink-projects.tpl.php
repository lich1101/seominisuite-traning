<?php drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-backlink-projects.js', ['weight' => 1000]); ?>
<?php
global $user;
$packet = cassiopeia_get_available_packet_by_uid($user->uid);

try{
    $domain_query = db_select("tbl_backlink","tbl_backlink");
    $domain_query->fields("tbl_backlink",array("domain","pid","_domain"));
    $domain_query->orderBy("tbl_backlink.created","DESC");
    $domain_query->groupBy("tbl_backlink.pid");
    $domain_query->addExpression("COUNT(DISTINCT _domain)","count_domain");
    $domain_query->condition("tbl_backlink.uid",$user->uid);
//    $domain_query->condition("tbl_backlink.pid",269461);

    $_domain_query = db_select($domain_query,"tbl_domain_query");
    $_domain_query->fields("tbl_domain_query");
    $_domain_query->addExpression("SUM(tbl_domain_query.count_domain)","total_domain");
    $_domain_query->groupBy("tbl_domain_query.pid");
//    $_domain_query->condition("tbl_domain_query.pid",269461);
//
    $query = db_select("node","tbl_node");;
    $query->fields("tbl_node");
    $query->condition("tbl_node.type","project_backlink");
    $query->condition("tbl_node.uid",$user->uid);
    $query->orderBy("tbl_node.changed","DESC");
//    $query->condition("tbl_node.nid",156406);

    $backlink_count_query = db_select("tbl_backlink","tbl_backlink");
    $backlink_count_query->fields("tbl_backlink");
    $backlink_count_query->condition("tbl_backlink.uid",$user->uid);
//    $backlink_count_query->condition("tbl_backlink.pid",156128);
    $backlink_count_query->leftJoin("tbl_backlink_detail","tbl_backlink_detail","tbl_backlink_detail.nid=tbl_backlink.id");
    $backlink_count_query->addExpression("SUM(CASE WHEN tbl_backlink_detail.rel='dofollow' THEN 1 ELSE 0 END)","total_dofollow");
//
    $backlink_count_query->addExpression("CASE WHEN tbl_backlink.indexed=1 THEN 1 ELSE 0 END","total_indexed");
//
    $backlink_count_query->addExpression("COUNT(DISTINCT(tbl_backlink_detail.id))","totalBacklink");
    $backlink_count_query->groupBy("tbl_backlink.id");
    $backlink_count_query->addExpression("COUNT(DISTINCT(tbl_backlink.id))","totalSource");
//    $backlink_count_query->range(0,5000);

    $backlink_count_query_sub = db_select($backlink_count_query,"tbl");

    $backlink_count_query_sub->addExpression("SUM(tbl.totalBacklink)","totalBacklink");
    $backlink_count_query_sub->addExpression("SUM(tbl.total_indexed)","totalIndex");
    $backlink_count_query_sub->addExpression("SUM(tbl.total_dofollow)","totalDofollow");
    $backlink_count_query_sub->addExpression("SUM(tbl.totalSource)","totalSource");
    $backlink_count_query_sub->fields("tbl",array("pid","id"));
    $backlink_count_query_sub->groupBy("tbl.pid");


    $query->leftJoin($backlink_count_query_sub,"tbl_backlink_count_sub_query","tbl_backlink_count_sub_query.pid=tbl_node.nid");
    $query->fields("tbl_backlink_count_sub_query");

    $query->leftJoin($_domain_query,"tbl_domain_query","tbl_domain_query.pid=tbl_node.nid");
    $query->fields("tbl_domain_query");

    $query->join("field_data_field_domain","field_domain","field_domain.entity_id=tbl_node.nid");
    $query->addField("field_domain","field_domain_value","project_domain");
//    $query->range(0,10);
    $result = $query->execute()->fetchAll();
}catch (Exception $e){
    _print_r($e);
}
//die;
//drupal_get_messages();
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
            <a class="btn btn-export btn-green btn-search-project <?php echo (empty($result))?'btn-disable':'' ?> <?php echo !empty($packet->excel)?"excel":""; ?>" href="#">
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
                                    <input type="checkbox" name="select" class="selectAll" <?php echo empty($result)?"readonly disabled":""; ?>>
                                    <i class="fa-regular fa-square"></i>
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
                        <?php if(!empty($result)): $stt=1;?>
                            <?php foreach($result as $item):  ?>
                                <?php
                                $_domain = str_replace("http://","",$item->project_domain);
                                $_domain = str_replace("https://","",$_domain);
                                $_domain = str_replace("www","",$_domain);
                                ?>
                                <tr>
                                    <?php if(!empty($result)): ?>
                                        <td width="50px;" data-title="select">
                                            <label class="mask-chekbox" >
                                                <input name="nid[]" type="checkbox" value="<?php echo($item->nid); ?>" class="" >
                                                <i class="fa-regular fa-square"></i>
                                            </label>
                                        </td>
                                    <?php endif; ?>

                                    <td width="50px;" data-title=Stt>
                                        <?php echo($stt); ?>
                                    </td>
                                    <td data-key="title" data-title="title" class="text-green text-left title">
                                        <div class="d-flex space-between">
                                            <a href="/quan-ly-backlink/du-an/<?php echo($item->nid); ?>" title="<?php echo(htmlspecialchars($item->title)); ?>" class="project-title sort-text">
                                                <?php echo(htmlspecialchars($item->title)); ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td data-key="domain" class="text-left ">
                                        <div class="d-flex space-between">
                                            <span class="domain sort-text" target="_blank" rel="\" href="//<?php echo $_domain; ?>" title="<?php echo $item->project_domain; ?>">
                                                <?php echo $item->project_domain; ?>
                                            </span>
                                            <a class="" href="//<?php echo $_domain; ?>" title="<?php echo $item->project_domain; ?>" target="_blank">
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td data-key="totalBacklink" ><?php echo !empty($item->totalBacklink)?$item->totalBacklink:"-"; ?></td>
                                    <td data-key="totalDomain" ><?php echo !empty($item->total_domain)?$item->total_domain:0; ?></td>
                                    <td data-value="<?php if(!empty($item->totalSource)) echo !empty($item->totalIndex)?round(100*$item->totalIndex/$item->totalSource):0; ?>" data-key="googleIndex" >
                                        <?php
                                        if(!empty($item->totalSource)) {
                                            echo !empty($item->totalIndex)?round(100*$item->totalIndex/$item->totalSource)."%":"-";
                                        }else{
                                            echo "-";
                                        }
                                        ?>
                                    </td>
                                    <td data-value="<?php echo !empty($item->totalBacklink)?round(100*$item->totalDofollow/$item->totalBacklink):0; ?>" data-key="dofollow" >
                                        <?php
                                        if(!empty($item->totalDofollow)){
                                            echo !empty($item->totalBacklink)?round(100*$item->totalDofollow/$item->totalBacklink)."%":"-";
                                        }else{
                                            echo "-";
                                        }

                                        ?>
                                    </td>
                                    <td data-value="<?php echo $item->changed ?>" data-key="created" ><?php echo date("d/m/Y",$item->changed); ?></td>
                                    <td data-title="actions">
                                        <button type="button" class="btn-edit-project" data-id="<?php echo($item->nid); ?>" title="Sửa dự án"><i class="far fa-pen-to-square fa-fw"></i></button>
                                        <button type="button" class="btn-delete-project" data-id="<?php echo($item->nid); ?>" data-name="<?php echo htmlspecialchars($item->title); ?>" title="Xóa"><i class="far fa-trash-can fa-fw"></i></button>
                                    </td>
                                </tr>
                                <?php $stt++; endforeach; ?>
                        <?php else: ?>
                            <?php for($i=1;$i<=5;$i++): ?>
                                <tr>
                                    <td width="50px;" data-title="select">
                                        <label class="mask-chekbox">
                                            <input name="nid[]" type="checkbox" value="156128" class="" disabled readonly>
                                            <i class="fa-regular fa-square"></i>
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

