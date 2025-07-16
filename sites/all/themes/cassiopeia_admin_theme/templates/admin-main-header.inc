<?php

global $user;
global $base_url;

if (!empty($user->uid)) {
  $_user = user_load($user->uid);
}

$_site_name = variable_get('site_name', '');

?>


<header class="main-header">
    <!-- Logo -->
    <a href="/admin/cassiopeia/dashboard" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b><?php  print (strtoupper (substr($_site_name, 0, 1)));  ?></b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b><?php print($_site_name); ?></b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas"
           role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Messages: style can be found in dropdown.less-->
                <li>
                    <a href="/"><span class="fa fa-home"></span></a>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle"
                       data-toggle="dropdown">

                      <?php if (!empty($_user) && !empty($_user->picture)): ?>
                        <?php
                        $_user_avatar = (array) $_user->picture;
                        $_user_avatar['style_name'] = 'style_60x60';
                        $_user_avatar['path'] = $_user_avatar['uri'];
                        $_user_avatar['attributes'] = array('class' => 'img-circle');
                        $_user_avatar_url = image_style_url('style_60x60', $_user_avatar['path']);
                        print ('<img src="' . $_user_avatar_url . '" class="user-image" alt="User Image">');
                        ?>
                      <?php else: ?>
                          <img src="<?php print ($base_url . '/' . drupal_get_path('theme', 'cassiopeia_admin_theme') . '/images/avatar.png') ?>"
                               class="user-image" alt="User Image">
                      <?php endif; ?>

                        <span class="hidden-xs"><?php print($user->name); ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                          <?php if (!empty($_user) && !empty($_user->picture)): ?>
                            <?php
                            $_user_avatar = (array) $_user->picture;
                            $_user_avatar['style_name'] = 'style_60x60';
                            $_user_avatar['path'] = $_user_avatar['uri'];
                            $_user_avatar['attributes'] = array('class' => 'img-circle');
                            $_user_avatar_url = image_style_url('style_60x60', $_user_avatar['path']);
                            print ('<img src="' . $_user_avatar_url . '" class="user-image" alt="User Image">');
                            ?>
                          <?php else: ?>
                              <img src="<?php print ($base_url . '/' . drupal_get_path('theme', 'cassiopeia_admin_theme') . '/images/avatar.png') ?>"
                                   class="img-circle" alt="User Image">
                          <?php endif; ?>
                            <p>
                              <?php print($user->name) ?>
                                <small><?php print (t('Member since') . ' ' . date('m/d/Y', $user->created)) ?></small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="row">
                                <div class="col-xs-4 text-center">
                                    <a href="#">Followers</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="#">Sales</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="#">Friends</a>
                                </div>
                            </div>
                            <!-- /.row -->
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="#"
                                   class="btn btn-default btn-flat"><?php print(t('Profile')); ?></a>
                            </div>
                            <div class="pull-right">
                                <a href="/user/logout"
                                   class="btn btn-default btn-flat"><?php print(t('Sign out')); ?></a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i
                                class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>


