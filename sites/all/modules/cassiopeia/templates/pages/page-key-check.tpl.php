<?php
    drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-key-search.js', ['weight' => 1000]);
    global $user;
?>
<div class="page-key-search">
    <div class="page-container">
        <div class="left-block">
            <?php echo(_cassiopeia_render_theme("module","cassiopeia","templates/nav/nav-user.tpl.php")); ?>
        </div>
        <div class="right-block">
            <h1>Thứ hạng từ khóa</h1>
            <div class="block-container">
                <div class="block-project">
                    <?php $projects = cassiopeia_get_project_by_uid_and_type($user->uid,"key_search"); ?>
                    <select name="project" id="" class="form-control">
                        <option value="0" hidden selected>Chọn dự án</option>
                        <?php if(!empty($projects)): ?>
                            <?php foreach($projects  as $project): ?>
                                <option value="<?php echo($project->id); ?>"><?php echo($project->title); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <button class="btn btn-primary btn-add-project" data-type="key_search"><i class="fa fa-plus"></i> Thêm dự án</button>
                    <button class="btn btn-success btn-edit-project"><i class="fa fa-edit"></i> Cập nhật</button>
                    <button class="btn btn-danger btn-delete-project"><i class="fa fa-trash"></i> Xóa dự án</button>
                </div>
                <div class="block-key">
                    <div>
                        <button class="btn btn-primary btn-add-key"><i class="fa fa-plus"></i> Thêm từ khóa</button>
                        <button class="btn btn-danger btn-delete-key"><i class="fa fa-trash"></i> Xóa từ khóa</button>
                    </div>
                    <div class="list-of-key">
                        <table class="table table-hover table-stripped">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="all" value="1"></th>
                                    <th>STT</th>
                                    <th>Từ khóa</th>
                                    <th>Vị trí</th>
                                    <th>Vị trí cũ</th>
                                    <th>Vị trí tốt nhất</th>
                                    <th>Cập nhật</th>
                                    <th>Liên kết</th>
                                    <th>Biểu đồ</th>
                                </tr>
                            </thead>
                            <tbody class="result_key_search">

                            </tbody>
                        </table>
                    </div>
                    <div class="key-check">
                        <button class="btn btn-warning btn-key-check">Tìm kiếm trong top 100</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="modalKeySearch" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Thêm mới từ khóa</h4>
            </div>
            <div class="modal-body">
                <?php
                    $cassiopeia_user_key_search_form = drupal_get_form("cassiopeia_user_key_search_form");
                    if(!empty($cassiopeia_user_key_search_form)){
                        $cassiopeia_user_key_search_form = drupal_render($cassiopeia_user_key_search_form);
                        print($cassiopeia_user_key_search_form);
                    }
                ?>
            </div>
        </div>

    </div>
</div>