<div class="visible-xs mobile-note">
    <div class="text confirm">
        Mời bạn dùng Laptop hoặc Desktop để sử dụng chức năng này
    </div>
    <!--                                   <span class="close">&times;</span>-->
</div>
<?php drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-content-projects.js', ['weight' => 1000]); ?>
<?php
$arg = arg();
$cache = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
$cache['sort_by'] = !empty($cache['sort_by'])?$cache['sort_by']:"changed";
$cache['sort_direction'] = !empty($cache['sort_direction'])?$cache['sort_direction']:"DESC";

_print_r($cache);
$export = false;
if(!empty($arg[1])&&$arg[1]=="export"){
    $export = true;
}
global $user;
$packet = cassiopeia_get_available_packet_by_uid($user->uid);

try{
    $cassiopeia_content_query = db_select("cassiopeia_content","cassiopeia_content");
    $cassiopeia_content_query->addField("cassiopeia_content","pid","pid");
    $cassiopeia_content_query->addExpression("AVG(word_count)","word_count_avg");
    $cassiopeia_content_query->addExpression("AVG(point)","point_avg");
    $cassiopeia_content_query->addExpression("COUNT(id)","id_count");
    $cassiopeia_content_query->groupBy("cassiopeia_content.pid");
    $cassiopeia_content_query->condition("uid",$user->uid);

    $query = db_select("node","tbl_node");;
    $query->fields("tbl_node");
    $query->condition("tbl_node.type","content_project");
    $query->condition("tbl_node.uid",$user->uid);

    $query->join("field_data_field_domain","field_domain","field_domain.entity_id=tbl_node.nid");
    $query->addField("field_domain","field_domain_value","project_domain");

    $query->leftJoin($cassiopeia_content_query,"tbl_count","tbl_count.pid=tbl_node.nid");
    $query->fields("tbl_count");
    $query->orderBy("tbl_node.changed","DESC");
//    $query->join("field_data_field_raw_title","field_raw_title","field_raw_title.entity_id=tbl_node.nid");
//    $query->addField("field_raw_title","field_raw_title_value","field_raw_title_value");
//    $final_query = db_select($query,"tbl_final");
//    $final_query->fields("tbl_final");

    $result = $query->execute()->fetchAll();
}catch (Exception $e){
    _print_r($e);
}
//_print_r($result);
?>
<?php if(!$export): ?>
    <div class="page page-backlink page-content-project">
        <div class="page-header">
            <div class="page-title">
                <h1>Tất cả dự án Content</h1>
                <button type="button" class="btn-green btn-add-content-project">
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
        <form action="" class="form-sort">
            <input type="hidden" name="sort" value="">
            <input type="hidden" name="direction" value="">
        </form>
        <div class="page-container">
            <div class="page-main">
                <form class="form-export" action="quan-ly-du-an-content/export">
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
                                <th data-sort="title" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-18 text-left sort">Tên dự án</th>
                                <th data-sort="domain" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-18 text-left sort">Website</th>
                                <th data-sort="article_count" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-18 text-center sort">Số bài viết</th>
                                <th data-sort="word_count" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-18 text-center sort">Số từ trung bình</th>
                                <th data-sort="point" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-18 text-center sort">Điểm trung bình</th>
                                <th data-sort="changed" data-direction="<?php echo $cache['sort_direction']; ?>" class="w-18 text-left sort">Ngày cập nhật</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="">
                            <?php if(!empty($result)): ?>
                                <?php $stt=1; foreach($result as $item): ?>
                                    <tr>
                                        <td width="50px;" data-title="select">
                                            <label class="mask-chekbox" >
                                                <input name="nid[]" type="checkbox" value="<?php echo $item->nid; ?>" class="" >
                                                <i class="fa-regular fa-square"></i>
                                            </label>
                                        </td>
                                        <td><?php echo $stt; ?></td>
                                        <td data-key="title" data-value="<?php echo stripVN($item->title); ?>" class="text-green text-left"><a href="/quan-ly-du-an-content/<?php echo $item->nid; ?>" class="project-title"><?php echo $item->title; ?></a></td>
                                        <td data-key="domain" data-value="<?php echo stripVN($item->project_domain); ?>" class="text-left"><?php echo $item->project_domain; ?></td>
                                        <td data-key="article_count" data-value="<?php echo $item->id_count; ?>"><?php echo number_format($item->id_count,0,",","."); ?></td>
                                        <td data-key="word_count" data-value="<?php echo $item->word_count_avg; ?>"><?php echo number_format((int)$item->word_count_avg,0,",","."); ?></td>
                                        <td data-key="point" data-value="<?php echo $item->point_avg; ?>"><span class="point rank-<?php if((int)$item->point_avg<50){ echo "low";}elseif((int)$item->point_avg<90){ echo "medium";}else{ echo "high";} ?>"><?php echo (int)$item->point_avg; ?></span>/100</td>
                                        <td data-key="changed" data-value="<?php echo $item->changed; ?>"><?php echo date("d/m/Y",$item->changed); ?></td>
                                        <td>
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
                                                <i class="fa-regular fa-square"></i>
                                            </label>
                                        </td>
                                        <td width="50px;" data-title="Stt">...</td>
                                        <td data-key="title" data-title="title" class="text-green text-left title">...</td>
                                        <td data-key="domain" class="text-left ">...</td>
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


