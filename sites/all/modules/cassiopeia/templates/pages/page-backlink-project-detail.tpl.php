<?php //drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user.js', ['weight' => 1000]); ?>
<?php
global $user;
$option = isset($_REQUEST['option']) ? $_REQUEST['option'] : "total-backlink";
$packet = cassiopeia_get_available_packet_by_uid($user->uid);
$_SESSION['backlink_valid'] = 0;
$_SESSION['backlink_invalid'] = 0;
$_SESSION['backlink_duplicate'] = 0;
$arg = arg();
drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-backlink-project-detail.js', ['weight' => 1000]);
drupal_add_js("https://code.jquery.com/ui/1.13.0/jquery-ui.js");
drupal_add_css("https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css");
try {
  $project_id = $variables['project_id'];
  $cache = !empty($_REQUEST['data']) ? $_REQUEST['data'] : [];
  $cache['project_id'] = $project_id;
  $project = node_load($project_id);
  $query = db_select("tbl_backlink", "tbl_backlink");
  $query->fields("tbl_backlink");
  $query->join("tbl_backlink_detail", "tbl_backlink_detail", "tbl_backlink_detail.nid=tbl_backlink.id");
  $query->addExpression("SUM(CASE WHEN tbl_backlink_detail.rel='dofollow' THEN 1 ELSE 0 END)", "total_dofollow");

  $query->addExpression("SUM(CASE WHEN tbl_backlink.indexed=1 THEN 1 ELSE 0 END)", "total_indexed");
  $query->addExpression("COUNT(DISTINCT(tbl_backlink_detail.id))", "totalBacklink");

  $query->condition("tbl_backlink.pid", $project_id);
  if (!empty($cache['from_date'])) {
    $query->condition("tbl_backlink.changed", strtotime(date("d-m-Y 00:00", strtotime($cache['from_date']))), ">=");;
  }
  if (!empty($cache['to_date'])) {
    $query->condition("tbl_backlink.changed", strtotime(date("d-m-Y 23:59", strtotime($cache['to_date']))), "<=");;
  }
  $result = $query->execute()->fetchObject();
  //    cassiopeia_dump($result);
  $conditions = [];
  $conditions['field_user'] = [
    "type" => "fieldCondition",
    "key" => "target_id",
    "value" => $user->uid,
    "condition" => "=",
  ];
  $conditions['field_backlink_project'] = [
    "type" => "fieldCondition",
    "key" => "nid",
    "value" => $project->nid,
    "condition" => "=",
  ];
  $tags = cassiopeia_get_items_by_conditions($conditions, "tags", "taxonomy_term");
  //    _print_r($tag)
  $tag_options = [];
  $tag_options['all'] = "Tất cả";
  if (!empty($tags)) {
    foreach ($tags as $tag) {
      $tag_options[$tag->tid] = $tag->name;
    }
  }
  $cache['tag_options'] = $tag_options;
  unset($tag_options['all']);
}
catch (Exception $e) {
  cassiopeia_dump($e);
}
$stt = 1;

?>


<input hidden type="number" id="totalItems">
<input hidden type="number" id="checkedItems">
<span id="listOfID" data-value=""></span>
<input type="hidden" id="stop" value="1">
<input type="hidden" id="project_id" value="<?php echo($project_id); ?>">
<input type="hidden" id="project_domain"
       value="<?php echo($project->field_domain['und'][0]['value']); ?>">
