<?php $index=1; ?>
<?php foreach($children as $child): ?>
    <tr class="<?php echo $index==1?"hasChild":"child"; ?> active" data-nid="<?php echo $child->nid; ?>">
        <td class="col-expand">
            <?php if($index==1): ?>
                <i class="fa fa-minus"></i>
            <?php endif; ?>
        </td>
        <td class="col-checkbox">
            <?php if($index==1): ?>
                <label class="mask-chekbox">
                    <input data-stt="<?php echo $stt; ?>" type="checkbox" name="select" class=""  value="<?php echo($child->nid); ?>" data-backlink-source="<?php echo($child->field_backlink_refer_page['und'][0]['value']); ?>" >
                    <i class="fa fa-square-o"></i>
                </label>
            <?php endif; ?>
        </td>
        <td class="col-stt">
            <?php if($index==1): ?>
                <?php echo $stt; ?>
            <?php endif; ?>
        </td>
        <td class="col-source" width="300px">
            <div class="d-flex space-between">
                <span class="domain" target="_blank" rel="\" href="\\<?php echo $child->source; ?>" title="<?php echo $child->source; ?>">
                    <?php echo $child->source; ?>                                           </span>
                <a class="" href="<?php echo $child->source; ?>" title="<?php echo $child->source; ?>" target="_blank" >
                    <i class="fa fa-external-link"></i>
                </a>
            </div>

        </td>
        <td class="col-rel">
            <?php if($index==1): ?>
                <span class="loading-item"></span><span class="td-content"><?php echo $child->rel; ?></span>
            <?php else: ?>
                <?php echo $child->rel; ?>
            <?php endif; ?>
        </td>
        <td class="col-anchor">
            <span class="loading-item"></span><span class="td-content"><?php echo $child->anchor_text; ?></span>
        </td>
        <td class="col-url">
            <span class="loading-item"></span>
            <div class="d-flex space-between">
                <span class="td-content">
                     <?php echo $child->url; ?>                                     </span>
                <a class="" href="<?php echo $child->url; ?>" title="<?php echo $child->url; ?>" target="_blank" >
                    <i class="fa fa-external-link"></i>
                </a>
            </div>
        </td>
        <td class="col-indexed">
            <span class="td-content   <?php
//            print_r($child);
            if($child->indexed==1){
                echo "Yes";
            }elseif($child->indexed==0){
                echo "No";
            }else{
                echo "-";
            }
            ?>">
                  <?php
                  if($child->indexed==1){
                      echo "Yes";
                  }elseif($child->indexed==0){
                      echo "No";
                  }else{
                      echo "-";
                  }
                  ?>
            </span>

        </td>
        <td class="col-tag">
            <?php echo $child->tags; ?>
        </td>
        <td class="col-created">
            <?php echo $child->date_created; ?>
        </td>
        <td class="col-status">
            <span class='loading-item'></span><span class='td-content'>
                <span title='"+errorMessage+"'>
                    <?php if(!empty($child->status)): ?>
                        <?php if($child->status==200): ?>
                            SUCCESS
                        <?php else: ?>
                            <?php
                            switch ($child->status) {
                                case "404":
                                    $errorCode = 404;
                                    $errorMessage = "Lỗi ".$errorCode.": Nguồn đặt Backlink không tồn tại!";
                                    break;
                                case "999":
                                    $errorCode = "error";
                                    $errorMessage = "Lỗi ".$errorCode.": Nguồn đặt Backlink không hợp lệ!";
                                    break;
                                case "500":
                                    $errorCode = 500;
                                    $errorMessage = "Lỗi ".$errorCode.": Nguồn đặt Backlink không hoạt động!";
                                    break;
                                default:
                                    $errorCode = $child->status;
                                    $errorMessage = "Lỗi ".$errorCode;
                            }
                            $errorCode = "FAIL <i class=\"fa fa-info-circle\" ></i>";
                            echo $errorCode;
                            ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </span>
            </span>
        </td>
    </tr>
<?php $index++; endforeach; ?>