<?php else: ?>
    <?php
//    if(kenfox_trademark_registration_export_access()){
        include  drupal_get_path('module','cassiopeia')."/excel/phpspreadsheet.inc";
//            print_r( drupal_get_path('module','cassiopeia')."/excel/phpspreadsheet.inc");
        $worksheets = array();
        $worksheet = array('title' => 'Trademark registration','data'=>array(), 'merge_cells'=> array());
        $worksheet['data'][] = array(
            mb_convert_encoding('Case ID' ,  "UTF-8"),
            mb_convert_encoding('Kenfox Ref' ,  "UTF-8"),
            mb_convert_encoding('Kenfox Attny' ,  "UTF-8"),
            mb_convert_encoding('Client' ,  "UTF-8"),
            mb_convert_encoding('Client Ref' ,  "UTF-8"),
            mb_convert_encoding('Client Attny' ,  "UTF-8"),

            mb_convert_encoding('Third Party' ,  "UTF-8"),
            mb_convert_encoding('Third Party ref' ,  "UTF-8"),
            mb_convert_encoding('Third Party attny' ,  "UTF-8"),

            mb_convert_encoding('Applicant' ,  "UTF-8"),
            mb_convert_encoding('Applicant Address' ,  "UTF-8"),
            mb_convert_encoding('Applicant Country' ,  "UTF-8"),

            mb_convert_encoding('Priority No' ,  "UTF-8"),
            mb_convert_encoding('Priority Date' ,  "UTF-8"),
            mb_convert_encoding('Priority Country' ,  "UTF-8"),

            mb_convert_encoding('Application No' ,  "UTF-8"),
            mb_convert_encoding('Application Date' ,  "UTF-8"),
            mb_convert_encoding('Application Country' ,  "UTF-8"),
            mb_convert_encoding('Application Formality' ,  "UTF-8"),
            mb_convert_encoding('Application Substantive' ,  "UTF-8"),

            mb_convert_encoding('Registration' ,  "UTF-8"),
            mb_convert_encoding('Registration Date' ,  "UTF-8"),
            mb_convert_encoding('Registration Epx1' ,  "UTF-8"),
            mb_convert_encoding('Registration Epx2' ,  "UTF-8"),

            mb_convert_encoding('IR No' ,  "UTF-8"),
            mb_convert_encoding('IR Date' ,  "UTF-8"),
            mb_convert_encoding('IR International' ,  "UTF-8"),
            mb_convert_encoding('IR Examiner' ,  "UTF-8"),

            mb_convert_encoding('Mark' ,  "UTF-8"),
//        mb_convert_encoding('Type' ,  "UTF-8"),
            mb_convert_encoding('Class' ,  "UTF-8"),
            mb_convert_encoding('Status' ,  "UTF-8"),
            mb_convert_encoding('Note' ,  "UTF-8"),
        );
        $rowIndex=2;