<!-- <button class="btn-scroll">Click me</button> -->
<div class="page page-detail page-user-backlink-project-detail">
  <div class="page-header">
    <div class="page-title">
      <h1
        title="<?php echo($project->title); ?> - <?php echo($project->field_domain['und'][0]['value']); ?>">
        <span><?php echo($project->title); ?> - <?php echo($project->field_domain['und'][0]['value']); ?></span>
      </h1>
      <button type="button" class="btn-green btn-add-backlink">
        <i class="fa fa-plus"></i>
        Thêm Backlinks
      </button>
      <button class="btn btn-green btn-tag-manager">
        <i class="fas fa-tags fa-fw"></i>
        Quản lý tags
      </button>
    </div>
    <div class="tutorial">
      <a href="/<?php echo drupal_get_path_alias("node/169970"); ?>"
         target="_blank" class="tutorial-keywords">
        <span class="icon"><img
            src="/sites/all/themes/cassiopeia_theme/img/icons/icon-20.png"
            alt=""></span> <span>Hướng dẫn sử dụng</span>
      </a>
    </div>
    <div class="page-header-bottom">
      <div class="backlink-status">
        <ul>
          <li><a href="#" data-option="total-backlink"
                 class="<?php if ($option == "total-backlink") {
                   echo "active";
                 } ?>">Tất cả backlink(<span
                class="total-backlink"><?php echo($result->totalBacklink); ?></span>)</a>
          </li>
          <li><a href="#" data-option="total-dofollow"
                 class="<?php if ($option == "total-dofollow") {
                   echo "active";
                 } ?>">Do follow(<?php echo($result->total_dofollow) ?>)</a>
          </li>
          <li><a href="#" data-option="total-indexed"
                 class="<?php if ($option == "total-indexed") {
                   echo "active";
                 } ?>">Google index(<?php echo($result->total_indexed); ?>)</a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="df-tabs">
    <ul class="p-tabs nav nav-pills mb-3" id="pills-tab">
      <li class="nav-item <?php if (empty($arg[3]) || $arg[3] != "bao-cao") {
        echo "active";
      } ?>">
        <a class="nav-link " id="pills-statistic-tab" href="#">Quản lý
          backlink</a>
      </li>
      <li class="nav-item <?php if (!empty($arg[3]) && $arg[3] == "bao-cao") {
        echo "active";
      } ?>">
        <a class="nav-link" id="pills-report-tab"
           href="/quan-ly-backlink/du-an/<?php echo($project->nid); ?>/bao-cao">Báo
          cáo</a>
      </li>
    </ul>
    <div class="page-search df-tabs-search">
      <div class="input-group-search">
        <input type="text" placeholder="Tìm backlink..." name="url"
               value="<?php if (!empty($_REQUEST['url']))
                 echo $_REQUEST['url'] ?>">
        <button type="button" class="btn-clear-text"><span class="fa fa-times"
                                                           aria-hidden="true"></span>
        </button>
        <button type="button" class="btn-type-1 btn-submit btn-search-project">
          <i class="fas fa-magnifying-glass fa-fw" aria-hidden="true"></i>
        </button>
      </div>
    </div>
  </div>

  <div class="tab-content" id="pills-tabContent">
    <div
      class="tab-pane fade <?php if (empty($arg[3]) || $arg[3] != "bao-cao") {
        echo "in active";
      } ?>" id="statistic">
      <div class="page-container">
        <div class="page-main result table-responsive">
          <div class="running"></div>
          <div class="smart-filter mb-24">
            <div class="smart-filter-name">Quản lý Backlink</div>
            <div class="smart-filter-form">
              <?php
              $cassiopeia_backlink_filter_form = drupal_get_form("cassiopeia_backlink_filter_form", $cache);
              if (!empty($cassiopeia_backlink_filter_form)) {
                $cassiopeia_backlink_filter_form = drupal_render($cassiopeia_backlink_filter_form);
                print($cassiopeia_backlink_filter_form);
              }
              ?>
            </div>
          </div>
          <div class="t-body">
            <table
              class="table table-striped table-div-responsive table-type-2 ">
              <thead>
              <tr>
                <th class="col-expand" data-title="expand"></th>
                <th class="col-checkbox" data-title="select">
                  <label class="mask-chekbox">
                    <input type="checkbox" name="select" class="selectAll">
                    <i class="fa-regular fa-square"></i>
                  </label>
                </th>
                <th class="col-stt">STT</th>
                <th data-direction="ASC" data-title="title"
                    data-sort="refer-page" class="sort col-source ">Nguồn đặt
                  Backlink
                </th>
                <th data-direction="ASC" data-sort="rel" class="sort col-rel">
                  Thuộc tính
                </th>
                <th data-direction="ASC" data-sort="anchor-text"
                    class="sort col-anchor">AnchorText
                </th>
                <th data-direction="ASC" data-sort="url" class="sort col-url">
                  URL SEO
                </th>
                <th data-direction="ASC" data-sort="indexed"
                    class="sort col-indexed">Google Indexed
                </th>
                <th data-direction="ASC" data-sort="tag" class="col-tag sort"
                    width="10%">Tag
                </th>
                <?php if (user_has_role(3)): ?>
                  <th data-direction="ASC" data-sort="tel" class="col-tel sort"
                      width="10%">SDT
                  </th>
                <?php endif; ?>
                <th data-direction="DESC" data-sort="changed"
                    class="sort col-created current">Ngày cập nhật
                </th>
                <th data-direction="ASC" data-sort="status"
                    class="sort col-status">Tình trạng
                </th>
              </tr>
              <tr class="">
                <th colspan="12" class="th-progress">
                  <div class="progress-bar-block modal-custom">
                    <progress id="file" value="0" max="2180"> 32%</progress>
                  </div>
                </th>
              </tr>
              </thead>
              <tbody class="result">

              </tbody>
            </table>
          </div>
          <div class="page-utilities-bot">
            <div class="page-utilities-left">
              <form action="#" class="form-bulk">
                <div class="form-item">
                  <select name="check-type" id="" class="btn-gray btn-type-1">
                    <option value="check_baclink">Quét backlink</option>
                    <?php if (user_has_role(3) || $user->uid == 31): ?>
                      <option value="get_tels">Quét số điện thoại</option>
                    <?php endif; ?>
                    <option value="check_indexed">Check google indexed</option>
                    <option value="delete_backlink">Xóa backlink</option>
                    <option value="backlink_tag">Thêm vào tags</option>
                    <option value="backlink_remove_form_tag">Xóa khỏi tags
                    </option>
                  </select>
                </div>
                <div class="form-item form-item-captcha-resolve hidden">
                  <select name="captcha-resolve" id=""
                          class="btn form-control btn-gray btn-type-1">
                    <option value="auto" >Giải Captcha tự động</option>
                    <option value="manual" selected>Giải Captcha thủ công</option>
                  </select>
                </div>
                <div class="form-item">
                  <button type="button"
                          class="btn-type-1 btn-submit bg-green c-white">Áp dụng
                  </button>
                </div>
              </form>
              <button type="button"
                      class="btn-text btn-show-options btn-col-options d-none"
                      data-toggle="modal" data-target="#editColumns">
                <img
                  src="/sites/all/themes/cassiopeia_theme/img/icons/icon-22.png"
                  alt="">
              </button>
            </div>
            <div class="page-utilities-right">
              <a
                class="btn-df btn-green icon-before btn-export <?php echo !empty($packet->excel) ? "excel" : ""; ?>"
                href="#">
                <span class="fa fa-file-excel-o fs-16 mr-5"
                      aria-hidden="true"></span>
                Xuất báo cáo
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div id="modalAddBacklink" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;
        </button>
        <h4 class="modal-title">Thêm Backlinks</h4>
      </div>
      <div class="modal-body">

        <?php
        $cassiopeia_add_backlink_form = drupal_get_form("cassiopeia_add_backlink_form", $project);
        if (!empty($cassiopeia_add_backlink_form)) {
          $cassiopeia_add_backlink_form = drupal_render($cassiopeia_add_backlink_form);
          print($cassiopeia_add_backlink_form);
        }
        ?>
      </div>
    </div>

  </div>
