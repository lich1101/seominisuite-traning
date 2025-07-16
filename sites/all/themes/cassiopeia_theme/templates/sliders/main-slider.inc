<?php
/**
 * Created by PhpStorm.
 * User: VDP
 * Date: 18/04/2017
 * Time: 2:17 PM
 */
?>

<?php

global $language;
$lang_name = $language->language;
$term = taxonomy_term_load(1);


if ( module_exists ('i18n')) {
	$translated_term = i18n_taxonomy_term_get_translation($term, $lang_name);

}else {
	$translated_term = $term;
}

$_mail_slider = cassiopeia_adv_view_adv($translated_term->tid);
?>
<?php if (!empty($_mail_slider)): ?>
  <?php drupal_add_js(drupal_get_path('theme', 'cassiopeia_theme') . '/js/slider-main.js', array('weight' => 1000)); ?>
    <div id="main-slider">
        <div class="swiper-container">
          <?php print($_mail_slider); ?>
        </div>
    </div>
<?php endif; ?>

