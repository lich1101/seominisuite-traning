
<div class="visible-xs mobile-note">
    <div class="text confirm">
        Mời bạn dùng Laptop hoặc Desktop để sử dụng chức năng này
    </div>
    <!--                                   <span class="close">&times;</span>-->
</div>
<?php
global $user;
$packet = cassiopeia_get_available_packet_by_uid($user->uid);
drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-check-duplicate-content.js', ['weight' => 1000]);
   try{
       module_load_include('inc', 'node', 'node.pages');
       $node_form = new stdClass;
       $node_form->type = 'duplicate_content';
       $node_form->language = LANGUAGE_NONE;
       $form = drupal_get_form('duplicate_content_node_form', $node_form);
   }catch (Exception $e){
       cassiopeia_dump($e);
   }
?>
<span id="listOfID" data-value=""></span>
<input hidden type="number" id="totalItems">
<input hidden type="number" id="checkedItems">
<div class="page page-check-duplicate-content hidden-xs">
    <div class="page-header">
        <div class="page-title">
            <h1>Kiểm tra đạo văn</h1>
        </div>
        <div class="tutorial">
            <a target="_blank" class="tutorial-keywords" href="/<?php echo drupal_get_path_alias("node/169973"); ?>">
                <span class="icon"><img src="/sites/all/themes/cassiopeia_theme/img/icons/icon-20.png" alt=""></span> <span>Hướng dẫn sử dụng</span></a>
        </div>
    </div>
    <div class="page-container">
        <div class="page-main result">
            <div class="running"></div>
            <div class="row heck-duplicate">
                <div class="col-md-6">
                    <div class="article-content">
                        <div class="block-content">
                            <?php
                            if(!empty($form)){
                                $form  = drupal_render($form);
                                echo $form;
                            }
                            ?>
<!--                            <div id="froala-editor"></div>-->
                        </div>
                    </div>
                  <div class="block-check-article">
                    <div class="block-check-article-container">
                      <div class="article-check">
                        <div class="block-title">
                          Loại trừ domain
                          <button type="button" class="btn btn-secondary btn-notice-tooltip" data-toggle="tooltip" data-placement="right" title="" data-original-title="Nội dung kiểm tra trên những Domains bị loại trừ sẽ không bị tính Trùng lặp">
                            <span class="fa fa-question-circle-o" aria-hidden="true"></span>
                          </button>
                        </div>
                        <div class="block-content d-flex align-center">
                          <div>
                            <div class="domain-name">
                              <input type="text" placeholder="Phân cách bằng phím Enter" class="tagify-input" id="edit-tags" name="domain">
                            </div>
                            <div class="form-item-captcha-resolve">
                              <select name="captcha-resolve" id=""
                                      class="btn form-control btn-gray btn-type-1">
                                <option value="auto" >Giải Captcha tự động</option>
                                <option value="manual" selected>Giải Captcha thủ công</option>
                              </select>
                            </div>
                            <div class="submit-check">
                              <button type="button" class="btn-green btn-check-duplicate-content">Kiểm tra</button>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white radius-5">
                        <div class="total-result">
                            <div class="total-result-amount">Kết quả</div>
                            <ul>
                                <li>
                                    <div class="total-result-item all active">
                                        <span class="all"></span> Tất cả
                                    </div>
                                </li>
                                <li>
                                    <span>|</span>
                                </li>
                                <li>
                                    <div class="total-result-item none-duplicate">
                                        <span class="none-duplicate"></span> Độc đáo
                                    </div>
                                </li>
                                <li>
                                    <span>|</span>
                                </li>
                                <li>
                                    <div class="total-result-item duplicate">
                                        <span class="duplicate"></span> Trùng lặp
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="table-wrap table-responsive result">
                            <div class="t-body">
                                <table class="table table-striped table-div-responsive table-type-3 duplicate-content-table mb-0">
                                    <thead>
                                    <tr>
                                        <!-- <th width="30px" data-sort="string" data-direction="DESC" class="text-center">STT</th> -->
                                        <th data-sort="string" data-direction="DESC" class="w-60 text-left">Câu truy vấn</th>
                                        <th data-sort="source" data-direction="DESC" class="">Nguồn trùng lặp</th>
                                        <th data-sort="result" data-direction="DESC" class="sort" style="width: 130px;">Kết quả</th>
                                    </tr>
                                    <tr class="">
                                        <th colspan="10" class="th-progress" style="background-color: transparent;">
                                            <div class="progress-bar-block modal-custom">
                                                <progress id="file" value="0" max="2180"> 32% </progress>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="table-result no-data">
                                        <tr>
                                            <td data-title="query">
                                                ...
                                            </td>
                                            <td data-title="links">
                                                ...
                                            </td>
                                            <td data-title="result">
                                                ...
                                            </td>
                                        </tr>
                                        <tr>
                                            <td data-title="query">
                                                ...
                                            </td>
                                            <td data-title="links">
                                                ...
                                            </td>
                                            <td data-title="result">
                                                ...
                                            </td>
                                        </tr>
                                        <tr>
                                            <td data-title="query">
                                                ...
                                            </td>
                                            <td data-title="links">
                                                ...
                                            </td>
                                            <td data-title="result">
                                                ...
                                            </td>
                                        </tr>
                                        <tr>
                                            <td data-title="query">
                                                ...
                                            </td>
                                            <td data-title="links">
                                                ...
                                            </td>
                                            <td data-title="result">
                                                ...
                                            </td>
                                        </tr>
                                        <tr>
                                            <td data-title="query">
                                                ...
                                            </td>
                                            <td data-title="links">
                                                ...
                                            </td>
                                            <td data-title="result">
                                                ...
                                            </td>
                                        </tr>
                                        <tr>
                                            <td data-title="query">
                                                ...
                                            </td>
                                            <td data-title="links">
                                                ...
                                            </td>
                                            <td data-title="result">
                                                ...
                                            </td>
                                        </tr>
                                        <tr>
                                            <td data-title="query">
                                                ...
                                            </td>
                                            <td data-title="links">
                                                ...
                                            </td>
                                            <td data-title="result">
                                                ...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                  <div class="export-excel">
                    <button type="button"  class="btn-disable btn-export btn-green btn-search-project <?php echo !empty($packet->excel)?"excel":""; ?>">
                      <span class="fa fa-file-excel-o fs-16 mr-5 " aria-hidden="true"></span>
                      Xuất Excel
                    </button>
                  </div>
                </div>
            </div>

        </div>
    </div>

</div>