</div>
<!-- Modal -->
<textarea type="hidden" id="tags"
          value=''><?php echo(json_encode($tag_options)); ?></textarea>
<span id="sorting" data-sort="changed" data-direction="DESC"></span>
<span id="option" data-option="<?php echo $option ?>"></span>
<div id="modalTags" class="modal fade" role="dialog">
  <input type="hidden" id="modalTagOption">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;
        </button>
        <h4 class="modal-title">Thêm vào tags</h4>
      </div>
      <div class="modal-body">
        <div class="Tag-toolTip">
          <button
            type="button"
            class="btn btn-secondary"
            data-toggle="tooltip"
            data-placement="right"
            title="Tạo Tag nhằm mục đích nhóm các Backlinks cùng Tính chất hoặc Chủ đề"
          >
            <span class="fa fa-question-circle-o" aria-hidden="true"></span>
          </button>
        </div>
        <div class="form-item form-item-tags form-type-textfield form-group">
          <label class="control-label" for="edit-tags">Tag</label>
          <input type="text" class="tagify-input"
                 placeholder="Phân cách các thẻ Tag nhấn phím Enter">
        </div>
        <div class="buttons">
          <button class="btn btn-success btn-green">Xác nhận</button>
        </div>
      </div>
    </div>

  </div>
</div>
<div class="modal-tag-manager">
  <div class="modal-dialog block-dialog">
    <div class="block-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">Quản lý tags</h4>
    </div>
    <div class="block-container">
      <table class="table table-hover table-stripped">
        <thead>
        <tr>
          <th width="80%">Tag</th>
          <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tags as $tag): ?>
          <tr data-tid="<?php echo $tag->tid; ?>">
            <td>
              <input type="text" class="form-control"
                     value="<?php echo $tag->name; ?>" name="tag"
                     data-tid="<?php echo $tag->tid; ?>">
            </td>
            <td class="text-center">
              <button class="btn btn-delete-tag"
                      data-tid="<?php echo $tag->tid; ?>"><i
                  class="fa fa-trash  danger"
                  data-tid="<?php echo $tag->tid; ?>"></i></button>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal-create-backlink-progress">
  <div class="block-dialog">
    <div class="block-header"><h4>Đang tạo backlink...</h4></div>
    <div class="block-body">
      <div class="create-backlink-progress-bar">
        <progress id="file" value="0" max="2180"> 32%</progress>
      </div>
    </div>
  </div>
</div>