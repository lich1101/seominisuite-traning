<?php

function theme_cassiopeia_adv_show_image($variables) {
	global $_domain;
	$node = $variables['node'];
	$pos = unserialize($node->field_c_adv_setting['und'][0]['value']);
	$out = '';
	if ($pos['field_show'] == 3) {
		$out = "";
		$_class_desktop = '';
		$_class_tablet = '';
		if (empty($node->field_c_adv_t_images['und'])) {
			if (empty($node->field_c_adv_m_images['und'])) {
				$_class_desktop =  'no-tablet-mobile';
			}else {
				$_class_desktop = 'no-tablet';
			}
		}else {
			if (empty($node->field_c_adv_m_images['und'])) {
				$_class_tablet .= 'no-mobile';
			}
		}
		
		if (!empty($node->field_c_adv_images['und']) && count($node->field_c_adv_images['und']) > 0) {
			$out .= '<div class="slider-wrapper theme-default"><div id="slider-' . $node->nid . '-desktop" class="cassiopeia-swiper cassiopeia-swiper-desktop '.$_class_desktop.' swiperSlide">';
			$out .= '<div class="swiper-container">';
			$out .= '<div class="swiper-wrapper">';
			foreach ($node->field_c_adv_images['und'] as $key => $image) {
				if (!empty($image['url'])) {
					if (!empty($node->field_c_adv_html_code['und'][$key]['value'])) {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"><a href="' . $image['url'] . '" class="swiper-slide-link"></a><div class=" html-code"> <div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div></div></div>';
							
						}else {
							$out .= '<div class="swiper-slide"><a href="' . $image['url'] . '" class="swiper-slide-link"><img src="'.image_style_url('original',$image['uri']).'" alt=""></a><div class=" html-code"> <div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div></div></div>';
						}
					}
					else {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"><a href="' . $image['url'] . '" class="swiper-slide-link"></a></div>';
						}else {
							$out .= '<div class="swiper-slide" ><a href="' . $image['url'] . '" class="swiper-slide-link"><img src="'.image_style_url('original',$image['uri']).'" alt=""></a></div>';
						}
					}
				}
				else {
					if (!empty($node->field_c_adv_html_code['und'][$key]['value'])) {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"><div class=" html-code"> <div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div></div></div>';
						}else {
							$out .= '<div class="swiper-slide" ><img src="'.image_style_url('original',$image['uri']).'" alt=""><div class=" html-code"><div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div></div></div>';
						}
						
						
					}
					else {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"></div>';
						}else {
							$out .= '<div class="swiper-slide"> <img src="'.image_style_url('original',$image['uri']).'"></div>';
						}
						
						
					}
				}
			}
			$out .= '<div class="swiper-pagination"></div>';
			$out .= '</div>';
			$out .= '<div class="swiper-control">
                            <div class="swiper-nav">
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>';
			$out .= '</div>';
			$out .= '</div></div>';
		}
		if (!empty($node->field_c_adv_t_images['und']) && count($node->field_c_adv_t_images['und']) > 0) {
			$out .= '<div class="slider-wrapper theme-default"><div id="slider-' . $node->nid . '-tablet" class="cassiopeia-swiper cassiopeia-swiper-tablet '.$_class_tablet.' swiperSlide">';
			$out .= '<div class="swiper-container">';
			$out .= '<div class="swiper-wrapper">';
			foreach ($node->field_c_adv_t_images['und'] as $key => $image) {
				if (!empty($image['url'])) {
					if (!empty($node->field_c_adv_html_code['und'][$key]['value'])) {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"><a href="' . $image['url'] . '" class="swiper-slide-link"></a><div class=" html-code"> <div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div></div></div>';
						}else {
							$out .= '<div class="swiper-slide"><a href="' . $image['url'] . '" class="swiper-slide-link"><img src="'.image_style_url('original',$image['uri']).'" alt=""></a><div class=" html-code"><div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div>';
						}
					}
					else {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"><a href="' . $image['url'] . '" class="swiper-slide-link"></a></div>';
						}else {
							$out .= '<div class="swiper-slide" ><a href="' . $image['url'] . '" class="swiper-slide-link"><img src="'.image_style_url('original',$image['uri']).'" alt=""></a></div>';
						}
						
					}
				}
				else {
					if (!empty($node->field_c_adv_html_code['und'][$key]['value'])) {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"><div class=" html-code"> <div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div></div></div>';
						}else {
							$out .= '<div class="swiper-slide"> <img src="'.image_style_url('original',$image['uri']).'" alt=""> <div class=" html-code"> <div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div></div></div>';
						}
					}
					else {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"></div>';
						}else {
							$out .= '<div class="swiper-slide"> <img src="' . image_style_url('original',$image['uri']) . '" alt=""></div>';
						}
					}
				}
			}
			$out .= '<div class="swiper-pagination"></div>';
			$out .= '</div>';
			$out .= '<div class="swiper-control">
                            <div class="swiper-nav">
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>';
			$out .= '</div>';
			$out .= '</div></div>';
		}
		if (!empty($node->field_c_adv_m_images['und']) && count($node->field_c_adv_m_images['und']) > 0) {
			$out .= '<div class="slider-wrapper theme-default"><div id="slider-' . $node->nid . '-mobile" class="cassiopeia-swiper cassiopeia-swiper-mobile swiperSlide">';
			$out .= '<div class="swiper-container">';
			$out .= '<div class="swiper-wrapper">';
			foreach ($node->field_c_adv_m_images['und'] as $key => $image) {
				if (!empty($image['url'])) {
					if (!empty($node->field_c_adv_html_code['und'][$key]['value'])) {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"><a href="' . $image['url'] . '" class="swiper-slide-link"></a><div class=" html-code"> <div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div></div></div>';
						}else {
							$out .= '<div class="swiper-slide" ><a href="' . $image['url'] . '" class="swiper-slide-link"><img src="'.image_style_url('original',$image['uri']).'" alt=""></a><div class=" html-code"> <div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div></div></div>';
						}
					}
					else {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"><a href="' . $image['url'] . '" class="swiper-slide-link"><img src="'.image_style_url('original',$image['uri']).'" alt=""></a></div>';
						}else {
							$out .= '<div class="swiper-slide"><img src="'.image_style_url('original',$image['uri']).'" alt=""></div>';
						}
					}
				}
				else {
					if (!empty($node->field_c_adv_html_code['und'][$key]['value'])) {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"><div class=" html-code"> <div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div></div></div>';
						}else {
							$out .= '<div class="swiper-slide"><img src="'.image_style_url('original',$image['uri']) .'" alt=""> <div class=" html-code"> <div class="container cassiopeia-container html-code-container"> <div class="container cassiopeia-container html-code-container-inner">' . $node->field_c_adv_html_code['und'][$key]['value'] . '</div></div></div></div>';
						}
					}
					else {
						if (!empty($node->field_c_adv_images_like_bg['und'][0]['value'])) {
							$out .= '<div class="swiper-slide" style="background-image:url(' . image_style_url('original',$image['uri']) . ');"></div>';
						}else {
							$out .= '<div class="swiper-slide"><img src="'.image_style_url('original',$image['uri']) .'" alt=""></div>';
						}
					}
				}
			}
			$out .= '<div class="swiper-pagination"></div>';
			$out .= '</div>';
			$out .= '<div class="swiper-control">
                            <div class="swiper-nav">
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>';
			$out .= '</div>';
			$out .= '</div></div>';
		}
		
	}
	
	return $out;
}