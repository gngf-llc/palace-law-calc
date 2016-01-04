<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Palace_Law_Calc {

	/**
	 * The single instance of Palace_Law_Calc.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'palace_law_calc';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

        // Register shortcodes
        add_shortcode('palace-law-calc', array($this, 'setup_steps_wizard'));

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Load API for generic admin functions
		if ( is_admin() ) {
			$this->admin = new Palace_Law_Calc_Admin_API();
		}

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()

	/**
	 * Wrapper function to register a new post type
	 * @param  string $post_type   Post type name
	 * @param  string $plural      Post type item plural name
	 * @param  string $single      Post type item single name
	 * @param  string $description Description of post type
	 * @return object              Post type class object
	 */
	public function register_post_type ( $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {

		if ( ! $post_type || ! $plural || ! $single ) return;

		$post_type = new Palace_Law_Calc_Post_Type( $post_type, $plural, $single, $description, $options );

		return $post_type;
	}

	/**
	 * Wrapper function to register a new taxonomy
	 * @param  string $taxonomy   Taxonomy name
	 * @param  string $plural     Taxonomy single name
	 * @param  string $single     Taxonomy plural name
	 * @param  array  $post_types Post types to which this taxonomy applies
	 * @return object             Taxonomy class object
	 */
	public function register_taxonomy ( $taxonomy = '', $plural = '', $single = '', $post_types = array(), $taxonomy_args = array() ) {

		if ( ! $taxonomy || ! $plural || ! $single ) return;

		$taxonomy = new Palace_Law_Calc_Taxonomy( $taxonomy, $plural, $single, $post_types, $taxonomy_args );

		return $taxonomy;
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'palace-law-calc', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'palace-law-calc';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main Palace_Law_Calc Instance
	 *
	 * Ensures only one instance of Palace_Law_Calc is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Palace_Law_Calc()
	 * @return Main Palace_Law_Calc instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

	/**
	 * Log errors
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */

    public static function log_error($error)
    {
        #file usually located at /wp-content/debug.log
        #make sure the following are uncommented in wp-config.php
        #define('WP_DEBUG', true);
        #define('WP_DEBUG_LOG', true);

       error_log("==PALACE-LAW-CALC-ERROR==: ".$error);
    }

    /**
	 * Load the template css file
	 * @access  public
	 * @since   1.0.0
	 * @return  css
	 */
    public function load_template_css()
    {
        echo '<style type="text/css">'."\n";
            include(dirname(__FILE__).'/templates/plc_style.css');
        echo '</style>'."\n";
    }

    /**
	 * Load the template js file
	 * @access  public
	 * @since   1.0.0
	 * @return  css
	 */
    public function load_template_js()
    {
        echo '<script type="text/javascript">'."\n";
            include(dirname(__FILE__).'/templates/plc_script.js');
        echo '</script>'."\n";
        echo '<noscript><p>Please enable javascript</p></noscript>';
    }

    /**
	 * Load the calculator page template html
	 * @access  public
	 * @since   1.0.0
	 * @return  html
	 */
    public function load_calc_template($params=Array())
    {
        include(dirname(__FILE__).'/templates/plc_calc.php');
    }

    /**
	 * Load the results page template html
	 * @access  public
	 * @since   1.0.0
	 * @return  html
	 */
    public function load_results_template($params=Array())
    {
        include(dirname(__FILE__).'/templates/plc_results.php');
    }

    /**
	 * Load the thankyou page template html
	 * @access  public
	 * @since   1.0.0
	 * @return  html
	 */
    public function load_thankyou_template()
    {
        include(dirname(__FILE__).'/templates/plc_thankyou.php');
    }

    /**
	 * Setup the contact form html and submit logic
	 * @access  public
	 * @since   1.0.0
	 * @return  html
	 */
    public function setup_steps_wizard()
    {
        ob_start();
            $this->load_template_css();
            $this->load_template_js();

            if(isset($_POST['plc_submit']) && $_POST['plc_current_step'] == 'calc')
            {
                $params = $this->results_page_submit_logic();
                $this->load_results_template($params);
            }
            elseif(isset($_POST['plc_submit']) && $_POST['plc_current_step'] == 'results')
            {
                //$this->thankyou_page_submit_logic();
                $this->load_thankyou_template();
            }
            else
            {
                $params = $this->calc_page_parameters();
                $this->load_calc_template($params);
            }
        return ob_get_clean();
    }

    /**
	 * Provide logic and variables for the calc template page
	 * @access  public
	 * @since   1.0.0
	 * @return  Array('months' => Array, 'years' => Array, 'injuries' => Array, 'ratings' => Array)
	 */
    public function calc_page_parameters()
    {
        #array of months
        for($i=1; $i<=12; $i++)
        {
            $month_num = str_pad( $i, 2, 0, STR_PAD_LEFT );
            $month_name = date('F', mktime(0,0,0,$i, 1, date('Y')));
            $months[$month_num] = $month_name;
        }
        $params['months'] = $months;

        #array of years
        for($i=2000; $i<=date("Y"); $i++)
            $years[] = $i;
        $params['years'] = $years;

        #array of injuries
        $params['injuries'] = Array('leg','arm','eye');

        #array of ratings
        $params['ratings'] = Array(1,2,3,4,5,6);

        return $params;
    }

    /**
	 * Process the form data and prepare for display on the results page
	 * @access  public
	 * @since   1.0.0
	 * @return  html
	 */
    public function results_page_submit_logic()
    {
        if(!isset($_POST['plc_submit']))
            return false;

echo "<pre>".print_r($_POST,true)."</pre>";
        $params['value'] = "7,000";

        return $params;
        //if(isset($_POST['lf_submit']))
        //{
            ////sanitize form values
            //$lf_first_name = sanitize_text_field( $_POST["lf_first_name"] );
            //$lf_last_name = sanitize_text_field( $_POST["lf_last_name"] );
            //$lf_email = sanitize_email( $_POST["lf_email"] );
            //$lf_phone = sanitize_text_field( $_POST["lf_phone"] );
            //$lf_message = esc_textarea( stripslashes( $_POST["lf_message"] ));
            //$lf_honeypot = sanitize_text_field( $_POST["leave_this_blank_url"] );
            //$lf_honeypot_time = sanitize_text_field( $_POST["leave_this_alone"] );

            ////manual validation
            //if(empty($lf_first_name))
                //$errors['first_name'] = "<li>First Name is invalid</li>";
            //if(empty($lf_last_name))
                //$errors['first_name'] = "<li>Last Name is invalid</li>";
            //if(empty($lf_email))
                //$errors['email'] = "<li>Email is invalid</li>";
            //if(empty($lf_message))
                //$errors['message'] = "<li>Message is invalid</li>";
            //if(!empty($lf_phone) && strlen(preg_replace('/\D/','',$lf_phone)) == 0) #allow blank but not garbage
                //$errors['phone'] = "<li>Phone is invalid</li>";

            //if(!empty($errors))
            //{
                //$html = '<ul class="lf_errors">';
                //foreach($errors as $key => $value)
                   //$html .= $value;
                //$html .= '</ul>';

                //echo $html;
                //return false;
            //}
            //elseif($this->check_honeypot(compact('lf_honeypot','lf_honeypot_time')))
            //{
                //$this->log_error("Bot Detected; submission denied; lead dump: ".print_r(compact('lf_first_name','lf_last_name','lf_email','lf_phone','lf_message','lf_referrer','lf_honeypot','lf_honeypot_time'),true));

                //#pretend it was successful
                //echo "<h3 class='lf_success'>invalid</h3>";
                //unset($_POST);
            //}
            //else
            //{
                //$lf_phone = preg_replace('/\D/','',$lf_phone);
                //$lf_referrer = $_SERVER['HTTP_REFERER'];
                //$lead = compact('lf_first_name','lf_last_name','lf_email','lf_phone','lf_message','lf_referrer');
                //if($this->submit_lead($lead))
                //{
                    //echo "<h3 class='lf_success'>".get_option('lf_successful_submit_message')."</h3>";
                    //unset($_POST);
                //}
                //else
                //{
                    //echo "<h3 class='lf_failure'>Unfortunately an error has occured. Please try again later.</h3>";
                //}
            //}
        //}
    }

}
