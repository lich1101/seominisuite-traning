<?php
global $user;
$query = db_select("cassiopeia_guest_post_tag","cassiopeia_guest_post_tag");
$query->fields("cassiopeia_guest_post_tag");
$query->condition("uid",$user->uid);
$tags = $query->execute()->fetchAll();
$tag_options = array();
if(!empty($tags)){
    foreach($tags as $tag){
        $tag_options[$tag->id] = $tag->title;
    }
}
$cache = !empty($form['#cache'])?$form['#cache']:null;
?>
<?php if($form['#preview']): ?>
    <?php
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' .$form['content']['value']['#value'],LIBXML_NOERROR);
    $h2Nodes = $doc->getElementsByTagName('h2');
//    _print_r($h2Nodes);
    ?>
    <div class="page-article-preview ">
        <div class="mb-24">
            <?php echo drupal_render($form['return']); ?>
        </div>
        <div class="node-regulation-detail page-article-view">
            <div class="node-container ">
                <div class="node-content row">
                    <div class="left-block col-md-8 ">
                        <div class="bg-white padding-24">
                            <div class="page-title mb-24">
                                <h1><?php echo $form['title']['#value']; ?></h1>
                            </div>
                            <div class="page-body">
                                <?php echo $form['content']['value']['#value']; ?>
                            </div>
                        </div>
                    </div>
                    <div class="right-block col-md-4 ">
                        <div class="right-nav bg-white">
                            <div class="nav-title">Nội dung chính</div>
                            <div class="nav-items">
                                <ul>
                                    <?php if($h2Nodes->length>=1): ?>
                                        <?php foreach($h2Nodes as $item): ?>
                                            <li><?php echo $item->nodeValue; ?></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li>...</li>
                                        <li>...</li>
                                        <li>...</li>
                                        <li>...</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="page-header">
        <div class="page-title">
            <h1 title="">
                <span style="max-width: unset;">Đăng guest post</span>
            </h1>
<!--            <button class="bg-green color-white border-none btn btn-success btn-pre-save" type="button" value="Lưu bài viết" ><span class="icon glyphicon glyphicon-ok" aria-hidden="true"></span>-->
<!--                Lưu bài viết</button>-->
            <?php echo drupal_render($form['pre-save']); ?>
            <?php echo drupal_render($form['preview']); ?>
            <?php echo drupal_render($form['save_draft']); ?>
        </div>
    </div>
    <div class="form-container">
        <div class="form-main">
            <div class="row">
                <div class="page-left col-md-8">
                    <div>
                        <?php echo drupal_render($form['title']); ?>
                    </div>
                    <div>
                        <?php echo drupal_render($form['content']); ?>
                    </div>
                    <div class="post-attributes">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#menu1">Đánh giá bài viết</a></li>
