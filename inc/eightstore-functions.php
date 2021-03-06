<?php
/**
 * 8Store Lite functions and definitions
 *
 * @package 8Store Lite
 */
/**
 * My Functions
 */

//adding class to body boxed/full-width
function eightstore_lite_bodyclass($classes){
	$classes[]= get_theme_mod('webpage_layout');
	return $classes;
}
add_filter('body_class','eightstore_lite_bodyclass' );

function eightstore_lite_count_widgets( $sidebar_id ) {
	// If loading from front page, consult $_wp_sidebars_widgets rather than options
	// to see if wp_convert_widget_settings() has made manipulations in memory.
	global $_wp_sidebars_widgets;
	if ( empty( $_wp_sidebars_widgets ) ) :
		$_wp_sidebars_widgets = get_option( 'sidebars_widgets', array() );
	endif;
	
	$sidebars_widgets_count = $_wp_sidebars_widgets;
	
	if ( isset( $sidebars_widgets_count[ $sidebar_id ] ) ) :
		$widget_count = count( $sidebars_widgets_count[ $sidebar_id ] );
		$widget_classes = 'es-widget-count-' . count( $sidebars_widgets_count[ $sidebar_id ] );
		return $widget_classes;
	endif;
}

function eightstore_ticker_header_customizer(){
	//Check if ticker is enabled
	$eightstore_lite_ticker = get_theme_mod('eightstore_ticker_checkbox');
	if($eightstore_lite_ticker==1)
	{
		$ticker_title = get_theme_mod('eightstore_ticker_title');
		$ticker_category = get_theme_mod('ticker_setting_category');
		if(empty($ticker_title)){$ticker_title="Latest";}
		if( !empty($ticker_category)) {
			?>
			<div class="top-ticker">
				<script>
					jQuery(document).ready(function($){
						var rtlena=false;
						if($('body').hasClass('rtl')){
							var rtlena = true;
						}
						$('#ticker').slick({
							slidesToShow: 1,
							slidesToScroll: 1,
							rtl:rtlena,
							autoplay: true,
							autoplaySpeed: 2000,
							speed:2000,
							cssEase:'linear',
							arrows:false
						});
					}); //jquery close
				</script> <!-- close script -->
				<?php
				$loop = new WP_Query(array(
					'cat' => $ticker_category,
					'posts_per_page' => -1    
				));
				if($loop->have_posts()) {
					?>
					<span class="ticker-title"><?php echo $ticker_title;?></span>
					<ul id="ticker" class="hidden">
						<?php
						$i=0;
						while($loop->have_posts()){
							$loop->the_post();
							$i++;
							?>
							<li>
								<h5 class="ticker_tick ticker-h5-<?php echo $i; ?>"> <?php the_title(); ?> </h5>
							</li>
							<?php  
						}
						?>
					</ul>
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
			<?php
		}
	}
}

/** Function to add span in the title */
function eightstore_lite_get_title($title){
    //$title = get_the_title();
	$e_title = '';
	$arr = explode(' ', $title);
	$count = count($arr);
	if( $count > 1 ){
		$i=0;
		$e_title .= "<p class='first-three'>".$arr[$i++];
		if($count>=2){$e_title .= " ".$arr[$i++];}
		if($count>=3){$e_title .= " ".$arr[$i++];}
		$e_title .= "</p>";
		$e_title .= "<p class='other-all'>";
		for ($j=$i; $j < $count; $j++) { 
			$e_title .= $arr[$j]." ";
		}
		$e_title .= "</p>";
		echo apply_filters('the_title', $e_title);
	}else{
		echo apply_filters('the_title', $title);
	}
}
//homepage slider configuration settings
function eightstore_lite_homepage_slider_config(){
	$display_slider = get_theme_mod('display_slider','1');
	$display_pager = (get_theme_mod('display_pager','1')=="0") ? "false" : "true";
	$display_controls = (get_theme_mod('display_controls','1') == "0") ? "false" : "true";
	$auto_transition = (get_theme_mod('enable_auto_transition','1') == "0") ? "false" : "true";
	$transition_type = get_theme_mod('transition_type','true');
	$transition_speed = (!get_theme_mod('transition_speed')) ? "1000" : get_theme_mod('transition_speed');
	if( $display_slider != "0") : 
		?>
		<script type="text/javascript">
			jQuery(function($){
				var rtlena=false;
				if($('body').hasClass('rtl')){
					var rtlena = true;
				}
				$('#home-slider .es-slider').slick({
					dots: <?php echo esc_attr($display_pager); ?>,
					arrows: <?php echo esc_attr($display_controls); ?>,
					autoplay:<?php echo esc_attr($auto_transition); ?>,
					fade: <?php echo esc_attr($transition_type); ?>,
					speed: <?php echo esc_attr($transition_speed); ?>,
					cssEase: 'linear',
					pauseOnHover: false,
					rtl:rtlena
				});				
			});
		</script>
		<?php
	endif;
}
add_action('wp_head','eightstore_lite_homepage_slider_config');

//homepage slider content
function eightstore_lite_homepage_slider_content(){
	$display_slider = (get_theme_mod('display_slider','1'))?get_theme_mod('display_slider','1'):"1";
	$display_captions = (get_theme_mod('display_captions','1'))?get_theme_mod('display_captions','1'):"1";
	if( $display_slider == "1") :
		?>
		<section id="home-slider">
			<div class="es-slider">
				<?php 
				$slider_category = get_theme_mod('slider_setting_category');
				if( !empty($slider_category)) :
					$loop = new WP_Query(array(
						'category_name' => $slider_category,
						'posts_per_page' => -1    
					));
					if($loop->have_posts()) : 
						while($loop->have_posts()) : 
							$loop-> the_post();
							$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full', false );
							?>
							<div class="slides">
								<a href="<?php the_permalink();?>">
									<img src="<?php echo esc_url($image[0]); ?>" alt="<?php the_title_attribute(); ?>" />
								</a>
								<?php 
								$display_captions = get_theme_mod('display_captions','1');
								if(($display_captions!=0)){$display_captions=1;}
								if($display_captions == 1): ?>
									<div class="banner-caption">
										<div class="caption-wrapper">
											<div class="caption-title">
												<a href="<?php the_permalink();?>">
													<?php eightstore_lite_get_title(get_the_title()); ?>
												</a>
											</div>
											<div class="caption-desc">
												<?php echo eightstore_lite_excerpt(get_the_content(),100,'...',true,true); ?>
											</div>
										</div>
									</div>
									<?php  
								endif; ?>
							</div>
							<?php 
						endwhile;
					endif; ?>
				<?php endif;
				?>
			</div>
			<?php  
		endif; 
		?>
	</section>
	<?php
}
add_action('eightstore_lite_homepage_slider','eightstore_lite_homepage_slider_content', 10);

	//Social Icons Settings
function eightstore_lite_social_links(){
	$facebooklink = get_theme_mod('social_facebook');
	$twitterlink = get_theme_mod('social_twitter');
	$google_pluslink = get_theme_mod('social_googleplus');
	$youtubelink = get_theme_mod('social_youtube');
	$pinterestlink = get_theme_mod('social_pinterest');
	$linkedinlink = get_theme_mod('social_linkedin');
	$vimeolink = get_theme_mod('social_vimeo');
	$instagramlink = get_theme_mod('social_instagram');
	$skypelink = get_theme_mod('social_skype');
	?>
	<div class="social-icons">
		<?php if(!empty($facebooklink)){ ?>
			<a href="<?php echo esc_url(get_theme_mod('social_facebook')); ?>" class="facebook" data-title="Facebook" target="_blank"><i class="fa fa-facebook"></i><span></span></a>
		<?php } ?>

		<?php if(!empty($twitterlink)){ ?>
			<a href="<?php echo esc_url(get_theme_mod('social_twitter')); ?>" class="twitter" data-title="Twitter" target="_blank"><i class="fa fa-twitter"></i><span></span></a>
		<?php } ?>

		<?php if(!empty($google_pluslink)){ ?>
			<a href="<?php echo esc_url(get_theme_mod('social_googleplus')); ?>" class="gplus" data-title="Google Plus" target="_blank"><i class="fa fa-google-plus"></i><span></span></a>
		<?php } ?>

		<?php if(!empty($youtubelink)){ ?>
			<a href="<?php echo esc_url(get_theme_mod('social_youtube')); ?>" class="youtube" data-title="Youtube" target="_blank"><i class="fa fa-youtube"></i><span></span></a>
		<?php } ?>

		<?php if(!empty($pinterestlink)){ ?>
			<a href="<?php echo esc_url(get_theme_mod('social_pinterest')); ?>" class="pinterest" data-title="Pinterest" target="_blank"><i class="fa fa-pinterest"></i><span></span></a>
		<?php } ?>

		<?php if(!empty($linkedinlink)){ ?>
			<a href="<?php echo esc_url(get_theme_mod('social_linkedin')); ?>" class="linkedin" data-title="Linkedin" target="_blank"><i class="fa fa-linkedin"></i><span></span></a>
		<?php } ?>

		<?php if(!empty($vimeolink)){ ?>
			<a href="<?php echo esc_url(get_theme_mod('social_vimeo')); ?>" class="vimeo" data-title="Vimeo" target="_blank"><i class="fa fa-vimeo-square"></i><span></span></a>
		<?php } ?>

		<?php if(!empty($instagramlink)){ ?>
			<a href="<?php echo esc_url(get_theme_mod('social_instagram')); ?>" class="instagram" data-title="instagram" target="_blank"><i class="fa fa-instagram"></i><span></span></a>
		<?php } ?>

		<?php if(!empty($skypelink)){ ?>
			<a href="<?php echo __('skype:', 'eightstore-lite').esc_url(get_theme_mod('social_skype')); ?>" class="skype" data-title="Skype"><i class="fa fa-skype"></i><span></span></a>
		<?php } ?>
	</div>
	<?php
}
add_action('eightstore_lite_social_links','eightstore_lite_social_links', 10);

function eightstore_lite_payment_partner_logos()
{
	$payment_partner_1 = get_theme_mod('paymentlogo1_image');
	$payment_partner_2 = get_theme_mod('paymentlogo2_image');
	$payment_partner_3 = get_theme_mod('paymentlogo3_image');
	$payment_partner_4 = get_theme_mod('paymentlogo4_image');
	$ssl_seal = get_theme_mod('other1_image');
	$other_seal_1 = get_theme_mod('other2_image');
	$other_seal_2 = get_theme_mod('other3_image');
	if($payment_partner_1!="" || $payment_partner_2!="" || $payment_partner_1!="" || $payment_partner_4!="" || $ssl_seal!="" || $other_seal_1!="" || $other_seal_1!="")
	{
		?>
		<div class="payment-partner">
			<div class="store=wrapper">
				<?php if(!empty($payment_partner_1)): ?>
					<img id="partner_logo1" class="partner-logos" src="<?php echo esc_url($payment_partner_1)?>" alt="<?php _e('Partner Logo 1', 'eightstore-lite') ?>" />
				<?php endif; ?>

				<?php if(!empty($payment_partner_2)): ?>
					<img id="partner_logo2" class="partner-logos" src="<?php echo esc_url($payment_partner_2)?>" alt="<?php _e('Partner Logo 2', 'eightstore-lite') ?>" />
				<?php endif; ?>

				<?php if(!empty($payment_partner_3)): ?>
					<img id="partner_logo3" class="partner-logos" src="<?php echo esc_url($payment_partner_3)?>" alt="<?php _e('Partner Logo 3', 'eightstore-lite') ?>" />
				<?php endif; ?>

				<?php if(!empty($payment_partner_4)): ?>
					<img id="partner_logo4" class="partner-logos" src="<?php echo esc_url($payment_partner_4)?>" alt="<?php _e('Partner Logo 4', 'eightstore-lite') ?>" />
				<?php endif; ?>

				<?php if(!empty($ssl_seal)): ?>
					<img id="ssl_seal" class="partner-logos" src="<?php echo esc_url($ssl_seal)?>" alt="<?php _e('SSL Seal', 'eightstore-lite') ?>" />
				<?php endif; ?>

				<?php if(!empty($other_seal_1)): ?>
					<img id="other_seal1" class="partner-logos" src="<?php echo esc_url($other_seal_1)?>" alt="<?php _e('Other Seal 1', 'eightstore-lite') ?>" />
				<?php endif; ?>

				<?php if(!empty($other_seal_2)): ?>
					<img id="other_seal2" class="partner-logos" src="<?php echo esc_url($other_seal_2)?>" alt="<?php _e('Other Seal 2', 'eightstore-lite') ?>" />
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
add_action('eightstore_lite_payment_partner_logos','eightstore_lite_payment_partner_logos', 10);

if ( ! function_exists( 'is_woocommerce_available' ) ) {
	function is_woocommerce_available() {
		if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
	}
}
if(is_woocommerce_available()):
	

	function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0  ) {
		global $post;

		if ( has_post_thumbnail() ) {
			return get_the_post_thumbnail( $post->ID, $size );
		} elseif ( wc_placeholder_img_src() ) {
			$placeholder = eightstore_lite_wc_placeholder_img_src();
			$alt = get_the_title();
			$placeholder_img = '<img src="'.$placeholder.'" alt="'.$alt.'" />';
			return $placeholder_img;
		}
	}

	function eightstore_lite_wc_placeholder_img_src(){
		$placeholder = "";
		$custom_placeholder = get_theme_mod('wc_custom_placeholder');
		if($custom_placeholder!='')
		{
			$placeholder = $custom_placeholder;
		}
		else
		{
			$placeholder = get_template_directory_uri()."/images/noimage.png";//wc_placeholder_img_src();
		}
		return $placeholder;
	}

	add_filter('loop_shop_columns', 'eightstore_lite_loop_columns');
	if (!function_exists('eightstore_lite_loop_columns')) {
		function eightstore_lite_loop_columns() {
				// Change number or products per row to $x
			if(get_theme_mod('wc_product_number_rows') && get_theme_mod('wc_product_number_rows')>0){
				$xr = get_theme_mod('wc_product_number_rows');
			} else {
				$xr = 4;
			}
			return $xr; 

		}
	}
	add_filter( 'loop_shop_per_page', 'eightstore_lite_shop_items', 20 );
	function eightstore_lite_shop_items($cols) {
		global $num_products;
		// Display $num_products products per page.
		if(get_theme_mod('wc_product_number_total') && get_theme_mod('wc_product_number_total')>0){

			$num_products = get_theme_mod('wc_product_number_total');
		} else {
			$num_products = 12;
		}
		return $num_products;
	}

endif;

	//Declare Woocommerce support
add_action( 'after_setup_theme', 'eightstore_lite_woocommerce_support' );
function eightstore_lite_woocommerce_support() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'eightstore_lite_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'eightstore_lite_wrapper_end', 10);

function eightstore_lite_wrapper_start() {
	echo '<div class="store-wrapper">';
}

function eightstore_lite_wrapper_end() {
	echo '</div>';
}


	/** 
	 * Truncates text without breaking HTML Code
	 */
	function eightstore_lite_excerpt($eightstore_lite_text, $eightstore_lite_length = 100, $eightstore_lite_ending = '...', $eightstore_lite_exact = true, $eightstore_lite_considerHtml = true) {
		if ($eightstore_lite_considerHtml) {
  // if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $eightstore_lite_text)) <= $eightstore_lite_length) {
				return $eightstore_lite_text;
			}

  // splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $eightstore_lite_text, $eightstore_lite_lines, PREG_SET_ORDER);

			$eightstore_lite_total_length = strlen($eightstore_lite_ending);
			$eightstore_lite_open_tags = array();
			$eightstore_lite_truncate = '';

			foreach ($eightstore_lite_lines as $eightstore_lite_line_matchings) {
   // if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($eightstore_lite_line_matchings[1])) {
    // if it???s an ???empty element??? with or without xhtml-conform closing slash (f.e.)
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $eightstore_lite_line_matchings[1])) {
    // do nothing
    // if tag is a closing tag (f.e.)
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $eightstore_lite_line_matchings[1], $eightstore_lite_tag_matchings)) {
     // delete tag from $open_tags list
						$eightstore_lite_pos = array_search($eightstore_lite_tag_matchings[1], $eightstore_lite_open_tags);
						if ($eightstore_lite_pos !== false) {
							unset($eightstore_lite_open_tags[$eightstore_lite_pos]);
						}
     // if tag is an opening tag (f.e. )
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $eightstore_lite_line_matchings[1], $eightstore_lite_tag_matchings)) {
     // add tag to the beginning of $open_tags list
						array_unshift($eightstore_lite_open_tags, strtolower($eightstore_lite_tag_matchings[1]));
					}
    // add html-tag to $truncate???d text
					$eightstore_lite_truncate .= $eightstore_lite_line_matchings[1];
				}

   // calculate the length of the plain text part of the line; handle entities as one character
				$eightstore_lite_content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $eightstore_lite_line_matchings[2]));
				if ($eightstore_lite_total_length+$eightstore_lite_content_length > $eightstore_lite_length) {
    // the number of characters which are left
					$eightstore_lite_left = $eightstore_lite_length - $eightstore_lite_total_length;
					$eightstore_lite_entities_length = 0;
    // search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $eightstore_lite_line_matchings[2], $eightstore_lite_entities, PREG_OFFSET_CAPTURE)) {
     // calculate the real length of all entities in the legal range
						foreach ($eightstore_lite_entities[0] as $eightstore_lite_entity) {
							if ($eightstore_lite_entity[1]+1-$eightstore_lite_entities_length <= $eightstore_lite_left) {
								$eightstore_lite_left--;
								$eightstore_lite_entities_length += strlen($eightstore_lite_entity[0]);
							} else {
       // no more characters left
								break;
							}
						}
					}
					$eightstore_lite_truncate .= substr($eightstore_lite_line_matchings[2], 0, $eightstore_lite_left+$eightstore_lite_entities_length);
    // maximum lenght is reached, so get off the loop
					break;
				} else {
					$eightstore_lite_truncate .= $eightstore_lite_line_matchings[2];
					$eightstore_lite_total_length += $eightstore_lite_content_length;
				}

   // if the maximum length is reached, get off the loop
				if($eightstore_lite_total_length >= $eightstore_lite_length) {
					break;
				}
			}
		} else {
			if (strlen($eightstore_lite_text) <= $eightstore_lite_length) {
				return $eightstore_lite_text;
			} else {
				$eightstore_lite_truncate = substr($eightstore_lite_text, 0, $eightstore_lite_length - strlen($eightstore_lite_ending));
			}
		}

 // if the words shouldn't be cut in the middle...
		if (!$eightstore_lite_exact) {
  // ...search the last occurance of a space...
			$eightstore_lite_spacepos = strrpos($eightstore_lite_truncate, ' ');
			if (isset($eightstore_lite_spacepos)) {
   // ...and cut the text in this position
				$eightstore_lite_truncate = substr($eightstore_lite_truncate, 0, $eightstore_lite_spacepos);
			}
		}

 // add the defined ending to the text
		$eightstore_lite_truncate .= $eightstore_lite_ending;

		if($eightstore_lite_considerHtml) {
  // close all unclosed html-tags
			foreach ($eightstore_lite_open_tags as $eightstore_lite_tag) {
				$eightstore_lite_truncate .= '';
			}
		}

		return $eightstore_lite_truncate;

	}

	function eightstore_lite_fonts_cb(){
		echo "<link href='//fonts.googleapis.com/css?family=Arimo:400,700|Open+Sans:400,700,600italic,300|Roboto+Condensed:300,400,700|Roboto:300,400,700|Slabo+27px|Oswald:400,300,700|Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic|Source+Sans+Pro:200,300,400,600,700,900,200italic,300italic,400italic,600italic,700italic,900italic|PT+Sans:400,700,400italic,700italic|Droid+Sans:400,700|Raleway:400,100,200,300,500,600,700,800,900|Droid+Serif:400,700,400italic,700italic|Ubuntu:300,400,500,700,300italic,400italic,500italic,700italic|Montserrat:400,700|Roboto+Slab:400,100,300,700|Merriweather:400italic,400,900,300italic,300,700,700italic,900italic|Lora:400,700,400italic,700italic|PT+Sans+Narrow:400,700|Bitter:400,700,400italic|Lobster|Yanone+Kaffeesatz:400,200,300,700|Arvo:400,700,400italic,700italic|Oxygen:400,300,700|Titillium+Web:400,200,200italic,300,300italic,400italic,600,600italic,700,700italic,900|Dosis:200,300,400,500,600,700,800|Ubuntu+Condensed|Playfair+Display:400,700,900,400italic,700italic,900italic|Cabin:400,500,600,700,400italic,500italic,600italic|Muli:300,400,300italic,400italic' rel='stylesheet' type='text/css'>";   
	}
	add_action('wp_footer', 'eightstore_lite_fonts_cb');



	/** adding ocdi compatibility */
	function eightstore_lite_ocdi_import_files() {
		return array(
			array(
				'import_file_name'             => 'Eightstore Lite Demo',
			//'categories'                   => array( 'Category 1', 'Category 2' ),
				'local_import_file'            => trailingslashit( get_template_directory() ) . 'welcome/demo/eightstore-lite/content.xml',
				'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'welcome/demo/eightstore-lite/widgets.wie',
				'local_import_customizer_file' => trailingslashit( get_template_directory() ) . 'welcome/demo/eightstore-lite/customizer_options.dat',
				'import_preview_image_url'     => get_template_directory_uri().'/screenshot.png',
				'import_notice'                => __( 'After you import this demo, you might have to setup the menu separately.', 'eightstore-lite' ),
				'preview_url'                  => 'https://8degreethemes.com/demo/8store-lite/',
			)
		);
	}
	add_filter( 'pt-ocdi/import_files', 'eightstore_lite_ocdi_import_files' );


	function eightstore_lite_ocdi_after_import( $selected_import ) {
	// Assign menus to their locations.
		$main_menu = get_term_by( 'name', 'Menu 1', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
			'primary' => $main_menu->term_id,
		));

	// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'Home' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
	}
	add_action( 'pt-ocdi/after_import', 'eightstore_lite_ocdi_after_import' );

	add_action('wp_head', function(){
		?>
			<script type="text/javascript">
				jQuery.browser = {};
			(function () {
			jQuery.browser.msie = false;
			jQuery.browser.version = 0;
			if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
			jQuery.browser.msie = true;
			jQuery.browser.version = RegExp.$1;
			}
			})();
			</script>
		<?php 
	});