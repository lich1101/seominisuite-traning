<?php
global $user;
global $base_url;
if(!empty($user->uid)){
    drupal_goto("/dashboard");
//    drupalremove
//    include DRUPAL_ROOT."/index.html";
}else{
    drupal_goto("index.html");
}
?>
<!--asdf-->
