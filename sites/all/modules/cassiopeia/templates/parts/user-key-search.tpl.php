<?php
global $user;
$project_id = $variables['project_id'];
$keys = cassiopeia_get_key_search_by_project_id($project_id);
$index=1;
?>
<?php if(!empty($keys)): ?>
    <?php foreach($keys as $key): ?>
        <tr>
            <td><input type="checkbox" value="<?php echo $key->id; ?>" data-key = "<?php echo $key->key_search; ?>"></td>
            <td><?php echo $index; ?></td>
            <td><?php echo($key->key_search); ?></td>
            <td><?php echo(!empty($key->position)?$key->position:"-"); ?></td>
            <td><?php echo(!empty($key->old_position)?$key->old_position:"-"); ?></td>
            <td><?php echo(!empty($key->best_position)?$key->best_position:"-"); ?></td>
            <td><?php echo(!empty($key->updated)?date("d-m-Y H:i",$key->updated):date("d-m-Y H:i",$key->created)) ?></td>
            <td><?php echo(!empty($key->url)?$key->url:"-"); ?></td>
            <td></td>
        </tr>
    <?php $index++; endforeach; ?>
<?php endif; ?>