<!--                            <li><a data-toggle="tab" href="#menu2">Mạng xã hội</a></li>-->
                        </ul>

                        <div class="tab-content">
                            <div id="menu1" class="tab-pane fade in active">
                                <div class="bg-white pd-24">
                                    <div>
                                        <?php echo drupal_render($form['seo_title']); ?>
                                    </div>
                                    <div>
                                        <?php echo drupal_render($form['seo_description']); ?>
                                    </div>
                                </div>
                            </div>
                            <div id="menu2" class="tab-pane fade">
                                <div class="bg-white pd-24">
                                    <div>
                                        <?php echo drupal_render($form['fb_image']); ?>
                                    </div>
                                    <div>
                                        <?php echo drupal_render($form['fb_title']); ?>
                                    </div>
                                    <div>
                                        <?php echo drupal_render($form['fb_description']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-right col-md-4">
                    <div class="right-block bg-white mb-24">
                        <div class="block-title btn-collapse" data-toggle="collapse" data-target="#website-group">
                            <h3 class="title">Chọn website đăng Guest Post</h3>
                        </div>
                        <div id="website-group" class="block-body collapse in">
                            <?php echo drupal_render($form['category']); ?>
                            <?php echo drupal_render($form['website']); ?>
                            <?php if(!empty($form['wp_category'])) echo drupal_render($form['wp_category']); ?>
                        </div>
                    </div>
                    <div class="right-block bg-white mb-24 duplicate-content-check">
                        <div class="block-title btn-collapse" data-toggle="collapse" data-target="#duplicate-group">
                            <h3 class="title">Kiểm tra đạo văn</h3>
                        </div>
                        <div id="duplicate-group" class="block-body collapse in">
                          <div class="form-items">
                            <div class="hidden">
                              <?php echo drupal_render($form['duplicate_content']['exclude_domains']); ?>
                            </div>
                            <div class="form-item-captcha-resolve">
                              <select name="captcha-resolve" id=""
                                      class="btn form-control btn-gray btn-type-1">
                                <option value="auto" >Giải Captcha tự động</option>
                                <option value="manual" selected>Giải Captcha thủ công</option>
                              </select>
                            </div>
                            <?php echo drupal_render($form['duplicate_content']['check']); ?>
                          </div>
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
                    </div>
                    <div class="right-block bg-white mb-24">
                        <div class="block-title btn-collapse collapsed" data-toggle="collapse" data-target="#outline-content-group">
                            <h3 class="title">Outline Content</h3>
                        </div>
                        <div id="outline-content-group" class="block-body information collapse">
                            <div class="d-flex mb-24">
                                <?php echo drupal_render($form['outline_content']['keyword']); ?>
<!--                                --><?php //echo drupal_render($form['outline_content']['check']); ?>
                                <button class="color-green border-none bg-grey btn btn-outline-content-check"><i class="fa fa-search"></i></button>
                            </div>
                            <div>
                                <?php
                                global $user;

                                $doc = new DOMDocument();
                                $doc->loadHTML('<?xml encoding="utf-8" ?>' .$form['content']['value']['#value'],LIBXML_NOERROR);
                                // check độ dài content
                                $content = str_replace("<p>"," ",$form['content']['value']['#value']);
                                $content = str_replace("</p>"," ",$content);
                                $content = str_replace("<br>"," ",$content);
                                $content = str_replace("\n"," ",$content);
                                $content = str_replace("&nbsp;"," ",$content);
                                $content = strip_tags($content);
                                $content = trim($content);
                                $splitter = explode(" ",$content);
                                $word_count = 0;
                                foreach($splitter as $value){
                                    if(!empty(trim($value))){
                                        $word_count++;
                                    }
                                }
                                $h2Nodes = $doc->getElementsByTagName('h2');
                                $h3Nodes = $doc->getElementsByTagName('h3');
                                $imgNodes = $doc->getElementsByTagName('img');
                                $current_word_count = $word_count;
                                $current_h2_count = $h2Nodes->length;
                                $current_h3_count = $h3Nodes->length;
                                $current_img_count = $imgNodes->length;


                                $query = db_select("cassiopeia_content_check","cassiopeia_content_check");
                                $query->fields("cassiopeia_content_check");
                                $query->condition("uid",$user->uid);
                                $query->condition("nid",0);
                                $query->orderBy("google_index","ASC");
                                $result = $query->execute()->fetchAll();
                                $_result = array();
                                $min_words = 0;
                                $max_words = 0;
                                $h2_min = 0;
                                $h2_max = 0;
                                $h3_min = 0;
                                $h3_max = 0;
                                $img_min = 0;
                                $img_max = 0;
                                $h1_count = $h2_count = $h3_count = 0;

                                if(!empty($result)){
                                    $stt = 1;
                                    foreach($result as $item){
                                        $data = unserialize($item->data);
                                        if($data->isArticle==1){
                                            if(empty($h2_min)){
                                                $h2_min = !empty($data->h2)?count($data->h2):0;
                                            }
                                            if(empty($h2_max)){
                                                $h2_max = !empty($data->h2)?count($data->h2):0;
                                            }
                                            if(empty($h3_min)){
                                                $h3_min = !empty($data->h3)?count($data->h3):0;
                                            }
                                            if(empty($h3_max)){
                                                $h3_max = !empty($data->h3)?count($data->h3):0;
                                            }
                                            if(empty($min_words)){
                                                $min_words = !empty($data->word_count)?($data->word_count):0;
                                            }
                                            if(empty($max_words)){
                                                $max_words = !empty($data->word_count)?($data->word_count):0;
                                            }
                                            if(empty($img_min)){
                                                $img_min = !empty($data->img)?($data->img):0;
                                            }
                                            if(empty($img_max)){
                                                $img_max = !empty($data->img)?($data->img):0;
                                            }
                                            if(!empty($data->word_count)){
                                                $min_words = $min_words>$data->word_count?$data->word_count:$min_words;
                                                $max_words = $max_words<$data->word_count?$data->word_count:$max_words;
                                            }
                                            if(!empty($data->h2)){
                                                $h2_min = $h2_min>count($data->h2)?count($data->h2):$h2_min;
                                                $h2_max = $h2_max<count($data->h2)?count($data->h2):$h2_max;
                                            }
                                            if(!empty($data->h3)){
                                                $h3_min = $h3_min>count($data->h3)?count($data->h3):$h3_min;
                                                $h3_max = $h3_max<count($data->h3)?count($data->h3):$h3_max;
                                            }
                                            if(!empty($data->img)){
                                                $img_min = $img_min>$data->img?$data->img:$img_min;
                                                $img_max = $img_max<$data->img?$data->img:$img_max;
                                            }
                                            $h1_count += isset($data->h1)?count($data->h1):0;
                                            $h2_count += isset($data->h2)?count($data->h2):0;
                                            $h3_count += isset($data->h3)?count($data->h3):0;
                                            $stt++;
                                        }
                                    }
                                }
                                if($min_words<1200){
                                    $min_words = 1200;
                                }
                                if($max_words<2000){
                                    $max_words = 2000;
                                }
                                if($h2_min<2){
                                    $h2_min = 2;
                                }
                                if($h2_max<5){
                                    $h2_max = 5;
                                }
                                if($h3_min<2){
                                    $h3_min = 2;
                                }
                                if($h3_max<5){
                                    $h3_max = 5;
                                }
                                if($img_min<2){
                                    $img_min = 2;
                                }
                                if($img_max<5){
                                    $img_max = 5;
                                }


                                $point = 0;
                                if(!empty($result)) $point = 0;
                                $point += $current_word_count<$min_words?round($current_word_count*70/$min_words):70;
                                $point += $current_h2_count<$h2_min?round($current_h2_count*15/$h2_min):15;
                                $point += $current_h3_count<$h3_min?round($current_h3_count*5/$h3_min):5;
                                $point += $current_img_count<$img_min?round($current_img_count*10/$img_min):10;
                                ?>
                                <ul class="nav nav-tabs <?php if(!empty($result)) echo "active"; ?>">
                                    <li data-index="1" class="<?php if(empty($result)) echo "disabled"; ?> active"><a data-toggle="tab" href="#tab1">Hỗ trợ dàn ý</a></li>
                                    <li data-index="2" class="<?php if(empty($result)) echo "disabled"; ?>"><a data-toggle="tab" href="#tab3">Chi tiết đối thủ</a></li>
                                    <li data-index="3" class="<?php if(empty($result)) echo "disabled"; ?>"><a data-toggle="tab" href="#tab2">Đánh giá bài viết</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div id="tab1" class="tab-pane in active fade tab-2">
                                        <div class="tab-title">Hỗ trợ dàn ý bài viết</div>
                                        <div class="tab-body">
                                            <div class="tab-note">
                                                <?php if(!empty($result)): ?>
                                                    Seo Minisuite đưa ra  <?php echo $h1_count; ?> tiêu đề ( H1), <?php echo $h2_count; ?> Heading ( H2) và <?php echo $h3_count; ?> Heading (H3). Kết quả lấy từ <?php echo count($result); ?> đối thủ có thứ hạng cao nhất.
                                                <?php else: ?>
                                                    Seo Minisuite đưa ra kết quả dựa trên 10 đối thủ có thứ hạng cao nhất trên Google Search Engine!
                                                <?php endif; ?>
                                            </div>
                                            <div class="tab-detail">
                                                <div class="">
                                                    <ul>
                                                        <li class="expanded active header">
                                                            <a href="#" class="sm-event-none">
                                                                <div>#</div>
                                                                <div>Title</div>
                                                                <div>Thứ hạng</div>
                                                                <div><i class="fa fa-angle-up"></i></div>
                                                            </a>
                                                            <ul class="mb-none">
                                                                <?php if(!empty($result)): ?>
                                                                    <?php foreach($result as $item): ?>
                                                                        <?php $data = unserialize($item->data); ?>
                                                                        <?php if(!empty($data->h1)): ?>
                                                                            <?php foreach($data->h1 as $h1): ?>
                                                                                <?php if(!empty($h1)): ?>
                                                                                    <li>
                                                                                        <div class="heading-h1">H1</div>
                                                                                        <div class="has-data"><?php echo $h1; ?></div>
                                                                                        <div><?php echo $item->google_index; ?></div>
                                                                                        <div><span class="btn-add-heading" data-heading="h1" data-text="<?php echo $h1; ?>"><i class="fa fa-plus"></i> Thêm</span></div>
                                                                                    </li>
                                                                                <?php endif; ?>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                <?php else: ?>
                                                                    <li class="disabled">
                                                                        <div>H1</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div>H1</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div>H1</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div>H1</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div>H1</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div>H1</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div>H1</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div>H1</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div>H1</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h1" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </li>
                                                        <li class="expanded header">
                                                            <a href="#" class="sm-event-none">
                                                                <div>#</div>
                                                                <div>Headings (H2)</div>
                                                                <div>Thứ hạng</div>
                                                                <div><i class="fa fa-angle-up"></i></div>
                                                            </a>
                                                            <ul class="mb-none">
                                                                <?php if(!empty($result)): ?>
                                                                    <?php foreach($result as $item): ?>
                                                                        <?php $data = unserialize($item->data); ?>
                                                                        <?php if(!empty($data->h2)): ?>
                                                                            <?php foreach($data->h2 as $h2): ?>
                                                                                <?php if(!empty($h2)): ?>
                                                                                    <li>
                                                                                        <div class="heading-h2">H2</div>
                                                                                        <div><?php echo $h2; ?></div>
                                                                                        <div><?php echo $item->google_index; ?></div>
                                                                                        <div><span class="btn-add-heading" data-heading="h2" data-text="<?php echo $h2; ?>"><i class="fa fa-plus"></i> Thêm</span></div>
                                                                                    </li>
                                                                                <?php endif; ?>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                <?php else: ?>
                                                                    <li class="disabled">
                                                                        <div class="heading-h2">H2</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h2">H2</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h2">H2</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h2">H2</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h2">H2</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h2">H2</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h2">H2</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h2">H2</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h2">H2</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h2">H2</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </li>
                                                        <li class="expanded header">
                                                            <a href="#" class="sm-event-none">
                                                                <div>#</div>
                                                                <div class="">Headings (H3)</div>
                                                                <div>Thứ hạng</div>
                                                                <div><i class="fa fa-angle-up"></i></div>
                                                            </a>
                                                            <ul class="mb-none">
                                                                <?php if(!empty($result)): ?>
                                                                    <?php foreach($result as $item): ?>
                                                                        <?php $data = unserialize($item->data); ?>
                                                                        <?php if(!empty($data->h3)): ?>
                                                                            <?php foreach($data->h3 as $h3): ?>
                                                                                <?php if(!empty($h3)): ?>
                                                                                    <li>
                                                                                        <div class="heading-h3">H3</div>
                                                                                        <div><?php echo $h3; ?></div>
                                                                                        <div><?php echo $item->google_index; ?></div>
                                                                                        <div><span class="btn-add-heading" data-heading="h3" data-text="<?php echo $h3; ?>"><i class="fa fa-plus"></i> Thêm</span></div>
                                                                                    </li>
                                                                                <?php endif; ?>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                <?php else: ?>
                                                                    <li class="disabled">
                                                                        <div class="heading-h3">H3</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h3">H3</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h3">H3</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h3">H3</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h3">H3</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h3">H3</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h3">H3</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h3">H3</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h3">H3</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                    <li class="disabled">
                                                                        <div class="heading-h3">H3</div>
                                                                        <div>...</div>
                                                                        <div>...</div>
                                                                        <div><span class="btn-add-heading disabled" data-heading="h2" data-text=""><i class="fa fa-plus"></i> Thêm</span></div>
                                                                    </li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab2" class="tab-pane fade  tab-1">
                                        <div class="tab-title">Điểm đánh giá</div>
                                        <div class="tab-note">
                                            SEO MiniSuite đưa ra kết quả dựa trên <?php echo count($result); ?> đối thủ có thứ hạng cao nhất trên Google Search Engine!
                                        </div>
                                        <div class="tab-body">

                                            <div class="tab-detail">
                                                <table class="table-hover table-stripped">
                                                    <tbody>
                                                    <?php if(!empty($result)): ?>
                                                        <tr>
                                                            <td>
                                                                <div>
                                                                    <?php echo $current_word_count; ?>
                                                                    <?php if($current_word_count<$min_words): ?>
                                                                        <img class="up" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                                                    <?php elseif($current_word_count>$max_words): ?>
                                                                        <img class="down" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                                                    <?php else:  ?>
                                                                        <img class="" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-check.png" alt="">
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div>Số từ hiện tại</div>
                                                            </td>
                                                            <td>
                                                                <div><?php echo number_format($min_words,0,",","."); ?> - <?php echo number_format($max_words,0,",","."); ?></div>
                                                                <div>Số từ tiêu chuẩn</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div>
                                                                    <?php echo $current_h2_count; ?>
                                                                    <?php if($current_h2_count<$h2_min): ?>
                                                                        <img class="up" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                                                    <?php elseif($current_h2_count>$h2_max): ?>
                                                                        <img class="down" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                                                    <?php else:  ?>
                                                                        <img class="" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-check.png" alt="">
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div>Số H2 hiện tại</div>
                                                            </td>
                                                            <td>
                                                                <div><?php echo $h2_min; ?> - <?php echo $h2_max; ?></div>
                                                                <div>Số H2 tiêu chuẩn</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div>
                                                                    <?php echo $current_h3_count; ?>
                                                                    <?php if($current_h3_count<$h3_min): ?>
                                                                        <img class="up" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                                                    <?php elseif($current_h3_count>$h3_max): ?>
                                                                        <img class="down" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                                                    <?php else: ?>
                                                                        <img class="" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-check.png" alt="">
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div>Số H3 hiện tại</div>
                                                            </td>
                                                            <td>
                                                                <div><?php echo $h3_min; ?> - <?php echo $h3_max; ?></div>
                                                                <div>Số H3 tiêu chuẩn</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div>
                                                                    <?php echo $current_img_count; ?>
                                                                    <?php if($current_img_count<$img_min): ?>
                                                                        <img class="up" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                                                    <?php elseif($current_img_count>$img_max): ?>
                                                                        <img class="down" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-arrow-up.png" alt="">
                                                                    <?php else:  ?>
                                                                        <img class="" src="/sites/all/themes/cassiopeia_theme/img/icons/icon-check.png" alt="">
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div>Số hình ảnh hiện tại</div>
                                                            </td>
                                                            <td>
                                                                <div><?php echo $img_min; ?> - <?php echo $img_max; ?></div>
                                                                <div>Hình ảnh tiêu chuẩn</div>
                                                            </td>
                                                        </tr>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td>
                                                                <div>
                                                                    <?php echo $current_word_count; ?>
                                                                </div>
                                                                <div>Số từ hiện tại</div>
                                                            </td>
                                                            <td>
                                                                <div>-</div>
                                                                <div>Số từ tiêu chuẩn</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div>
                                                                    <?php echo $current_h2_count; ?>
                                                                </div>
                                                                <div>Số H2 hiện tại</div>
                                                            </td>
                                                            <td>
                                                                <div>-</div>
                                                                <div>Số H2 tiêu chuẩn</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div>
                                                                    <?php echo $current_h3_count; ?>
                                                                </div>
                                                                <div>Số H3 hiện tại</div>
                                                            </td>
                                                            <td>
                                                                <div>-</div>
                                                                <div>Số H3 tiêu chuẩn</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div>
                                                                    <?php echo $current_img_count; ?>
                                                                </div>
                                                                <div>Số hình ảnh hiện tại</div>
                                                            </td>
                                                            <td>
                                                                <div>-</div>
                                                                <div>Hình ảnh tiêu chuẩn</div>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--            --><?php //$point = 90; ?>
                                            <div class="tab-chart">
                                                <div class="gauge" data-point="<?php echo $point; ?>">
                                                    <img src="/sites/all/themes/cassiopeia_theme/img/gauge.png" alt="">
                                                    <img style="transform: rotate(<?php echo round($point*2.7); ?>deg)" class="vector" src="/sites/all/themes/cassiopeia_theme/img/gauge-vector.png" alt="">
                                                    <div class="point">
                                                        <span class="rank-<?php if((int)$point<50){ echo "low";}elseif((int)$point<90){ echo "medium";}else{ echo "high";} ?>"><?php echo $point; ?></span>/100
                                                    </div>
                                                    <div class="chart-label">
                                                        <div class="low">
                                                            <span></span>
                                                            <span>0-49</span>
                                                        </div>
                                                        <div class="medium">
                                                            <span></span>
                                                            <span>50-89</span>
                                                        </div>
                                                        <div class="high">
                                                            <span></span>
                                                            <span>90-100</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="tab3" class="tab-pane fade tab-3">
                                        <div class="tab-title">Chi tiết đối thủ</div>
                                        <div class="tab-body">
                                            <div class="tab-note">
                                                SEO MiniSuite đưa ra kết quả dựa trên <?php echo count($result); ?> đối thủ có thứ hạng cao nhất trên Google Search Engine!
                                            </div>
                                            <div class="tab-detail">
                                                <div class="">
                                                    <ul>
                                                        <?php if(!empty($result)): ?>
                                                            <?php foreach($result as $item): ?>
                                                                <?php $data = unserialize($item->data);?>
                                                                <li class="expanded header">
                                                                    <div href="#" class="">
                                                                        <div>
                                                                            <div class="article-title"><?php echo $data->anchorText; ?></div>
                                                                            <i class="fa fa-angle-up"></i>
                                                                        </div>
                                                                        <div>
                                                                            <span><?php echo $data->href; ?></span> <a  class="btn-goto" href="<?php echo $data->href; ?>" title="<?php echo $data->href; ?>" target="_blank">
                                                                                <i class="fa fa-external-link" aria-hidden="true"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <ul class="mb-none">
                                                                        <?php if(!empty($data->h1)): ?>
                                                                            <?php foreach($data->h1 as $h1): ?>
                                                                                <?php if(!empty(trim($h1))): ?>
                                                                                    <li>
                                                                                        <div class="heading-h1">H1</div>
                                                                                        <div><?php echo $h1; ?></div>
                                                                                        <div><span class="btn-add-heading" data-heading="h1" data-text="<?php echo $h1; ?>"><i class="fa fa-plus"></i> Thêm</span></div>
                                                                                    </li>
                                                                                <?php endif; ?>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                        <?php if(!empty($data->h2)): ?>
                                                                            <?php foreach($data->h2 as $h2): ?>
                                                                                <?php if(!empty(trim($h2))): ?>
                                                                                    <li>
                                                                                        <div class="heading-h2">H2</div>
                                                                                        <div><?php echo $h2; ?></div>
                                                                                        <div><span class="btn-add-heading" data-heading="h2" data-text="<?php echo $h2; ?>"><i class="fa fa-plus"></i> Thêm</span></div>
                                                                                    </li>
                                                                                <?php endif; ?>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                        <?php if(!empty($data->h3)): ?>
                                                                            <?php foreach($data->h3 as $h3): ?>
                                                                                <?php if(!empty(trim($h3))): ?>
                                                                                    <li>
                                                                                        <div class="heading-h3">H3</div>
                                                                                        <div><?php echo $h3; ?></div>
                                                                                        <div><span class="btn-add-heading" data-heading="h3" data-text="<?php echo $h3; ?>"><i class="fa fa-plus"></i> Thêm</span></div>
                                                                                    </li>
                                                                                <?php endif; ?>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                    </ul>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <li class="expanded header">
                                                                <div href="#" class="">
                                                                    <div>
                                                                        <div class="article-title">-</div>
                                                                        <i class="fa fa-angle-up"></i>
                                                                    </div>
                                                                    <div>
                                                                        <a href="" rel="nofollow" target="_blank">-</a>
                                                                    </div>
                                                                </div>
                                                                <ul class="mb-none">
                                                                    <li>
                                                                        <div class="heading-h1">H1</div>
                                                                        <div>-</div>
                                                                        <div>-</div>
                                                                    </li>
                                                                    <li>
                                                                        <div class="heading-h2">H2</div>
                                                                        <div>-</div>
                                                                        <div>-</div>
                                                                    </li>
                                                                    <li>
                                                                        <div class="heading-h3">H3</div>
                                                                        <div>-</div>
                                                                        <div>-</div>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="right-block bg-white mb-24">
                        <div class="block-title btn-collapse collapsed" data-toggle="collapse" data-target="#tag-group">
                            <h3 class="title">Tag</h3>
                        </div>
                        <div id="tag-group" class="block-body collapse">
                            <div>
                                <?php echo drupal_render($form['tags']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="right-block bg-white mb-24">
                        <div class="block-title">
                            <h3 class="title">Ảnh đại diện</h3>
                        </div>
                        <div class="block-body">
                            <div>
                                <?php echo drupal_render($form['image']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="hidden">
    <?php echo drupal_render_children($form); ?>
</div>
<textarea class="hidden" type="hidden" id="tags" value=''><?php echo(json_encode($tag_options)); ?></textarea>
<!-- Modal -->

<span id="listOfID" data-value=""></span>
<input hidden type="number" id="totalItems">
<input hidden type="number" id="checkedItems">