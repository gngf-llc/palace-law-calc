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
        register_deactivation_hook( $this->file, array( $this, 'uninstall' ) );

        // Register shortcodes
        add_shortcode('palace-law-calc', array($this, 'setup_steps_wizard'));

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

        // Specify Ajax actions
        add_action( 'wp_ajax_nopriv_get_injury_rating_options', array( $this, 'get_injury_rating_options') ); #non-logged in user
        add_action( 'wp_ajax_get_injury_rating_options', array( $this, 'get_injury_rating_options') ); #logged in user

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
        wp_enqueue_style( 'plc_style', plugins_url( '/templates/plc_style.css', __FILE__ ) );
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
		wp_enqueue_script( 'plc_script', plugins_url( '/templates/plc_script.js', __FILE__), array('jquery'), '1.0', true );
        // AJAX url
        wp_localize_script( 'plc_script', 'plc_ajax_url', array(
            'ajax_url' => admin_url( 'admin-ajax.php' )
        ));

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
        $this->load_sql_table();
	} // End install ()

    /**
	 * Uninstalll. Runs on deactivation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function uninstall () {
        $this->remove_sql_table();
	} // End uninstall ()

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
	 * Load injury SQL data
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
    public function load_sql_table()
    {
        global $wpdb;

        $table = $wpdb->prefix."plc_calc_data";
        $query = "CREATE TABLE $table (
            begin_date DATE,
            end_date DATE,
            body_part VARCHAR(100),
            rating_type VARCHAR(8),
            category TINYINT(1),
            percent TINYINT(3),
            amount DECIMAL(10,2)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        $wpdb->query($query);

        //load csv file
        $lines = file(plugins_url('/injury_data.tsv', __FILE__ ));
        $columns = array_shift($lines);
        $columns = explode("\t",rtrim($columns));
        foreach($lines as $line_num => $line)
        {
            $values = explode("\t",rtrim($line));
            $data = array_combine($columns,$values);
            $wpdb->insert($table, $data);
        }
    }

    /**
	 * Remove injury SQL data
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
    public function remove_sql_table()
    {
        global $wpdb;
        $query = "DROP TABLE IF EXISTS ".$wpdb->prefix."plc_calc_data";
        $wpdb->query($query);
    }

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
            if(isset($_POST['plc_submit']) && $_POST['plc_current_step'] == 'calc')
            {
                $params = $this->calc_page_submit_logic();
                $this->load_results_template($params);
            }
            elseif(isset($_POST['plc_submit']) && $_POST['plc_current_step'] == 'results')
            {
                $params = $this->results_page_submit_logic();
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
        global $wpdb;
        $table = $wpdb->prefix."plc_calc_data";

        #array of months
        for($i=1; $i<=12; $i++)
        {
            $month_num = str_pad( $i, 2, 0, STR_PAD_LEFT );
            $month_name = date('F', mktime(0,0,0,$i, 1, date('Y')));
            $months[$month_num] = $month_name;
        }
        $params['months'] = $months;

        #array of years
        for($i=2010; $i<=date("Y"); $i++)
            $years[] = $i;
        $params['years'] = $years;

        #array of injuries
        $query = "SELECT DISTINCT body_part FROM $table";
        $injuries_list = $wpdb->get_results($query, ARRAY_A);

        foreach($injuries_list as $list)
            $injury_options[] = "<option>{$list['body_part']}</option>\n";
        $params['injuries'] = $injury_options;

        #array of ratings
        $params['ratings'] = range(1,6);

        return $params;
    }

    /**
	 * Process the form data from the calc page and prepare for display on the results page
	 * @access  public
	 * @since   1.0.0
	 * @return  html & params
	 */
    public function calc_page_submit_logic()
    {
        if(!isset($_POST['plc_submit']))
            return false;

        global $wpdb;
        $table = $wpdb->prefix."plc_calc_data";

        #santize input
        $injuries_array = isset( $_POST['plc_injuries'] ) ? (array) $_POST['plc_injuries'] : array();
        $injuries_array = array_map( 'esc_attr', $injuries_array );
        $ratings_array = isset( $_POST['plc_ratings'] ) ? (array) $_POST['plc_ratings'] : array();
        $ratings_array = array_map( 'esc_attr', $ratings_array );
        $month = sanitize_text_field($_POST['plc_month']);
        $year = sanitize_text_field($_POST['plc_year']);

        $total_amount = 0;
        foreach($injuries_array as $i => $injury)
        {
            $rating_string = explode('-',$ratings_array[$i]); #ie. category-3; percent-15; fixed
            $type = $rating_string[0];

            if($type != 'fixed')
                $val = $rating_string[1];

            if($type == 'category')
                $query = $wpdb->prepare("SELECT amount FROM $table WHERE body_part = %s AND rating_type = %s AND category = %s AND %s BETWEEN begin_date AND end_date",$injury,$type,$val,"$year-$month-01");
            elseif($type == 'percent')
                $query = $wpdb->prepare("SELECT amount FROM $table WHERE body_part = %s AND rating_type = %s AND percent = %s AND %s BETWEEN begin_date AND end_date",$injury,$type,$val,"$year-$month-01");
            elseif($type == 'fixed')
                $query = $wpdb->prepare("SELECT amount FROM $table WHERE body_part = %s AND rating_type = %s AND %s BETWEEN begin_date AND end_date",$injury,$type,"$year-$month-01");

            $results = $wpdb->get_row($query, ARRAY_A);
            $total_amount += $results['amount'];
        }

        $params['value'] = number_format($total_amount,2,'.',',');

        return $params;
    }

    /**
	 * Process the form data from the results page and prepare for display on the thank you page
	 * @access  public
	 * @since   1.0.0
	 * @return  html
	 */
    public function results_page_submit_logic()
    {
        if(isset($_POST['plc_submit']))
        {
            //sanitize form values
            $plc_first_name = sanitize_text_field( $_POST['plc_first_name'] );
            $plc_last_name = sanitize_text_field( $_POST['plc_last_name'] );
            $plc_email = sanitize_email( $_POST['plc_email'] );
            $plc_phone = sanitize_text_field( $_POST['plc_phone'] );
            $plc_message = esc_textarea( stripslashes( $_POST['plc_message'] ));

            $plc_phone = preg_replace('/\D/','',$plc_phone);
            $plc_referrer = $_SERVER['HTTP_REFERER'];
            $lead = compact('plc_first_name','plc_last_name','plc_email','plc_phone','plc_message','plc_referrer');
            if($this->submit_lead($lead))
            {
                //echo "<h3 class='plc_success'>".get_option('plc_successful_submit_message')."</h3>";
                unset($_POST);
            }
            else
            {
                echo "<h3 class='plc_failure'>Unfortunately an error has occured. Please try again later.</h3>";
            }
        }
    }

    /**
	 * Used by Ajax call, it takes a body part and returns its rating and rating type
	 * @access  public
	 * @since   1.0.0
	 * @return html
	 */
    public function get_injury_rating_options()
    {
        global $wpdb;
        $table = $wpdb->prefix."plc_calc_data";
        $body_part = sanitize_text_field($_POST['body_part']);
        $month = sanitize_text_field($_POST['month']);
        $year = sanitize_text_field($_POST['year']);

        $query = $wpdb->prepare("SELECT rating_type, category, percent FROM $table WHERE body_part = %s AND %s BETWEEN begin_date AND end_date GROUP BY rating_type, category, percent",$body_part,"$year-$month-01");
        $results = $wpdb->get_results($query, ARRAY_A);

        $type = $results[0]['rating_type'];

        if($type == 'category'):
            ?>
            <label class="label_rating">Rating:</label>
            <select name="plc_ratings[]" class="rating_select" required>
                <option value="" disabled selected>Choose a Category</option>
                <?php foreach($results as $rating): ?>
                    <option value="<?php echo $type.'-'.$rating[$type]; ?>"><?php echo $rating[$type]; ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        elseif($type == 'percent'):
            ?>
            <label class="label_rating">%TBI:</label>
            <select name="plc_ratings[]" class="percentinjury_select" required>
                <option value="" disabled selected>Choose a %TBI</option>
                <?php foreach($results as $rating): ?>
                    <option value="<?php echo $type.'-'.$rating[$type]; ?>"><?php echo $rating[$type]; ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        elseif($type == 'fixed'):
            ?>
            <label class="label_rating">Rating:</label>
            <select name="plc_ratings[]" class="fixed_select" required>
                <?php foreach($results as $rating): ?>
                    <option value="<?php echo $type; ?>">Fixed</option>
                <?php endforeach; ?>
            </select>
            <?php
        endif;

        die(); #required
    }

    /**
	 * Submit lead to recipient
	 * @access  private
	 * @since   1.0.6
	 * @return  boolean
	 */
    private function submit_lead($lead)
    {
        $headers = "From: PL Calc <".get_option('plc_leads_email').">" . "\r\n";
        $message = "<pre>".print_r($lead,true)."</pre>";
        wp_mail( get_option('plc_leads_email'), 'PL Calc Lead', $message, $headers);

        return true;
    }
}
