<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://ilmdesigns.com/
 * @since      1.0.0
 *
 * @package    Square_Thumbnails
 * @subpackage Square_Thumbnails/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Square_Thumbnails
 * @subpackage Square_Thumbnails/admin
 * @author     ILMDESIGNS <narcisbodea@gmail.com>
 */
class Square_Thumbnails_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

        public function display_admin_page(){
            add_submenu_page('upload.php',
                    'Square Thumbnails Options',
                    'Square Thumbnails',
                    'manage_options',
                    'square-thumbnails-admin-page',
                    array($this,'showPage'),
                    '',
                    '3.0'
           );
        }
        
        
	public function link_settings( $links ) {

		$links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'upload.php?page=square-thumbnails-admin-page' ) ), esc_html__( 'Settings', 'square-thumbnails' ) );

		return $links;

	} // link_settings()        
        
        
        public function showPage(){
            include 'partials/square-thumbnails-admin-display.php';
        }
            


        public function make_square_size_image($meta){
                if(!function_exists('imagecreatefromjpeg')) return;
                if($meta['width']===$meta['height']){
                    return $meta;
                }         
                global $_wp_additional_image_sizes;
                $sizes=$_wp_additional_image_sizes;
                //defaults: thumbnail, medium, large 
                $isize='thumbnail';
                $sizes[$isize]=array(
                    'width'=>get_option( "{$isize}_size_w" ),
                    'height'=>get_option( "{$isize}_size_h" ),
                    'crop'=>(bool)get_option( "{$isize}_size_crop" ),      
                );
                $isize='medium';
                $sizes[$isize]=array(
                    'width'=>get_option( "{$isize}_size_w" ),
                    'height'=>get_option( "{$isize}_size_h" ),
                    'crop'=>(bool)get_option( "{$isize}_size_crop" ),      
                );
                $isize='large';
                $sizes[$isize]=array(
                    'width'=>get_option( "{$isize}_size_w" ),
                    'height'=>get_option( "{$isize}_size_h" ),
                    'crop'=>(bool)get_option( "{$isize}_size_crop" ),      
                );


                $file = wp_upload_dir();
                $file = trailingslashit($file['basedir']).$meta['file'];
                $info = getimagesize($file);
                $dir=trailingslashit(dirname($file));  
                $width=$meta['width'];
                $height=$meta['height'];
                foreach($sizes as $nsize=>$size){
                    if($size['width']===$size['height']){          
                          $thumb=$meta['sizes'][$nsize];            
                          if($thumb['width']!==$size['width'] || $thumb['height']!==$size['height']){
                                  $f=$dir.$thumb['file'];
                                  if($thumb['mime-type']=='image/png'){
                                      $im= imagecreatefrompng($file);
                                  }
                                  elseif($thumb['mime-type']=='image/jpeg'){
                                      $im= imagecreatefromjpeg($file);
                                  }
                                  elseif($thumb['mime-type']=='image/gif'){
                                      $im= imagecreatefromgif($file);
                                  }
                                  elseif($thumb['mime-type']=='image/bmp'){
                                      $im= imagecreatefromgif($file);
                                  }
                                  elseif($thumb['mime-type']=='image/vnd.wap.wbmp'){
                                      $im= imagecreatefromwbmp($file);
                                  }
                                  else{
                                      continue;
                                  }                    
                                  $dest = imagecreatetruecolor($size['width'], $size['width']);	
                                  $white=imagecolorallocate($dest,255,255,255);
                                  imagefilledrectangle($dest,0,0,$size['width'], $size['width'],$white);
                                  $destx=0;
                                  $desty=0;
                                  $proportion=$width/$height;
                                  $h=$size['width'];
                                  $w=$size['width'];

                                  if($width>$height){
                                         $h=$w/$proportion;
                                         $desty=($w-$h)/2;
                                  }
                                  else{
                                         $w=$w*$proportion;
                                         $h=$size['width'];
                                         $destx=($size['width']-$w)/2;
                                  }
                                  imagecopyresampled ( $dest, $im, $destx, $desty, 0, 0 , $w, $h, $width, $height );
                                  if($thumb['mime-type']=='image/png'){
                                      imagepng($dest, $f);
                                  }
                                  elseif($thumb['mime-type']=='image/jpeg'){
                                      imagejpeg($dest, $f);
                                  }
                                  elseif($thumb['mime-type']=='image/bmp'){
                                      imagebmp($dest, $f);
                                  }
                                  elseif($thumb['mime-type']=='image/gif'){
                                      imagegifg($dest, $f);
                                  }
                                  elseif($thumb['mime-type']=='image/vnd.wap.wbmp'){
                                      imagewbmp($dest, $f);
                                  }
                                 imagedestroy($im);
                                 imagedestroy($dest);
                          }          
                    }
                }

                return $meta;
        }
        
        
        
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
        
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Square_Thumbnails_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Square_Thumbnails_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/square-thumbnails-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Square_Thumbnails_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Square_Thumbnails_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/square-thumbnails-admin.js', array( 'jquery' ), $this->version, false );

	}

}
