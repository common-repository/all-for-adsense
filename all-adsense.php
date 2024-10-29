<?php
/* 
	Plugin Name: Framework All For Adsense
	Plugin URI: http://wordpress.org/extend/plugins/all-adsense/
	Description: Improves your AdSense Plugin adding: protection against fraudulent clicks, anti adblock, loads the ads asynchronously
	Author: Friedrich Schmidt
	Version: 1.0.3
	Author URI: http://wordpress.org/extend/plugins/all-adsense/
*/
if (!class_exists("sense")) {
  class sense {
    public static $Preselected = [
            'code'=>'',
            'lib'=>'',
            'async'=>1,
            'no_yellow'=>0,
            'version'=>'1.0.3',
    ];
    function sense(){
		$this->p=$this->read_settings();
		$this->wpu=WP_PLUGIN_URL . '/all-for-adsense/';
		$this->u=($this->p['lib']!='')?$this->p['lib']:$this->wpu.'js/'.$this->lb[0]['async_c'];
        register_activation_hook( __FILE__, array( $this, 'activation' ) );
    }
	public static function activation( $networkwide ) {
		global $wpdb;
		if( function_exists( 'is_multisite' ) && is_multisite() ) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if( $networkwide ) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					sense::during_activation();
				}
				switch_to_blog( $old_blog );
				return;
			}
		}
		sense::during_activation();
	}
	/**
	 * This function is fired from the activation method.
	 *
	 * @since 2.1.1
	 * @access public
	 *
	 * @return void
	 */
	public static function during_activation() {
		$options = sense::read_settings();
		// Add plugin installation date and variable for rating div
		$options['installation_date'] = date( 'Y-m-d h:i:s' );
		// Add Upgraded From Option
		$current_version = $options['version'];
		// Update the current version
		$options['version'] = sense::$Preselected['version'];
		if( $current_version != $options['version']) {
			$options['upgraded_from'] = $current_version;
		}
		update_option( 'afa', $options );
		// Add the transient to redirect (not for multisites)
		set_transient( 'afa_activation_redirect', true, 4600 );
	}
	// Add menu page
	public static function afu_options_add_page() {
		global $esnes;
		add_options_page('All For Adsense Options', 'All For Adsense', 'manage_options', basename(__FILE__), array(&$esnes,'afaoptions_do_page') );
	}
	// Draw the menu page itself
	function afaoptions_do_page() {
			include_once('all-adsense-admin.php');
	}
    function no_yellow(){
        wp_register_style('yellowStyles', $this->wpu . 'css/y.css');
        wp_enqueue_style('yellowStyles');
    }
    var $lb = [
			0 => array(
			   'desc' => 'This library not only detect click fraud, but also automate stopping it too',
			   'sync' => '//www.adsense4u.org/lib/fs.js',
			   'async' => '//www.adsense4u.org/lib/f.js',
			   'async_c' => 'f.js',
			   'pol' => '//www.adsense4u.org/',
			   'ban' => true,
			   'adblock' => false,
			),
			1 => array(
			   'desc' => 'It is an anti Adblock. Finally you can reclaim lost revenue on your site',
			   'sync' => '//www.adsense4u.org/lib/bs.js',
			   'async' => '//www.adsense4u.org/lib/b.js',
			   'async_c' => 'b.js',
			   'pol' => '//www.adsense4u.org/',
			   'ban' => false,
			   'adblock' => true,
			),
			2 => array(
			   'desc' => 'This library not only protect click fraud, but also kill Adblock',
			   'sync' => '//www.adsense4u.org/lib/bfs.js',
			   'async' => '//www.adsense4u.org/lib/bf.js',
			   'async_c' => 'bf.js',
			   'pol' => '//www.adsense4u.org/',
			   'ban' => true,
			   'adblock' => true,
			),
			3 => array(
			   'desc' => 'If you don t want protection against adblock and click fraud, use this library.',
			   'sync' => '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
			   'async' => '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
			   'async_c' => '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
			   'pol' => '//www.google.com/',
			   'ban' => false,
			   'adblock' => false,
			),
	];
    function afa_options_init() {
		register_setting( 'afa_options', 'afa', array($this, 'afa_options_validate') );
    }
	// Sanitize and validate input. Accepts an array, return a sanitized array.
	function afa_options_validate($input) {
		$input['async'] = ( $input['async'] == 1 ? 1 : 0 );
		$input['no_yellow'] = ( $input['no_yellow'] == 1 ? 1 : 0 );
		$input['code'] = strtolower($input['code']);		
		$pos = strpos($input['code'], 'ca-pub-');
		if($pos === false){
			$input['code'] = '';
		}else{
			$input['code'] = '<script>
(adsbygoogle = window.adsbygoogle || []).push({
google_ad_client: "ca-pub-' . substr ($input['code'], $pos + 7, 16) . '",
enable_page_level_ads: true
});
</script>';
		}
		$input['lib'] =  wp_filter_nohtml_kses($input['lib']);
		reset($this->lb);
		while (list($key, $value) = each($this->lb)){
			if(($value['async']==$input['lib'])&&($key!=3)){
				$input['lib']=$this->wpu .'js/'.$value['async_c'];
			}
		}
		return $input;
	}
	public static function read_settings() {
		$options = get_option('afa');
		foreach (self::$Preselected as $key => $value) {
			if(!isset($options[$key])){
				$options[$key]=$value;
			}
		}
		return($options);
	}
    function ads_plugin_links($links,$file) {
        if($file==plugin_basename(__FILE__)) {
            array_unshift($links,'<a href="options-general.php?page='.basename(__FILE__).'">'.__('Settings').'</a>');
        }
        return $links;
    }
	function explain_notice_frontend(){
		printf( '
			<div id="explain_message" class="notice notice-info">
				<p>
					<strong>%1$s</strong>
					%2$s
				</p>
			</div>',
			__( 'All For Adsense:', 'all-adsense' ),
			__('is a framework that extends the functionalities of your AdSense plugin', 'all-adsense')
		);
	}
	function c(){return(strpos($this->u,$this->wpu)===false);}
    function load_css_js_admin(){
        wp_register_script('validate','http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');
        wp_register_script('bootstrapjs','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
        wp_enqueue_script('validate');
        wp_enqueue_script('bootstrapjs');
        wp_register_style('bootstrap','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
        wp_enqueue_style('bootstrap');
        add_action('all_admin_notices', array( $this, 'explain_notice_frontend'));
    }
    function make_ads_async($tag, $handle, $src){
		$found=(strpos($src,$this->lb[3]['async'])!==false);
		if($found){
			$a=($this->p['async']==1?' async':'');
			return '<script'.$a.' src="'.$this->u.'"></script>'.$this->p['code'];
		}else{
			return $tag;
		}
	}
    function process_content($content)
    {
		$content=str_replace($this->lb[3]['async'], $this->u, $content);
		return $content;
    }
  }
}
$esnes = new sense();
if(is_admin()){
	if((isset($_GET['page'])) && ($_GET['page'] == 'all-adsense.php')){
		add_action('admin_menu', array($esnes, 'afu_options_add_page')); // Add setting page
	}
    add_action('admin_init', array($esnes, 'afa_options_init'),-2147483647);
	// Add welcome page
	if( !defined( 'AFA_PLUGIN_DIR' ) ) {
		define( 'AFA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}
    require_once AFA_PLUGIN_DIR . 'welcome.php';
    if( strpos($_SERVER['REQUEST_URI'], basename(plugin_basename(__FILE__))) !== false)
        add_action('admin_enqueue_scripts', array($esnes, 'load_css_js_admin'),-2147483647);
}
if($esnes->p['no_yellow']==1){
	add_action('wp_enqueue_scripts', array($esnes, 'no_yellow'),-2147483647);
}
add_action('script_loader_tag', array($esnes, 'make_ads_async'),2147483645, 3);
add_filter('plugin_action_links', array($esnes, 'ads_plugin_links'),10,2);
add_filter('the_content', array($esnes, 'process_content'),2147483645);
?>