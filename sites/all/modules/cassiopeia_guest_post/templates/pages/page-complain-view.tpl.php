<?php
global $user;
$article = cassiopeia_guest_post_article_load($node->aid);
$website = cassiopeia_guest_post_website_load($article->website);
if($node->target==1){ // người đăng khiếu nại
    $plaintiff = user_load($article->uid);
    $defendant = !empty($website)?user_load($website->uid):null;
}else{
    $plaintiff = !empty($website)?user_load($website->uid):null;
    $defendant = user_load($article->uid);
}
?>
<div class="page-complain-view mb-24 hidden-xs">
    <div class="page-title mb-24">
        <h1 class="title">Chi tiết khiếu nại</h1>
        <?php print(l('<i class="fa fa-plus"></i> Khiếu nại', 'guest-post/complain/add',  array('html'=>true, 'attributes' => array('class' => 'btn btn-green btn-icon-before ml-10'))));?>
    </div>
    <div class="row">
        <div class="col-md-8">

            <div class="page-top mb-24">
                <div class="row mg-0">
                    <div class="col-md-6 bg-white padding-24 border-right">
                        <div class="mb-10"><h4 class="mg-0"><b>Bài viết lưu trên hệ thống</b></h4></div>
                        <div class="d-flex"><div class="ellipsis-2 line-height-22">
                                <?php echo l($article->title,"/guest/post/article/".$article->id."/view",array("html"=>TRUE,"attributes"=>array("target"=>"_blank","class"=>array("color-green")))); ?>
                            </div>
                            <div class="ml-10">
                                <?php echo l("<i class=\"fa fa-external-link color-green \" aria-hidden=\"true\"></i>","/guest/post/article/".$article->id."/view",array("html"=>TRUE,"attributes"=>array("target"=>"_blank"))); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 bg-white padding-24">
                        <div class="mb-10"><h4 class="mg-0"><b>Bài viết đăng trên website</b></h4></div>
                        <div class="d-flex">
                            <div class="ellipsis-2 line-height-22">
                                <?php echo l($article->url_guest_post,$article->url_guest_post,array("html"=>TRUE,"attributes"=>array("target"=>"_blank"))); ?>
                            </div>
                            <div class="ml-10">
                                <?php echo l(" <i class=\"fa fa-external-link\" aria-hidden=\"true\"></i>",$article->url_guest_post,array("html"=>TRUE,"attributes"=>array("target"=>"_blank"))); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class=" padding-24 bg-white mb-24">
                <h2 class="node-title"><b><?php echo $node->title; ?></b></h2>
                <div>
                    <?php echo $node->content;   ?>
                </div>
            </div>
            <?php if(!empty($node->note)): ?>
                <div class="note">
                    <div>Thông tin phản hồi của Admin</div>
                    <div class="padding-24">
                        <?php echo $node->note; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-4 block-right">
            <div class="block-container bg-white padding-24">
                <h3 class="mg-0 mb-24"><b class="color-black">Thông tin</b></h3>
                <div>
                    <div>Người yêu cầu</div>
                    <div><b><?php echo $plaintiff->field_full_name['und'][0]['value']; ?></b></div>
                </div>
                <div>
                    <div>Người bị khiếu nại</div>
                    <div><b><?php if(!empty($defendant)) echo !empty($defendant->field_full_name['und'][0]['value'])?$defendant->field_full_name['und'][0]['value']:$defendant->name; ?></b></div>
                </div>
                <div>
                    <div>Ngày bắt đầu</div>
                    <div><b><?php echo date("d/m/Y",$node->created); ?></b></div>
                </div>
                <?php if($node->status!=0): ?>
                    <div>
                        <div>Ngày kết thúc</div>
                        <div>
                            <b><?php echo date("d/m/Y",$node->changed); ?></b>
                        </div>
                    </div>
                <?php endif; ?>
                <div>
                    <div>Trạng thái</div>
                    <div>
                        <?php  if($node->uid==$user->uid):// chủ đơn ?>
                            <span class="complain-status complain-status-<?php echo $node->status; ?>">
                                <?php
                                switch ($node->status){
                                    case 0 : echo "Admin đang kiểm tra"; break;
                                    case 1 : echo "Thành công"; break;
                                    case 2 : echo "Hủy"; break;
                                    case 3 : echo "Thất bại"; break;
                                }
                                ?>
                            </span>
                        <?php else: ?>
                            <?php
                            switch ($node->status){
                                case 0 : $status = 0;$text = "Admin đang kiểm tra"; break;
                                case 3 : $status = 1;$text = "Thành công"; break;
                                case 2 : $status = 0;$text = "Hủy"; break;
                                case 1 : $status = 3;$text = "Thất bại"; break;
                            }
                            ?>
                            <span class="complain-status complain-status-<?php echo $status; ?>">
                                <?php echo $text; ?>
                            </span>
                        <?php endif; ?>

                    </div>
                </div>
                <?php if($node->status!=0): ?>
                    <div>
                        <div>Điểm thay đổi</div>
                        <div>
                           <b class="color-red">
                               <?php
                               $_point = 0;
                               if($node->uid==$user->uid){
                                   $_point = $node->point;
                               }else{
                                   $_point = $node->defendant_point;
                               }
                               echo $_point>0?"<span class='color-green'>"."+".$_point."</span>":"<span class='color-red'>".$_point."</span>";
                               ?>
                           </b>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>