//        foreach ($trademark_registrations as $trademark_registration) {
//            $trademark_registration = kenfox_trademark_trademark_registration_load($trademark_registration->id);
//            $maxRow = 1;
//            $maxRow = $maxRow<count($trademark_registration->field_class)?count($trademark_registration->field_class):$maxRow;
//            $maxRow = $maxRow<count($trademark_registration->field_kenfox_attorney)?count($trademark_registration->field_kenfox_attorney):$maxRow;
//            $maxRow = $maxRow<count($trademark_registration->field_client)?count($trademark_registration->field_client):$maxRow;
//            $maxRow = $maxRow<count($trademark_registration->field_third_party)?count($trademark_registration->field_third_party):$maxRow;
//            $maxRow = $maxRow<count($trademark_registration->field_applicant)?count($trademark_registration->field_applicant):$maxRow;
//            $maxRow = $maxRow<count($trademark_registration->field_application)?count($trademark_registration->field_application):$maxRow;
//            $maxRow = $maxRow<count($trademark_registration->field_priority)?count($trademark_registration->field_priority):$maxRow;
//            $maxRow = $maxRow<count($trademark_registration->field_ir)?count($trademark_registration->field_ir):$maxRow;
//            $activeRow = array();
//
//            $field_classes = array();
//            foreach ($trademark_registration->field_class as $field_class) {
//                $field_classes[] = kenfox_trademark_trademark_class_load($field_class['id']);
//            }
//
//            $field_kenfox_attorneys = array();
//            foreach ($trademark_registration->field_kenfox_attorney as $field_kenfox_attorney) {
//                $field_kenfox_attorneys[] = user_load($field_kenfox_attorney['id']);
//            }
//
//            $field_clients = array();
//            foreach ($trademark_registration->field_client as $field_client) {
//                $field_clients[] = kenfox_entities_entities_load($field_client['id']);
//            }
////        _print_r($field_clients);
//            $field_third_parties = array();
//            foreach ($trademark_registration->field_third_party as $field_third_party) {
//                $field_third_parties[] = kenfox_entities_entities_load($field_third_party['id']);
//            }
//
//            $field_applicants = array();
//            foreach ($trademark_registration->field_applicant as $field_applicant) {
//                $field_applicants[] = kenfox_entities_entities_load($field_applicant['id']);
//            }
////        _print_r("Applicants");
//            $field_applications =$trademark_registration->field_application;
//            $field_applications =$trademark_registration->field_application;
//
//            $field_priorities = $trademark_registration->field_priority;
////        _print_r($field_priorities);
//            $field_irs = $trademark_registration->field_ir;
//            $_status_names_ = array();
//            foreach (unserialize(TRADEMARK_STATUS) as $key => $value) {
//                if (!empty($trademark_registration->{$value['machine_name']})) {
//                    $_status_names_[] = $value['name'];
//                }
//            }
//
//            for($i=0;$i<$maxRow;$i++){
//                $client_attorneys = array();
//                if(!empty($trademark_registration->field_client[$i]['attorney'])){
//                    foreach($trademark_registration->field_client[$i]['attorney'] as $_item){
//                        $client_attorneys[] = !empty(kenfox_lawyer_lawyer_load($_item['id']))?kenfox_lawyer_lawyer_load($_item['id'])->name:"";
//                    }
//                }
//                $third_partys = array();
//                if(!empty($trademark_registration->field_third_party[$i]['attorney'])){
//                    foreach($trademark_registration->field_third_party[$i]['attorney'] as $_item){
//                        $third_partys[] = !empty(kenfox_lawyer_lawyer_load($_item['id']))?kenfox_lawyer_lawyer_load($_item['id'])->name:"";
//                    }
//                }
//                $applicant_country = !empty($field_applicants[$i])?kenfox_country_country_load($field_applicants[$i]->country):null;
//                $_priority_country = !empty($field_priorities[$i])?kenfox_country_country_load($field_priorities[$i]['country']):null;
//                $application_country = !empty($field_applications[$i])?kenfox_country_country_load($field_applications[$i]['country']):null;
//                $worksheet['data'][] = array(
//                    mb_convert_encoding($trademark_registration->code,  "UTF-8"),
//                    mb_convert_encoding($trademark_registration->kenfox_ref,  "UTF-8"),
//
//                    mb_convert_encoding(!empty($field_kenfox_attorneys[$i])?$field_kenfox_attorneys[$i]->name:'',  "UTF-8"),
//                    mb_convert_encoding(!empty($field_clients[$i])?$field_clients[$i]->name:'',  "UTF-8"),
//                    mb_convert_encoding(!empty($trademark_registration->field_client[$i])?$trademark_registration->field_client[$i]['ref']:'',  "UTF-8"),
//                    mb_convert_encoding(implode(",",$client_attorneys),  "UTF-8"),
//
//                    mb_convert_encoding(!empty($field_third_parties[$i])?$field_third_parties[$i]->name:'',  "UTF-8"),
//                    mb_convert_encoding(!empty($trademark_registration->field_third_party[$i])?$trademark_registration->field_third_party[$i]['ref']:'',  "UTF-8"),
//                    mb_convert_encoding(implode(",",$third_partys),  "UTF-8"),
//
//                    mb_convert_encoding(!empty($field_applicants[$i])?$field_applicants[$i]->name:'',  "UTF-8"),
//                    mb_convert_encoding(!empty($field_applicants[$i])?$field_applicants[$i]->address:'',  "UTF-8"),
//                    mb_convert_encoding(!empty($applicant_country)?$applicant_country->code:'' ,  "UTF-8"),
//
//                    mb_convert_encoding(!empty($field_priorities[$i])?$field_priorities[$i]['no']:'',  "UTF-8"),
//                    mb_convert_encoding(!empty($field_priorities[$i])?date("d/m/Y",$field_priorities[$i]['date']):'',  "UTF-8"),
//                    mb_convert_encoding(!empty($_priority_country)?$_priority_country->code:'' ,  "UTF-8"),
//
//                    mb_convert_encoding(!empty($field_applications[$i]['no'])?$field_applications[$i]['no']:'',  "UTF-8"),
//                    mb_convert_encoding(!empty($field_applications[$i]['date'])?date('d/m/Y',$field_applications[$i]['date']):'',  "UTF-8"),
//                    mb_convert_encoding(!empty($application_country)?$application_country->code:'',  "UTF-8"),
//                    mb_convert_encoding(!empty($field_applications[$i])?$field_applications[$i]['formality']:'',  "UTF-8"),
//                    mb_convert_encoding(!empty($field_applications[$i])?$field_applications[$i]['substantive']:'',  "UTF-8"),
//
//                    mb_convert_encoding($trademark_registration->reg,  "UTF-8"),
//                    mb_convert_encoding(!empty($trademark_registration->date)?date("d/m/Y",$trademark_registration->date):'',  "UTF-8"),
//                    mb_convert_encoding(!empty($trademark_registration->epx1)?date("d/m/Y",$trademark_registration->epx1):'',  "UTF-8"),
//                    mb_convert_encoding(!empty($trademark_registration->epx2)?date("d/m/Y",$trademark_registration->epx2):'',  "UTF-8"),
//
//                    mb_convert_encoding(!empty($field_irs[$i]['no'])?$field_irs[$i]['no']:"",  "UTF-8"),
//                    mb_convert_encoding(!empty($field_irs[$i]['date'])?date("d/m/Y",$field_irs[$i]['date']):'',  "UTF-8"),
//                    mb_convert_encoding(!empty($field_irs[$i]['international'])?$field_irs[$i]['international']:'',  "UTF-8"),
//                    mb_convert_encoding(!empty($field_irs[$i]['examiner'])?$field_irs[$i]['examiner']:'',  "UTF-8"),
//
//                    mb_convert_encoding(!empty($trademark_registration->mark_text)?$trademark_registration->mark_text:'',  "UTF-8"),
//                    mb_convert_encoding(!empty($field_classes[$i])?$field_classes[$i]->name:'',  "UTF-8"),
//                    mb_convert_encoding(implode("|",$_status_names_),  "UTF-8"),
//                    mb_convert_encoding(!empty($trademark_registration->note)?$trademark_registration->note:'',  "UTF-8"),
//                );
//            }
//            $merge_columns = array(
//                'A',
//                'B',
//                'D',
//                'E',
//                'F',
//                'U',
//                'V',
//                'W',
//                'X',
//                'Y',
//                'Z',
//                'AA',
//                'AB',
//                'AC',
//                'AE',
//                'AF',
//            );
//            if($maxRow>1){
//                foreach($merge_columns as $column){
//                    $worksheet['merge_cells'][] = $column.$rowIndex.':'.$column.($rowIndex+$maxRow-1);
//                }
//            }
//            $rowIndex++;
//        }
        $worksheets[]=$worksheet;
//        print_r($worksheets);
        phpspreadsheet_export($worksheets, 'file_'.date('d_m_Y',REQUEST_TIME));
//    }
    ?>
<?php endif; ?>

<div class="hidden">
    <?php
    $cassiopeia_backlink_filter_form = drupal_get_form("cassiopeia_content_project_filter_form",$cache);
    if(!empty($cassiopeia_backlink_filter_form)){
        $cassiopeia_backlink_filter_form = drupal_render($cassiopeia_backlink_filter_form);
        print($cassiopeia_backlink_filter_form);
    }
    ?>
</div>

