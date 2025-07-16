<?php
global $user;
//print_r($user);
print render($page['content']['metatags']);

//?>
<!--<div class="page-home">-->
<!--   --><?php //echo _cassiopeia_render_theme("module","cassiopeia","templates/pages/page-backlink-projects.tpl.php"); ?>
<!--</div>-->
<div class="wrapper">
<!--    --><?php //include('header.inc'); ?>
    <div id="main-container">
        <section class="content cassiopeia-container">
            <div id="content" class="clearfix">

                <?php if ($action_links): ?>
                    <ul class="action-links"><?php print render($action_links); ?></ul>
                <?php endif; ?>
                <?php if ($tabs): ?>
                    <div class="tabs">
                        <?php print render($tabs); ?>
                    </div>
                <?php endif; ?>
                <div class="page-relog">
                    <div class="page-relog-container">
                        <div class="page-relog-inner">
                            <div class="block-relog block-login">
                                <?php if ($messages): ?>
                                    <div id="console" class="clearfix"><?php print $messages; ?></div>
                                <?php endif; ?>
                                <?php print render($page['content']); ?>
                            </div>
                            <div class="block-bg"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php include('footer.inc'); ?>
</div>

