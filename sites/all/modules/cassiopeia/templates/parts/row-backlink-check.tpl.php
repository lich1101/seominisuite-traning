<?php
$item = $variables['item'];
$stt = $variables['stt'];
$project = node_load($item->field_backlink_project['und'][0]['nid']);
$OtherItems = null;
$query = db_select("node","tbl_node");
$query->fields("tbl_node");
$query->condition("tbl_node.nid",$item->nid);
$query->join("field_data_field_backlink_project","field_backlink_project","field_backlink_project.entity_id=tbl_node.nid");
$query->join("field_data_field_backlink_refer_page","field_backlink_refer_page","field_backlink_refer_page.entity_id=tbl_node.nid");
$query->addField("field_backlink_refer_page","field_backlink_refer_page_value","refer_page");
$query->condition("field_backlink_project.field_backlink_project_nid",$project->nid);
$query->leftJoin("tbl_backlink_detail","tbl_backlink_detail","tbl_backlink_detail.nid=tbl_node.nid");
$query->fields("tbl_backlink_detail");
$query->addField("tbl_backlink_detail","created","changed");
$query->orderBy("tbl_backlink_detail.nid","DESC");
$backlink_detail = $query->execute()->fetchAll();
//cassiopeia_dump($item);
$FirstItem = array_values($backlink_detail)[0];
if(count($backlink_detail)>1){
    $OtherItems = array_slice($backlink_detail,1);
}
//cassiopeia_dump($item);
?>
<tr class="<?php echo ($stt%2==1?"ODD":"EVEN"); ?>" data-nid="<?php echo $item->nid; ?>" data-backlink="<?php echo $FirstItem->url; ?>" data-backlink-id="<?php echo $FirstItem->id; ?>">
    <td>
        <label class="mask-chekbox">
            <input data-stt="<?php echo $stt; ?>" type="checkbox" name="select" class="" data-domain="<?php echo($project->field_domain['und'][0]['value']); ?>" value="<?php echo($item->nid); ?>" data-backlink-source="<?php echo($item->field_backlink_refer_page['und'][0]['value']); ?>" >
            <span class="mask-checked"></span>
        </label>
    </td>
    <td><?php echo($stt); ?></td>
    <td class="text-left " title="<?php echo($FirstItem->refer_page); ?>">
        <?php echo($FirstItem->refer_page); ?>
    </td>
    <td class="_loading">
        <?php if($FirstItem->is_in_content>1){
            echo $FirstItem->is_in_content==1?"Yes":"No";
        }else{
            echo "-";
        } ?>
    </td>
    <td class="_loading">
        <?php echo($FirstItem->rel); ?>
    </td>
    <td class="_loading" title="<?php echo($FirstItem->anchor_text); ?>">
        <?php echo($FirstItem->anchor_text); ?>
    </td>
    <td class="_loading">
        <?php echo($FirstItem->url); ?>
    </td>
    <td class="_loading indexed-loading">
        <?php echo !empty($item->field_backlink_indexed['und'][0]['value'])?"Indexed":"Không";?>
    </td>
    <td data-key="tag">
        <?php if(!empty($item->field_tags['und'])): ?>
            <?php foreach($item->field_tags['und'] as $_tag):  ?>
                    <?php
                        $tag_name = ''; 
                        $tag = taxonomy_term_load($_tag['tid']); 
                        if(!empty($tag)) $tag_name .= $tag->name.","; 
                    ?>
            <?php endforeach; ?>
            <div class="line-break-2" title="<?php echo $tag_name ?>">
                <?php echo $tag_name ?>
            </div>
        <?php endif; ?>
    </td>
    <td><?php echo(!empty($item->changed)?date("d/m/Y h:i",$item->changed):"-"); ?></td>
    <td class="_loading">
        <?php
        $errorMessage = "";
            if(!empty($item->field_status['und'][0]['value'])){
                switch ($item->field_status['und'][0]['value']){
                    case 404 :
                        $errorCode = 404;
                        $errorMessage = "Nguồn đặt Backlink không tồn tại!";
                        break;
                    case 999 :
                        $errorCode = "error";
                        $errorMessage = "Nguồn đặt Backlink không hợp lệ!";
                        break;
                    case 500 :
                        $errorCode = 500;
                        $errorMessage = "Nguồn đặt Backlink không hoạt động!";
                        break;
                    default :
                        $errorMessage = "";
                        $errorCode = $item->field_status['und'][0]['value'];
                }
            }
        ?>
        <?php if(!empty($errorCode)): ?>
            <span title="<?php echo $errorMessage; ?>"><?php echo $errorCode; ?> <i class="fa fa-info-circle" ></i></span>
        <?php endif; ?>
    </td>
</tr>
<?php if(!empty($OtherItems)): ?>
    <?php foreach($OtherItems as $otherItem): ?>
        <tr data-nid="<?php echo $item->nid; ?>" class="child" data-backlink="<?php echo $otherItem->url; ?>" data-backlink-id="<?php echo $otherItem->id; ?>">
            <td>

            </td>
            <td></td>
            <td class="text-left " >
                <?php echo($otherItem->refer_page); ?>
            </td>
            <td class="_loading">
                <?php echo $otherItem->is_in_content==1?"Yes":"No"; ?>
            </td>
            <td class="_loading">
                <?php echo($otherItem->rel); ?>
            </td>
            <td class="_loading">
                <?php echo($otherItem->anchor_text); ?>
            </td>
            <td class="_loading">
                <?php echo($otherItem->url); ?>
            </td>
            <td class="_loading">
                <?php
                if($otherItem->indexed==0){
                    echo ("Không");
                }elseif($otherItem->indexed==1){
                    echo "Indexed";
                }else{
                    echo "-";
                }
                ?>
            </td>
            <td >

            </td>
            <td><?php echo(!empty($item->created)?date("d/m/Y h:i",$item->created):"-"); ?></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
