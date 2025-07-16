<div class="visible-xs mobile-note">
    <div class="text confirm">
        Mời bạn dùng Laptop hoặc Desktop để sử dụng chức năng này
    </div>
    <!--                                   <span class="close">&times;</span>-->
</div>
<?php
global $user;
$tutorial = true;
$project = node_load($pid);
if(empty($project->field_tutorial['und'][0]['value'])){
    $project->field_tutorial['und'][0]['value'] = 1;
    node_save($project);
}else{
    $tutorial=false;
}
//$tutorial = true;
$packet = cassiopeia_get_available_packet_by_uid($user->uid);
drupal_add_js("https://www.gstatic.com/charts/loader.js");
drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-check-content-add.js', ['weight' => 1000]);
$node = !empty($variables['node'])?$variables['node']:null;
_print_r($node);
try{
    module_load_include('inc', 'node', 'node.pages');
    $node_form = new stdClass;
    $node_form->type = 'content_article';
    $node_form->language = LANGUAGE_NONE;
    $node_form->pid = $pid;
    $node_form->id = !empty($node)?$node->id:null;
    $node_form->title = !empty($node)?$node->title:"";
    $node_form->body['und'][0]['value'] = !empty($node)?htmlspecialchars_decode($node->body):"";
    $node_form->field_content_point['und'][0]['value'] = !empty($node)?$node->point:0;
    $form = drupal_get_form('content_article_node_form', $node_form);
    $conditions = array();
    $conditions['field_user'] = array(
        "type"      => "fieldCondition",
        "key"       => "target_id",
        "value"     => $user->uid,
        "condition" => "=",
    );
    $conditions['field_content_project'] = array(
        "type"      => "fieldCondition",
        "key"       => "nid",
        "value"     => $pid,
        "condition" => "=",
    );
    $tags = cassiopeia_get_items_by_conditions($conditions,"tags","taxonomy_term");
//    _print_r($tags);
    $tag_options = array();
    $tag_options['all'] = "Tất cả";
    if(!empty($tags)){
        foreach($tags as $tag){
            $tag_options[$tag->tid] = $tag->name;
        }
    }
    $cache['tag_options'] = $tag_options;
    unset($tag_options['all']);
}catch (Exception $e){
    cassiopeia_dump($e);
}
?>
<span id="listOfID" data-value=""></span>
<input hidden type="text" id="nid" value="<?php echo !empty($node)?$node->id:""; ?>">
<input hidden type="number" id="totalItems">
<input hidden type="number" id="checkedItems">
<div class="page page-check-content">
    <div class="page-header">
        <div class="page-title">
            <h1>Chi tiết bài viết </h1> <button class="btn-save btn-green"><i class="fa fa-bookmark"></i> Lưu bài viết</button>
        </div>
    </div>
    <div class="page-container">
        <div class="check-content">
            <div class="left-block">
                <div class="article-content">
                    <div class="block-content">
                        <?php
                        if(!empty($form)){
                            $form  = drupal_render($form);
                            echo $form;
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="right-block">
                <div class="">
                    <form action="" class="check-form">
                        <?php if($tutorial): ?>
                            <div class="tutorial-text">
                                <img src="/sites/all/themes/cassiopeia_theme/img/toturial-arrow.png" alt=""> Mời bạn nhập từ khóa chính cho bài viết
                            </div>
                        <?php endif; ?>
                        <div class="form-group form-item-keyword">
                            <input placeholder="Nhập từ khóa chính của bài viết..." type="text" class="seo-input" name="keyword" value="<?php if(!empty($node)) echo $node->keyword; ?>">
                        </div>
                      <div class="form-item-captcha-resolve">
                        <select name="captcha-resolve" id=""
                                class="btn form-control btn-gray btn-type-1">
                          <option value="auto" >Giải Captcha tự động</option>
                          <option value="manual" selected>Giải Captcha thủ công</option>
                        </select>
                      </div>
                      <div class="form-item">
                        <button type="button" class="btn-check-content btn-seo-search btn-submit"><i class="fa fa-search"></i></button>
                      </div>
                    </form>
                    <div class="information result">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if($tutorial): ?>
        <div class="tutorial-background">

        </div>
    <?php endif; ?>

</div>
<div class="cache-block" style="display: none;"></div>

<textarea class="hidden" type="hidden" id="tags" value=''><?php echo(json_encode($tag_options)); ?></textarea>
<?php
db_delete("cassiopeia_content_check")->condition("uid",$user->uid)->condition("nid",0)->execute();
?>
