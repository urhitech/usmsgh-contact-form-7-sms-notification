<?php 
global $Custom_pagetitle, $slugs; 

$menu =''; 
$menus = array('history' => __('History',Contact_FormSI_TXT),'settings' => __('Settings',Contact_FormSI_TXT));
$link = menu_page_url($slugs,false);
$addClass = false;

// Sanitize and validate tab parameter
$current_tab = isset($_REQUEST['tab']) ? sanitize_key( wp_unslash( $_REQUEST['tab'] ) ) : '';
$current_tab = array_key_exists($current_tab, $menus) ? $current_tab : '';

foreach($menus as $menuI => $menuV){
	$class = ''; 
	if( !empty($current_tab) ){
		if($menuI == $current_tab){ $class = 'nav-tab-active'; }
	} else {
		if(! $addClass){$class = 'nav-tab-active'; $addClass = true;}
		
	}

	$menu .= '<a id="'.esc_attr($menuI).'" class="nav-tab '.esc_attr($class).'" href="'.esc_url($link.'&tab='.$menuI).'">'.esc_html($menuV).'</a>';
}

?>

<div class="wrap">
	<h2 class="nav-tab-wrapper woo-nav-tab-wrapper"><?php echo $menu; ?></h2>
	<?php
		if( !empty($current_tab) ){
			if($current_tab == 'history'){
				cf7si_history_listing(); 
			} else if($current_tab == 'settings'){
				Contact_FormSI()->load_files(Contact_FormSI()->get_vars('PATH').'template/cf7-settings.php'); 
			}
		} else {
			cf7si_history_listing(); 
		}
	?>