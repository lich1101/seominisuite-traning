<?php
$item = $variables['item'];
$stt = $variables['stt'];
//_print_r($item->field_keyword_position);
?>
<tr data-nid="<?php echo($item->nid); ?>">
    <td class="w-3" data-title="select">
        <label class="mask-chekbox">
            <input <?php if(!empty($item->checked)&&$item->checked==1) echo "checked"; ?> value="<?php echo($item->nid); ?>" type="checkbox" name="select" class="" data-stt="<?php echo $stt; ?>" data-id="<?php echo($item->nid); ?>" data-key="<?php echo($item->title); ?>" data-url="<?php echo(!empty($item->field_url['und'][0]['value'])?$item->field_url['und'][0]['value']:""); ?>">
            <i class="fa-regular fa-square"></i>
        </label>
    </td>
    <td class="w-3 stt"><?php echo $stt; ?></td>
    <td data-key="sort_1" class="w-18 text-left" data-title="title" data-value="<?php echo vn_to_str($item->title); ?>">
        <div title="<?php echo($item->title); ?>">
            <?php echo($item->title); ?>
        </div>
    </td>
    <?php
    $status_class = "";
    if(!empty($item->field_keyword_old_position) && !empty($item->field_keyword_position)){
        if($item->field_keyword_position - $item->field_keyword_old_position>0){
            $status_class = "status_decrease";
        }elseif($item->field_keyword_position - $item->field_keyword_old_position<0){
            $status_class = "status_increase";
        }
    }
    ?>
    <td data-key="sort_2" data-value="<?php echo(!empty($item->field_keyword_old_position)?abs($item->field_keyword_position-$item->field_keyword_old_position):"-"); ?>" class="w-6 status <?php echo($status_class); ?> _loading">
        <span class='loading-item'></span><span class='td-content'><?php echo(!empty($item->field_keyword_old_position)?abs($item->field_keyword_position-$item->field_keyword_old_position):"-"); ?></span>
    </td>
    <td data-key="sort_3" data-value="<?php echo(!empty($item->field_keyword_position)?$item->field_keyword_position:"-"); ?>" class="w-7 _loading">  <span class='loading-item'></span><span class='td-content'><?php echo(!empty($item->field_keyword_position)?$item->field_keyword_position:"-"); ?></span></td>
    <td data-key="sort_4" data-value="<?php echo(!empty($item->field_keyword_old_position)?$item->field_keyword_old_position:"-"); ?>" class="w-10 _loading"> <span class='loading-item'></span><span class='td-content'><?php echo(!empty($item->field_keyword_old_position)?$item->field_keyword_old_position:"-"); ?></span></td>
    <td data-key="sort_5" data-value="<?php echo(!empty($item->field_keyword_best_position)?$item->field_keyword_best_position:"-"); ?>" class="w-10 _loading"> <span class='loading-item'></span><span class='td-content'><?php echo(!empty($item->field_keyword_best_position)?$item->field_keyword_best_position:"-"); ?></span></td>
    <td data-key="sort_6" class="w-20 _loading url-seo" title="<?php echo(!empty($item->field_url)?$item->field_url:"-"); ?>" data-value="<?php echo(!empty($item->field_url)?$item->field_url:"-");?>">
        <span class='loading-item'></span>
        <div class="d-flex space-between">
            <span target="_blank" href="<?php echo(!empty($item->field_url)?$item->field_url:"-"); ?>"><?php echo(!empty($item->field_url)?$item->field_url:"-"); ?></span>
            <?php if(!empty($item->field_url)): ?>
                <a href="<?php echo(!empty($item->field_url)?$item->field_url:"-"); ?>" title="<?php echo(!empty($item->field_url)?$item->field_url:"-"); ?>" target="_blank">
                    <i class="fa fa-external-link"></i>
                </a>
            <?php endif; ?>
        </div>
    </td>
    <td data-key="sort_8" class="w-10">
        <?php if(!empty($item->field_tags)):  $_tags = explode(",",$item->field_tags);?>
            <?php $tag_name = ''; ?>
            <?php foreach($_tags as $_tag):  ?>
                    <?php

                        $tag = taxonomy_term_load($_tag);
                        if(!empty($tag)) $tag_name .= $tag->name.',';
                    ?>
            <?php endforeach; ?>
            <div class="line-break-2" title="<?php echo $tag_name ?>">
                <?php echo $tag_name ?>
            </div>
        <?php endif; ?>
    </td>
    <td data-key="sort_7" style="white-space: nowrap" data-value="<?php echo $item->changed; ?>" class="w-7"><?php echo date("d/m/Y",$item->changed); ?></td>
</tr>