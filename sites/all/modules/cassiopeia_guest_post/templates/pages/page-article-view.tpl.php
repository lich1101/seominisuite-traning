<?php
$query = db_select("cassiopeia_guest_post_article_log","cassiopeia_guest_post_article_log");
$query->fields("cassiopeia_guest_post_article_log");
$query->condition("aid",$node->id);
$result = $query->execute()->fetchAll();
_print_r(unserialize($result[0]->wp_post));
_print_r($result);
$node->content = str_replace("figure","div",$node->content);
//$node->content = str_replace("article","div",$node->content);
$doc = new DOMDocument();
$doc->loadHTML('<?xml encoding="utf-8" ?>' .$node->content,LIBXML_NOERROR);
$h2Nodes = $doc->getElementsByTagName('h2');
?>
<div class="node-regulation-detail page-article-view">
    <div class="node-container ">
        <div class="node-content row">
            <div class="left-block col-md-8 ">
                <div class="bg-white padding-24">
                    <div class="node-title">
                        <h1><?php echo $node->title; ?></h1>
                    </div>
                    <div class="node-body">
                        <?php echo $node->content; ?>
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