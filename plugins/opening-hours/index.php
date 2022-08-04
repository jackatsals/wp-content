<?php

if (!defined('ABSPATH'))
{
	die();
}

class we_are_open
{
	public
		$days = array(),
		$regular = array(),
		$special = array(),
		$closure = array();

	private
		$dashboard = NULL,
		$alias = NULL,
		$prefix = NULL,
		$data = array(),
		$consolidation = array(),
		$google_data = array(),
		$google_result = array(),
		$google_result_valid = NULL,
		$settings_updated = FALSE,
		$request_count = NULL,
		$day_range_min = NULL,
		$day_formats = array(),
		$time_formats = array(),
		$offset = NULL,
		$offset_changes = NULL,
		$week_start = NULL,
		$current_timestamp = NULL,
		$today_timestamp = NULL,
		$today = NULL,
		$yesterday_timestamp = NULL,
		$yesterday = NULL,
		$tomorrow_timestamp = NULL,
		$tomorrow = NULL,
		$week_start_timestamp = NULL,
		$next_week_start_timestamp = NULL,
		$logo_image_id = NULL,
		$logo_image_url = NULL;
	
	protected
		$wp_date = FALSE,
		$date_i18n = FALSE;
		
	public function __construct()
	{
		// Class contructor that starts everything
		
		$this->dashboard = (is_admin() || defined('DOING_CRON'));
		$this->class_name = 'we_are_open';
		$this->alias = 'we_are_open';
		$this->prefix = $this->alias . '_';
		$this->settings_updated = FALSE;
		$this->request_count = 0;
		$this->google_result_valid = NULL;
		$this->day_range_min = 3;
		$this->days = array();
		$this->offset = round(floatval(get_option('gmt_offset')) * HOUR_IN_SECONDS);
		$this->offset_changes = NULL;
		$this->wp_date = function_exists('wp_date');
		$this->date_i18n = function_exists('date_i18n');
		$this->logo_image_id = NULL;
		$this->image_url = NULL;
		$this->accepted_day_format = '#^(?:[dDjlSwzFMmntYy ,.:;_()/-]+|[dDjlSwzFMmntYy ,.:;_()/-]+[^][S][dDjlSwzFMmntYy ,.:;_()/-]+)$#';
		$this->current_timestamp = time();
		$this->week_start = 0;
		$this->week_start_timestamp = NULL;
		$this->next_week_start_timestamp = NULL;
		$this->today_timestamp = $this->get_day_timestamp();
		$this->yesterday_timestamp = $this->get_day_timestamp(-1);
		$this->tomorrow_timestamp = $this->get_day_timestamp(1);
		
		if ($this->wp_date)
		{
			$this->today = wp_date("w", $this->today_timestamp);
			$this->yesterday = wp_date("w", $this->yesterday_timestamp);
			$this->tomorrow = wp_date("w", $this->tomorrow_timestamp);
			$this->week_start = (intval(get_option($this->prefix . 'week_start')) < 0) ? ((intval(get_option($this->prefix . 'week_start')) == -2) ? wp_date("w", $this->yesterday_timestamp) : wp_date("w", $this->today_timestamp)) : get_option($this->prefix . 'week_start');
			
			for ($i = 0; $i < 7; $i++)
			{
				$this->days[$i] = $this->sentence_case(wp_date("l", 1590883200 + $i * DAY_IN_SECONDS + ($this->offset * -1) + HOUR_IN_SECONDS));
				
				if ($this->week_start_timestamp == NULL && $this->week_start == wp_date("w", mktime(0, 0, 0, wp_date("m"), wp_date("j") + $i, wp_date("Y"))))
				{
					$this->week_start_timestamp = $this->get_day_timestamp((($i > 0) ? $i - 7 : 0));
					$this->next_week_start_timestamp = $this->get_day_timestamp((($i > 0) ? $i : 7));
				}
			}
		}
		else
		{
			$this->today = gmdate("w", $this->today_timestamp);
			$this->yesterday = gmdate("w", $this->yesterday_timestamp);
			$this->tomorrow = gmdate("w", $this->tomorrow_timestamp);
			$this->week_start = (intval(get_option($this->prefix . 'week_start')) < 0) ? ((intval(get_option($this->prefix . 'week_start')) == -2) ? gmdate("w", $this->yesterday_timestamp) : gmdate("w", $this->today_timestamp)) : get_option($this->prefix . 'week_start');
			
			for ($i = 0; $i < 7; $i++)
			{
				$this->days[$i] = $this->sentence_case(($this->date_i18n) ? date_i18n("l", 1590883200 + $i * DAY_IN_SECONDS) : gmdate("l", 1590883200 + $i * DAY_IN_SECONDS));
				
				if ($this->week_start_timestamp == NULL && $this->week_start == gmdate("w", mktime(0, 0, 0, gmdate("m"), gmdate("j") + $i, gmdate("Y"))))
				{
					$this->week_start_timestamp = $this->get_day_timestamp((($i > 0) ? $i - 7 : 0));
					$this->next_week_start_timestamp = $this->get_day_timestamp((($i > 0) ? $i : 7));
				}
			}
		}
		
		if (!is_numeric($this->week_start))
		{
			$this->week_start = 0;
		}

		$this->time_formats = array(
			'12_colon_gap' => array('9:30 am – 5:00 pm', 'g:i a', FALSE),
			'12_colon_gap_uc' => array('9:30 AM – 5:00 PM', 'g:i A', FALSE),
			'12_colon_gap_trim' => array('9:30 am – 5 pm', 'g:i a', TRUE),
			'12_colon_gap_uc_trim' => array('9:30 AM – 5 PM', 'g:i A', TRUE),
			'12_colon' => array('9:30am – 5:00pm', 'g:ia', FALSE),
			'12_colon_uc' => array('9:30AM – 5:00PM', 'g:iA', FALSE),
			'12_colon_trim' => array('9:30am – 5pm', 'g:ia', TRUE),
			'12_colon_uc_trim' => array('9:30AM – 5PM', 'g:iA', TRUE),
			'12_dot_gap' => array('9:30am – 5.00 pm', 'g.i a', FALSE),
			'12_dot_gap_uc' => array('9:30AM – 5.00 PM', 'g.i A', FALSE),
			'12_dot_gap_trim' => array('9:30 am – 5 pm', 'g.i a', TRUE),
			'12_dot_gap_uc_trim' => array('9:30 AM – 5 PM', 'g.i A', TRUE),
			'12_dot' => array('9:30am – 5.00pm', 'g.ia', FALSE),
			'12_dot_uc' => array('9:30AM – 5.00PM', 'g.iA', FALSE),
			'12_dot_trim' => array('9.30am – 5pm', 'g.ia', TRUE),
			'12_dot_uc_trim' => array('9.30AM – 5PM', 'g.iA', TRUE),
			'24_none' => array('0930 – 1700', 'Hi', FALSE),
			'24_colon' => array('09:30 – 17:00', 'H:i', FALSE),
			'24_dot_single_digit' => array('9:30 – 17:00', 'G:i', FALSE),
			'24_colon_trim' => array('09:30 – 17', 'H:i', TRUE),
			'24_colon_dash' => array('09:30 – 17:–', 'H:i', '–'),
			'24_colon_mdash' => array('09:30 – 17:—', 'H:i', '—'),
			'24_dot_single_digit_dash' => array('9:30 – 17:–', 'G:i', '–'),
			'24_dot_single_digit_mdash' => array('9:30 – 17:—', 'G:i', '—'),
			'24_dot' => array('09.30 – 17.00', 'H.i', FALSE),
			'24_dot_single_digit' => array('9.30 – 17.00', 'G.i', FALSE),
			'24_dot_trim' => array('09.30 – 17', 'H.i', FALSE),
			'24_dot_dash' => array('09.30 – 17.–', 'H.i', '–'),
			'24_dot_mdash' => array('09.30 – 17.—', 'H.i', '—'),
			'24_dot_single_digit_dash' => array('9.30 – 17.–', 'G.i', '–'),
			'24_dot_single_digit_mdash' => array('9.30 – 17.—', 'G.i', '—'),
			'24_h' => array('09h30 – 17h00', 'H\\hi', FALSE),
			'24_h_single_digit' => array('9h30 – 17h00', 'G\\hi', FALSE),
			'24_h_trim' => array('09h30 – 17h', 'H\\hi', FALSE),
			'24_h_dash' => array('09h30 – 17h–', 'H\\hi', '–'),
			'24_h_mdash' => array('09h30 – 17h—', 'H\\hi', '—'),
			'24_h_single_digit_dash' => array('9h30 – 17h–', 'G\\hi', '–'),
			'24_h_single_digit_mdash' => array('9h30 – 17h—', 'G\\hi', '—')
		);
		
		$this->consolidation_types = array(
			NULL => __('None', 'opening-hours'),
			'weekdays' => __('Weekdays only', 'opening-hours'),
			'weekend' => __('Weekend only', 'opening-hours'),
			'separate' => __('Weekdays and weekend, separately', 'opening-hours'),
			'all' => __('All days', 'opening-hours')
		);
		
		$this->admin_init();
		$this->wp_init();
		return TRUE;
	}
	
	public static function activate()
	{
		// Activate plugin
		
		if (!current_user_can('activate_plugins', __CLASS__))
		{
			return;
		}
		
		$language = 'en';
		$google_api_languages = array(
			'af',
			'am',
			'ar',
			'az',
			'be',
			'bg',
			'bn',
			'bs',
			'ca',
			'cs',
			'da',
			'de',
			'el',
			'en',
			'en-AU',
			'en-GB',
			'es',
			'es-419',
			'et',
			'eu',
			'fa',
			'fi',
			'fil',
			'fr',
			'fr-CA',
			'gl',
			'gu',
			'hi',
			'hr',
			'hu',
			'hy',
			'id',
			'is',
			'it',
			'iw',
			'ja',
			'ka',
			'kk',
			'km',
			'kn',
			'ko',
			'ky',
			'lo',
			'lt',
			'lv',
			'mk',
			'ml',
			'mn',
			'mr',
			'ms',
			'my',
			'ne',
			'nl',
			'no',
			'pa',
			'pl',
			'pt',
			'pt-BR',
			'pt-PT',
			'ro',
			'ru',
			'si',
			'sk',
			'sl',
			'sq',
			'sr',
			'sv',
			'sw',
			'ta',
			'te',
			'th',
			'tr',
			'uk',
			'ur',
			'uz',
			'vi',
			'zh',
			'zh-CN',
			'zh-HK',
			'zh-TW',
			'zu'
		);

		if (is_string(get_option('WPLANG')))
		{
			if (preg_match('/^[^_]+$/', get_option('WPLANG')) && in_array(get_option('WPLANG'), $google_api_languages))
			{
				$language = get_option('WPLANG');
			}
			elseif (preg_match('/^([^_]+)_([^_]+)$/', get_option('WPLANG'), $m) && in_array($m[1] . '-' . $m[2], $google_api_languages))
			{
				$language = $m[1] . '-' . $m[2];
			}
		}
		
		if (!is_string(get_option(__CLASS__ . '_time_format')))
		{
			$regular = array(
				0 => array(
					'closed' => TRUE,
					'hours' => array(),
					'hours_24' => FALSE,
					'modified' => NULL
				),
				1 => array(
					'closed' => TRUE,
					'hours' => array(),
					'hours_24' => FALSE,
					'modified' => NULL
				),
				2 => array(
					'closed' => TRUE,
					'hours' => array(),
					'hours_24' => FALSE,
					'modified' => NULL
				),
				3 => array(
					'closed' => TRUE,
					'hours' => array(),
					'hours_24' => FALSE,
					'modified' => NULL
				),
				4 => array(
					'closed' => TRUE,
					'hours' => array(),
					'hours_24' => FALSE,
					'modified' => NULL
				),
				5 => array(
					'closed' => TRUE,
					'hours' => array(),
					'hours_24' => FALSE,
					'modified' => NULL
				),
				6 => array(
					'closed' => TRUE,
					'hours' => array(),
					'hours_24' => FALSE,
					'modified' => NULL
				)
			);
			$plugin_data = (function_exists('get_file_data')) ? get_file_data(plugin_dir_path(__FILE__) . 'opening-hours.php', array('Version' => 'Version'), FALSE) : array();
			$version = (array_key_exists('Version', $plugin_data)) ? $plugin_data['Version'] : NULL;
			$week_start = get_option('start_of_week');
	
			update_option(__CLASS__ . '_24_hours_text', __('Open 24 Hours', 'opening-hours'), 'yes');
			update_option(__CLASS__ . '_address', NULL, 'no');
			update_option(__CLASS__ . '_api_key', NULL, 'no');
			update_option(__CLASS__ . '_business_type', NULL, 'no');
			update_option(__CLASS__ . '_closed_show', TRUE, 'yes');
			update_option(__CLASS__ . '_closed_text', __('Closed', 'opening-hours'), 'yes');
			update_option(__CLASS__ . '_closure', NULL, 'no');
			update_option(__CLASS__ . '_consolidation', NULL, 'yes');
			update_option(__CLASS__ . '_custom_styles', NULL, 'yes');
			update_option(__CLASS__ . '_day_format', 'full', 'yes');
			update_option(__CLASS__ . '_day_format_special', NULL, 'yes');
			update_option(__CLASS__ . '_everyday_text', __('Everyday', 'opening-hours'), 'yes');
			update_option(__CLASS__ . '_google_result', NULL, 'no');
			update_option(__CLASS__ . '_javascript', 1, 'yes');
			update_option(__CLASS__ . '_initial_version', $version, 'no');
			update_option(__CLASS__ . '_language', $language, 'no');
			update_option(__CLASS__ . '_logo', NULL, 'no');
			update_option(__CLASS__ . '_name', NULL, 'no');
			update_option(__CLASS__ . '_place_id', NULL, 'no');
			update_option(__CLASS__ . '_price_range', NULL, 'no');
			update_option(__CLASS__ . '_regular', $regular, 'no');
			update_option(__CLASS__ . '_retrieval', NULL, 'no');
			update_option(__CLASS__ . '_section', NULL, 'no');
			update_option(__CLASS__ . '_special', NULL, 'no');
			update_option(__CLASS__ . '_special_cut_off', 14, 'yes');
			update_option(__CLASS__ . '_structured_data', FALSE, 'yes');
			update_option(__CLASS__ . '_stylesheet', 1, 'yes');
			update_option(__CLASS__ . '_telephone', NULL, 'no');
			update_option(__CLASS__ . '_time_format', NULL, 'yes');
			update_option(__CLASS__ . '_time_group_separator', ', ', 'yes');
			update_option(__CLASS__ . '_time_separator', ' – ', 'yes');
			update_option(__CLASS__ . '_time_type', NULL, 'yes');
			update_option(__CLASS__ . '_day_separator', ', ', 'yes');
			update_option(__CLASS__ . '_day_range_separator', ' – ', 'yes');
			update_option(__CLASS__ . '_day_range_suffix', ':', 'yes');
			update_option(__CLASS__ . '_day_range_suffix_special', ':', 'yes');
			update_option(__CLASS__ . '_week_start', $week_start, 'yes');
			update_option(__CLASS__ . '_weekdays', (($week_start == 0) ? array('_', 1, 2, 3, 4) : (($week_start == 6) ? array('_', 1, 2, 3, 6) : array(1, 2, 3, 4, 5))), 'yes');
			update_option(__CLASS__ . '_weekdays_text', __('Weekdays', 'opening-hours'), 'yes');
			update_option(__CLASS__ . '_weekend', (($week_start == 0) ? array(5, 6) : (($week_start == 6) ? array(4, 5) : array('_', 6))), 'yes');
			update_option(__CLASS__ . '_weekend_text', __('Weekend', 'opening-hours'), 'yes');
		}

		if ($language != 'en')
		{
			update_option(__CLASS__ . '_language', $language, 'no');
		}

		return TRUE;
	}
	
	public static function deactivate()
	{
		// Deactivate the plugin

		if (!current_user_can('activate_plugins', __CLASS__))
		{
			return;
		}
		
		wp_cache_delete('data', __CLASS__);
		wp_cache_delete('regular', __CLASS__);
		wp_cache_delete('special', __CLASS__);
		wp_cache_delete('closure', __CLASS__);
		wp_cache_delete('structured_data', __CLASS__);
		wp_cache_delete('google_result', __CLASS__);
		wp_cache_delete('consolidation', __CLASS__);
		delete_transient(__CLASS__ . '_offset_changes');
		update_option(__CLASS__ . '_google_result', NULL, 'no');
		
		return TRUE;
	}
	
	public static function uninstall($check = NULL)
	{
		// Uninstall plugin

		if (!current_user_can('activate_plugins', __CLASS__))
		{
			return;
		}

		if ($check != NULL && $check != md5(__FILE__ . ':' . __CLASS__))
		{
			die();
		}

		delete_option(__CLASS__ . '_24_hours_text');
		delete_option(__CLASS__ . '_address');
		delete_option(__CLASS__ . '_api_key');
		delete_option(__CLASS__ . '_business_type');
		delete_option(__CLASS__ . '_closed_show');
		delete_option(__CLASS__ . '_closed_text');
		delete_option(__CLASS__ . '_closure');
		delete_option(__CLASS__ . '_consolidation');
		delete_option(__CLASS__ . '_custom_styles');
		delete_option(__CLASS__ . '_day_format');
		delete_option(__CLASS__ . '_day_format_special');
		delete_option(__CLASS__ . '_day_range_separator');
		delete_option(__CLASS__ . '_day_range_suffix');
		delete_option(__CLASS__ . '_day_range_suffix_special');
		delete_option(__CLASS__ . '_day_separator');
		delete_option(__CLASS__ . '_everyday_text');
		delete_option(__CLASS__ . '_force');
		delete_option(__CLASS__ . '_google_result');
		delete_option(__CLASS__ . '_initial_version');
		delete_option(__CLASS__ . '_javascript');
		delete_option(__CLASS__ . '_language');
		delete_option(__CLASS__ . '_logo');
		delete_option(__CLASS__ . '_name');
		delete_option(__CLASS__ . '_place_id');
		delete_option(__CLASS__ . '_price_range');
		delete_option(__CLASS__ . '_regular');
		delete_option(__CLASS__ . '_result');
		delete_option(__CLASS__ . '_retrieval');
		delete_option(__CLASS__ . '_section');
		delete_option(__CLASS__ . '_special');
		delete_option(__CLASS__ . '_special_cut_off');
		delete_option(__CLASS__ . '_structured_data');
		delete_option(__CLASS__ . '_stylesheet');
		delete_option(__CLASS__ . '_telephone');
		delete_option(__CLASS__ . '_time_format');
		delete_option(__CLASS__ . '_time_group_separator');
		delete_option(__CLASS__ . '_time_separator');
		delete_option(__CLASS__ . '_time_type');
		delete_option(__CLASS__ . '_week_start');
		delete_option(__CLASS__ . '_weekdays');
		delete_option(__CLASS__ . '_weekdays_text');
		delete_option(__CLASS__ . '_weekend');
		delete_option(__CLASS__ . '_weekend_text');
		delete_option('widget_' . __CLASS__);

		return TRUE;
	}
	
	public static function upgrade($object, $options)
	{
		// Upgrade plugin
		
		if (!isset($options['action']) || isset($options['action']) && $options['action'] != 'update' || !isset($options['type']) || isset($options['type']) && $options['type'] != 'plugin' || !isset($options['plugins']) || isset($options['plugins']) && !is_array($options['plugins']))
		{
			return TRUE;
		}
		
		$plugin_directory_name = preg_replace('#^/?([^/]+)/.*$#', '$1', plugin_basename(__FILE__));
		
		foreach ($options['plugins'] as $path)
		{
			if (!preg_match('#^/?' . preg_quote($plugin_directory_name, '#'). '/.*$#', $path))
			{
				continue;
			}

			global $wpdb;

			wp_cache_delete('data', __CLASS__);
			wp_cache_delete('regular', __CLASS__);
			wp_cache_delete('special', __CLASS__);
			wp_cache_delete('closure', __CLASS__);
			wp_cache_delete('structured_data', __CLASS__);
			wp_cache_delete('google_result', __CLASS__);
			wp_cache_delete('consolidation', __CLASS__);
			
			$plugin_data = (function_exists('get_file_data')) ? get_file_data(plugin_dir_path(__FILE__) . 'opening-hours.php', array('Version' => 'Version'), FALSE) : array();
			$version = (array_key_exists('Version', $plugin_data)) ? $plugin_data['Version'] : 0;
			$initial_version = get_option(__CLASS__ . '_initial_version', 0);
			$custom_styles = get_option(__CLASS__ . '_custom_styles');

			if (!version_compare($initial_version, '1.35'))
			{
				update_option(__CLASS__ . '_javascript', 1, 'yes');
				update_option(__CLASS__ . '_stylesheet', get_option(__CLASS__ . '_stylesheet', TRUE) ? 1 : 0, 'yes');
			}
			
			if ($custom_styles == NULL)
			{
				return TRUE;
			}
			
			$fp = FALSE;
			$custom_styles_file = plugin_dir_path(__FILE__) . 'wp/css/custom.css';

			if (!is_file($custom_styles_file))
			{
				if (!is_writable(plugin_dir_path(__FILE__) . 'wp/css/'))
				{
					return TRUE;
				}
				
				$fp = fopen($custom_styles_file, 'w');
				
				if (!$fp || !is_file($custom_styles_file))
				{
					if ($fp)
					{
						fclose($fp);
					}
					
					return TRUE;
				}
			}
			
			if (!is_writable($custom_styles_file))
			{
				return TRUE;
			}
			
			if (!$fp)
			{
				$fp = fopen($custom_styles_file, 'w');
			}
				
			if (!$fp || !fwrite($fp, $custom_styles))
			{
				return TRUE;
			}
			
			fclose($fp);

			return TRUE;
		}
		
		return TRUE;
	}
	
	private function reset()
	{
		// Reset the plugin to a fresh installation
		
		$this->set(NULL, TRUE);
		
		if (!self::deactivate())
		{
			return FALSE;
		}
		
		$md5 = md5(__FILE__ . ':' . __CLASS__);

		if (!self::uninstall($md5))
		{
			return FALSE;
		}
		
		return self::activate();
	}

	public function admin_init()
	{
		// Initiate the plugin in the dashboard
		
		$this->settings_updated = ($this->dashboard && isset($_REQUEST['settings-updated']) && (is_bool($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] || is_string($_REQUEST['settings-updated']) && preg_match('/^(?:true|1)$/i', $_REQUEST['settings-updated'])));

		register_setting($this->prefix . 'settings', $this->prefix . 'day_format', array('type' => 'string'));
		register_setting($this->prefix . 'settings', $this->prefix . 'day_format_special', array('type' => 'string'));
		register_setting($this->prefix . 'settings', $this->prefix . 'time_format', array('type' => 'string'));
		register_setting($this->prefix . 'settings', $this->prefix . 'time_type', array('type' => 'integer'));
		register_setting($this->prefix . 'settings', $this->prefix . 'closed_show', array('type' => 'boolean'));
		register_setting($this->prefix . 'settings', $this->prefix . 'weekdays', array('type' => 'string', 'sanitize_callback' => array($this, 'sanitize_array')));
		register_setting($this->prefix . 'settings', $this->prefix . 'weekend', array('type' => 'string', 'sanitize_callback' => array($this, 'sanitize_array')));
		register_setting($this->prefix . 'settings', $this->prefix . 'consolidation', array('type' => 'string'));
		register_setting($this->prefix . 'settings', $this->prefix . 'week_start', array('type' => 'string'));
		register_setting($this->prefix . 'settings', $this->prefix . 'logo', array('type' => 'string'));
		register_setting($this->prefix . 'settings', $this->prefix . 'name', array('type' => 'string'));
		register_setting($this->prefix . 'settings', $this->prefix . 'address', array('type' => 'string'));
		register_setting($this->prefix . 'settings', $this->prefix . 'telephone', array('type' => 'string'));
		register_setting($this->prefix . 'settings', $this->prefix . 'business_type', array('type' => 'string'));
		register_setting($this->prefix . 'settings', $this->prefix . 'price_range', array('type' => 'string'));
		register_setting($this->prefix . 'settings', $this->prefix . 'structured_data', array('type' => 'number'));
		register_setting($this->prefix . 'settings', $this->prefix . 'stylesheet', array('type' => 'number'));
		register_setting($this->prefix . 'settings', $this->prefix . 'javascript', array('type' => 'number'));
		
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_enqueue_scripts', array($this, 'admin_css_load'));
		add_action('admin_enqueue_scripts', array($this, 'admin_js_load'));
		add_action('wp_ajax_' . $this->class_name . '_admin_ajax', array($this, 'admin_ajax'));
		add_action('admin_notices', array($this, 'admin_notices'));
		add_action('widgets_init', array($this, 'widget'));
		add_action('plugins_loaded', array($this, 'loaded'));		
		
		add_filter('upload_mimes', array($this->class_name, 'admin_uploads_file_types'), 10, 2);
		add_filter('plugin_action_links', array($this->class_name, 'admin_add_action_links'), 10, 5);
		add_filter('plugin_row_meta', array($this->class_name, 'admin_add_plugin_meta'), 10, 2);
		
		if (!$this->set())
		{
			return TRUE;
		}
		
		$this->set_logo();
		
		return TRUE;
	}
	
	public function wp_init()
	{
		// Initiate the plugin in the front-end

		$stylesheet = get_option(__CLASS__ . '_stylesheet', TRUE);
		$javascript = get_option(__CLASS__ . '_javascript', TRUE);

		add_shortcode('we_are_open', array($this, 'wp_display'));
		add_shortcode('opening_hours', array($this, 'wp_display'));
		add_shortcode('open', array($this, 'wp_display'));
		add_shortcode('opening_hours_text', array($this, 'wp_display'));
		add_shortcode('open_text', array($this, 'wp_display'));
		add_shortcode('open_now', array($this, 'wp_display_open_now'));
		add_shortcode('closed_now', array($this, 'wp_display_closed_now'));
		
		if (is_bool($stylesheet) && $stylesheet || is_numeric($stylesheet) && $stylesheet > 0 || is_string($stylesheet) && $stylesheet != NULL)
		{
			add_action('wp_enqueue_scripts', array($this, 'wp_css_load'));
		}
		
		if (is_bool($javascript) && $javascript || is_numeric($javascript) && $javascript > 0 || is_string($javascript) && $javascript != NULL)
		{
			add_action('wp_enqueue_scripts', array($this, 'wp_js_load'));
			add_action('wp_ajax_' . $this->class_name . '_wp_ajax', array($this, 'wp_ajax'));
			add_action('wp_ajax_nopriv_' . $this->class_name . '_wp_ajax', array($this, 'wp_ajax'));
		}

		if (get_option($this->prefix . 'structured_data'))
		{
			add_action('wp_head', array($this, 'structured_data'));
		}

		add_action('plugins_loaded', array($this, 'loaded'));		

		$this->weekdays = get_option($this->prefix . 'weekdays', array());
		$this->weekend = get_option($this->prefix . 'weekend', array());

		return TRUE;
	}
	
	public function admin_menu()
	{
		// Set the menu item
		
		if (!current_user_can('edit_published_posts', $this->class_name))
		{
			return;
		}
		
		$icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDE2IDE2Ij4KICA8cGF0aCBkPSJNNy44IDcuNzVjLTEuMzIgMC4wNS0zLjEzIDEuNzItMy4xMyAzLjMyIDAgMS44NSAxLjggMy4zMyAzLjEzIDMuMzMgMS44NSAwIDMuMzMtMS40NyAzLjMzLTMuMzJDMTEuMTMgOS4yMiA5LjY1IDcuNjkgNy44IDcuNzV6TTguODcgMTMuNTdjLTAuMDYgMC4wNi0xLjMzLTEuNzUtMS4zMy0xLjc1cy0wLjIyLTAuMjMtMC4yOC0wLjRjLTAuMDYtMC4xNy0wLjA2LTAuNTEtMC4wNi0wLjU2IDAtMC4wNiAwLjMzLTIuNzEgMC40NS0yLjcxIDAuMDYgMCAwLjUgMi44OCAwLjUgM0M4LjIxIDExLjI1IDguOTMgMTMuNTEgOC44NyAxMy41N3oiIGZpbGw9IiNhMGE1YWEiLz4KICA8cGF0aCBkPSJNMTQuNjIgNS45MWMtMC4wMi0wLjA1LTAuMDQtMC4wNi0wLjA1LTAuMDcgMC4wMSAwIDAuMDMgMCAwIDAgLTAuMDEgMC0wLjAyIDAtMC4wMiAwIC0wLjAyIDAtMC4wNSAwLTAuMSAwIC0wLjAyLTAuMDItMC4wNC0wLjAxLTAuMDQtMC4wMXMtMC41NyAwLjAxLTEuNDcgMC4wM2MtMC45LTEuMzMtNS4zNi01LjI5LTUuNTEtNS40NiAtMC40Ny0wLjU0LTEuNzMtMC40Ni0yLjA5IDAgLTAuMjggMC4zNS0yLjQ2IDUuMzYtMi44MiA2LjA1QzEuNjMgNi40NyAxLjA3IDYuNDkgMS4wNiA2LjVjLTAuMDcgMC4wNC0wLjA1IDAuMDMtMC4wNiAwLjA3TDAuOTggOC40M2MwIDAgMC4wMyA2LjgxIDAuMDQgNi44NEMxLjAzIDE1LjI5IDEuMTIgMTUuMzIgMS4xMiAxNS4zMmwwLjI1IDAuMDFjMC43NiAwLjA1IDIuODggMC4yOSA1LjY4IDAuNDQgMi43NCAwLjE1IDQuODkgMC4yIDYuODUgMC4yMSAwLjA1IDAgMC4xNCAwIDAuMjEgMCAwLjEzIDAgMC4zMi0wLjAxIDAuMzQtMC4wMiAwLjA1LTAuMDIgMC4wNS0wLjExIDAuMDUtMC4xMVMxNC42NSA2IDE0LjYyIDUuOTF6TTUuODMgMS41MkM1Ljg3IDEuNDUgNS45MyAxLjQgNiAxLjM3YzAuMDggMC4xNSAwLjIgMC4yOSAwLjM4IDAuMjkgMC4xOSAwIDAuMzItMC4xMiAwLjQyLTAuMjVDNi44MyAxLjQzIDYuODYgMS40NiA2Ljg4IDEuNDljMC4xNyAwLjIzIDQuMTcgMy4zNiA0Ljg4IDQuNCAtMi4yNiAwLjEtNS42MSAwLjM0LTguMDUgMC41MkM0LjQ3IDUuMDYgNS43NCAxLjY1IDUuODMgMS41MnpNMi44NCA4LjJDMi43OCA4LjIgMi43MyA4LjE4IDIuNjkgOC4xNiAyLjUxIDguMDYgMi4zOCA3Ljg2IDIuMzcgNy42Yy0wLjAxLTAuMTkgMC4wOS0wLjM1IDAuMjItMC40OCAwLjIxIDAuMiAwLjQ3IDAuMjcgMC43OSAwLjAzIDAuMDcgMC4xMiAwLjE2IDAuMzQgMC4xNSAwLjQ3QzMuNSA3Ljk3IDMuMDggOC4yMSAyLjg0IDguMnpNNy44NSAxNS4zYy0xLjg4LTAuMDEtNC4yLTIuMDMtNC4xMS00LjIzQzMuODIgOC45MyA1Ljg4IDYuODggNy44NSA2Ljc2YzIuMzQtMC4xNCA0LjI3IDEuOTIgNC4yNyA0LjI3QzEyLjExIDEzLjM4IDEwLjE5IDE1LjMxIDcuODUgMTUuM3pNMTIuOTEgNy43MUMxMi44NiA3LjcxIDEyLjgxIDcuNjkgMTIuNzYgNy42N2MtMC4xOC0wLjEtMC4zLTAuMy0wLjMyLTAuNTcgLTAuMDEtMC4xMSAwLjAzLTAuMjIgMC4wOC0wLjMxIDAuNTEgMC4yOCAwLjczLTAuMiAwLjY4LTAuMzIgMC4yOCAwLjEyIDAuNDEgMC4zOSAwLjQgMC42NEMxMy41OCA3LjQ4IDEzLjE1IDcuNzIgMTIuOTEgNy43MXoiIGZpbGw9IiNhMGE1YWEiLz4KPC9zdmc+';
			
		$pages = array(
			array('add_menu_page', __('We’re Open!', 'opening-hours'), __('We’re Open!', 'opening-hours'), 'edit_published_posts', 'opening_hours', array($this, 'admin'), $icon, 51),
		);
		
		if (current_user_can('manage_options', $this->class_name))
		{
			$pages[] = array('add_options_page', __('We’re Open!', 'opening-hours'), __('We’re Open!', 'opening-hours'), 'manage_options', 'opening_hours_settings', array($this, 'admin_settings'));
		}
		
		foreach ($pages as $i => $p)
		{
			if ($p[0] == 'add_menu_page' || $p[0] == 'add_options_page')
			{
				$function = $p[0];
			}
			else
			{
				$function = 'add_submenu_page';
			}

			array_shift($p);
			call_user_func_array($function, $p);
			continue;
		}
		
		return TRUE;
	}
	
	private function admin_current()
	{
		// Check if the plugin is showing in the Dashboard

		if (!current_user_can('edit_published_posts', $this->class_name))
		{
			return FALSE;
		}
				
		return (isset($_GET['page']) && preg_match('/^(?:we[\s_-]?a?re[\s_-]?open|opening[\s_-]?hours)[\s_-]?.*$/i', $_GET['page']));
	}
	
	private function google_data_exists($valid = FALSE, $reset = FALSE)
	{
		// Check there is any existing data
		
		if ($reset || !isset($this->google_result_valid) || isset($this->google_result_valid) && !is_bool($this->google_result_valid))
		{
			$this->google_result_valid = (!empty($this->google_data) && isset($this->google_data['status']) && preg_match('/^OK$/i', $this->google_data['status']) && isset($this->google_data['result']) && isset($this->google_data['result']['name']) && isset($this->google_data['result']['opening_hours']) && is_array($this->google_data['result']['opening_hours']));
		}

		if ($valid)
		{
			return $this->google_result_valid;
		}
		
		return ($this->google_result_valid || !empty($this->google_data) && isset($this->google_data['status']));
	}
	
	public function admin()
	{
		// Management page in the Dashboard
		
		if (!current_user_can('edit_published_posts', $this->class_name))
		{
			wp_die(__('You do not have sufficient permissions to access this page.', 'opening-hours'));
		}

		$this->set_localized_dates();
		
		if (!isset($this->regular) || !is_array($this->regular))
		{
			$this->regular = array();
		}
		
		if (!isset($this->special) || !is_array($this->special))
		{
			$this->special = array();
		}
		
		if (!isset($this->closure) || !is_array($this->closure))
		{
			$this->closure = array();
		}
				
		include(plugin_dir_path(__FILE__) . 'templates/index.php');
	}
	
	public function admin_settings()
	{
		// Set and process settings in the Dashboard
		
		if (!current_user_can('manage_options', $this->class_name))
		{
			wp_die(__('You do not have sufficient permissions to access this page.', 'opening-hours'));
		}
		
		$this->set_localized_dates();
		
		$this->business_types = array(
			'AnimalShelter' => __('Animal Shelter', 'opening-hours'),
			'ArchiveOrganization' => __('Archive Organization', 'opening-hours'),
			'AutomotiveBusiness' => __('Automotive Business', 'opening-hours'),
			'ChildCare' => __('Child Care', 'opening-hours'),
			'Dentist' => __('Dentist', 'opening-hours'),
			'DryCleaningOrLaundry' => __('Dry Cleaning or Laundry', 'opening-hours'),
			'EmergencyService' => __('Emergency Service', 'opening-hours'),
			'EmploymentAgency' => __('Employment Agency', 'opening-hours'),
			'EntertainmentBusiness' => __('Entertainment Business', 'opening-hours'),
			'FinancialService' => __('Financial Service', 'opening-hours'),
			'FoodEstablishment' => __('Food Establishment', 'opening-hours'),
			'GovernmentOffice' => __('Government Office', 'opening-hours'),
			'HealthAndBeautyBusiness' => __('Health and Beauty Business', 'opening-hours'),
			'HomeAndConstructionBusiness' => __('Home and Construction Business', 'opening-hours'),
			'InternetCafe' => __('Internet Café', 'opening-hours'),
			'LegalService' => __('Legal Service', 'opening-hours'),
			'Library' => __('Library', 'opening-hours'),
			'LodgingBusiness' => __('Lodging Business', 'opening-hours'),
			'MedicalBusiness' => __('Medical Business', 'opening-hours'),
			'ProfessionalService' => __('Professional Service', 'opening-hours'),
			'RadioStation' => __('Radio Station', 'opening-hours'),
			'RealEstateAgent' => __('Real Estate Agent', 'opening-hours'),
			'RecyclingCenter' => __('Recycling Center', 'opening-hours'),
			'SelfStorage' => __('Self Storage', 'opening-hours'),
			'ShoppingCenter' => __('Shopping Center', 'opening-hours'),
			'SportsActivityLocation' => __('Sports Activity Location', 'opening-hours'),
			'Store' => __('Store', 'opening-hours'),
			'TelevisionStation' => __('Television Station', 'opening-hours'),
			'TouristInformationCenter' => __('Tourist Information Center', 'opening-hours'),
			'TravelAgency' => __('Travel Agency', 'opening-hours')
		);
		$this->price_ranges = array(
			1 => array(
					'name' => __('Inexpensive $', 'opening-hours'),
					'symbol' => '$'
				),
			2 => array(
					'name' => __('Moderate $$', 'opening-hours'),
					'symbol' => str_repeat('$', 2)
				),
			3 => array(
					'name' => __('Expensive $$$', 'opening-hours'),
					'symbol' => str_repeat('$', 3)
				),
			4 => array(
					'name' => __('Very Expensive $$$$', 'opening-hours'),
					'symbol' => str_repeat('$', 4)
				)
		);
		
		$this->section = get_option($this->prefix . 'section');

		include(plugin_dir_path(__FILE__) . 'templates/settings.php');
	}
	
	public function admin_notices()
	{
		// Handle Dashboard notices
		
		if (!current_user_can('edit_published_posts', $this->class_name) || !$this->admin_current())
		{
			return;
		}
		
		$html = '';
		
		if (is_string(get_option($this->prefix . 'api_key')) && is_string(get_option($this->prefix . 'place_id')))
		{
			$this->set();
			
			if (!current_user_can('manage_options', $this->class_name) || !isset($_GET['page']) || isset($_GET['page']) && !preg_match('/^opening[\s_-]?hours[\s_-]?settings$/i', $_GET['page']) || !isset($this->google_data['status']) || preg_match('/^OK$/i', $this->google_data['status']))
			{
				$html = '';
			}
			elseif (preg_match('/^REQUEST[\s_-]?DENIED$/i', $this->google_data['status']))
			{
				$html = '<div class="notice notice-error visible is-dismissible">
	<p>'
				/* translators: %s refers to useful URLs to resolve errors and should remain untouched */
				. sprintf(__('<strong>Google API Error:</strong> Your Google API Key is not valid for this request and permission is denied. Please check your Google <a href="%s" target="_blank">API Key</a>.', 'opening-hours'), 'https://developers.google.com/maps/documentation/javascript/get-api-key') . '</p>
</div>
';
			}
			elseif (preg_match('/^INVALID[\s_-]?REQUEST$/i', $this->google_data['status']))
			{
				$html = '<div class="notice notice-error visible is-dismissible">
	<p>'
				/* translators: %s refers to useful URLs to resolve errors and should remain untouched */
				. sprintf(__('<strong>Google API Error:</strong> Google has returned an invalid request error. Please check your <a href="%s" target="_blank">Place ID</a>.', 'opening-hours'), 'https://developers.google.com/places/place-id') . '</p>
</div>
';
			}
			elseif (preg_match('/^NOT[\s_-]?FOUND$/i', $this->google_data['status']))
			{
				$html = '<div class="notice notice-error visible is-dismissible">
	<p>'
				/* translators: %s refers to useful URLs to resolve errors and should remain untouched */
				. sprintf(__('<strong>Google API Error:</strong> Google has not found data for the current Place ID. Please ensure you search for a specific business location; not a region or coordinates using the <a href="%s" target="_blank">Place ID Finder</a>.', 'opening-hours'), 'https://developers.google.com/places/place-id') . '</p>
</div>
';
			}
			else
			{
				$html = '<div class="notice notice-error visible is-dismissible">
	<p>' . ((isset($this->google_data['error_message'])) ? preg_replace('/\s+rel="nofollow"/i', ' target="_blank"', '<strong>' . __('Google API Error:', 'opening-hours') . '</strong> ' . $this->google_data['error_message']) : __('<strong>Google API Error:</strong> Unknown error returned by the Google Places API.', 'opening-hours')) . '</p>
</div>
';
			}
		}
		
		if ($html == '')
		{
			return;
		}
		
		echo $html;
	}
	
	public function admin_ajax()
	{
		// Handle AJAX requests from Dashboard

		$post = stripslashes_deep($_POST);
		$ret = array();
		$id = (isset($post['id'])) ? intval($post['id']) : NULL;
		$type = (isset($post['type'])) ? preg_replace('/[^\w_]/', '', strtolower($post['type'])) : NULL;
		$section = (isset($post['section']) && !preg_match('/^setup$/i', $post['section'])) ? preg_replace('/[^\w_-]/', '', strtolower($post['section'])) : NULL;
		$regular = (isset($post['regular']) && is_array($post['regular'])) ? stripslashes_deep($post['regular']) : array();
		$special = (isset($post['special']) && is_array($post['special'])) ? stripslashes_deep($post['special']) : array();
		$closure = (isset($post['closure']) && is_array($post['closure'])) ? stripslashes_deep($post['closure']) : array();
		$api_key = (isset($post['api_key']) && is_string($post['api_key'])) ? stripslashes($post['api_key']) : NULL;
		$place_id = (isset($post['place_id']) && is_string($post['place_id'])) ? stripslashes($post['place_id']) : NULL;
		$custom_styles = (isset($post['custom_styles']) && strlen($post['custom_styles']) > 2 && !preg_match('/<\?(?:php|=)/i', $post['custom_styles'])) ? $post['custom_styles'] : NULL;
		
		switch($type)
		{
		case 'section':
			$this->section = $section;
			update_option($this->prefix . 'section', $this->section, 'no');
			$ret = array(
				'success' => TRUE
			);
			break;
		case 'update':
			$this->update($regular, $special, $closure);
			$ret = array(
				'google_result' => $this->google_data,
				'regular' => $this->regular,
				'special' => $this->special,
				'closure' => $this->closure,
				'message' => __('Successfully saved opening hours', 'opening-hours'),
				'date' => ($this->wp_date) ? wp_date("Y/m/d") : date("Y/m/d"),
				'success' => TRUE
			);
			break;
		case 'delete':
			$this->delete($special);
			
			if (count($special) <= count($this->special))
			{
				$ret = array(
					'special' => $this->special,
					'date' => ($this->wp_date) ? wp_date("Y/m/d") : date("Y/m/d"),
					'success' => TRUE
				);
				break;
			}
			
			$ret = array(
				'special' => $this->special,
				'message' => __('Successfully removed special opening hours', 'opening-hours'),
				'date' => ($this->wp_date) ? wp_date("Y/m/d") : date("Y/m/d"),
				'success' => TRUE
			);
			break;
		case 'google_business_credentials':
			$current_api_key = get_option($this->prefix . 'api_key');
			$current_place_id = get_option($this->prefix . 'place_id');
			
			$api_key = $this->set_api_key($api_key, $current_api_key);
			$place_id = $this->set_place_id($place_id, $current_place_id, $current_api_key);
			$set_data = array(
				'api_key' => $api_key,
				'place_id' => $place_id
			);
			
			$this->set($set_data);
			
			$business_name = ($this->google_data_exists(TRUE) && isset($this->google_data['result']['name'])) ? $this->google_data['result']['name'] : NULL;

			if (($current_api_key != NULL || $current_place_id != NULL) && $api_key == NULL && $place_id == NULL)
			{
				$ret = array(
					'message' => __('Successfully cleared Google My Business credentials', 'opening-hours'),
					'business_name' => $business_name,
					'google_data_exists' => $this->google_data_exists(),
					'success' => TRUE
				);
				break;
			}
			
			if ($api_key != NULL && $place_id == NULL)
			{
				$ret = array(
					'message' => __('Successfully set API Key for Google My Business', 'opening-hours'),
					'business_name' => $business_name,
					'google_data_exists' => $this->google_data_exists(),
					'success' => TRUE
				);
				break;
			}
			
			if ($api_key == NULL && $place_id != NULL)
			{
				$ret = array(
					'message' => __('Successfully set Place ID for Google My Business', 'opening-hours'),
					'business_name' => $business_name,
					'google_data_exists' => $this->google_data_exists(),
					'success' => TRUE
				);
				break;
			}

			$ret = array(
				'message' => __('Successfully set Google My Business credentials', 'opening-hours'),
				'business_name' => $business_name,
				'google_data_exists' => $this->google_data_exists(),
				'success' => TRUE
			);
			break;
		case 'google_data':
		case 'google_business':
			if (!$this->set_google_data())
			{
				if (!isset($this->google_data['result']['opening_hours']) || isset($this->google_data['result']['opening_hours']) && !is_array($this->google_data['result']['opening_hours']) || isset($this->google_data['result']['opening_hours']) && is_array($this->google_data['result']['opening_hours']) && empty($this->google_data['result']['opening_hours']))
				{
					$ret = array(
						'regular' => $this->regular,
						'message' => __('Failed to set data from Google My Business because opening hours do not exist for this place', 'opening-hours'),
						'success' => FALSE
					);
					break;
				}
				
				$ret = array(
					'regular' => $this->regular,
					'message' => __('Failed to set data from Google My Business', 'opening-hours'),
					'success' => FALSE
				);
				break;
			}

			$ret = array(
				'regular' => $this->regular,
				'message' => __('Successfully set data from Google My Business', 'opening-hours'),
				'success' => TRUE
			);
			break;
		case 'separators':
			$time_separator = (isset($post['time_separator']) && is_string($post['time_separator'])) ? $this->sanitize_separator($post['time_separator']) : NULL;
			$time_group_separator = (isset($post['time_group_separator']) && is_string($post['time_group_separator'])) ? $this->sanitize_separator($post['time_group_separator']) : NULL;
			$day_separator = (isset($post['day_separator']) && is_string($post['day_separator'])) ? $this->sanitize_separator($post['day_separator']) : NULL;
			$day_range_separator = (isset($post['day_range_separator']) && is_string($post['day_range_separator'])) ? $this->sanitize_separator($post['day_range_separator']) : NULL;
			$day_range_suffix = (isset($post['day_range_suffix']) && is_string($post['day_range_suffix'])) ? $this->sanitize_separator($post['day_range_suffix'], 'right') : NULL;
			$day_range_suffix_special = (isset($post['day_range_suffix_special']) && is_string($post['day_range_suffix_special'])) ? $this->sanitize_separator($post['day_range_suffix_special'], 'right') : NULL;
			$closed_text = (isset($post['closed_text']) && is_string($post['closed_text'])) ? $this->sanitize_separator($post['closed_text']) : NULL;
			$hours_24_text = (isset($post['hours_24_text']) && is_string($post['hours_24_text'])) ? $this->sanitize_separator($post['hours_24_text']) : NULL;
			$weekdays_text = (isset($post['weekdays_text']) && is_string($post['weekdays_text'])) ? $this->sanitize_separator($post['weekdays_text']) : NULL;
			$weekend_text = (isset($post['weekend_text']) && is_string($post['weekend_text'])) ? $this->sanitize_separator($post['weekend_text']) : NULL;
			$everyday_text = (isset($post['everyday_text']) && is_string($post['everyday_text'])) ? $this->sanitize_separator($post['everyday_text']) : NULL;
			
			if ($time_separator == NULL || $time_group_separator == NULL || $day_separator == NULL || $day_range_separator == NULL || $closed_text == NULL || $hours_24_text == NULL)
			{
				$ret = array(
					'message' => __('Failed to update separators and text — values must be set for each.', 'opening-hours'),
					'success' => FALSE
				);
				break;
			}
			
			update_option($this->prefix . 'time_separator', $time_separator, 'yes');
			update_option($this->prefix . 'time_group_separator', $time_group_separator, 'yes');
			update_option($this->prefix . 'day_separator', $day_separator, 'yes');
			update_option($this->prefix . 'day_range_separator', $day_range_separator, 'yes');
			update_option($this->prefix . 'day_range_suffix', $day_range_suffix, 'yes');
			update_option($this->prefix . 'day_range_suffix_special', $day_range_suffix_special, 'yes');
			update_option($this->prefix . 'closed_text', $closed_text, 'yes');
			update_option($this->prefix . '24_hours_text', $hours_24_text, 'yes');
			update_option($this->prefix . 'weekdays_text', $weekdays_text, 'yes');
			update_option($this->prefix . 'weekend_text', $weekend_text, 'yes');
			update_option($this->prefix . 'everyday_text', $everyday_text, 'yes');

			$ret = array(
				'message' => __('Settings Saved.', 'opening-hours'),
				'success' => TRUE
			);

			break;
		case 'logo-delete':
		case 'logo_delete':
		case 'logo-remove':
		case 'logo_remove':
			$this->delete_logo();
			
			$ret = array(
				'id' => NULL,
				'image' => NULL,
				'success' => TRUE
			);
			break;	
		case 'logo':
			if (!is_numeric($id))
			{
				$this->delete_logo();
				
				$ret = array(
					'id' => NULL,
					'image' => NULL,
					'success' => FALSE
				);
				break;	
			}
			
			$this->set_logo($id);
			
			if (!is_string($this->logo_image_url) || is_string($this->logo_image_url) && strlen($this->logo_image_url) < 5)
			{
				$this->delete_logo();
				
				$ret = array(
					'id' => NULL,
					'image' => NULL,
					'success' => FALSE
				);
				
				break;	
			}
			
			$ret = array(
				'id' => $this->logo_image_id,
				'image' => preg_replace('/\s+(?:width|height)="\d*"/i', '', wp_get_attachment_image($this->logo_image_id, 'large', FALSE, array('id' => 'logo-image-preview-image'))),
				'success' => TRUE
			);
			break;
		case 'structured-data':
		case 'structured_data':
			$data = array();
			
			if (preg_match('/.+\.(?:jpe?g|png|svg|gif)$/i', $this->logo_image_url))
			{
				$data['logo'] = $this->logo_image_url;
			}
			
			if (isset($post['name']) && strlen($post['name']) > 1)
			{
				$data['name'] = $post['name'];
			}
			
			if (isset($post['address']) && strlen($post['address']) > 1)
			{
				$data['address'] = $post['address'];
			}
			
			if (isset($post['telephone']) && preg_match('/^[\d _()\[\].+-]+$/', $post['telephone']))
			{
				$data['telephone'] = $post['telephone'];
			}
			
			if (isset($post['business_type']) && preg_match('/^[\w\s_-]{1,64}$/i', $post['business_type']))
			{
				$data['business_type'] = $post['business_type'];
			}
			
			if (isset($post['price_range']))
			{
				$data['price_range'] = (is_numeric($post['price_range'])) ? intval($post['price_range']) : NULL;
			}
			
			$ret = array(
				'data' => $this->structured_data('json', $data),
				'success' => TRUE
			);
			break;
		case 'google_data_preview':
			$this->set();
			
			if (!$this->google_data_exists())
			{
				$ret = array(
					'data' => NULL,
					'success' => FALSE
				);
				break;
			}
			
			$ret = array(
				'data' => $this->get_google_data('json'),
				'success' => TRUE
			);
			break;
		case 'custom-styles':
		case 'custom_styles':
			if ($custom_styles == get_option($this->prefix . 'custom_styles'))
			{
				$ret = array(
					'success' => TRUE
				);
			}
			
			update_option($this->prefix . 'custom_styles', $custom_styles, 'yes');

			$fp = FALSE;
			$file = plugin_dir_path(__FILE__) . 'wp/css/custom.css';

			if (!is_file($file))
			{
				if (!is_writable(plugin_dir_path(__FILE__) . 'wp/css/'))
				{
					$ret = array(
						/* translators: %s: file directory, this should remain untouched */
						'message' => sprintf(__('Cannot create a new file in plugin directory: %s', 'opening-hours'), './wp/css/'),
						'success' => FALSE
					);
					break;
				}
				
				$fp = fopen($file, 'w');
				
				if (!$fp || !is_file($file))
				{
					if ($fp)
					{
						fclose($fp);
					}
					
					$ret = array(
						/* translators: %s: file name, this should remain untouched */
						'message' => sprintf(__('Cannot create a new file: %s', 'opening-hours'), './wp/css/custom.css'),
						'success' => FALSE
					);
					break;
				}
			}
			
			if (!is_writable($file))
			{
				$ret = array(
					/* translators: %s: file name, this should remain untouched */
					'message' => sprintf(__('File at: %s is not writable.', 'opening-hours'), './wp/css/custom.css'),
					'success' => FALSE
				);
				break;
			}
			
			if (!$fp)
			{
				$fp = fopen($file, 'w');
			}
				
			if (!$fp)
			{
				$ret = array(
					/* translators: %s: file name, this should remain untouched */
					'message' => sprintf(__('Cannot write new data to file at: %s', 'opening-hours'), './wp/css/custom.css'),
					'success' => FALSE
				);
				break;
			}
			
			if (!fwrite($fp, $custom_styles) && $custom_styles != NULL)
			{
				fclose($fp);
				$ret = array(
					'success' => FALSE
				);
				break;
			}
			
			fclose($fp);
			
			$ret = array(
				'success' => TRUE
			);
			break;
		case 'clear':
		case 'cache':
		case 'clear-cache':
		case 'clear_cache':
			wp_cache_delete('structured_data', $this->class_name);
			wp_cache_delete('google_result', $this->class_name);
			delete_transient($this->prefix . 'offset_changes');
			update_option($this->prefix . 'google_result', NULL, 'no');

			$this->google_data = array();
			$this->google_result = array();

			if (!$this->set(NULL, TRUE))
			{
				$ret = array(
					'success' => FALSE
				);
				break;
			}
			
			$this->section = NULL;
			update_option($this->prefix . 'section', $this->section, 'no');

			$ret = array(
				'success' => TRUE
			);
			break;
		case 'reset':
			$ret = array(
				'success' => $this->reset()
			);
			break;
		default:
			break;
		}

		echo json_encode($ret);
		wp_die();

		return;
	}

	public static function admin_uploads_file_types($types)
	{
		// Add SVG to acceptable file uploads
		
		if (!array_key_exists('svg', $types))
		{
			$types['svg'] = 'image/svg+xml';
		}

		if (!array_key_exists('svgz', $types))
		{
			$types['svgz'] = 'image/svg+xml';
		}

		return $types;
	}
	
	public static function admin_add_action_links($links, $file)
	{
		// Add action link in Dashboard Plugin list
		
		if (!preg_match('#^([^/]+).*$#', $file, $m1) || !preg_match('#^([^/]+).*$#', plugin_basename(__FILE__), $m2) || $m1[1] != $m2[1])
		{
			return $links;
		}
		
		$new_links = array('settings' => '<a href="' . admin_url('options-general.php?page=opening_hours_settings') . '">' . esc_html(__('Settings', 'opening-hours')) . '</a>');
		$links = array_merge($new_links, $links);

		return $links;
	}
	
	public static function admin_add_plugin_meta($links, $file)
	{
		// Add support link in Dashboard Plugin list
		
		if (!preg_match('#^([^/]+).*$#', $file, $m1) || !preg_match('#^([^/]+).*$#', plugin_basename(__FILE__), $m2) || $m1[1] != $m2[1])
		{
			return $links;
		}
		
		$new_links = array(
			'reviews' => '<a href="https://wordpress.org/support/plugin/opening-hours/reviews/#new-post" title="' . esc_attr__('Like our plugin? Please leave a review!', 'opening-hours') . '" style="color: #ffb900; line-height: 90%; font-size: 1.3em; letter-spacing: -0.12em; position: relative; top: 0.08em;">★★★★★</a>',
			'support' => '<a href="https://designextreme.com/wordpress/we-are-open/" target="_blank" title="' . esc_attr__('Support', 'opening-hours') . '">' . esc_html__('Support', 'opening-hours') . '</a>'
		);
		$links = array_merge($links, $new_links);
				
		return $links;
	}

	public function admin_css_load()
	{
		// Load style sheet in the Dashboard
		
		if (!current_user_can('edit_published_posts', $this->class_name))
		{
			return;
		}

		wp_register_style('open_admin_css', plugins_url('opening-hours/admin/css/css.css'));
		wp_enqueue_style('open_admin_css');
		
		if (!$this->admin_current())
		{
			return;
		}
		
		wp_register_style('open_wp_css', plugins_url('opening-hours/wp/css/css.css'));
		wp_enqueue_style('open_wp_css');
		wp_enqueue_media();
	}
	
	public function admin_js_load()
	{
		// Load Javascript in the Dashboard
		
		if (!$this->admin_current() || !current_user_can('edit_published_posts', $this->class_name))
		{
			return;
		}

		wp_register_script('open_admin_js', plugins_url('opening-hours/admin/js/js.js'));
		wp_localize_script('open_admin_js', 'we_are_open_admin_ajax', array('url' => admin_url('admin-ajax.php'), 'action' => 'we_are_open_admin_ajax'));
		wp_register_script('open_wp_js', plugins_url('opening-hours/wp/js/js.js'), array('jquery'));
		wp_enqueue_script('open_admin_js');
		wp_enqueue_script('open_wp_js');
	}
	
	public function wp_css_load()
	{
		// Load style sheet in the front-end
		
		$mode = get_option(__CLASS__ . '_stylesheet', TRUE);
		$compressed = (is_numeric($mode) && $mode == 2 || is_string($mode) && ($mode == 'compress' || $mode == 'compressed' || $mode == 'min'));
		
		wp_register_style('open_wp_css', ($compressed && is_file(plugins_url('opening-hours/wp/css/css.min.css'))) ? plugins_url('opening-hours/wp/css/css.min.css') : plugins_url('opening-hours/wp/css/css.css'));
		wp_enqueue_style('open_wp_css');
		
		if (is_file(plugin_dir_path(__FILE__) . 'wp/css/custom.css') && filesize(plugin_dir_path(__FILE__) . 'wp/css/custom.css') > 20)
		{
			wp_register_style('open_wp_custom_css', plugins_url('opening-hours/wp/css/custom.css'));
			wp_enqueue_style('open_wp_custom_css');
		}
	}
	
	public function wp_js_load()
	{
		// Load Javascript in the front-end
		
		$mode = get_option(__CLASS__ . '_javascript', TRUE);
		$compressed = (is_numeric($mode) && $mode == 2 || is_string($mode) && ($mode == 'compress' || $mode == 'compressed' || $mode == 'min'));

		wp_register_script('open_wp_js', ($compressed && is_file(plugins_url('opening-hours/wp/js/js.min.js'))) ? plugins_url('opening-hours/wp/js/js.min.js') : plugins_url('opening-hours/wp/js/js.js'), array('jquery'));
		wp_localize_script('open_wp_js', 'we_are_open_wp_ajax', array('url' => admin_url('admin-ajax.php'), 'action' => 'we_are_open_wp_ajax'));
		wp_enqueue_script('open_wp_js');
	}
	
	public function get_day_timestamp($day_offset = NULL, $month_offset = NULL, $year_offset = NULL)
	{
		// Get the timestamp from the start of a local day relative to today
		
		if (!is_numeric($day_offset))
		{
			$day_offset = 0;
		}
		
		if (!is_numeric($month_offset))
		{
			$month_offset = 0;
		}
		
		if (!is_numeric($year_offset))
		{
			$year_offset = 0;
		}
		
		if (!is_numeric($this->current_timestamp))
		{
			$this->current_timestamp = time();
		}

		if (!$this->wp_date)
		{
			return mktime(0, 0, 0, gmdate("m", $this->current_timestamp) + $month_offset, gmdate("j", $this->current_timestamp) + $day_offset, gmdate("Y", $this->current_timestamp) + $year_offset);
		}

		if (!is_numeric($this->offset))
		{
			$this->offset = round(floatval(get_option('gmt_offset')) * HOUR_IN_SECONDS);
		}
				
		if (!is_array($this->offset_changes))
		{
			$this->offset_changes = get_transient($this->prefix . 'offset_changes');
		}
		
		if (!is_array($this->offset_changes))
		{
			$timezone = FALSE;
			
			if (class_exists('DateTimeZone') && get_option('timezone_string') != NULL)
			{
				$timezone = new DateTimeZone(get_option('timezone_string'));
			}
			
			if (is_object($timezone))
			{
				$this->offset_changes = $timezone->getTransitions(mktime(0, 0, 0, gmdate("m")-6, 1, gmdate("Y")), mktime(0, 0, 0, 12, 31, gmdate("Y")+2));
				set_transient($this->prefix . 'offset_changes', $this->offset_changes, MONTH_IN_SECONDS);
			}
		}
		
		$offset = $this->offset;
		$timestamp = mktime(0, 0, $this->offset * -1, wp_date("m", $this->current_timestamp) + $month_offset, wp_date("j", $this->current_timestamp) + $day_offset, wp_date("Y", $this->current_timestamp) + $year_offset);

		if (is_array($this->offset_changes))
		{
			foreach ($this->offset_changes as $i => $a)
			{
				if ($a['ts'] > $timestamp || $a['ts'] <= $timestamp && array_key_exists($i + 1, $this->offset_changes) && isset($this->offset_changes[$i + 1]['ts']) && $this->offset_changes[$i + 1]['ts'] < $timestamp)
				{
					continue;
				}
				
				$offset = $a['offset'];
				$timestamp = mktime(0, 0, $offset * -1, wp_date("m", $this->current_timestamp) + $month_offset, wp_date("j", $this->current_timestamp) + $day_offset, wp_date("Y", $this->current_timestamp) + $year_offset);
				break;
			}
		}
		
		return mktime(0, 0, -1 * $offset, wp_date("m", $timestamp), wp_date("j", $timestamp), wp_date("Y", $timestamp));
	}
	
	public function get_google_data($format = 'array', $force = FALSE)
	{
		// Return data from either Google Places or option value
		
		$ret = ($format == 'array') ? array() : '';
		
		if (!$this->dashboard)
		{
			return $ret;
		}
	
		$this->api_key = ($this->api_key != NULL) ? $this->api_key : get_option($this->prefix . 'api_key');
		$this->place_id = ($this->place_id != NULL) ? $this->place_id : get_option($this->prefix . 'place_id');
				
		return $this->retrieve_google_data($format, $force);
	}
	
	private function set_google_data()
	{
		// Set opening hours using data retrieved from Google My Business
		
		$this->set();
		
		if (!isset($this->google_data['result']['opening_hours']) || isset($this->google_data['result']['opening_hours']) && !is_array($this->google_data['result']['opening_hours']) || !isset($this->google_data['result']['opening_hours']['periods']) || isset($this->google_data['result']['opening_hours']['periods']) && !is_array($this->google_data['result']['opening_hours']['periods']))
		{
			return FALSE;
		}
		
		$regular = array();
		$open_always = (count($this->google_data['result']['opening_hours']['periods']) == 1 && isset($this->google_data['result']['opening_hours']['periods'][0]['periods']['open']) && !isset($this->google_data['result']['opening_hours']['periods'][0]['periods']['close']));

		if (!$open_always && !empty($this->google_data['result']['opening_hours']['periods']))
		{
			foreach ($this->google_data['result']['opening_hours']['periods'] as $a)
			{
				if (!array_key_exists('open', $a) || !array_key_exists('day', $a['open']))
				{
					continue;
				}
				
				$weekday = intval($a['open']['day']);
				
				if (!array_key_exists($weekday, $regular))
				{
					if (!array_key_exists('close', $a) || !array_key_exists('day', $a['close']) || !array_key_exists('time', $a['close']) || isset($a['close']['time']) && !preg_match('/^(\d{2})[^\d]*(\d{2})$/', $a['close']['time']))
					{
						$regular[$weekday] = array(
							'closed' => FALSE,
							'hours_24' => TRUE,
							'hours' => NULL
						);
						
						continue;
					}
					
					$regular[$weekday] = array(
						'closed' => FALSE,
						'hours_24' => FALSE,
						'hours' => array()
					);
				}
				
				$regular[$weekday]['hours'][] = array(((preg_match('/^(\d{2})[^\d]*(\d{2})$/', $a['open']['time'], $m)) ? $m[1] . ':'. $m[2] : NULL), ((preg_match('/^(\d{2})[^\d]*(\d{2})$/', $a['close']['time'], $m)) ? $m[1] . ':'. $m[2] : NULL));
			}
		}
		
		foreach (array_keys($this->days) as $weekday)
		{
			if (!array_key_exists($weekday, $regular))
			{
				$regular[$weekday] = array(
					'closed' => !$open_always,
					'hours_24' => $open_always,
					'hours' => NULL
				);
			}
		}
		
		ksort($regular);
		
		return $this->update($regular);
	}
	
	private function get_closure()
	{
		// Get relevant details of closure for Dashboard
		
		if (empty($this->closure) || !isset($this->closure['start']) || !isset($this->closure['end']) || isset($this->closure['start']) && !is_numeric($this->closure['start']) || isset($this->closure['end']) && !is_numeric($this->closure['end']))
		{
			return array(NULL, NULL, NULL, NULL, NULL, NULL);
		}
		
		$closure_date_start = ($this->wp_date) ? wp_date("Y-m-d", $this->closure['start_display']) : gmdate("Y-m-d", $this->closure['start_display']);
		$closure_date_end = ($this->wp_date) ? wp_date("Y-m-d", $this->closure['end_display']) : gmdate("Y-m-d", $this->closure['end_display']);
		$closure_count = (isset($this->closure['count']) && is_numeric($this->closure['count'])) ? $this->closure['count'] : NULL;
		$closure_modified = (isset($this->closure['modified']) && is_numeric($this->closure['modified'])) ? $this->closure['modified'] : NULL;
		
		return array($this->closure['start'], $this->closure['end'], $closure_date_start, $closure_date_end, $closure_count, $closure_modified);
	}

	private function update($regular = NULL, $special = NULL, $closure = NULL)
	{
		// Update opening hours from form data array
		
		$this->data = array();
		$this->consolidation = array();
		
		wp_cache_delete('data', $this->class_name);
		wp_cache_delete('special', $this->class_name);
		wp_cache_delete('closure', $this->class_name);
		wp_cache_delete('consolidation', $this->class_name);
		
		if (is_array($regular))
		{
			if (!is_array($this->regular))
			{
				$this->regular = array();
			}
			
			wp_cache_delete('regular', $this->class_name);
			
			foreach (array_keys($this->days) as $weekday)
			{
				$a = (array_key_exists($weekday, $regular)) ? $regular[$weekday] : array();
				$modified = (!empty($a) && array_key_exists($weekday, $this->regular) && array_key_exists('modified', $this->regular[$weekday])) ? $this->regular[$weekday]['modified'] : NULL;
				$checksum = ($modified != NULL) ? md5(serialize(array($this->regular[$weekday]['closed'], $this->regular[$weekday]['hours_24'], $this->regular[$weekday]['hours']))) : NULL;
				
				if (!array_key_exists($weekday, $regular) || array_key_exists($weekday, $regular)
					&& (is_bool($a['closed']) && $a['closed']
					|| is_string($a['closed']) && $a['closed'] == 'true'
					|| !isset($a['hours'])
					|| isset($a['hours']) && (empty($a['hours'])
					|| !isset($a['hours'][0][0])
					|| isset($a['hours'][0][0]) && !preg_match('/^\d{2}:\d{2}$/', $a['hours'][0][0])
					|| !isset($a['hours'][0][1])
					|| isset($a['hours'][0][1]) && !preg_match('/^\d{2}:\d{2}$/', $a['hours'][0][1])
					|| (isset($a['hours_24']) && (is_bool($a['hours_24']) && $a['hours_24'] || is_string($a['hours_24']) && $a['hours_24'] == 'true'))
					|| isset($a['hours'][0][0]) && isset($a['hours'][0][1]) && preg_match('/^00:00$/', $a['hours'][0][0]) && preg_match('/^(?:00:00|23:5[5-9])$/', $a['hours'][0][1]))))
				{
					$hours_24 = (isset($a['hours_24']) && (is_bool($a['hours_24']) && $a['hours_24'] || is_string($a['hours_24']) && $a['hours_24'] == 'true'));
					
					$this->regular[$weekday] = array(
						'closed' => !$hours_24,
						'hours' => array(),
						'hours_24' => $hours_24
					);
					$this->regular[$weekday]['modified'] = ($checksum == NULL || $checksum != md5(serialize(array($this->regular[$weekday]['closed'], $this->regular[$weekday]['hours_24'], $this->regular[$weekday]['hours'])))) ? time() : $modified;
					
					continue;
				}
				
				$this->regular[$weekday] = array(
					'closed' => FALSE,
					'hours' => $this->hours_filter($a['hours']),
					'hours_24' => FALSE
				);
				$this->regular[$weekday]['modified'] = ($checksum == NULL || $checksum != md5(serialize(array($this->regular[$weekday]['closed'], $this->regular[$weekday]['hours_24'], $this->regular[$weekday]['hours'])))) ? time() : $modified;
			}
			
			ksort($this->regular);
			update_option($this->prefix . 'regular', $this->regular, 'yes');
			wp_cache_add('regular', $this->regular, $this->class_name, HOUR_IN_SECONDS);
		}
		
		if (!is_array($closure) || is_array($closure) && count($closure) != 2)
		{
			if (is_array($closure))
			{
				$this->closure = array();
				update_option($this->prefix . 'closure', $this->closure, 'yes');
				wp_cache_add('closure', $this->closure, $this->class_name, HOUR_IN_SECONDS);
			}
		}
	
		if (!is_array($special) || is_array($special) && empty($special))
		{
			if (is_array($special))
			{
				$this->special = array();
				update_option($this->prefix . 'special', $this->special, 'yes');
				wp_cache_add('special', $this->special, $this->class_name, HOUR_IN_SECONDS);
			}
			
			$this->set(NULL);
		}
		else
		{
			if (!is_array($this->special))
			{
				$this->special = array();
			}
			
			$set_dates = array();
			$special_cut_off = (is_numeric(get_option($this->prefix . 'special_cut_off', NULL)) && get_option($this->prefix . 'special_cut_off') >= 1) ? get_option($this->prefix . 'special_cut_off') : 14;
			$current_date = $this->get_day_timestamp();
			$remove_date = $this->get_day_timestamp(-14);
			
			foreach ($special as $a)
			{
				if (!isset($a['date']) || isset($a['date']) && !preg_match('#^\d{4}[/-]\d{1,2}[/-]\d{1,2}$#', $a['date']))
				{
					continue;
				}
				
				$day_offset = round((strtotime($a['date']) - $this->offset - $this->today_timestamp) / DAY_IN_SECONDS);
				$timestamp = $this->get_day_timestamp($day_offset);
				
				if ($current_date > $timestamp)
				{
					if ($timestamp > $remove_date)
					{
						$set_dates[] = $timestamp;
					}
					
					continue;
				}
				
				$a['date'] = $timestamp;
				$modified = (array_key_exists($timestamp, $this->special) && array_key_exists('modified', $this->special[$timestamp])) ? $this->special[$timestamp]['modified'] : NULL;
				$checksum = ($modified != NULL) ? md5(serialize(array($this->special[$timestamp]['closed'], $this->special[$timestamp]['hours_24'], $this->special[$timestamp]['hours']))) : NULL;
				$set_dates[] = $timestamp;
	
				if (is_bool($a['closed']) && $a['closed']
					|| is_string($a['closed']) && $a['closed'] == 'true'
					|| !isset($a['hours'])
					|| isset($a['hours']) && (empty($a['hours'])
					|| !isset($a['hours'][0][0])
					|| isset($a['hours'][0][0]) && !preg_match('/^\d{2}:\d{2}$/', $a['hours'][0][0])
					|| !isset($a['hours'][0][1])
					|| isset($a['hours'][0][1]) && !preg_match('/^\d{2}:\d{2}$/', $a['hours'][0][1]))
					|| (isset($a['hours_24']) && (is_bool($a['hours_24']) && $a['hours_24'] || is_string($a['hours_24']) && $a['hours_24'] == 'true'))
					|| isset($a['hours'][0][0]) && isset($a['hours'][0][1]) && preg_match('/^00:00$/', $a['hours'][0][0]) && preg_match('/^(?:00:00|23:5[5-9])$/', $a['hours'][0][1]))
				{
					$hours_24 = (isset($a['hours_24']) && (is_bool($a['hours_24']) && $a['hours_24'] || is_string($a['hours_24']) && $a['hours_24'] == 'true'));
					$this->special[$timestamp] = array(
						'closed' => !$hours_24,
						'date' => $timestamp,
						'hours' => array(),
						'hours_24' => $hours_24
					);
					$this->special[$timestamp]['modified'] = ($modified == NULL || $checksum == NULL || $checksum != md5(serialize(array($this->special[$timestamp]['closed'], $this->special[$timestamp]['hours_24'], $this->special[$timestamp]['hours'])))) ? time() : $modified;
					
					continue;
				}
				
				$this->special[$timestamp] = array(
					'closed' => FALSE,
					'date' => $timestamp,
					'hours' => $this->hours_filter($a['hours']),
					'hours_24' => FALSE
				);
				$this->special[$timestamp]['modified'] = ($modified == NULL || $checksum == NULL || $checksum != md5(serialize(array($this->special[$timestamp]['closed'], $this->special[$timestamp]['hours_24'], $this->special[$timestamp]['hours'])))) ? time() : $modified;
								
			}
			
			foreach (array_keys($this->special) as $timestamp)
			{
				if (!in_array($timestamp, $set_dates))
				{
					unset($this->special[$timestamp]);
				}
			}
			
			ksort($this->special);
			update_option($this->prefix . 'special', $this->special, 'yes');
			wp_cache_add('special', $this->special, $this->class_name, HOUR_IN_SECONDS);
		}
		
		if (is_array($closure) && count($closure) == 2)
		{
			if (!is_string($closure[0]) || !is_string($closure[1]) || !preg_match('#^\d{4}[/-]\d{1,2}[/-]\d{1,2}$#', $closure[0]) || !preg_match('#^\d{4}[/-]\d{1,2}[/-]\d{1,2}$#', $closure[1]))
			{
				$this->closure = array();
			}
			else
			{
				$closure_timestrings = array(
					strtotime($closure[0]),
					strtotime($closure[1])
				);
				sort($closure_timestrings);
				$day_start_offset = round(($closure_timestrings[0] - $this->today_timestamp)/DAY_IN_SECONDS);
				$day_end_display_offset = round(($closure_timestrings[1] - $this->today_timestamp)/DAY_IN_SECONDS);
				$day_end_offset = $day_end_display_offset + 1;
				$closure_date_start = $closure_date_start_display = $this->get_day_timestamp($day_start_offset);
				$closure_date_end = $this->get_day_timestamp($day_end_offset);
				$closure_date_end_display = $this->get_day_timestamp($day_end_display_offset);
				$this->closure = array(
					'start' => $closure_date_start,
					'start_display' => $closure_date_start,
					'end' => $closure_date_end,
					'end_display' => $closure_date_end_display,
					'count' => round($closure_date_end/DAY_IN_SECONDS) - round($closure_date_start/DAY_IN_SECONDS),
					'modified' => (isset($this->closure['modified']) && $this->closure['modified'] != NULL && $this->closure['start'] == $closure_date_start && $this->closure['end'] == $closure_date_end) ? $this->closure['modified'] : time()
				);
			}
			
			update_option($this->prefix . 'closure', $this->closure, 'yes');
			wp_cache_add('closure', $this->closure, $this->class_name, HOUR_IN_SECONDS);
		}
		
		$this->set(NULL);

		return TRUE;
	}
	
	private function delete($special = NULL)
	{
		// Update (delete) special opening hours from form data array
		
		if ($special == NULL || is_array($special) && empty($special))
		{
			return TRUE;
		}
		
		return $this->update(NULL, $special);
		
	}
	
	public function retrieve_google_data($format = 'array', $force = FALSE)
	{
		// Collect data from Google Places as JSON string
		
		$ret = ($format == 'array') ? array() : '';
		
		if ($this->request_count > 2)
		{
			return $ret;
		}
		
		global $wpdb;
		
		$fields = array('opening_hours', 'name', 'url', 'business_status');
		$language = get_option($this->prefix . 'language');
		$recheck = FALSE;
		$retrieval = NULL;
		$last_retrieval = NULL;
		$data_array = array();
		$data_string = '';

		if ($this->place_id == NULL || $this->api_key == NULL)
		{
			return $ret;
		}
		
		if ($force)
		{
			$retrieval = get_option($this->prefix . 'retrieval');
			
			if (is_array($retrieval) && isset($retrieval['requests']) && is_array($retrieval['requests']) && count($retrieval) > 1)
			{
				$last_retrieval = end($retrieval['requests']);
				$force = (!isset($last_retrieval['place_id']) || isset($last_retrieval['place_id']) && $last_retrieval['place_id'] != $this->place_id || (!isset($last_retrieval['time']) || isset($last_retrieval['time']) && (time() - $last_retrieval['time']) > 10));
			}
		}
		
		if (!$force && (!is_array($this->google_result) || is_array($this->google_result) && empty($this->google_result)))
		{
			$this->google_result = get_option($this->prefix . 'google_result', array());
		}
		
		if (!$force && is_array($this->google_result) && !empty($this->google_result))
		{
			$data_string = json_encode($this->google_result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			$data_array = $this->google_result;
		}
				
		if (!$force && !is_array($retrieval) && (!is_array($this->google_result) || is_array($this->google_result) && (empty($this->google_result) || !empty($this->google_result) && (!isset($this->google_result['status']) || $this->settings_updated && isset($this->google_result['status']) && !preg_match('/^OK$/i', $this->google_result['status'])))))
		{
			$retrieval = get_option($this->prefix . 'retrieval');
			
			if ($this->settings_updated && (!is_array($retrieval) || !isset($retrieval['requests']) || isset($retrieval['requests']) && count($retrieval['requests']) < 5))
			{
				$recheck = TRUE;
			}
			elseif (is_array($retrieval) && isset($retrieval['requests']) && is_array($retrieval['requests']))
			{
				$last_retrieval = end($retrieval['requests']);
				$recheck = ((!isset($last_retrieval['place_id']) || isset($last_retrieval['place_id']) && $last_retrieval['place_id'] == $this->place_id) && (!isset($last_retrieval['time']) || isset($last_retrieval['time']) && (time() - $last_retrieval['time']) > 10));
			}
		}
		
		if ($recheck)
		{
			$this->request_count++;
			
			if (!$force && $format != 'array')
			{
				return $ret;
			}			
		}
		
		if ($force || $recheck)
		{
			$url = 'https://maps.googleapis.com/maps/api/place/details/json?placeid=' . rawurlencode($this->place_id)
				. '&fields=' . rawurlencode(implode(',', $fields))
				. '&key=' . rawurlencode($this->api_key)
				. (($language != NULL) ? '&language=' . rawurlencode($language) : '');

			if (function_exists('wp_remote_get') && function_exists('wp_remote_retrieve_body'))
			{
				$data_string = wp_remote_retrieve_body(wp_remote_get($url));
			}
			
			if (!is_string($data_string))
			{
				if ($ret == 'html')
				{
					$ret = '<p class="error">'
					/* translators: %s: URL of remote data, this should remain untouched */
					. sprintf(__('Error: Unable to collect remote data from URL: <em>%s</em>', 'opening-hours'), $url) . '</p>';
				}
				
				return $ret;
			}
			
			$data_array = ($data_string != NULL) ? json_decode($data_string, TRUE) : array();
			$this->google_result = $data_array;
			$retrieval = ($retrieval == NULL) ? get_option($this->prefix . 'retrieval') : $retrieval;
			
			if (!is_array($retrieval))
			{
				$retrieval = array(
					'count' => 0,
					'initial' => time(),
					'requests' => array()
				);
			}
			elseif (!is_array($retrieval['requests']))
			{
				$retrieval['requests'] = array();
			}
			elseif (count($retrieval['requests']) > 10)
			{
				$retrieval['requests'] = array_slice($retrieval['requests'], -10);
			}
			
			$this->request_count++;
			$retrieval['requests'][] = array(
				'time' => time(),
				'place_id' => $this->place_id,
				'name' => (isset($this->google_result['result']['name'])) ? $this->google_result['result']['name'] : NULL,
				'status' => (isset($this->google_result['status'])) ? $this->google_result['status'] : NULL,
				'opening_hours' => (isset($this->google_result['result']['opening_hours']) && is_array($this->google_result['result']['opening_hours'])) ? count($this->google_result['result']['opening_hours']) : NULL,
				'count' => $this->request_count
			);
			$retrieval['count'] = intval($retrieval['count']) + 1;

			update_option($this->prefix . 'retrieval', $retrieval, 'no');
		}
		
		switch ($format)
		{
		case 'html':
			if ($this->place_id == NULL && $this->api_key == NULL)
			{
				$ret = '<p class="error">' . __('Error: Place ID and Google API Key are required.', 'opening-hours') . '</p>';
			}
			elseif ($this->place_id == NULL)
			{
				$ret = '<p class="error">' . __('Error: Place ID is required.', 'opening-hours') . '</p>';
			}
			elseif ($this->api_key == NULL)
			{
				$ret = '<p class="error">' . __('Error: Google API Key is required.', 'opening-hours') . '</p>';
			}
			
			if ($ret != '')
			{
				break;
			}
			
			$ret = '	<pre id="open-google-data">' . esc_html($data_string) . '</pre>
';
			break;
		case 'array':
			$ret = $data_array;
			break;
		case 'json':
		default:
			if ($this->place_id == NULL && $this->api_key == NULL)
			{
				$ret = json_encode(array(
					'error' => __('Place ID and Google API Key are required.', 'opening-hours')
				));
			}
			elseif ($this->place_id == NULL)
			{
				$ret = json_encode(array(
					'error' => __('Error: Place ID is required.', 'opening-hours')
				));
			}
			elseif ($this->api_key == NULL)
			{
				$ret = json_encode(array(
					'error' => __('Error: Google API Key is required.', 'opening-hours')
				));
			}
			
			if ($ret != '')
			{
				return $ret;
			}
			
			$ret = $data_string;
			break;
		}
		
		return $ret;
	}
	
	private function hours_filter($a, $return = NULL)
	{
		// Checks and filtering of groups of start and end hours for a day
		
		$strings = array();
		$seconds = array();
		$l = 0;
		$next_day = 0;
		
		foreach (array_values($a) as $h)
		{
			if (!is_array($h) || is_array($h) && count($h) != 2)
			{
				continue;
			}
			
			$h = array_values($h);
			
			if (!preg_match('/^(\d{2}):(\d{2})$/', $h[0], $m) || !preg_match('/^(\d{2}):(\d{2})$/', $h[1], $n))
			{
				continue;
			}
			
			$strings[] = $h[0];
			$strings[] = $h[1];
			$seconds[] = (intval($m[1]) * HOUR_IN_SECONDS) + (intval($m[2]) * MINUTE_IN_SECONDS);
			$seconds[] = (intval($n[1]) * HOUR_IN_SECONDS) + (intval($n[2]) * MINUTE_IN_SECONDS);
		}
		
		$a = array();
		
		foreach (array_keys($seconds) as $i)
		{
			if ($i%2 == 1)
			{
				continue;
			}
			
			$j = $i + 1;
			$k = (array_key_exists(($j + 1), $seconds)) ? $j + 1 : NULL;
			
			if ($seconds[$i] < $seconds[$j])
			{
				if ($k == NULL)
				{
					break;
				}
				
				if ($seconds[$j] < $seconds[$k])
				{
					continue;
				}
				
				$seconds[$k] += DAY_IN_SECONDS;
				
				break;
			}
			
			$seconds[$j] += DAY_IN_SECONDS;
			
			if ($k == NULL)
			{
				break;
			}
			
			if ($seconds[$j] < $seconds[$k])
			{
				continue;
			}
			
			$seconds[$k] += DAY_IN_SECONDS;
			
			break;
		}
		
		$next_day = FALSE;
		
		foreach ($seconds as $i => $x)
		{
			$y = $seconds[($i + 1)];
			
			if ($x >= 129600 || $y >= 129600)
			{
				break;
			}
			
			$j = $i + 1;
			$z = array_key_exists(($j + 1), $seconds) ? $seconds[($j + 1)] : NULL;
			$k = ($z != NULL) ? $j + 1 : NULL;
			
			if ($i%2 == 1)
			{
				continue;
			}
			
			if ($next_day && $x > $y)
			{
				continue;
			}
			
			if ($next_day && $z != NULL && $y > $z)
			{
				$a[$l] = array($strings[$i], $strings[$k]);
				$l++;
				continue;
			}
			
			$a[$l] = array($strings[$i], $strings[$j]);
			
			if (!$next_day && ($x > $y || $z != NULL && $y > $z))
			{
				$next_day = TRUE;
			}
			
			$l++;
			
			if ($z == NULL)
			{
				break;
			}
		}
		
		if (is_string($return))
		{
			if (count($seconds) != count($strings))
			{
				$seconds = array_slice($seconds, 0, count($strings));
			}
			
			$seconds = array_map( function($v) { return $v; }, $seconds);
			
			switch ($return)
			{
			case 'days':
			case 'day':
				return array_map( function($v) { return $v / DAY_IN_SECONDS; }, $seconds);
			case 'hours':
			case 'hour':
				return array_map( function($v) { return $v / HOUR_IN_SECONDS; }, $seconds);
			case 'minutes':
			case 'minute':
				return array_map( function($v) { return $v / MINUTE_IN_SECONDS; }, $seconds);
			case 'seconds':
			case 'second':
				return $seconds;
			}
		}
		
		return $a;
	}
	
	private function set_localized_dates()
	{
		// Set days and dates in the local language
		
		if (is_array($this->day_formats) && !empty($this->day_formats))
		{
			return TRUE;
		}
		
		if ($this->wp_date)
		{
			for ($i = 0; $i < 7; $i++)
			{
				$this->days[$i] = $this->sentence_case(wp_date("l", 1590883200 + $i * DAY_IN_SECONDS + ($this->offset * -1) + HOUR_IN_SECONDS));
			}
			
			$this->day_formats = array(
				'full' => array($this->sentence_case(wp_date("l", $this->next_week_start_timestamp)), 'l', NULL),
				'short' => array($this->sentence_case(wp_date("D", $this->next_week_start_timestamp)), 'D', NULL),
				'initial' => array(substr(ucfirst(wp_date("D", $this->next_week_start_timestamp)), 0, 1), 'D', 1),
				'short_date_short_month' => array($this->sentence_case(wp_date("D jS M", $this->next_week_start_timestamp)), 'D jS M', NULL),
				'short_date_short_month_comma' => array($this->sentence_case(wp_date("D, jS M", $this->next_week_start_timestamp)), 'D, jS M', NULL),
				'short_date_short_month_first' => array($this->sentence_case(wp_date("D M jS", $this->next_week_start_timestamp)), 'D M jS', NULL),
				'short_date_short_month_first_comma' => array($this->sentence_case(wp_date("D, M jS", $this->next_week_start_timestamp)), 'D, M jS', NULL),
				'short_date_short_month_nos' => array($this->sentence_case(wp_date("D j M", $this->next_week_start_timestamp)), 'D j M', NULL),
				'short_date_short_month_comma_nos' => array($this->sentence_case(wp_date("D, j M", $this->next_week_start_timestamp)), 'D, j M', NULL),
				'short_date_short_month_first_nos' => array($this->sentence_case(wp_date("D M j", $this->next_week_start_timestamp)), 'D M j', NULL),
				'short_date_short_month_first_comma_nos' => array($this->sentence_case(wp_date("D, M j", $this->next_week_start_timestamp)), 'D, M j', NULL),
				'full_date' => array($this->sentence_case(wp_date("l jS", $this->next_week_start_timestamp)), 'l jS', NULL),
				'full_date_comma' => array($this->sentence_case(wp_date("l, jS", $this->next_week_start_timestamp)), 'l, jS', NULL),
				'full_date_nos' => array($this->sentence_case(wp_date("l j", $this->next_week_start_timestamp)), 'l j', NULL),
				'full_date_comma_nos' => array($this->sentence_case(wp_date("l, j", $this->next_week_start_timestamp)), 'l, j', NULL),
				'full_date_month' => array($this->sentence_case(wp_date("l jS F", $this->next_week_start_timestamp)), 'l jS F', NULL),
				'full_date_month_comma' => array($this->sentence_case(wp_date("l, jS F", $this->next_week_start_timestamp)), 'l, jS F', NULL),
				'full_date_month_first' => array($this->sentence_case(wp_date("l F jS", $this->next_week_start_timestamp)), 'l F jS', NULL),
				'full_date_month_first_comma' => array($this->sentence_case(wp_date("l, F jS", $this->next_week_start_timestamp)), 'l, F jS', NULL),
				'full_date_month_nos' => array($this->sentence_case(wp_date("l j F", $this->next_week_start_timestamp)), 'l j F', NULL),
				'full_date_month_comma_nos' => array($this->sentence_case(wp_date("l, j F", $this->next_week_start_timestamp)), 'l, j F', NULL),
				'full_date_month_first_nos' => array($this->sentence_case(wp_date("l F j", $this->next_week_start_timestamp)), 'l F j', NULL),
				'full_date_month_first_comma_nos' => array($this->sentence_case(wp_date("l, F j", $this->next_week_start_timestamp)), 'l, F j', NULL),
				'full_date_short_month' => array($this->sentence_case(wp_date("l jS M", $this->next_week_start_timestamp)), 'l jS M', NULL),
				'full_date_short_month_comma' => array($this->sentence_case(wp_date("l, jS M", $this->next_week_start_timestamp)), 'l, jS M', NULL),
				'full_date_short_month_first' => array($this->sentence_case(wp_date("l M jS", $this->next_week_start_timestamp)), 'l M jS', NULL),
				'full_date_short_month_first_comma' => array($this->sentence_case(wp_date("l, M jS", $this->next_week_start_timestamp)), 'l, M jS', NULL),
				'full_date_short_month_nos' => array($this->sentence_case(wp_date("l j M", $this->next_week_start_timestamp)), 'l j M', NULL),
				'full_date_short_month_comma_nos' => array($this->sentence_case(wp_date("l, j M", $this->next_week_start_timestamp)), 'l, j M', NULL),
				'full_date_short_month_first_nos' => array($this->sentence_case(wp_date("l M j", $this->next_week_start_timestamp)), 'l M j', NULL),
				'full_date_short_month_first_comma_nos' => array($this->sentence_case(wp_date("l, M j", $this->next_week_start_timestamp)), 'l, M j', NULL)
			);

			return TRUE;				
		}
		
		if ($this->date_i18n)
		{
			for ($i = 0; $i < 7; $i++)
			{
				$this->days[$i] = $this->sentence_case(date_i18n("l", 1590883200 + $i * DAY_IN_SECONDS));
			}
			
			$this->day_formats = array(
				'full' => array($this->sentence_case(date_i18n("l", $this->next_week_start_timestamp)), 'l', NULL),
				'short' => array($this->sentence_case(date_i18n("D", $this->next_week_start_timestamp)), 'D', NULL),
				'initial' => array(substr(ucfirst(date_i18n("D", $this->next_week_start_timestamp)), 0, 1), 'D', 1),
				'short_date_short_month' => array($this->sentence_case(date_i18n("D jS M", $this->next_week_start_timestamp)), 'D jS M', NULL),
				'short_date_short_month_comma' => array($this->sentence_case(date_i18n("D, jS M", $this->next_week_start_timestamp)), 'D, jS M', NULL),
				'short_date_short_month_first' => array($this->sentence_case(date_i18n("D M jS", $this->next_week_start_timestamp)), 'D M jS', NULL),
				'short_date_short_month_first_comma' => array($this->sentence_case(date_i18n("D, M jS", $this->next_week_start_timestamp)), 'D, M jS', NULL),
				'short_date_short_month_nos' => array($this->sentence_case(date_i18n("D j M", $this->next_week_start_timestamp)), 'D j M', NULL),
				'short_date_short_month_comma_nos' => array($this->sentence_case(date_i18n("D, j M", $this->next_week_start_timestamp)), 'D, j M', NULL),
				'short_date_short_month_first_nos' => array($this->sentence_case(date_i18n("D M j", $this->next_week_start_timestamp)), 'D M j', NULL),
				'short_date_short_month_first_comma_nos' => array($this->sentence_case(date_i18n("D, M j", $this->next_week_start_timestamp)), 'D, M j', NULL),
				'full_date' => array($this->sentence_case(date_i18n("l jS", $this->next_week_start_timestamp)), 'l jS', NULL),
				'full_date_comma' => array($this->sentence_case(date_i18n("l, jS", $this->next_week_start_timestamp)), 'l, jS', NULL),
				'full_date_nos' => array($this->sentence_case(date_i18n("l j", $this->next_week_start_timestamp)), 'l j', NULL),
				'full_date_comma_nos' => array($this->sentence_case(date_i18n("l, j", $this->next_week_start_timestamp)), 'l, j', NULL),
				'full_date_month' => array($this->sentence_case(date_i18n("l jS F", $this->next_week_start_timestamp)), 'l jS F', NULL),
				'full_date_month_comma' => array($this->sentence_case(date_i18n("l, jS F", $this->next_week_start_timestamp)), 'l, jS F', NULL),
				'full_date_month_first' => array($this->sentence_case(date_i18n("l F jS", $this->next_week_start_timestamp)), 'l F jS', NULL),
				'full_date_month_first_comma' => array($this->sentence_case(date_i18n("l, F jS", $this->next_week_start_timestamp)), 'l, F jS', NULL),
				'full_date_month_nos' => array($this->sentence_case(date_i18n("l j F", $this->next_week_start_timestamp)), 'l j F', NULL),
				'full_date_month_comma_nos' => array($this->sentence_case(date_i18n("l, j F", $this->next_week_start_timestamp)), 'l, j F', NULL),
				'full_date_month_first_nos' => array($this->sentence_case(date_i18n("l F j", $this->next_week_start_timestamp)), 'l F j', NULL),
				'full_date_month_first_comma_nos' => array($this->sentence_case(date_i18n("l, F j", $this->next_week_start_timestamp)), 'l, F j', NULL),
				'full_date_short_month' => array($this->sentence_case(date_i18n("l jS M", $this->next_week_start_timestamp)), 'l jS M', NULL),
				'full_date_short_month_comma' => array($this->sentence_case(date_i18n("l, jS M", $this->next_week_start_timestamp)), 'l, jS M', NULL),
				'full_date_short_month_first' => array($this->sentence_case(date_i18n("l M jS", $this->next_week_start_timestamp)), 'l M jS', NULL),
				'full_date_short_month_first_comma' => array($this->sentence_case(date_i18n("l, M jS", $this->next_week_start_timestamp)), 'l, M jS', NULL),
				'full_date_short_month_nos' => array($this->sentence_case(date_i18n("l j M", $this->next_week_start_timestamp)), 'l j M', NULL),
				'full_date_short_month_comma_nos' => array($this->sentence_case(date_i18n("l, j M", $this->next_week_start_timestamp)), 'l, j M', NULL),
				'full_date_short_month_first_nos' => array($this->sentence_case(date_i18n("l M j", $this->next_week_start_timestamp)), 'l M j', NULL),
				'full_date_short_month_first_comma_nos' => array($this->sentence_case(date_i18n("l, M j", $this->next_week_start_timestamp)), 'l, M j', NULL)
			);
			
			return TRUE;				
		}
			
		for ($i = 0; $i < 7; $i++)
		{
			$this->days[$i] = $this->sentence_case(gmdate("l", 1590883200 + $i * DAY_IN_SECONDS));
		}
		
		$this->day_formats = array(
			'full' => array($this->sentence_case(gmdate("l", $this->next_week_start_timestamp)), 'l', NULL),
			'short' => array($this->sentence_case(gmdate("D", $this->next_week_start_timestamp)), 'D', NULL),
			'initial' => array(substr(ucfirst(gmdate("D", $this->next_week_start_timestamp)), 0, 1), 'D', 1),
			'short_date_short_month' => array($this->sentence_case(gmdate("D jS M", $this->next_week_start_timestamp)), 'D jS M', NULL),
			'short_date_short_month_comma' => array($this->sentence_case(gmdate("D, jS M", $this->next_week_start_timestamp)), 'D, jS M', NULL),
			'short_date_short_month_first' => array($this->sentence_case(gmdate("D M jS", $this->next_week_start_timestamp)), 'D M jS', NULL),
			'short_date_short_month_first_comma' => array($this->sentence_case(gmdate("D, M jS", $this->next_week_start_timestamp)), 'D, M jS', NULL),
			'short_date_short_month_nos' => array($this->sentence_case(gmdate("D j M", $this->next_week_start_timestamp)), 'D j M', NULL),
			'short_date_short_month_comma_nos' => array($this->sentence_case(gmdate("D, j M", $this->next_week_start_timestamp)), 'D, j M', NULL),
			'short_date_short_month_first_nos' => array($this->sentence_case(gmdate("D M j", $this->next_week_start_timestamp)), 'D M j', NULL),
			'short_date_short_month_first_comma_nos' => array($this->sentence_case(gmdate("D, M j", $this->next_week_start_timestamp)), 'D, M j', NULL),
			'full_date' => array($this->sentence_case(gmdate("l jS", $this->next_week_start_timestamp)), 'l jS', NULL),
			'full_date_comma' => array($this->sentence_case(gmdate("l, jS", $this->next_week_start_timestamp)), 'l, jS', NULL),
			'full_date_nos' => array($this->sentence_case(gmdate("l j", $this->next_week_start_timestamp)), 'l j', NULL),
			'full_date_comma_nos' => array($this->sentence_case(gmdate("l, j", $this->next_week_start_timestamp)), 'l, j', NULL),
			'full_date_month' => array($this->sentence_case(gmdate("l jS F", $this->next_week_start_timestamp)), 'l jS F', NULL),
			'full_date_month_comma' => array($this->sentence_case(gmdate("l, jS F", $this->next_week_start_timestamp)), 'l, jS F', NULL),
			'full_date_month_first' => array($this->sentence_case(gmdate("l F jS", $this->next_week_start_timestamp)), 'l F jS', NULL),
			'full_date_month_first_comma' => array($this->sentence_case(gmdate("l, F jS", $this->next_week_start_timestamp)), 'l, F jS', NULL),
			'full_date_month_nos' => array($this->sentence_case(gmdate("l j F", $this->next_week_start_timestamp)), 'l j F', NULL),
			'full_date_month_comma_nos' => array($this->sentence_case(gmdate("l, j F", $this->next_week_start_timestamp)), 'l, j F', NULL),
			'full_date_month_first_nos' => array($this->sentence_case(gmdate("l F j", $this->next_week_start_timestamp)), 'l F j', NULL),
			'full_date_month_first_comma_nos' => array($this->sentence_case(gmdate("l, F j", $this->next_week_start_timestamp)), 'l, F j', NULL),
			'full_date_short_month' => array($this->sentence_case(gmdate("l jS M", $this->next_week_start_timestamp)), 'l jS M', NULL),
			'full_date_short_month_comma' => array($this->sentence_case(gmdate("l, jS M", $this->next_week_start_timestamp)), 'l, jS M', NULL),
			'full_date_short_month_first' => array($this->sentence_case(gmdate("l M jS", $this->next_week_start_timestamp)), 'l M jS', NULL),
			'full_date_short_month_first_comma' => array($this->sentence_case(gmdate("l, M jS", $this->next_week_start_timestamp)), 'l, M jS', NULL),
			'full_date_short_month_nos' => array($this->sentence_case(gmdate("l j M", $this->next_week_start_timestamp)), 'l j M', NULL),
			'full_date_short_month_comma_nos' => array($this->sentence_case(gmdate("l, j M", $this->next_week_start_timestamp)), 'l, j M', NULL),
			'full_date_short_month_first_nos' => array($this->sentence_case(gmdate("l M j", $this->next_week_start_timestamp)), 'l M j', NULL),
			'full_date_short_month_first_comma_nos' => array($this->sentence_case(gmdate("l, M j", $this->next_week_start_timestamp)), 'l, M j', NULL)
		);

		return TRUE;				
	}
	
	public function set($data = NULL, $force = NULL)
	{
		// Set data with cache check
		
		if (is_array($data) && !empty($data))
		{
			ksort($data);
			$hash_key = md5(implode('|', array_keys($data)) . '|' . implode('|', array_values($data)));
			extract($data, EXTR_SKIP);
		}
		else
		{
			$data = NULL;
			$hash_key = 'data';
		}

		$cache = FALSE;
		$cache_retrieved = FALSE;
		$consolidation_cache = FALSE;
		$consolidation_cache_retrieved = FALSE;
		
		if (!is_bool($force) || !$force)
		{
			$force_check = get_option($this->prefix . 'force', NULL);
			
			if (is_string($force_check) && preg_match('#^(\d+(?:\.\d+)?)/0$#', $force_check, $m))
			{
				$force = ((time() - intval($m[1])) < 10);
				update_option($this->prefix . 'force', $m[1] . '/1', 'yes');
			}
			
			$force = ($force || ((is_bool($force) && !$force || !is_bool($force)) && $this->settings_updated && !is_array(get_option($this->prefix . 'regular'))));
		}

		$this->regular = (isset($regular) && is_array($regular)) ? $regular : ((is_array($this->regular) && !empty($this->regular)) ? $this->regular : get_option($this->prefix . 'regular'));
		$this->special = (isset($special) && is_array($special)) ? $special : ((is_array($this->special) && !empty($this->special)) ? $this->special : get_option($this->prefix . 'special'));
		$this->closure = (isset($closure) && is_array($closure)) ? $closure : ((is_array($this->closure) && !empty($this->closure)) ? $this->closure : get_option($this->prefix . 'closure'));
		$this->api_key = (isset($api_key) && is_array($api_key) && $api_key != NULL) ? $api_key : get_option($this->prefix . 'api_key');
		$this->place_id = (isset($place_id) && is_array($place_id) && $place_id != NULL) ? $place_id : get_option($this->prefix . 'place_id');
		$consolidation = (is_array($data) && array_key_exists('consolidation', $data)) ? ((isset($consolidation)) ? $consolidation : NULL) : get_option($this->prefix . 'consolidation');
		
		if (isset($regular) || isset($special))
		{
			$regular = (isset($regular) && is_bool($regular)) ? $regular : TRUE;
			$special = (isset($special) && is_bool($special) && ($special || $regular)) ? $special : TRUE;
		}
		else
		{
			$regular = TRUE;
			$special = TRUE;
		}
		
		if (!is_array($this->data) || is_array($this->data) && empty($this->data))
		{
			$cache = wp_cache_get('data', $this->class_name);
			
			if (is_array($cache) && array_key_exists($hash_key, $cache))
			{
				$this->data = $cache[$hash_key];
				$cache_retrieved = TRUE;
			}
		}
		
		if ($consolidation == NULL)
		{
			$consolidation_cache_retrieved = TRUE;
		}
		elseif (!is_array($this->consolidation) || is_array($this->consolidation) && empty($this->consolidation) && $consolidation != NULL)
		{
			$consolidation_cache = wp_cache_get('consolidation', $this->class_name);
			
			if (is_array($consolidation_cache) && array_key_exists($hash_key, $consolidation_cache))
			{
				$this->consolidation = $consolidation_cache[$hash_key];
				$consolidation_cache_retrieved = TRUE;
			}
		}
		
		if (!$force && $cache_retrieved && $consolidation_cache_retrieved && (is_array($this->data) && !empty($this->data) && is_array($this->consolidation) && !empty($this->consolidation)))
		{
			return TRUE;
		}
		
		$this->data = array();
		$this->consolidation = array();
		
		if (is_array($cache) || is_array($consolidation_cache))
		{
			wp_cache_delete('data', $this->class_name);
			wp_cache_delete('consolidation', $this->class_name);
		}
		
		if (isset($start) && is_numeric($start))
		{
			if ($start >= -91 && $start <= 724)
			{
				$start = $this->get_day_timestamp($start);
			}
			
			if ($start >= 946684800)
			{
				$week_start = ($this->wp_date) ? wp_date("w", $start) : (($this->date_i18n) ? date_i18n("w", $start) : gmdate("w", $start));
			}
			else
			{
				$start = NULL;
			}
		}
		else
		{
			$start = NULL;
		}
		
		if (isset($end) && is_numeric($end) && $end >= -7 && $end <= 731)
		{
			$end = $this->get_day_timestamp($end + 1);
		}
		
		if (isset($end) && is_numeric($end) && $end >= 946684800 && (is_numeric($start) && $start < $end || !is_numeric($start) && $this->today_timestamp < $end))
		{
			if (!$regular && $special)
			{
				if (is_numeric($start))
				{
					$end = ($end - WEEK_IN_SECONDS - $start > YEAR_IN_SECONDS) ? $start + YEAR_IN_SECONDS + WEEK_IN_SECONDS : $end;
					$count = ceil(($end - $start)/DAY_IN_SECONDS);
				}
				else
				{
					$end = ($end - WEEK_IN_SECONDS - $this->today_timestamp > YEAR_IN_SECONDS) ? $this->today_timestamp + YEAR_IN_SECONDS + WEEK_IN_SECONDS : $end;
					$count = ceil(($end - $this->today_timestamp)/DAY_IN_SECONDS);
				}
			}
			else
			{
				if (is_numeric($start))
				{
					$end = ($end - $start > 31 * DAY_IN_SECONDS) ? $this->today_timestamp + 31 * DAY_IN_SECONDS : $end;
					$count = ceil(($end - $start)/DAY_IN_SECONDS);
				}
				else
				{
					$end = ($end - $this->today_timestamp > 31 * DAY_IN_SECONDS) ? $this->today_timestamp + 31 * DAY_IN_SECONDS : $end;
					$count = ceil(($end - $this->today_timestamp)/DAY_IN_SECONDS);
				}
			}
		}
		else
		{
			$end = NULL;
			$count = (isset($count) && is_numeric($count) && $count >= 1 && $count <= ((!$regular && $special) ? 61 : 31)) ? intval($count) : 7;
		}
		
		$days = array();
		$closed_show = (!isset($closed_show) || isset($closed_show) && $closed_show);
		$week_start = (isset($week_start) && is_numeric($week_start)) ? (($week_start < 0) ? (($week_start == -2) ? $this->yesterday : $this->today) : $week_start) : $this->week_start;
		$start_modifier = (is_numeric($start) && abs(round(($start - $this->today_timestamp)/DAY_IN_SECONDS)) <= 731) ? round(($start - $this->today_timestamp)/DAY_IN_SECONDS) : 0;
		
		for ($i = (($start_modifier != 0 || $this->today == $week_start) ? 0 : -7); $i <= ((!$regular && $special) ? 372 : 31); $i++)
		{
			if (count($days) == $count)
			{
				break;
			}
			
			$timestamp = $this->get_day_timestamp($i + $start_modifier);
			
			if ($start == NULL)
			{
				if ($this->wp_date && $week_start == wp_date("w", $timestamp) || !$this->wp_date && $week_start == gmdate("w", $timestamp))
				{
					$start = $timestamp;
					$days[] = $timestamp;
				}
				continue;
			}
			
			$days[] = $timestamp;
		}
		
		$end = $timestamp;
		$start = (isset($start) && is_numeric($start)) ? $start : $week_start;
		$end = (isset($end) && is_numeric($end)) ? $end : (($this->wp_date) ? mktime(0, 0, 0, wp_date("m"), wp_date("j") + ($count - 1), wp_date("Y")) : (($this->date_i18n) ? mktime(0, 0, 0, date_i18n("m"), date_i18n("j") + ($count - 1), date_i18n("Y")) : mktime(0, 0, 0, gmdate("m"), gmdate("j") + ($count - 1), gmdate("Y"))));
		$consecutive = array();
		$consecutive_replacement = array();
		
		foreach ($days as $i => $timestamp)
		{
			$day = ($this->wp_date) ? wp_date("w", $timestamp) : (($this->date_i18n) ? date_i18n("w", $timestamp) : gmdate("w", $timestamp));
			$a = ($special && !empty($this->closure) && $timestamp >= $this->closure['start'] && $timestamp < $this->closure['end']) ? array('closed' => TRUE) : (($special && is_array($this->special) && array_key_exists($timestamp, $this->special)) ? $this->special[$timestamp] : ((isset($this->regular[$day])) ? $this->regular[$day] : array()));
			$closed = (empty($a) || !empty($a) && isset($a['closed']) && $a['closed']);
			$day_weekday = (isset($weekdays) && is_array($weekdays) && in_array($day, $weekdays) || (!isset($weekdays) || isset($weekdays) && !is_array($weekdays)) && isset($this->weekdays) && is_array($this->weekdays) && in_array($day, $this->weekdays));
			$day_weekend = (isset($weekend) && is_array($weekend) && in_array($day, $weekend) || (!isset($weekend) || isset($weekend) && !is_array($weekend)) && isset($this->weekend) && is_array($this->weekend) && in_array($day, $this->weekend));
			
			if (!$regular && (!is_array($this->special) || !array_key_exists($timestamp, $this->special)))
			{
				continue;
			}
			
			if ($consolidation == 'all' || $consolidation == 'separate' || $consolidation == 'weekdays' && $day_weekday || $consolidation == 'weekend' && $day_weekend)
			{
				if ($closed)
				{
					$consecutive[$i] = 'closed';
					
					if (!array_key_exists('closed', $this->consolidation))
					{
						$this->consolidation['closed'] = array();
					}
					
					$this->consolidation['closed'][$i] = array(
						'timestamp' => $timestamp,
						'weekday' => $day_weekday,
						'weekend' => $day_weekend
					);
				}
				elseif (isset($a['hours_24']) && $a['hours_24'])
				{
					$consecutive[$i] = 'hours_24';
					
					if (!array_key_exists('hours_24', $this->consolidation))
					{
						$this->consolidation['hours_24'] = array();
					}
					
					$this->consolidation['hours_24'][$i] = array(
						'timestamp' => $timestamp,
						'weekday' => $day_weekday,
						'weekend' => $day_weekend
					);
				}
				else
				{
					$hours_key = md5(serialize(array_values($a['hours'])));
					
					if (!array_key_exists('hours_' . $hours_key, $this->consolidation))
					{
						$this->consolidation['hours_' . $hours_key] = array();
						$consecutive[$i] = 'hours_' . $hours_key;
					}
					else
					{
						while (in_array($hours_key, $consecutive_replacement))
						{
							$hours_key = $consecutive_replacement[$hours_key];
						}
												
						if (count($consecutive) > 2 && in_array('hours_' . $hours_key, $consecutive) && array_key_exists($i - 1, $consecutive) && $consecutive[$i - 1] != 'hours_' . $hours_key)
						{
							for ($j = count($consecutive) - 2; $j >= 0; $j--)
							{
								if (array_key_exists($j, $consecutive) && $consecutive[$j] == 'hours_' . $hours_key)
								{
									$c = 1;
									$previous_hours_key = $hours_key;
									$hours_key = md5(serialize(array(array_values($a['hours']), $c)));
									
									while (array_key_exists($hours_key, $consecutive_replacement))
									{
										if ($c >= 31)
										{
											break;
										}
										
										$c++;
										$previous_hours_key = $hours_key;
										$hours_key = md5(serialize(array(array_values($a['hours']), $c)));
									}
									
									$consecutive_replacement[$previous_hours_key] = $hours_key;
									break;
								}
							}
						}
						
						$consecutive[$i] = 'hours_' . $hours_key;
					}
					
					$this->consolidation['hours_' . $hours_key][$i] = array(
						'timestamp' => $timestamp,
						'weekday' => $day_weekday,
						'weekend' => $day_weekend
					);
				}
			}
			elseif ($consolidation != NULL)
			{
				if (!array_key_exists('ignore', $this->consolidation))
				{
					$this->consolidation['ignore'] = array();
				}
				
				$this->consolidation['ignore'][$i] = array(
					'timestamp' => $timestamp,
					'weekday' => $day_weekday,
					'weekend' => $day_weekend
				);
			}
			
			if ($closed && !$closed_show)
			{
				continue;
			}
			
			$this->data[$timestamp] = array(
				'date' => $timestamp,
				'regular' => ((!is_array($this->special) || !array_key_exists($timestamp, $this->special)) && (empty($this->closure) || !empty($this->closure) && ($timestamp < $this->closure['start'] || $timestamp >= $this->closure['end']))),
				'special' => (is_array($this->special) && array_key_exists($timestamp, $this->special) || !empty($this->closure) && $timestamp >= $this->closure['start'] && $timestamp < $this->closure['end']),
				'day' => $day,
				'count' => $i,
				'today' => ($timestamp == $this->today_timestamp),
				'tomorrow' => ($timestamp == $this->tomorrow_timestamp),
				'past' => ($timestamp < $this->today_timestamp),
				'future' => ($timestamp > $this->today_timestamp),
				'weekday' => $day_weekday,
				'weekend' => $day_weekend,
				'closed' => $closed,
				'hours_24' => (!$closed && isset($a['hours_24']) && $a['hours_24']),
				'hours' => (!$closed && isset($a['hours']) && is_array($a['hours'])) ? $a['hours'] : array(),
				'consolidated' => FALSE,
				'consolidated_first' => FALSE
			);
		}
		
		if ($consolidation != NULL && (count($this->consolidation) + ((array_key_exists('ignore', $this->consolidation)) ? count($this->consolidation['ignore']) - 1 : 0)) < count($this->data))
		{
			foreach ($this->consolidation as $k => $days)
			{
				if ($k == 'ignore' || count($days) < 2)
				{
					continue;
				}
				
				ksort($days);
				
				foreach ($days as $count => $a)
				{
					$i = 0;
					$consolidated = array(
						'weekdays' => array(),
						'weekend' => array(),
						'days' => array()
					);
										
					while (array_key_exists(($count + $i), $days) && $i <= 31)
					{
						$weekday = $days[($count + $i)]['weekday'];
						$weekend = $days[($count + $i)]['weekend'];
						
						if ($consolidation == 'separate')
						{
							if ($weekday)
							{
								$consolidated['weekdays'][] = $days[($count + $i)]['timestamp'];
							}
							elseif ($weekend)
							{
								$consolidated['weekend'][] = $days[($count + $i)]['timestamp'];
							}
						}
						
						$consolidated['days'][] = $days[($count + $i)]['timestamp'];
						
						$i++;
					}
					
					if (count($consolidated['days']) < 2)
					{
						continue(2);
					}
				
					break;
				}
				
				foreach (array_keys($this->data) as $timestamp)
				{
					if (is_array($this->data[$timestamp]['consolidated']) || in_array($timestamp, $consolidated['days']) === FALSE)
					{
						continue;
					}
					
					if ($consolidation == 'separate')
					{
						if (in_array($timestamp, $consolidated['weekdays']) !== FALSE)
						{
							$this->data[$timestamp]['consolidated'] = $consolidated['weekdays'];
							$this->data[$timestamp]['consolidated_first'] = ($consolidated['weekdays'][0] == $timestamp);
						}
						elseif (in_array($timestamp, $consolidated['weekend']) !== FALSE)
						{
							$this->data[$timestamp]['consolidated'] = $consolidated['weekend'];
							$this->data[$timestamp]['consolidated_first'] = ($consolidated['weekend'][0] == $timestamp);
						}
						
						continue;
					}
										
					$this->data[$timestamp]['consolidated'] = $consolidated['days'];
					$this->data[$timestamp]['consolidated_first'] = ($consolidated['days'][0] == $timestamp);
				}
			}
		}

		$cache_refresh_time = (mktime(0, 0, 0, gmdate("m"), gmdate("j") + 1, gmdate("Y")) - time());
		$cache_refresh_time = ($cache_refresh_time > HOUR_IN_SECONDS) ? HOUR_IN_SECONDS : $cache_refresh_time;
		
		if ($cache_refresh_time > 15)
		{
			if (!is_array($cache))
			{
				$cache = array();
			}

			if (!is_array($consolidation_cache))
			{
				$consolidation_cache = array();
			}

			$cache[$hash_key] = $this->data;
			wp_cache_add('data', $cache, $this->class_name, $cache_refresh_time);
			
			if ($consolidation != NULL)
			{
				$consolidation_cache[$hash_key] = $this->consolidation;
				wp_cache_add('consolidation', $consolidation_cache, $this->class_name, $cache_refresh_time);
			}
		}
		
		if (!$this->dashboard || $this->api_key == NULL || $this->place_id == NULL || defined('XMLRPC_REQUEST') && XMLRPC_REQUEST || ((!is_bool($force) || !$force) && defined('DOING_CRON') && DOING_CRON) || isset($_POST['action']) && is_string($_POST['action']) && preg_match('/^heartbeat$/i', $_POST['action']) || isset($_POST['log']) && $_POST['log'] != NULL)
		{
			return TRUE;
		}

		if (!$force)
		{
			global $wpdb;
			
			if (!isset($this->google_data) || isset($this->google_data) && !is_array($this->google_data) || isset($this->google_data) && is_array($this->google_data) && empty($this->google_data))
			{
				$this->google_data = get_option($this->prefix . 'google_result', NULL);
			}
			
			if ($this->google_data_exists(TRUE))
			{
				return TRUE;
			}

			if ((!is_array($this->google_data) || is_array($this->google_data) && empty($this->google_data)) && $this->request_count == 0)
			{
				$this->request_count++;
				$this->google_data = $this->get_google_data();
				$this->google_data_exists(TRUE, TRUE);
				update_option($this->prefix . 'google_result', $this->google_data, 'no');
				wp_cache_add('google_result', $this->google_data, $this->class_name, HOUR_IN_SECONDS);
				
				return (is_array($this->google_data) && !empty($this->google_data));
			}
			
			return TRUE;
		}
		
		delete_transient($this->prefix . 'offset_changes');
		wp_cache_delete('structured_data', $this->class_name);
		wp_cache_delete('google_result', $this->class_name);

		if ($this->request_count > 2)
		{
			return FALSE;
		}
		
		$this->google_data = $this->get_google_data('array', TRUE);
		$this->google_data_exists(TRUE, TRUE);
		update_option($this->prefix . 'google_result', $this->google_data, 'no');
		wp_cache_add('google_result', $this->google_data, $this->class_name, HOUR_IN_SECONDS);

		return TRUE;
	}
	
	public function structured_data($return = FALSE, $data = array())
	{
		// Collect Structured Data to display on the home page
				
		$test = (is_bool($return) && $return);
		$string = (is_string($return) && $return == 'json');
		$html = (is_string($return) && $return == 'html');
		$show_in_page = get_option($this->prefix . 'structured_data', 0);
		$show_in_page = (!$this->dashboard && (is_numeric($show_in_page) && $show_in_page > 1 && function_exists('get_the_ID') && get_the_ID() == intval($show_in_page) || (is_bool($show_in_page) && $show_in_page || is_numeric($show_in_page) && intval($show_in_page) == 1) && is_front_page()));
		
		if (!$return && !$string && empty($data) && !$show_in_page)
		{
			return;
		}
		
		$this->set(array('consolidation' => 'all', 'regular' => TRUE, 'special' => FALSE, 'week_start' => 0));
	
		if ($test)
		{
			return TRUE;
		}
		
		if (!$string && !$html)
		{
			$structured_data = wp_cache_get('structured_data', $this->class_name);
			if (is_string($structured_data) && strlen($structured_data) > 20)
			{
				echo $structured_data;
				return;
			}
		}
		
		$logo = FALSE;
		
		$this->set_logo();

		if (is_string($this->logo_image_url))
		{
			$logo = $this->logo_image_url;
		}
		
		if (!is_string($logo) || is_string($logo) && !preg_match('/.+\.(?:jpe?g|png|svg|gif)$/i', $logo))
		{
			$a = get_option('wpseo_titles');
			
			if (is_array($a) && isset($a['company_logo']) && is_string($a['company_logo']))
			{
				$logo = $a['company_logo'];
			}
			elseif (is_string($logo))
			{
				$logo = (!$string && isset($this->google_data['result']['icon'])) ? $this->google_data['result']['icon'] : FALSE;
			}
			
			if (is_null($logo))
			{
				$logo = FALSE;
			}
		}

		$name = (is_string(get_option($this->prefix . 'name'))) ? get_option($this->prefix . 'name') : ((isset($this->google_data['result']['name'])) ? $this->google_data['result']['name'] : FALSE);
		$address = (is_string(get_option($this->prefix . 'address'))) ? get_option($this->prefix . 'address') : ((isset($this->google_data['result']['formatted_address'])) ? $this->google_data['result']['formatted_address'] : FALSE);
		$telephone = get_option($this->prefix . 'telephone', FALSE);
		$business_type = (is_string(get_option($this->prefix . 'business_type'))) ? get_option($this->prefix . 'business_type') : FALSE;
		$price_range = (is_numeric(get_option($this->prefix . 'price_range', NULL))) ? str_repeat('$', get_option($this->prefix . 'price_range')) : FALSE;
		
		extract($data, EXTR_OVERWRITE);
		
		if (!is_string($name))
		{
			if ($test)
			{
				return FALSE;
			}
			
			if (!$string && !$html)
			{
				echo '';
				
				return;
			}
		}

		$data = array(
			'@context' => 'http://schema.org',
			'@type' => 'LocalBusiness',
			'name' => ($name != NULL) ? $name : FALSE,
			'address' => ($address != NULL) ? $address : FALSE,
			'image' => ($logo != NULL) ? $logo : FALSE,
			'url' => get_site_url(),
			'telephone' => ($telephone != NULL) ? $telephone : FALSE,
			'additionalType' => ($business_type != NULL) ? $business_type : FALSE,
			'priceRange' => ($price_range != NULL) ? $price_range : FALSE,
			'openingHoursSpecification' => array()
		);
		
		if (preg_match('/^\s*([^\r\n]+[^, \r\n])(?:,\s*|[ \t]*[\r\n]+[ \t]*)([^\r\n,]+)(?:,\s*|[ \t]*[\r\n]+[ \t]*)(?:([^\r\n,]+)(?:,\s*|[ \t]*[\r\n]+[ \t]*))?([^\r\n,]+)(?:,\s*|[ \t]*[\r\n]+[ \t]*)([a-z]{2})\s*$/si', $address, $m))
		{
			$data['address'] = array(
				'@type' => 'PostalAddress',
				'streetAddress' => $m[1],
				'addressLocality' => $m[2],
				'addressRegion' => $m[3],
				'postalCode' => $m[4],
				'addressCountry' => $m[5]
			);
		}
		
		$day_names_english = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

		foreach ($this->consolidation as $k => $day)
		{
			$d = array();
			$hours = NULL;
			
			if ($k == 'closed')
			{
				$hours = array(array('00:00', '00:00'));
			}
			elseif ($k == 'hours_24')
			{
				$hours = array(array('00:00', '23:59'));
			}
			
			foreach ($this->data as $timestamp => $a)
			{
				$match = FALSE;
				
				foreach ($day as $count => $t)
				{
					if ($t['timestamp'] == $timestamp)
					{
						$match = TRUE;
						break;
					}
				}
				
				if (!$match)
				{
					continue;
				}

				if ($hours == NULL)
				{
					$hours = $a['hours'];
				}
				
				$day_number = ($this->wp_date) ? wp_date("w", $timestamp) : (($this->date_i18n) ? date_i18n("w", $timestamp) : gmdate("w", $timestamp));
				$d[] = $day_names_english[$day_number];
			}
			
			$data['openingHoursSpecification'][] = array(
				'@type' => 'OpeningHoursSpecification',
				'dayOfWeek' => (count($d) == 1) ? $d[0] : $d,
				'opens' => (count($hours) == 3) ? array($hours[0][0], $hours[1][0], $hours[2][0]) : ((count($hours) == 2) ? array($hours[0][0], $hours[1][0]) : $hours[0][0]),
				'closes' => (count($hours) == 3) ? array($hours[0][1], $hours[1][1], $hours[2][1]) : ((count($hours) == 2) ? array($hours[0][1], $hours[1][1]) : $hours[0][1]),
			);			
		}
		
		if (isset($this->closure) && is_array($this->closure) && isset($this->closure['start']) && $this->closure['start'] != NULL && isset($this->closure['end']) && $this->closure['end'] != NULL)
		{
			$data['openingHoursSpecification'][] = array(
				'@type' => 'OpeningHoursSpecification',
				'opens' => '00:00',
				'closes' => '00:00',
				'validFrom' => ($this->wp_date) ? wp_date("Y-m-d", mktime(gmdate("H", $this->closure['start']), gmdate("i", $this->closure['start']), 0, gmdate("m", $this->closure['start']), gmdate("j", $this->closure['start']), gmdate("Y", $this->closure['start']))) : gmdate("Y-m-d", mktime(gmdate("H", $this->closure['start']), gmdate("i", $this->closure['start']), $this->offset, gmdate("m", $this->closure['start']), gmdate("j", $this->closure['start']), gmdate("Y", $this->closure['start']))),
				'validThrough' => ($this->wp_date) ? wp_date("Y-m-d", mktime(gmdate("H", $this->closure['end']), gmdate("i", $this->closure['end']), 0, gmdate("m", $this->closure['end']), gmdate("j", $this->closure['end']) - 1, gmdate("Y", $this->closure['end']))) : gmdate("Y-m-d", mktime(gmdate("H", $this->closure['end']), gmdate("i", $this->closure['end']), $this->offset, gmdate("m", $this->closure['end']), gmdate("j", $this->closure['end']) - 1, gmdate("Y", $this->closure['end'])))
			);
		}
		
		if (isset($this->special) && is_array($this->special))
		{
			$included = array();
			
			foreach ($this->special as $timestamp => $a)
			{
				if (!empty($included) && in_array($timestamp, $included) !== FALSE || (isset($this->closure) && is_array($this->closure) && isset($this->closure['start']) && $this->closure['start'] != NULL && isset($this->closure['end']) && $this->closure['end'] != NULL && $timestamp >= $this->closure['start'] && $timestamp <= $this->closure['end']))
				{
					continue;
				}
				
				$range = FALSE;
				
				if ($a['closed'])
				{
					$hours = array(array('00:00', '00:00'));
				}
				elseif ($a['hours_24'])
				{
					$hours = array(array('00:00', '23:59'));
				}
				else
				{
					$hours = $a['hours'];
				}
				
				foreach ($this->special as $timestamp_check => $a_check)
				{
					if ($timestamp == $timestamp_check)
					{
						$range = 0;
						continue;
					}
					
					if (!is_numeric($range))
					{
						continue;
					}
					
					if (round($timestamp_check/DAY_IN_SECONDS) == round($timestamp/DAY_IN_SECONDS) + $range + 1 && $a['closed'] == $a_check['closed'] && $a['hours_24'] == $a_check['hours_24'] && $a['hours'] == $a_check['hours'])
					{
						$included[] = $timestamp_check;
						$range++;
					}
				}
				
				if (!is_numeric($range))
				{
					$range = 0;
				}
				
				$data['openingHoursSpecification'][] = array(
					'@type' => 'OpeningHoursSpecification',
					'opens' => (count($hours) == 3) ? array($hours[0][0], $hours[1][0], $hours[2][0]) : ((count($hours) == 2) ? array($hours[0][0], $hours[1][0]) : $hours[0][0]),
					'closes' => (count($hours) == 3) ? array($hours[0][1], $hours[1][1], $hours[2][1]) : ((count($hours) == 2) ? array($hours[0][1], $hours[1][1]) : $hours[0][1]),
					'validFrom' => ($this->wp_date) ? wp_date("Y-m-d", $timestamp) : gmdate("Y-m-d", $timestamp + $this->offset),
					'validThrough' => ($this->wp_date) ? wp_date("Y-m-d", mktime(gmdate("H", $timestamp), gmdate("i", $timestamp), 0, gmdate("m", $timestamp), gmdate("j", $timestamp) + $range, gmdate("Y", $timestamp))) : gmdate("Y-m-d", mktime(gmdate("H", $timestamp), gmdate("i", $timestamp), $this->offset, gmdate("m", $timestamp), gmdate("j", $timestamp) + $range, gmdate("Y", $timestamp)))
				);
			}
		}
		
		$data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$structured_data = '<script type="application/ld+json">' . PHP_EOL . '[ ' . $data . ' ]' . PHP_EOL . '</script>';
		wp_cache_add('structured_data', $structured_data, $this->class_name, HOUR_IN_SECONDS);
		
		if ($html)
		{
			return esc_html($data);
		}
		
		if ($string)
		{
			return $data;
		}
		
		echo $structured_data;
		return;
	}
		
	private function delete_logo()
	{
		// Delete the logo image for Structured Data
		
		$this->logo_image_id = NULL;
		$this->logo_image_url = NULL;
		update_option($this->prefix . 'logo', $this->logo_image_id);

		return TRUE;
	}
	
	private function set_logo($id = NULL)
	{
		// Set the logo image for Structured Data
		
		if (is_numeric($id))
		{
			update_option($this->prefix . 'logo', $id);
			$this->logo_image_id = $id;
		}
		else
		{
			$this->logo_image_id = get_option($this->prefix . 'logo');
		}
		
		if (is_numeric($this->logo_image_id))
		{
			$a = wp_get_attachment_image_src($this->logo_image_id, 'full');
			$this->logo_image_url = $a[0];
		}
		
		return TRUE;
	}
	
	public function server_ip()
	{
		// Retrieve an accurate IP Address for the web server
		
		if (is_string(wp_cache_get('server_ip', $this->class_name)))
		{
			return wp_cache_get('server_ip', $this->class_name);
		}
		
		if (function_exists('wp_remote_get') && function_exists('wp_remote_retrieve_body'))
		{
			$a = preg_split('/,/i', wp_remote_retrieve_body(wp_remote_get('http://ip6.me/api/')));
			
			if (preg_match('/^(?:(?:(?:(?:[1]?\d)?\d|2[0-4]\d|25[0-5])\.){3}(?:(?:[1]?\d)?\d|2[0-4]\d|25[0-5]))|(?:[\da-f]{1,4}(?:\:[\da-f]{1,4}){7})|(?:(?:[\da-f]{1,4}:){0,5}::(?:[\da-f]{1,4}:){0,5}[\da-f]{1,4})$/i', $a[1]))
			{
				$string = strtolower($a[1]);
				wp_cache_set('server_ip', $string, $this->class_name, HOUR_IN_SECONDS);
				return $string;
			}
			
			$string = preg_replace('/^.+ip\s+address[:\s]+\[?([^<>\s\b\]]+)\]?.*$/i', '$1', wp_remote_retrieve_body(wp_remote_get('http://checkip.dyndns.com/')));
			
			if (preg_match('/^(?:(?:(?:(?:[1]?\d)?\d|2[0-4]\d|25[0-5])\.){3}(?:(?:[1]?\d)?\d|2[0-4]\d|25[0-5]))|(?:[\da-f]{1,4}(?:\:[\da-f]{1,4}){7})|(?:(?:[\da-f]{1,4}:){0,5}::(?:[\da-f]{1,4}:){0,5}[\da-f]{1,4})$/i', $string))
			{
				$string = strtolower($string);
				wp_cache_set('server_ip', $string, $this->class_name, HOUR_IN_SECONDS);
				return $string;
			}
		}

		if (function_exists('gethostname') && function_exists('gethostbyname'))
		{
			$string = strtolower(gethostbyname(gethostname()));
			wp_cache_set('server_ip', $string, $this->class_name, HOUR_IN_SECONDS);
			return $string;
		}
		
		if (isset($_SERVER['SERVER_ADDR']))
		{
			wp_cache_set('server_ip', $_SERVER['SERVER_ADDR'], $this->class_name, HOUR_IN_SECONDS);
			return $_SERVER['SERVER_ADDR'];
		}
		
		return NULL;
	}
	
	public function data_hunter($format = 'array', $force = FALSE)
	{
		// Find all references to existing Google Reviews, API Key and Place ID
		
		if (!$force && is_string(get_option($this->prefix . 'time_format')) && get_option($this->prefix . 'time_format') != NULL)
		{
			switch ($format)
			{
			case 'boolean':
			case 'test':
				return FALSE;
			case 'json':
				return json_encode(NULL);
			default:
				break;
			}
			return array();
		}
		
		global $wpdb;
		
		$ret = array();
		
		if (get_option('google_business_reviews_rating_api_key') != NULL && get_option('google_business_reviews_rating_place_id') != NULL)
		{
			$ret = array(
				'api_key' => get_option('google_business_reviews_rating_api_key'),
				'place_id' => get_option('google_business_reviews_rating_place_id')
			);
		}

		if (empty($ret) && is_string(get_option('grw_google_api_key')) && $wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "grp_google_place'") == $wpdb->prefix . 'grp_google_place')
		{
			$id = $wpdb->get_var("SELECT `id` FROM `" . $wpdb->prefix . "grp_google_place` ORDER BY `id` DESC LIMIT 1");
			$place_id = $wpdb->get_var("SELECT `place_id` FROM `" . $wpdb->prefix . "grp_google_place` WHERE `id` = '" . esc_sql($id) . "' LIMIT 1");
			$ret = array(
				'api_key' => get_option('grw_google_api_key'),
				'place_id' => $place_id
			);
		}
		
		if (empty($ret) && is_array(get_option('wpfbr_google_options')))
		{
			$d = get_option('wpfbr_google_options');
			if ($d['select_google_api'] != 'default' && is_string($d['google_api_key']))
			{				
				$ret = array(
					'api_key' => $d['google_api_key'],
					'place_id' => (isset($d['google_location_set']['place_id'])) ? $d['google_location_set']['place_id'] : NULL
				);
			}
		}
		
		if (empty($ret) && is_array(get_option('googleplacesreviews_options')))
		{
			$d = get_option('googleplacesreviews_options');
			$w = array('place_id' => NULL);
			
			if (array_key_exists('google_places_api_key', $d))
			{
				$w = get_option('googleplacesreviews_options');
				if (is_array($w) && array_key_exists('place_id', $w))
				{
					$place_id = $w['place_id'];
				}
				
				$ret = array(
					'api_key' => $d['google_places_api_key'],
					'place_id' => $place_id
				);
			}
		}
		
		if (empty($ret) && is_string(get_option('google_places_api_key')))
		{
			$ret = array(
				'api_key' => get_option('google_places_api_key')
			);
		}
		
		switch ($format)
		{
		case 'boolean':
		case 'test':
			$ret = (!empty($ret) || !is_numeric(get_option($this->prefix . 'week_start')));
			break;
		case 'json':
			$ret['day_format'] = get_option('day_format');			
			$ret['time_format'] = get_option('time_format');			
			$ret['week_start'] = get_option('start_of_week');
			$ret = json_encode($ret);
			break;
		default:
			break;
		}
		
		return $ret;
	}
		
	public function open_change($range = NULL, $timestamp_return = FALSE, $set = TRUE)
	{
		// Find out current open status and time to change in seconds
		
		$range = (is_int($range) && $range >= 1 && $range <= 31) ? $range : count($this->days);
		$seconds_to_change = NULL;
		$open_now = FALSE;
		
		if ($set)
		{
			$this->set();
		}
		
		for ($i = 0; $i < $range; $i++)
		{
			if ($seconds_to_change > $range * DAY_IN_SECONDS)
			{
				break;
			}
			
			$d = ($this->today + $i) % 7;
			$timestamp = ($i == 0) ? $this->today_timestamp : $this->get_day_timestamp($i);
			$a = (!empty($this->closure) && $timestamp >= $this->closure['start'] && $timestamp < $this->closure['end']) ? array('closed' => TRUE) : ((is_array($this->special) && array_key_exists($timestamp, $this->special)) ? $this->special[$timestamp] : ((isset($this->regular[$d])) ? $this->regular[$d] : array()));
			$day_closed = (empty($a) || !empty($a) && isset($a['closed']) && $a['closed']);
			$day_hours_24 = (!$day_closed && isset($a['hours_24']) && $a['hours_24']);
			$day_hours = (!$day_closed && isset($a['hours']) && is_array($a['hours'])) ? $a['hours'] : array();
			$day_seconds = (!empty($day_hours)) ? $this->hours_filter($day_hours, 'seconds') : array();

			if ($i == 0)
			{
				if ($day_closed || $day_hours_24)
				{
					$seconds_to_change = ($timestamp + DAY_IN_SECONDS - $this->current_timestamp);
					$open_now = $day_hours_24;
					continue;
				}
				
				foreach ($day_seconds as $j => $h)
				{
					if ($j%2 == 1)
					{
						continue;
					}
					
					$start = $h - $this->offset;
					$end = $day_seconds[($j + 1)] - $this->offset;
					$timestamp_hours_start = ($this->wp_date) ? mktime(0, 0, $start, wp_date("m", $timestamp), wp_date("j", $timestamp), wp_date("Y", $timestamp)) : mktime(0, 0, $start, gmdate("m", $timestamp), gmdate("j", $timestamp), gmdate("Y", $timestamp));
					$timestamp_hours_end = ($this->wp_date) ? mktime(0, 0, $end, wp_date("m", $timestamp), wp_date("j", $timestamp), wp_date("Y", $timestamp)) : mktime(0, 0, $end, gmdate("m", $timestamp), gmdate("j", $timestamp), gmdate("Y", $timestamp));
					
					if ($timestamp_hours_start > $this->current_timestamp)
					{
						$seconds_to_change = ($timestamp_hours_start - $this->current_timestamp);
						break(2);
					}
					
					if ($this->current_timestamp >= $timestamp_hours_start && $this->current_timestamp < $timestamp_hours_end)
					{
						$seconds_to_change = ($timestamp_hours_end - $this->current_timestamp);
						$open_now = TRUE;
						break(2);
					}
					
					$next = (count($day_seconds) >= $j + 3) ? $day_seconds[($j + 2)] - $this->offset : NULL;
					
					if ($next == NULL)
					{
						$seconds_to_change = ($timestamp + DAY_IN_SECONDS - $this->current_timestamp);
						break;
					}
					
					$timestamp_hours_next = ($this->wp_date) ? mktime(0, 0, $next, wp_date("m", $timestamp), wp_date("j", $timestamp), wp_date("Y", $timestamp)) : mktime(0, 0, $next, gmdate("m", $timestamp), gmdate("j", $timestamp), gmdate("Y", $timestamp));

					if ($this->current_timestamp >= $timestamp_hours_end && $this->current_timestamp < $timestamp_hours_next)
					{
						$seconds_to_change = ($timestamp_hours_next - $this->current_timestamp);
						break(2);
					}
				}
				continue;
			}
			
			if ($day_closed || $day_hours_24)
			{
				if ($open_now == $day_closed)
				{
					break;
				}
				
				$seconds_to_change += DAY_IN_SECONDS;
				continue;
			}
			
			if (!is_array($day_seconds))
			{
				break;
			}

			$start = $day_seconds[0] - $this->offset;
			$end = $day_seconds[1] - $this->offset;
			$timestamp_hours_start = ($this->wp_date) ? mktime(0, 0, $start, wp_date("m", $timestamp), wp_date("j", $timestamp), wp_date("Y", $timestamp)) : mktime(0, 0, $start, gmdate("m", $timestamp), gmdate("j", $timestamp), gmdate("Y", $timestamp));
			$timestamp_hours_end = ($this->wp_date) ? mktime(0, 0, $end, wp_date("m", $timestamp), wp_date("j", $timestamp), wp_date("Y", $timestamp)) : mktime(0, 0, $end, gmdate("m", $timestamp), gmdate("j", $timestamp), gmdate("Y", $timestamp));
			
			if (!$open_now && $timestamp_hours_start > $this->current_timestamp)
			{
				$seconds_to_change += ($timestamp_hours_start - $timestamp);
			}
			elseif ($open_now && $this->current_timestamp >= $timestamp_hours_start && $this->current_timestamp < $timestamp_hours_end)
			{
				$seconds_to_change += ($timestamp_hours_end - $timestamp);
			}
			
			break;
		}
		
		if ($seconds_to_change > WEEK_IN_SECONDS * 2)
		{
			$seconds_to_change = WEEK_IN_SECONDS * 2;
		}
		
		if ($timestamp_return)
		{
			return array($open_now, $this->current_timestamp + $seconds_to_change);
		}

		return array($open_now, $seconds_to_change);
	}
	
	private function day_string($data, $day_format, $day_range_suffix = NULL, $day_format_length = NULL, $format = 'html', $preferences = NULL)
	{
		// Create a text string of day or day range from arguments
	
		if (is_array($preferences))
		{
			extract($preferences, EXTR_OVERWRITE);
		}

		$day = $data['day'];
		$replace_day_name = (isset($days) && is_array($days) && count($days) == 7);
		$day_name = ($replace_day_name) ? $days[$day] : $this->days[$day];
		$day_replacement_word = NULL;
		$consolidated = $data['consolidated'];
		$consolidated_first = $data['consolidated_first'];
		$consolidated_range = FALSE;
		
		if (array_key_exists('weekdays_text', $preferences) && $preferences['weekdays_text'] == NULL)
		{
			$weekdays_text = NULL;
		}
		
		if (array_key_exists('weekend_text', $preferences) && $preferences['weekend_text'] == NULL)
		{
			$weekend_text = NULL;
		}
		
		if (array_key_exists('everyday_text', $preferences) && $preferences['everyday_text'] == NULL)
		{
			$everyday_text = NULL;
		}
		
		if (preg_match($this->accepted_day_format, $day_format))
		{
			if (preg_match('/^(.+)\^(S)(.+)$/', $day_format, $m))
			{
				$day_format = $m[1] . '<\\s\\u\\p>' . $m[2] . '</\\s\\u\\p>' . $m[3];
			}
			
			$timestamp = (isset($data['date']) && is_numeric($data['date'])) ? $data['date'] : NULL;
			
			if ($timestamp == NULL)
			{
				for ($i = $this->week_start; $i < ($this->week_start + 7); $i++)
				{
					$timestamp = ($this->wp_date) ? mktime(0, 0, 0, wp_date("m", $this->week_start_timestamp), wp_date("j", $this->week_start_timestamp) - (($day == $this->week_start) ? 0 : 7) + $i, wp_date("Y", $this->week_start_timestamp)) : mktime(0, 0, 0, gmdate("m", $this->week_start_timestamp), gmdate("j", $this->week_start_timestamp) - (($day == $this->week_start) ? 0 : 7) + $i, gmdate("Y", $this->week_start_timestamp));
					if ($this->wp_date && wp_date("w", $timestamp) == $data['day'] || gmdate("w", $timestamp) == $data['day'])
					{
						break;
					}
				}
			}
			
			if (!$replace_day_name)
			{
				$day_name = ($this->wp_date) ? wp_date($day_format, $timestamp) : gmdate($day_format, $timestamp);
			}
		}

		if (is_array($this->data) && is_array($consolidated) && $consolidated_first)
		{
			if (count($this->data) == count($consolidated))
			{
				$day_replacement_word = (array_key_exists('everyday_text', $preferences)) ? $everyday_text : get_option($this->prefix . 'everyday_text');
			}
			elseif (count($this->weekdays) == count($consolidated) || count($this->weekend) == count($consolidated) && ((array_key_exists('weekdays_text', $preferences) && $weekdays_text != NULL || get_option($this->prefix . 'weekdays_text') != NULL) || (array_key_exists('weekend_text', $preferences) && $weekend_text != NULL || get_option($this->prefix . 'weekend_text') != NULL)))
			{
				$weekdays_check = 0;
				$weekend_check = 0;
				
				foreach ($consolidated as $timestamp)
				{
					$day_value = ($this->wp_date) ? wp_date("w", $timestamp) : gmdate("w", $timestamp);
					
					if (in_array($day_value, $this->weekdays) !== FALSE)
					{
						$weekdays_check++;
					}
					elseif (in_array($day_value, $this->weekend) !== FALSE)
					{
						$weekend_check++;
					}
				}
				
				if ($weekend_check == 0 && count($this->weekdays) == $weekdays_check)
				{
					$day_replacement_word = (array_key_exists('weekdays_text', $preferences) && ($weekdays_text == NULL || is_string($weekdays_text))) ? $weekdays_text : get_option($this->prefix . 'weekdays_text');
				}
				elseif ($weekdays_check == 0 && count($this->weekend) == $weekend_check)
				{
					$day_replacement_word = (array_key_exists('weekend_text', $preferences) && ($weekend_text == NULL || is_string($weekend_text))) ? $weekend_text : get_option($this->prefix . 'weekend_text');
				}
			}
		}
		
		$html = ($day_replacement_word == NULL && (is_numeric($day_format_length)) ? substr($day_name, 0, $day_format_length) : (($day_replacement_word != NULL) ? $day_replacement_word : $day_name));
		
		if ($day_replacement_word == NULL && is_array($consolidated) && $consolidated_first)
		{
			if (count($consolidated) >= ((isset($day_range_min)) ? $day_range_min : $this->day_range_min))
			{
				$day = $this->data[max($consolidated)]['day'];
				$timestamp = $this->data[max($consolidated)]['date'];
				$day_name = ($replace_day_name) ? $days[$day] : $this->days[$day];
				
				if (preg_match($this->accepted_day_format, $day_format))
				{
					if (preg_match('/^(.+)\^(S)(.+)$/', $day_format, $m))
					{
						$day_format = $m[1] . '<\\s\\u\\p>' . $m[2] . '</\\s\\u\\p>' . $m[3];
					}
					
					if ($timestamp == NULL)
					{
						for ($i = $this->week_start; $i < ($this->week_start + 7); $i++)
						{
							$timestamp = ($this->wp_date) ? mktime(0, 0, 0, wp_date("m", $this->week_start_timestamp), wp_date("j", $this->week_start_timestamp) - (($day == $this->week_start) ? 0 : 7) + $i, wp_date("Y", $this->week_start_timestamp)) : mktime(0, 0, 0, gmdate("m", $this->week_start_timestamp), gmdate("j", $this->week_start_timestamp) - (($day == $this->week_start) ? 0 : 7) + $i, gmdate("Y", $this->week_start_timestamp));
							if ($this->wp_date && wp_date("w", $timestamp) == $day || gmdate("w", $timestamp) == $day)
							{
								if (!$replace_day_name)
								{
									$day_name = ($this->wp_date) ? wp_date($day_format, $timestamp) : gmdate($day_format, $timestamp);
								}
								break;
							}
						}
					}
					elseif (!$replace_day_name)
					{
						$day_name = ($this->wp_date) ? wp_date($day_format, $timestamp) : gmdate($day_format, $timestamp);
					}
				}

				$day_range_separator = (isset($day_range_separator)) ? $day_range_separator : get_option($this->prefix . 'day_range_separator');
				
				if (preg_match('/^["]([^"]+)["]$/', $day_range_separator, $m))
				{
					$day_range_separator = $m[1];
				}
				
				$html .= $day_range_separator . ((is_numeric($day_format_length)) ? substr($day_name, 0, $day_format_length) : $day_name);
			}
			else
			{
				$day_separator = (isset($day_separator)) ? $day_separator : get_option($this->prefix . 'day_separator');
				
				if (preg_match('/^["]?([^|]+)\|([^|"]+)["]?$/', $day_separator, $m))
				{
					$day_separator_first = $m[1];
					$day_separator_last = $m[2];
				}
				elseif (preg_match('/^["]([^"]+)["]$/', $day_separator, $m))
				{
					$day_separator_first = $day_separator_last = $m[1];
				}
				else
				{
					$day_separator_first = $day_separator_last = $day_separator;
				}
				
				$i = 0;
				array_shift($consolidated);
				
				foreach ($consolidated as $timestamp)
				{
					$day = $this->data[$timestamp]['day'];
					$day_name = ($replace_day_name) ? $days[$day] : $this->days[$day];
					
					if (!$replace_day_name && preg_match($this->accepted_day_format, $day_format))
					{
						if (preg_match('/^(.+)\^(S)(.+)$/', $day_format, $m))
						{
							$day_format = $m[1] . '<\\s\\u\\p>' . $m[2] . '</\\s\\u\\p>' . $m[3];
						}
						
						$day_name = ($this->wp_date) ? wp_date($day_format, $timestamp) : gmdate($day_format, $timestamp);
					}
					
					$html .= (($i == count($consolidated) - 1) ? $day_separator_last : $day_separator_first) . ((is_numeric($day_format_length)) ? substr($day_name, 0, $day_format_length) : $day_name);
					$i++;
				}
			}
			
			$consolidated_range = TRUE;
		}
		
		$html .= (($day_suffix != NULL) ? $day_suffix : '');
		$html .= ($day_replacement_word == NULL && isset($day_range_suffix) && $day_range_suffix != NULL && $day_range_suffix != $day_suffix) ? $day_range_suffix : '';
		
		switch ($format)
		{
		case 'text':
			$html = strip_tags($html);
			break;
		case 'html':
		default:
			$html = esc_html($html);
			
			if (is_string($day_format) && preg_match('#' . preg_quote('<\\s\\u\\p>S</\\s\\u\\p>', '#') . '#', $day_format))
			{
				$html = preg_replace('#&lt;(sup)&gt;([^&]{1,10})&lt;(/sup)&gt;#i', '<$1>$2<$3>', $html);
			}
			break;
		}
		
		return $html;
	}
	
	private function hours_string($hours, $closed, $hours_24, $format = NULL, $preferences = NULL)
	{
		// Create a text string of opening hours from arguments
		
		$html = '';

		if ($closed)
		{
			if ($format == 'start' || $format == 'end' || $format == 'next')
			{
				return NULL;
			}
			
			return (is_array($preferences) && isset($preferences['closed'])) ? $preferences['closed'] : get_option($this->prefix . 'closed_text');
		}
				
		if ($hours_24 && (is_array($preferences) && array_key_exists('hours_24', $preferences) && $preferences['hours_24'] != NULL || !is_array($preferences) && get_option($this->prefix . '24_hours_text') != NULL || is_array($preferences) && !array_key_exists('hours_24', $preferences) && get_option($this->prefix . '24_hours_text') != NULL))
		{
			return (is_array($preferences) && array_key_exists('hours_24', $preferences) && $preferences['hours_24'] != NULL) ? $preferences['hours_24'] : get_option($this->prefix . '24_hours_text');
		}
		
		if ($hours_24 && !is_array($hours) || is_array($hours) && empty($hours))
		{
			if ($format == 'next')
			{
				return NULL;
			}
			
			$hours = array(
				0 => array(
					0 => '00:00',
					1 => '00:00'
				)
			);
		}
		
		$html = array();
		$time_group_separator_first = NULL;
		$time_group_separator_last = NULL;
		$time_format_key = (is_array($preferences) && isset($preferences['time_format'])) ? $preferences['time_format'] : ((get_option($this->prefix . 'time_format') != NULL) ? get_option($this->prefix . 'time_format') : '24_colon');
		$time_format = $this->time_formats[$time_format_key][1];
		$time_trim = (is_bool($this->time_formats[$time_format_key][2]) && $this->time_formats[$time_format_key][2]);
		$time_minute_replacement = (is_string($this->time_formats[$time_format_key][2])) ? $this->time_formats[$time_format_key][2] : NULL;
		$time_separator = (is_array($preferences) && isset($preferences['time_separator'])) ? $preferences['time_separator'] : get_option($this->prefix . 'time_separator');
		$time_group_separator = (is_array($preferences) && isset($preferences['time_group_separator'])) ? $preferences['time_group_separator'] : get_option($this->prefix . 'time_group_separator');
		$time_group_prefix = (is_array($preferences) && isset($preferences['time_group_prefix'])) ? $preferences['time_group_prefix'] : NULL;
		$time_group_suffix = (is_array($preferences) && isset($preferences['time_group_suffix'])) ? $preferences['time_group_suffix'] : NULL;
		
		if (preg_match('/^([^|]+)\|([^|]+)$/', $time_group_separator, $m))
		{
			$time_group_separator_first = $m[1];
			$time_group_separator_last = $m[2];
		}
		
		$hours = (is_array($hours)) ? array_values($hours) : array();
		
		if ($format == 'next')
		{
			list($open_now, $change_timestamp) = $this->open_change(1, TRUE);
			
			if ($open_now && count($hours) == 1 || count($hours) > 1)
			{
				$hours = array(
					0 => array(
						0 => (count($hours) == 1) ? $hours[0][1] : (($this->wp_date) ? wp_date("H:i", $change_timestamp) : gmdate("H:i", $change_timestamp)),
						1 => '00:00'
					)
				);
			}
		}
		
		foreach ($hours as $i => $a)
		{
			$a = array_values($a);
			
			if (count($a) != 2)
			{
				break;
			}
			
			if ($format == 'end' && $i < (count($hours) - 1))
			{
				continue;
			}

			list($hour_first, $minute_first, $hour_last, $minute_last) = preg_split('/[:-]/', implode('-', $a), 4);
			
			if ($format == 'end')
			{
				$hour_first = $hour_last;
				$minute_first = $minute_last;
			}
			
			if ($time_trim)
			{
				$html[] = (($time_group_prefix != NULL) ? $time_group_prefix : '')
				. ((intval($minute_first) == 0) ? preg_replace('/^(\d{1,2})[^\d]*[0]{2}(.*)$/', '$1$2', gmdate($time_format, mktime($hour_first, $minute_first, 0, 1, 1, 2020))) : gmdate($time_format, mktime($hour_first, $minute_first, 0, 1, 1, 2020)))
				. (($format != 'start' && $format != 'end' && $format != 'next') ? $time_separator
				. ((intval($minute_last) == 0) ? preg_replace('/^(\d{1,2})[^\d]*[0]{2}(.*)$/', '$1$2', gmdate($time_format, mktime($hour_last, $minute_last, 0, 1, 1, 2020))) : gmdate($time_format, mktime($hour_last, $minute_last, 0, 1, 1, 2020))) : '')
				. (($time_group_suffix != NULL) ? $time_group_suffix : '');
			}
			elseif ($time_minute_replacement != NULL)
			{
				$html[] = (($time_group_prefix != NULL) ? $time_group_prefix : '')
				. ((intval($minute_first) == 0) ? preg_replace('/^(\d{1,2}[^\d]*)[0]{2}(.*)$/', '$1' . $time_minute_replacement . '$2', gmdate($time_format, mktime($hour_first, $minute_first, 0, 1, 1, 2020))) : gmdate($time_format, mktime($hour_first, $minute_first, 0, 1, 1, 2020)))
				. (($format != 'start' && $format != 'end' && $format != 'next') ? $time_separator
				. ((intval($minute_last) == 0) ? preg_replace('/^(\d{1,2}[^\d]*)[0]{2}(.*)$/', '$1' . $time_minute_replacement . '$2', gmdate($time_format, mktime($hour_last, $minute_last, 0, 1, 1, 2020))) : gmdate($time_format, mktime($hour_last, $minute_last, 0, 1, 1, 2020))) : '')
				. (($time_group_suffix != NULL) ? $time_group_suffix : '');
			}
			else
			{
				$html[] = (($time_group_prefix != NULL) ? $time_group_prefix : '')
				. gmdate($time_format, mktime($hour_first, $minute_first, 0, 1, 1, 2020))
				. (($format != 'start' && $format != 'end' && $format != 'next') ? $time_separator
				. gmdate($time_format, mktime($hour_last, $minute_last, 0, 1, 1, 2020)) : '')
				. (($time_group_suffix != NULL) ? $time_group_suffix : '');
			}
			
			if ($format == 'start' || $format == 'next')
			{
				break;
			}
		}
		
		if (count($html) == 3 && $time_group_separator_first != NULL && $time_group_separator_last != NULL)
		{
			$html = $html[0] . $time_group_separator_first . $html[1] . $time_group_separator_last . $html[2];
		}
		else
		{
			$html = ($time_group_separator_last != NULL) ? implode($time_group_separator_last, $html) : implode($time_group_separator, $html);
		}
		
		switch ($format)
		{
		case 'text':
			$html = strip_tags($html);
			break;
		case 'html':
		default:
			$html = esc_html($html);
			break;
		}
		
		return $html;
	}

	public function set_api_key($api_key, $current_api_key = NULL)
	{
		// Sanitize data from API Key setting input
		
		if (strlen($api_key) < 10)
		{
			$api_key = NULL;
		}
		
		if ($current_api_key === NULL)
		{
			$current_api_key = get_option($this->prefix . 'api_key');
		}
		
		if ($current_api_key != $api_key)
		{
			wp_cache_delete('structured_data', $this->class_name);
			wp_cache_delete('google_result', $this->class_name);
			$this->api_key = $api_key;
			
			if ($api_key != NULL)
			{
				update_option($this->prefix . 'force', time() . '/0', 'yes');
			}
		}
		
		update_option($this->prefix . 'api_key', $api_key, 'no');
		
		return $api_key;
	}
	
	public function set_place_id($place_id, $current_place_id = NULL, $current_api_key = NULL)
	{
		// Sanitize data from Place ID setting input
		
		if (strlen($place_id) < 10)
		{
			$place_id = NULL;
		}
		
		if ($current_api_key === NULL)
		{
			$current_api_key = get_option($this->prefix . 'api_key');
		}
		
		if ($current_place_id === NULL)
		{
			$current_place_id = get_option($this->prefix . 'place_id');
		}
		
		if ($current_place_id != $place_id)
		{
			wp_cache_delete('structured_data', $this->class_name);
			wp_cache_delete('google_result', $this->class_name);
			update_option($this->prefix . 'google_result', NULL, 'no');
			update_option($this->prefix . 'structured_data', FALSE, 'yes');
			$this->place_id = $place_id;
			$this->google_data = array();
			$this->google_result = array();
			$this->google_result_valid = FALSE;
			
			if ($place_id != NULL)
			{
				update_option($this->prefix . 'force', time() . '/0', 'yes');
			}
			else
			{
				wp_cache_delete('structured_data', $this->class_name);
				wp_cache_delete('regular', $this->class_name);
				wp_cache_delete('special', $this->class_name);
			}
		}
		
		update_option($this->prefix . 'place_id', $place_id, 'no');

		return $place_id;
	}
	
	public function sanitize_separator($text, $trim = NULL)
	{
		// Sanitize data for time separator
		
		if (preg_match('/^"(.*)"$/', $text, $m))
		{
			$text = $m[1];
		}

		if (preg_match('/&[0-9a-z]+;/', $text))
		{
			$text = html_entity_decode($text);
		}
		
		if ($trim == 'left')
		{
			$text = ltrim($text);
		}
		elseif ($trim == 'right')
		{
			$text = rtrim($text);
		}
		
		return $text;
	}

	public function sanitize_array($data)
	{
		// Sanitize string or array data for weekdays and weekend
		
		if (is_array($data))
		{
			return array_filter($data);
		}
		
		if (is_string($data))
		{
			$a = preg_split('/,+\s*/i', $data);
			$data = array();
			
			foreach ($a as $d)
			{
				$data[] = (preg_match('/^\d+$/', $d)) ? intval($d) : (($d != NULL) ? $d : NULL);
			}
			
			return $data;
		}
		
		return array();
	}

	public function wp_ajax()
	{
		// Handle AJAX requests from Frontend
		
		$post = stripslashes_deep($_POST);
		$type = (isset($post['type'])) ? preg_replace('/[^\w_]/', '', strtolower($post['type'])) : NULL;
		$elements = (isset($post['elements']) && is_array($post['elements'])) ? $post['elements'] : array();
		$seconds_to_change = FALSE;
		$seconds_to_change_day = FALSE;
		$open_now = FALSE;
		$ret = array();

		switch($type)
		{
		case 'update':
			if (!is_array($elements) || empty($elements))
			{
				$ret = array(
					'elements' => array(),
					'open_now' => $open_now,
					'closed_now' => !$open_now,
					'success' => FALSE
				);
				break;
			}
			
			$ret = array(
				'elements' => array(),
				'open_now' => $open_now,
				'closed_now' => !$open_now,
				'success' => TRUE
			);
			
			foreach ($elements as $index => $a)
			{
				if (strtolower($a['action']) == 'refresh')
				{
					if (is_bool($seconds_to_change_day) && !$seconds_to_change_day)
					{
						list($open_now, $seconds_to_change_day) = $this->open_change(1);
					}
					
					$parameters = (isset($a['parameters']) && is_array($a['parameters'])) ? $a['parameters'] : array();
					$parameters['update'] = TRUE;
					$parameters['outer_tag'] = FALSE;
					$content = (isset($a['content']) && is_string($a['content']) && strlen($a['content']) > 1) ? $a['content'] : NULL;
					
					if ($content != NULL)
					{
						$parameters['shortcodes'] = FALSE;
					}
					
					$html = $this->wp_display($parameters, $content);
					
					if ($content != NULL && !isset($a['shortcodes']))
					{
						unset($parameters['shortcodes']);
					}
					
					unset($parameters['outer_tag']);
					
					$ret['elements'][$index] = array(
						'action' => 'refresh',
						'content' => $content,
						'html' => $html,
						'parameters' => $parameters,
						'reload' => (array_key_exists('reload', $parameters) && isset($reload) && (is_bool($reload) && $reload || is_string($reload) && preg_match('/^(?:t(?:rue)?|y(?:es)?|1|on|show)$/i', $reload))),
						'seconds_to_change' => $seconds_to_change_day
					);
					
					continue;
				}
				
				if (is_bool($seconds_to_change) && !$seconds_to_change)
				{
					list($open_now, $seconds_to_change) = $this->open_change();
				}
				
				$ret['elements'][$index] = array(
					'action' => 'update',
					'seconds_to_change' => $seconds_to_change
				);
			}
			
			if (is_bool($seconds_to_change) && is_bool($seconds_to_change_day) && !$seconds_to_change && !$seconds_to_change_day)
			{
				list($open_now, $seconds_to_change) = $this->open_change();
			}
			
			$ret['open_now'] = $open_now;
			$ret['closed_now'] = !$open_now;
			
			break;
		default:
			break;
		}
		
		echo json_encode($ret);
		wp_die();

		return;
	}

	public function wp_display($atts = NULL, $content = NULL, $shortcode = NULL)
	{
		// Display HTML from shortcodes
		
		global $wpdb;
		
		$this->set_localized_dates();
		
		$type_check = 'table';
		$shortcode_defaults = array(
			'class' => NULL,
			'class_strip' => NULL,
			'closed' => NULL,
			'closed_show' => NULL,
			'consolidation' => NULL,
			'count' => NULL,
			'day_format' => NULL,
			'day_format_special' => NULL,
			'day_range_min' => NULL,
			'day_range_separator' => NULL,
			'day_range_suffix' => NULL,
			'day_separator' => NULL,
			'day_separator' => NULL,
			'day_separator_last' => NULL,
			'day_range_suffix' => NULL,
			'day_range_suffix_special' => NULL,
			'days' => NULL,
			'end' => NULL,
			'everyday_text' => NULL,
			'errors' => NULL,
			'hours_24' => NULL,
			'id' => NULL,
			'outer_tag' => NULL,
			'regular' => NULL,
			'reload' => NULL,
			'shortcodes' => NULL,
			'span_strip' => NULL,
			'special' => NULL,
			'start' => NULL,
			'stylesheet' => NULL,
			'time_format' => NULL,
			'time_group_prefix' => NULL,
			'time_group_separator' => NULL,
			'time_group_suffix' => NULL,
			'time_separator' => NULL,
			'tag' => NULL,
			'type' => NULL,
			'update' => NULL,
			'week_start' => NULL,
			'weekdays_text' => NULL,
			'weekend_text' => NULL
		);
		
		$types = array(
			'br',
			'closed_now',
			'closednow',
			'closed-now',
			'line',
			'lines',
			'list',
			'new_line',
			'new_lines',
			'newline',
			'new-line',
			'newlines',
			'new-lines',
			'now',
			'ol',
			'open_now',
			'opennow',
			'open-now',
			'ordered_list',
			'orderedlist',
			'ordered-list',
			'p',
			'paragraph',
			'paragraphs',
			'sentence',
			'structured_data',
			'table',
			'text',
			'ul',
			'unordered_list',
			'unorderedlist',
			'unordered-list'
		);

		foreach ($types as $t)
		{
			$shortcode_defaults[$t] = 0;
		}
		
		$args = shortcode_atts($shortcode_defaults, $atts);
		
		if (!is_array($atts))
		{
			$atts = array();
		}
	
		if (array_key_exists(0, $atts) && in_array($atts[0], $types))
		{
			$type_check = $atts[0];
		}
		
		foreach ($args as $k => $v)
		{
			if (is_string($v) && (strlen($v) == 0 || $v == 'NULL' || $v == 'null'))
			{
				$args[$k] = NULL;
			}
		}

		extract($args, EXTR_SKIP);
		
		$html = '';		
		$day_preferences = array();
		$time_preferences = array();
		$update_data = array();

		$type = (is_string($type)) ? preg_replace('/[^\w_]/', '_', trim(strtolower($type))) : $type_check;
		$regular = (!array_key_exists('regular', $atts) || array_key_exists('regular', $atts) && (is_bool($regular) && $regular || is_string($regular) && !preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off|hide)$/i', $regular)));
		$special = (!array_key_exists('special', $atts) || array_key_exists('special', $atts) && (is_bool($special) && $special || is_string($special) && !preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off|hide)$/i', $special)));
		$id = (is_string($id)) ? preg_replace('/[^\w_-]/', '-', trim($id)) : NULL;
		$class = (is_string($class)) ? preg_replace('/[^\w _-]/', '-', trim(strtolower($class))) : NULL;
		$day_format_key = (is_string($day_format) && array_key_exists($day_format, $this->day_formats)) ? $day_format : NULL;
		$day_format_special_key = (is_string($day_format_special) && array_key_exists($day_format_special, $this->day_formats)) ? $day_format_special : NULL;
		$time_format_key = (is_string($time_format) && array_key_exists($time_format, $this->time_formats)) ? $time_format : ((get_option($this->prefix . 'time_format') != NULL) ? get_option($this->prefix . 'time_format') : '24_colon');
		$time_separator = (is_string($time_separator) && $time_separator != '') ? $time_separator : NULL;
		$time_group_separator = (is_string($time_group_separator) && $time_group_separator != '') ? $time_group_separator : NULL;
		$time_group_prefix = (is_string($time_group_prefix) && $time_group_prefix != '') ? ltrim($time_group_prefix) : NULL;
		$time_group_suffix = (is_string($time_group_suffix) && $time_group_suffix != '') ? rtrim($time_group_suffix) : NULL;
		$consolidation = (array_key_exists('consolidation', $atts) && ($consolidation == NULL || is_string($consolidation) && array_key_exists($consolidation, $this->consolidation_types))) ? (($consolidation == NULL) ? NULL : $consolidation) : get_option($this->prefix . 'consolidation');
		$days = (is_string($days) && preg_match('/^(?:[^,]+,\s*){6}[^,]+$/', $days)) ? preg_split('/,\s*/', $days, 7) : NULL;
		$day_separator = (is_string($day_separator) && $day_separator != '') ? $day_separator : NULL;
		$day_range_separator = (is_string($day_range_separator) && $day_range_separator != '') ? $day_range_separator : NULL;
		$day_suffix = (array_key_exists('day_suffix', $atts) && (is_null($day_suffix) || is_string($day_suffix))) ? rtrim($day_suffix) : NULL;
		$day_suffix_special = (array_key_exists('day_suffix_special', $atts) && (is_null($day_suffix_special) || is_string($day_suffix_special))) ? rtrim($day_suffix_special) : NULL;
		$day_range_suffix = (array_key_exists('day_range_suffix', $atts) && (is_null($day_range_suffix) || is_string($day_range_suffix))) ? rtrim($day_range_suffix) : get_option($this->prefix . 'day_range_suffix');
		$day_range_suffix_special = (array_key_exists('day_range_suffix_special', $atts) && (is_null($day_range_suffix_special) || is_string($day_range_suffix_special))) ? rtrim($day_range_suffix_special) : get_option($this->prefix . 'day_range_suffix_special');
		$day_range_min = (is_numeric($day_range_min) && $day_range_min >= 2 && $day_range_min <= 31) ? intval($day_range_min) : ((array_key_exists('hours_24', $atts)) ? NULL : $this->day_range_min);
		$weekdays_text = (array_key_exists('weekdays_text', $atts)) ? ((is_string($weekdays_text) && $weekdays_text != '') ? $weekdays_text : NULL) : get_option($this->prefix . 'weekdays_text');
		$weekend_text = (array_key_exists('weekend_text', $atts)) ? ((is_string($weekend_text) && $weekend_text != '') ? $weekend_text : NULL) : get_option($this->prefix . 'weekend_text');
		$everyday_text = (array_key_exists('everyday_text', $atts)) ? ((is_string($everyday_text) && $everyday_text != '') ? $everyday_text : NULL) : get_option($this->prefix . 'everyday_text');
		$closed = (is_string($closed) && $closed != '') ? $closed : get_option($this->prefix . 'closed_text');
		$closed_show = ((array_key_exists('closed_show', $atts) && (is_null($closed_show) || is_bool($closed_show) && $closed_show || is_string($closed_show) && !preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off|hide)$/i', $closed_show))) || !array_key_exists('closed_show', $atts) && get_option($this->prefix . 'closed_show', TRUE));
		$stylesheet = (is_null($stylesheet) || is_bool($stylesheet) && $stylesheet || is_string($stylesheet) && !preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off|hide)$/i', $stylesheet));
		$hours_24 = (array_key_exists('hours_24', $atts) && is_string($hours_24)) ? $hours_24 : NULL;
		$week_start = (is_numeric($week_start) && $week_start < 0 || is_string($week_start) && preg_match('/^(?:today|now|yesterday|-\d+)$/i', $week_start)) ? ((is_numeric($week_start) && $week_start == -2 || is_string($week_start) && preg_match('/^(?:yesterday|-2)$/i', $week_start)) ? $this->yesterday : $this->today) : ((is_numeric($week_start) && $week_start >= 0 && $week_start <= 6) ? intval($week_start) : $this->week_start);
		$start = (is_string($start) && preg_match('#^(\d{4})[ .-/](\d{1,2})[ .-/](\d{1,2})$#', $start, $m)) ? (($this->wp_date) ? mktime(0, 0, ($this->offset * -1), $m[2], $m[3], $m[1]) : mktime(0, 0, 0, $m[2], $m[3], $m[1])) : ((is_numeric($start) && $start >= -91 && $start <= 724) ? intval($start) : NULL);
		$end = (is_string($end) && preg_match('#^(\d{4})[ .-/](\d{1,2})[ .-/](\d{1,2})$#', $end, $m)) ? (($this->wp_date) ? mktime(0, 0, ($this->offset * -1), $m[2], $m[3] + 1, $m[1]) : mktime(0, 0, 0, $m[2], $m[3] + 1, $m[1])) : ((is_numeric($end) && $end >= -7 && $end <= 731) ? intval($end) : NULL);
		$count = (is_numeric($count) && $count >= 1 && $count <= ((!$regular && $special) ? 61 : 31)) ? intval($count) : NULL;
		$update_immediate = (array_key_exists('update', $atts) && is_string($update) && preg_match('/^(?:immediate|instant)(?:ly)?$/i', $update));
		$update = ($update_immediate || array_key_exists('update', $atts) && (is_bool($update) && $update || is_string($update) && preg_match('/^(?:t(?:rue)?|y(?:es)?|1|on|show)$/i', $update)));
		$reload = ($update && array_key_exists('reload', $atts) && (is_bool($reload) && $reload || is_string($reload) && preg_match('/^(?:t(?:rue)?|y(?:es)?|1|on|show)$/i', $reload)));
		$class_strip = (is_bool($class_strip) && $class_strip || is_string($class_strip) && preg_match('/^(?:t(?:rue)?|y(?:es)?|1|on|show)$/i', $class_strip));
		$span_strip = (is_bool($span_strip) && $span_strip || is_string($span_strip) && preg_match('/^(?:t(?:rue)?|y(?:es)?|1|on|show)$/i', $span_strip));
		$tag = (array_key_exists('tag', $atts) && is_string($tag) && preg_match('/^(?:span|div|p|section|aside|em|strong|abbr|label|h[123456]|li)$/', trim(strtolower($tag)))) ? preg_replace('/[^0-9a-z]/', '', trim(strtolower($tag))) : NULL;
		$outer_tag = (is_null($outer_tag) || is_bool($outer_tag) && $outer_tag || is_string($outer_tag) && !preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off|hide)$/i', $outer_tag));
		$shortcodes = (is_null($shortcodes) || is_bool($shortcodes) && $shortcodes || is_string($shortcodes) && !preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off|hide)$/i', $shortcodes));
		$errors = (is_bool($errors) && !$errors || is_string($errors) && preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off|hide)$/i', $errors)) ? FALSE : ((defined('WP_DEBUG')) ? WP_DEBUG : FALSE);
		
		if ($errors && (!array_key_exists($time_format_key, $this->time_formats) || get_option($this->prefix . 'time_format', NULL) == NULL))
		{
			$html = ($errors) ? '<p class="opening-hours error">' . __('Error: Please set <em>We’re Open!</em> preferences in Dashboard→Settings', 'opening-hours') . '</p>' : '';
			
			return $html;
		}
		
		if ($day_format != NULL && $day_format_key == NULL && preg_match($this->accepted_day_format, $day_format))
		{
			$day_format_length = NULL;
		}
		else
		{
			if ($day_format_key == NULL)
			{
				$day_format_key = get_option($this->prefix . 'day_format');
			}
			
			$day_format = $this->day_formats[$day_format_key][1];
			$day_format_length = $this->day_formats[$day_format_key][2];
		}
		
		if ($day_format_special != NULL && $day_format_special_key == NULL && preg_match($this->accepted_day_format, $day_format_special))
		{
			$day_format_special_length = NULL;
			$day_range_suffix_special = $day_range_suffix;
		}
		else
		{
			if ($day_format_special_key == NULL)
			{
				$day_format_special_key = get_option($this->prefix . 'day_format_special');
			}
			
			$day_format_special = ($day_format_special_key != NULL) ? $this->day_formats[$day_format_special_key][1] : $day_format;
			$day_range_suffix_special = ($day_format_special_key != NULL) ? $day_range_suffix_special : $day_range_suffix;
			$day_format_special_length = ($day_format_special_key != NULL) ? $this->day_formats[$day_format_special_key][2] : $day_format_length;
		}
		
		if ($regular != $special)
		{
			$day_preferences['regular'] = $regular;
			$day_preferences['special'] = $special;
		}
		
		if (is_numeric($week_start) && $week_start != $this->week_start)
		{
			$day_preferences['week_start'] = $week_start;
		}
		
		if (is_numeric($start))
		{
			$day_preferences['start'] = $start;
		}
		
		if (is_numeric($end))
		{
			$day_preferences['end'] = $end;
		}
		
		if (is_numeric($count))
		{
			$day_preferences['count'] = $count;
		}
		
		if ($week_start != $this->week_start)
		{
			$day_preferences['week_start'] = $week_start;
		}
		
		if (!$closed_show)
		{
			$day_preferences['closed_show'] = FALSE;
		}
		
		if ($consolidation != get_option($this->prefix . 'consolidation'))
		{
			$day_preferences['consolidation'] = $consolidation;
		}
		
		if ($day_separator != NULL && $day_separator != get_option($this->prefix . 'day_separator'))
		{
			$day_preferences['day_separator'] = $day_separator;
		}

		if ($day_range_separator != NULL && $day_range_separator != get_option($this->prefix . 'day_range_separator'))
		{
			$day_preferences['day_range_separator'] = $day_range_separator;
		}

		if ($day_range_min != $this->day_range_min)
		{
			$day_preferences['day_range_min'] = $day_range_min;
		}

		if ($weekdays_text != get_option($this->prefix . 'weekdays_text'))
		{
			$day_preferences['weekdays_text'] = $weekdays_text;
		}

		if ($weekend_text != get_option($this->prefix . 'weekend_text'))
		{
			$day_preferences['weekend_text'] = $weekend_text;
		}

		if ($everyday_text != get_option($this->prefix . 'everyday_text'))
		{
			$day_preferences['everyday_text'] = $everyday_text;
		}

		if ($time_format_key != NULL && $time_format_key != get_option($this->prefix . 'time_format'))
		{
			$time_preferences['time_format'] = $time_format_key;
		}

		if ($time_separator != NULL)
		{
			$time_preferences['time_separator'] = $time_separator;
		}

		if ($time_group_separator != NULL)
		{
			$time_preferences['time_group_separator'] = $time_group_separator;
		}

		if ($time_group_prefix != NULL)
		{
			$time_preferences['time_group_prefix'] = $time_group_prefix;
		}

		if ($time_group_suffix != NULL)
		{
			$time_preferences['time_group_suffix'] = $time_group_suffix;
		}

		if ($closed != NULL && $closed != get_option($this->prefix . 'closed_text'))
		{
			$time_preferences['closed'] = $closed;
		}

		if (array_key_exists('hours_24', $atts))
		{
			$time_preferences['hours_24'] = $hours_24;
		}
		
		$this->set($day_preferences);

		if (!is_array($this->data) || empty($this->data))
		{
			$html = ($errors) ? '<p class="opening-hours error">' . __('Error: No opening hours are available to display', 'opening-hours') . '</p>' : '';
			
			return $html;
		}
		
		if ($update && !$span_strip && !$class_strip)
		{
			list($open_now, $seconds_to_change) = $this->open_change(NULL, FALSE, FALSE);
			
			$update_data = array(
				'open_now' => $open_now,
				'closed_now' => !$open_now,
				'parameters' => $atts,
				'change' => (($seconds_to_change > 0) ? $seconds_to_change : 0),
				'immediate' => $update_immediate,
				'reload' => $reload
			);
		}

		if (!array_key_exists('day_separator', $day_preferences))
		{
			$day_preferences['day_separator'] = $day_separator;
		}

		if (!array_key_exists('day_range_separator', $day_preferences))
		{
			$day_preferences['day_range_separator'] = $day_range_separator;
		}

		if (!array_key_exists('day_suffix', $day_preferences))
		{
			$day_preferences['day_suffix'] = $day_suffix;
		}

		if (!array_key_exists('day_suffix_special', $day_preferences))
		{
			$day_preferences['day_suffix_special'] = $day_suffix_special;
		}

		if (!array_key_exists('day_range_min', $day_preferences))
		{
			$day_preferences['day_range_min'] = $day_range_min;
		}

		if (is_array($days) && count($days) == 7)
		{
			$day_preferences['days'] = $days;
		}
		
		switch ($type)
		{
		case 'schema':
		case 'structured-data':
		case 'structured_data':
			$html = '<pre>' . $this->structured_data('html') . '</pre>';
			break;
		case 'now':
		case 'opennow':
		case 'open_now':
		case 'open-now':
		case 'closednow':
		case 'closed_now':
		case 'closed-now':
			if ($content != NULL)
			{
				return $this->wp_display_open_now($atts, $content, !preg_match('/closed/i', $type));
			}
			$html = '';
			break;
		case 'txt':
		case 'text':
		case 'sentence':
			if ($content != NULL)
			{
				if (!$outer_tag || !$update_immediate && !$update)
				{
					return $this->wp_display_text($content, $time_preferences, $shortcodes);
				}
				
				$update_data['content'] = $content;

				$html = $this->wp_display_text($content, $time_preferences, FALSE);
				
				if ($tag == NULL)
				{
					$tag = ($this->phrasing_content($html)) ? 'span' : 'div';
				}
				
				return '<'
					. $tag . (($id != NULL) ? ' id="' . esc_attr($id) . '"' : '')
					. ' class="opening-hours open-text'
					. (($class != NULL) ? ' ' . $class : '')
					. ' update' . (($reload) ? ' reload' : '') . (($open_now) ? ' open-now' : ' closed-now') . '"'
					. ' data-data="' .  esc_attr(json_encode($update_data)) . '"'
					. '>' . $html . '</' . $tag . '>';
			}
			
			$first = TRUE;
			$day_separator = (is_string($day_separator) && $day_separator != '') ? $day_separator : '; ';
			$day_separator_last = (is_string($day_separator_last) && $day_separator_last != '') ? $day_separator_last : $day_separator;
			$day_end = (array_key_exists('day_end', $atts) && isset($day_end) && ($day_end == NULL || is_string($day_end))) ? $day_end : '.';
			$text = array();
			
			if (preg_match('/^([^|]+)\|([^|]+)$/', $day_separator, $m))
			{
				$day_separator = $m[1];
				$day_separator_last = $m[2];
			}	
			else
			{
				$day_separator_last = $day_separator;
			}
			
			if ($outer_tag)
			{
				$html .= '<' . (($tag != NULL) ? $tag : 'span')
				. (($id != NULL) ? ' id="' . esc_attr($id) . '"' : '')
				. ' class="opening-hours'
				. (($class != NULL) ? ' ' . $class : '')
				. (($update) ? ' update' . (($reload) ? ' reload' : '') . (($open_now) ? ' open-now' : ' closed-now')  : '') . '"'
				. (($update) ? ' data-data="' . (($update_data != NULL) ? esc_attr(json_encode($update_data)) : '') . '"' : '')
				. '>
';
			}

			foreach ($this->data as $timestamp => $a)
			{
				$day = $a['day'];
				$day_name = $this->days[$day];
				$day_alias = preg_replace('/[^0-9a-z-]/', '-', strtolower($day_name));
				$special = $a['special'];
				$count = $a['count'];
				$today = $a['today'];
				$tomorrow = $a['tomorrow'];
				$future = $a['future'];
				$closed = $a['closed'];
				$hours_24 = $a['hours_24'];
				$hours = (is_array($a['hours']) && !$closed && !$hours_24) ? $a['hours'] : array();
				$consolidated = $a['consolidated'];
				$consolidated_first = $a['consolidated_first'];
				
				if ($consolidation != NULL && is_array($consolidated) && !$consolidated_first)
				{
					continue;
				}
				
				if ($first)
				{
					$text[] = '<span class="day-name">' . $this->sentence_case(($special) ? $this->day_string($a, $day_format_special, $day_range_suffix_special, $day_format_special_length, 'html', $day_preferences) : $this->day_string($a, $day_format, $day_range_suffix, $day_format_length, 'html', $day_preferences), FALSE, FALSE) . '</span> '
					. '<span class="hours' . (($closed) ? ' closed' : (($hours_24) ? ' hours-24' : ((count($hours) > 1) ? ' group-' . count($hours) : ''))) . '">' . $this->hours_string($hours, $closed, $hours_24, 'html', $time_preferences) . '</span>';
					$first = FALSE;
					continue;
				}
				
				$text[] = '<span class="day-name">' . (($special) ? $this->day_string($a, $day_format_special, $day_range_suffix_special, $day_format_special_length, 'html', $day_preferences) : $this->day_string($a, $day_format, $day_range_suffix, $day_format_length, 'html', $day_preferences)) . '</span> '
				. '<span class="hours' . (($closed) ? ' closed' : (($hours_24) ? ' hours-24' : ((count($hours) > 1) ? ' group-' . count($hours) : ''))) . '">' . $this->hours_string($hours, $closed, $hours_24, 'html', $time_preferences) . '</span>';
			}
			
			if (count($text) > 2)
			{
				$text_last = array_pop($text);
				$html .= implode($day_separator, $text) . $day_separator_last . $text_last . $day_end;
			}
			else
			{
				$html .= implode($day_separator_last, $text) . $day_end;
			}
			
			if ($outer_tag)
			{
				$html .= '</' . (($tag != NULL) ? $tag : 'span') . '>
';
			}
			break;
		case 'br':
		case 'line':
		case 'lines':
		case 'newline':
		case 'newlines':
		case 'new-line':
		case 'new-lines':
		case 'new_line':
		case 'new_lines':
			if ($outer_tag)
			{
				$html = '<' . (($tag != NULL) ? $tag : 'p')
				. (($id != NULL) ? ' id="' . esc_attr($id) . '"' : '')
				. ' class="opening-hours'
				. (($class != NULL) ? ' ' . $class : '')
				. (($update) ? ' update' . (($reload) ? ' reload' : '') . (($open_now) ? ' open-now' : ' closed-now')  : '') . '"'
				. (($update) ? ' data-data="' . (($update_data != NULL) ? esc_attr(json_encode($update_data)) : '') . '"' : '')
				. '>
';
			}
			
			foreach ($this->data as $timestamp => $a)
			{
				$day = $a['day'];
				$day_name = $this->days[$day];
				$special = $a['special'];
				$count = $a['count'];
				$closed = $a['closed'];
				$hours_24 = $a['hours_24'];
				$hours = (is_array($a['hours']) && !$closed && !$hours_24) ? $a['hours'] : array();
				$consolidated = $a['consolidated'];
				$consolidated_first = $a['consolidated_first'];
				
				if ($consolidation != NULL && is_array($consolidated) && !$consolidated_first)
				{
					continue;
				}
				
				$html .= '	<span class="day-name">' . $this->sentence_case(($special) ? $this->day_string($a, $day_format_special, $day_range_suffix_special, $day_format_special_length, 'html', $day_preferences) : $this->day_string($a, $day_format, $day_range_suffix, $day_format_length, 'html', $day_preferences), FALSE, FALSE) . '</span> <span class="hours">' . $this->hours_string($hours, $closed, $hours_24, 'html', $time_preferences) . '</span>' . (($count < (count($this->data) - 1)) ? '<br>' . PHP_EOL : PHP_EOL);
			}
			
			if ($outer_tag)
			{
				$html .= '</' . (($tag != NULL) ? $tag : 'p') . '>
';
			}
			break;
		case 'list':
		case 'ol':
		case 'orderedlist':
		case 'ordered_list':
		case 'ordered-list':
		case 'p':
		case 'paragraph':
		case 'paragraphs':
		case 'ul':
		case 'unorderedlist':
		case 'unordered_list':
		case 'unordered-list':
			if ($outer_tag)
			{
				$outer_tag = (preg_match('/^[lou].+$/i', $type)) ? ((preg_match('/^[lu].+$/i', $type)) ? 'ul' : 'ol') : 'div';
	
				$html = '<' . $outer_tag
				. (($id != NULL) ? ' id="' . esc_attr($id) . '"' : '')
				. ' class="opening-hours'
				. (($class != NULL) ? ' ' . $class : '')
				. (($update) ? ' update' . (($reload) ? ' reload' : '') . (($open_now) ? ' open-now' : ' closed-now')  : '') . '"'
				. (($update) ? ' data-data="' . (($update_data != NULL) ? esc_attr(json_encode($update_data)) : '') . '"' : '')
				. '>
';
			}
			
			$inner_tag = (preg_match('/^[lou].+$/i', $type)) ? 'li' : 'p';
			
			foreach ($this->data as $timestamp => $a)
			{
				$day = $a['day'];
				$day_name = $this->days[$day];
				$day_alias = preg_replace('/[^0-9a-z-]/', '-', strtolower($day_name));
				$special = $a['special'];
				$count = $a['count'];
				$today = $a['today'];
				$tomorrow = $a['tomorrow'];
				$weekday = $a['weekday'];
				$weekend = $a['weekend'];
				$past = $a['past'];
				$future = $a['future'];
				$closed = $a['closed'];
				$hours_24 = $a['hours_24'];
				$hours = (is_array($a['hours']) && !$closed && !$hours_24) ? $a['hours'] : array();
				$consolidated = $a['consolidated'];
				$consolidated_first = $a['consolidated_first'];
				
				if ($consolidation != NULL && is_array($consolidated) && !$consolidated_first)
				{
					continue;
				}
				
				$html .= '	<' . $inner_tag . ' class="day '
				. esc_attr($day_alias)
				. (($special) ? ' special' : '')
				. (($today) ? ' today' : (($tomorrow) ? ' tomorrow' : ''))
				. (($future) ? ' future' : (($past) ? ' past' : ''))
				. (($weekday) ? ' weekday' : (($weekend) ? ' weekend' : ''))
				. (($closed) ? ' closed' : (($hours_24) ? ' hours-24' : ((count($hours) > 1) ? ' group-' . count($hours) : '')))
				. '">'
				. '<span class="day-name">' . $this->sentence_case(($special) ? $this->day_string($a, $day_format_special, $day_range_suffix_special, $day_format_special_length, 'html', $day_preferences) : $this->day_string($a, $day_format, $day_range_suffix, $day_format_length, 'html', $day_preferences), FALSE, FALSE) . '</span> <span class="hours">' . $this->hours_string($hours, $closed, $hours_24, 'html', $time_preferences) . '</span></' . $inner_tag . '>
';
			}

			if (is_string($outer_tag))
			{
				$html .= '</' . $outer_tag . '>
';
			}
			break;
		case 'html':
		case 'open_hours':
		case 'opening_hours':
		case 'open_hours_html':
		case 'opening_hours_html':
		default:
			if ($content != NULL)
			{
				if (!$outer_tag || !$update_immediate && !$update)
				{
					return $this->wp_display_text($content, $time_preferences, $shortcodes);
				}
				
				$update_data['content'] = $content;

				$html = $this->wp_display_text($content, $time_preferences, FALSE);
				
				if ($tag == NULL)
				{
					$tag = ($this->phrasing_content($html)) ? 'span' : 'div';
				}
				
				return '<'
					. $tag . (($id != NULL) ? ' id="' . esc_attr($id) . '"' : '')
					. ' class="opening-hours open-text'
					. (($class != NULL) ? ' ' . $class : '')
					. ' update' . (($reload) ? ' reload' : '') . (($open_now) ? ' open-now' : ' closed-now') . '"'
					. ' data-data="' .  esc_attr(json_encode($update_data)) . '"'
					. '>' . $html . '</' . $tag . '>';
			}
			
			if ($outer_tag)
			{
				$html = '<table'
				. (($id != NULL) ? ' id="' . esc_attr($id) . '"' : '')
				. ' class="opening-hours'
				. (($class != NULL) ? ' ' . $class : '')
				. (($update) ? ' update' . (($reload) ? ' reload' : '') . (($open_now) ? ' open-now' : ' closed-now')  : '') . '"'
				. (($update) ? ' data-data="' . (($update_data != NULL) ? esc_attr(json_encode($update_data)) : '') . '"' : '')
				. '>
';
			}
			
			foreach ($this->data as $timestamp => $a)
			{
				$day = $a['day'];
				$day_name = $this->days[$day];
				$day_alias = preg_replace('/[^0-9a-z-]/', '-', strtolower($day_name));
				$special = $a['special'];
				$count = $a['count'];
				$today = $a['today'];
				$tomorrow = $a['tomorrow'];
				$weekday = $a['weekday'];
				$weekend = $a['weekend'];
				$past = $a['past'];
				$future = $a['future'];
				$closed = $a['closed'];
				$hours_24 = $a['hours_24'];
				$hours = (is_array($a['hours']) && !$closed && !$hours_24) ? $a['hours'] : array();
				$consolidated = $a['consolidated'];
				$consolidated_first = $a['consolidated_first'];
				
				if ($consolidation != NULL && is_array($consolidated) && !$consolidated_first)
				{
					continue;
				}
				
				$html .= '	<tr class="day '
				. esc_attr($day_alias)
				. (($special) ? ' special' : '')
				. (($today) ? ' today' : (($tomorrow) ? ' tomorrow' : ''))
				. (($future) ? ' future' : (($past) ? ' past' : ''))
				. (($weekday) ? ' weekday' : (($weekend) ? ' weekend' : ''))
				. (($closed) ? ' closed' : (($hours_24) ? ' hours-24' : ((count($hours) > 1) ? ' group-' . count($hours) : '')))
				. '">
		<th class="day-name">' . $this->sentence_case(($special) ? $this->day_string($a, $day_format_special, $day_range_suffix_special, $day_format_special_length, 'html', $day_preferences) : $this->day_string($a, $day_format, $day_range_suffix, $day_format_length, 'html', $day_preferences), FALSE, FALSE) . '</th>
		<td class="hours">' . $this->hours_string($hours, $closed, $hours_24, 'html', $time_preferences) . '</td>
	</tr>
';
			}
			
			if ($outer_tag)
			{
				$html .= '</table>
';
			}
			
			break;
		}
		
		if ($span_strip && $class_strip)
		{
			$html = preg_replace('#</?span[^>]*>|\s+class=["\'][^"\'>]*["\']#i', '', $html);
		}
		elseif ($span_strip)
		{
			$html = preg_replace('#</?span[^>]*>#i', '', $html);
		}
		elseif ($class_strip)
		{
			$html = preg_replace('/\s+class=["\'][^"\'>]*["\']/i', '', $html);
		}

		return $html;
	}
	
	public function wp_display_open_now($atts = NULL, $content = NULL, $shortcode = NULL, $open = TRUE)
	{
		// Display conditional content based on open or closed now
		
		$this->set_localized_dates();
		
		$shortcode_defaults = array(
			'tag' => NULL,
			'id' => NULL,
			'class' => NULL,
			'class_strip' => NULL,
			'update' => NULL,
			'reload' => NULL,
			'hide' => NULL,
			'remove_html' => NULL,
			'shortcodes' => NULL
		);
		$data = NULL;
		$open = (boolean)$open;
		$args = shortcode_atts($shortcode_defaults, $atts);
		list($open_now, $seconds_to_change) = $this->open_change();
		
		if (!is_array($atts))
		{
			$atts = array();
		}

		foreach ($args as $k => $v)
		{
			if (is_string($v) && (strlen($v) == 0 || $v == 'NULL' || $v == 'null'))
			{
				$args[$k] = NULL;
			}
		}

		extract($args, EXTR_SKIP);
		
		$shortcodes = (is_null($shortcodes) || is_bool($shortcodes) && $shortcodes || is_string($shortcodes) && !preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off|hide)$/i', $shortcodes));
		
		if ($shortcodes && preg_match('#\[/?[a-z][^[\]]+\]#i', $content))
		{
			$content = do_shortcode($content);
		}
		
		$update_immediate = (array_key_exists('update', $atts) && is_string($update) && preg_match('/^(?:immediate|instant)(?:ly)?$/i', $update));
		$update = ($update_immediate || !array_key_exists('update', $atts) || array_key_exists('update', $atts) && (is_bool($update) && $update || is_string($update) && !preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off|hide)$/i', $update)));
		$reload = ($update && array_key_exists('reload', $atts) && (is_null($reload) || is_bool($reload) && $reload || is_string($reload) && !preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off|hide)$/i', $reload)));
		$hide = (is_null($hide) || is_bool($hide) && $hide || is_string($hide) && !preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off|show)$/i', $hide));
		$remove_html = (is_null($remove_html) || is_bool($remove_html) && $remove_html || is_string($remove_html) && !preg_match('/^(?:f(?:alse)?|no?(?:ne)?|0|off)$/i', $remove_html));
		$tag = (array_key_exists('tag', $atts) && is_string($tag) && preg_match('/^(?:span|div|p|section|aside|em|strong|abbr|label|h[123456]|li)$/', trim(strtolower($tag)))) ? preg_replace('/[^0-9a-z]/', '', trim(strtolower($tag))) : ((!array_key_exists('tag', $atts) && (!array_key_exists('update', $atts) || array_key_exists('update', $atts) && $update)) ? (($this->phrasing_content($content)) ? 'span' : 'div') : NULL);
		$id = (is_string($id)) ? preg_replace('/[^\w_-]/', '-', trim($id)) : NULL;
		$class = (is_string($class)) ? preg_replace('/[^\w _-]/', '-', trim(strtolower($class))) : NULL;
		$class_strip = (is_bool($class_strip) && $class_strip || is_string($class_strip) && preg_match('/^(?:t(?:rue)?|y(?:es)?|1|on|show)$/i', $class_strip));
		
		if ($tag != NULL)
		{
			$class = 'opening-hours-conditional'
			. (($class != NULL) ? ' ' . $class : '')
			. (($open) ? ' open' : ' closed')
			. (($hide) ? (($open == $open_now) ? ' show' : ' hide') : '')
			. (($update) ? ' update' : '')
			. (($reload) ? ' reload' : '');
			
			$data = array(
				'open' => $open,
				'open_now' => $open_now,
				'closed' => !$open,
				'closed_now' => !$open_now,
				'hide' => $hide,
				'remove_html' => $remove_html,
				'change' => (($seconds_to_change > 0) ? $seconds_to_change : 0),
				'reload' => $reload,
				'immediate' => $update_immediate,
				'html' => ($remove_html && $open != $open_now) ? $content : NULL
			);
		}
		
		$html = ($tag != NULL) ? '<' . $tag
		. (($id != NULL) ? ' id="' . esc_attr($id) . '"' : '')
		. ((!$class_strip && $class != NULL) ? ' class="' . esc_attr($class) . '"' : '')
		. ' data-data="' . (($data != NULL) ? esc_attr(json_encode($data)) : '') . '"'
		. '>' : '';
		
		if (!$remove_html || $open == $open_now)
		{
			$html .= $content;
		}

		$html .= (($tag != NULL) ? '</' . $tag . '>' : '');
		return $html;
	}
	
	public function wp_display_closed_now($atts = NULL, $content = NULL, $shortcode = NULL, $closed = TRUE)
	{
		// Display conditional content based on closed now
		
		return $this->wp_display_open_now($atts, $content, $shortcode, FALSE);
	}
	
	private function wp_display_text($content, $time_preferences = NULL, $shortcodes = TRUE)
	{
		// Display text with replacement codes for logic and variables
		
		if ($shortcodes && preg_match('#\[/?[a-z][^[\]]+\]#i', $content))
		{
			$content = do_shortcode($content);
		}
		
		if (!preg_match('/%[a-z _]+[ :]?%/i', $content) || !preg_match_all('/(?:(%[a-z _]+[ :]?%)|([^%]*[^%\s]+[^%]*))/i', $content, $match))
		{
			return $content;
		}

		$this->set_localized_dates();
		
		$text = array();
		$logic_variables = array();
		$if_open_now = -1;
		$if_closed_now = -1;
		$if_open_today = -1;
		$if_closed_today = -1;
		$if_open_later = -1;
		$if_not_open_later = -1;
		$if_open_tomorrow = -1;
		$if_closed_tomorrow = -1;
		$if_hours_24_today = -1;
		$if_not_hours_24_today = -1;
		$if_hours_24_tomorrow = -1;
		$if_not_hours_24_tomorrow = -1;
		$if_regular_today = -1;
		$if_not_regular_today = -1;
		$if_special_today = -1;
		$if_not_special_today = -1;
		$if_closure_today = -1;
		$if_not_closure_today = -1;
		$if_regular_tomorrow = -1;
		$if_not_regular_tomorrow = -1;
		$if_special_tomorrow = -1;
		$if_not_special_tomorrow = -1;
		$if_closure_tomorrow = -1;
		$if_not_closure_tomorrow = -1;
		$now = $this->hours_string(array(array(($this->wp_date) ? wp_date("H:i", $this->current_timestamp) : gmdate("H:i", $this->current_timestamp), '00:00')), NULL, NULL, 'start', $time_preferences);
		$today_closed = NULL;
		$today_hours_24 = NULL;
		$today_hours = NULL;
		$today_end = NULL;
		$today_text = NULL;
		$today_start_text = NULL;
		$today_end_text = NULL;
		$today_next_text = NULL;
		$today_name = NULL;
		$today_hours_type = NULL;
		$tomorrow_closed = NULL;
		$tomorrow_hours_24 = NULL;
		$tomorrow_hours = NULL;
		$tomorrow_start = NULL;
		$tomorrow_text = NULL;
		$tomorrow_start_text = NULL;
		$tomorrow_end_text = NULL;
		$tomorrow_name = NULL;
		$tomorrow_hours_type = NULL;
		
		list($open_now, $seconds_to_change) = $this->open_change();
		$closed_now = !$open_now;
		$open_later = NULL;
		
		foreach (array_keys($this->days) as $d)
		{
			if ($this->today != $d && $this->tomorrow != $d)
			{
				continue;
			}

			if ($this->today == $d)
			{
				$today_hours_type = (!empty($this->closure) && $this->today_timestamp >= $this->closure['start'] && $this->today_timestamp < $this->closure['end']) ? 'closure' : ((is_array($this->special) && array_key_exists($this->today_timestamp, $this->special)) ? 'special' : NULL);
				$a = ($today_hours_type == 'closure') ? array('closed' => TRUE) : (($today_hours_type == 'special') ? $this->special[$this->today_timestamp] : ((isset($this->regular[$d])) ? $this->regular[$d] : array()));
				$today_closed = (empty($a) || !empty($a) && isset($a['closed']) && $a['closed']);
				$today_hours_24 = (!$today_closed && isset($a['hours_24']) && $a['hours_24']);
				$today_hours = (!$today_closed && isset($a['hours']) && is_array($a['hours'])) ? $a['hours'] : array();
				$today_text = $this->hours_string($today_hours, $today_closed, $today_hours_24, 'text', $time_preferences);
				$today_start_text = $this->hours_string($today_hours, $today_closed, $today_hours_24, 'start', $time_preferences);
				$today_end_text = $this->hours_string($today_hours, $today_closed, $today_hours_24, 'end', $time_preferences);
				$today_name = $this->days[$d];
				$open_later = (!$today_closed && !$open_now && $this->current_timestamp + $seconds_to_change < $this->tomorrow_timestamp);
				$today_next_text = $this->hours_string($today_hours, (!$open_now && ($today_closed || !$open_later)), $today_hours_24, 'next', $time_preferences);
				continue;
			}

			$tomorrow_hours_type = (!empty($this->closure) && $this->tomorrow_timestamp >= $this->closure['start'] && $this->tomorrow_timestamp < $this->closure['end']) ? 'closure' : ((is_array($this->special) && array_key_exists($this->tomorrow_timestamp, $this->special)) ? 'special' : NULL);
			$a = ($tomorrow_hours_type == 'closure') ? array('closed' => TRUE) : (($tomorrow_hours_type == 'special') ? $this->special[$this->tomorrow_timestamp] : ((isset($this->regular[$d])) ? $this->regular[$d] : array()));
			$tomorrow_closed = (empty($a) || !empty($a) && isset($a['closed']) && $a['closed']);
			$tomorrow_hours_24 = (!$tomorrow_closed && isset($a['hours_24']) && $a['hours_24']);
			$tomorrow_hours = (!$tomorrow_closed && isset($a['hours']) && is_array($a['hours'])) ? $a['hours'] : array();
			$tomorrow_text = $this->hours_string($tomorrow_hours, $tomorrow_closed, $tomorrow_hours_24, 'text', $time_preferences);
			$tomorrow_start_text = $this->hours_string($tomorrow_hours, $tomorrow_closed, $tomorrow_hours_24, 'start', $time_preferences);
			$tomorrow_end_text = $this->hours_string($tomorrow_hours, $tomorrow_closed, $tomorrow_hours_24, 'end', $time_preferences);
			$tomorrow_name = $this->days[$d];
		}
		
		foreach ($match[0] as $i => $v)
		{
			if ($v == NULL)
			{
				continue;
			}
			
			$logic_variables[$i] = ($match[1][$i] != NULL) ? strtolower(preg_replace('/[^a-z_]/', '_', preg_replace('/^%\s*([a-z _]+)\s*%$/i', '$1', $match[1][$i]))) : NULL;
			$text[$i] = ($match[2][$i] != NULL) ? $match[2][$i] : NULL;
			
			if ($logic_variables[$i] == NULL && $text[$i] == NULL)
			{
				continue;
			}
		
			if ($i == 0 || ($i > 0 && isset($logic_variables[($i - 1)]) && preg_match('/^(?:if_.+|else|end(?:if)?)$/i', $logic_variables[($i - 1)])))
			{
				$text[$i] = preg_replace('/\s*([^\s].+)$/', '$1', $text[$i]);
			}
			
			if ($i == (count($match[0]) - 1) || ($i < (count($match[0]) - 2) && isset($match[1][($i + 1)]) && preg_match('/^%\s*(?:if_.+|else|end(?:if)?)\s*%$/i', $match[1][($i + 1)])))
			{
				$text[$i] = preg_replace('/(.+[^\s])\s*$/', '$1', $text[$i]);
			}
		}
		
		foreach ($logic_variables as $i => $lv)
		{
			if ($lv == 'end' || $lv == 'endif' || $lv == 'else')
			{
				if (is_numeric($if_open_now) && $if_open_now >= 0
					|| is_numeric($if_closed_now) && $if_closed_now >= 0
					|| is_numeric($if_open_today) && $if_open_today >= 0
					|| is_numeric($if_closed_today) && $if_closed_today >= 0
					|| is_numeric($if_open_later) && $if_open_later >= 0
					|| is_numeric($if_not_open_later) && $if_not_open_later >= 0
					|| is_numeric($if_open_tomorrow) && $if_open_tomorrow >= 0
					|| is_numeric($if_closed_tomorrow) && $if_closed_tomorrow >= 0
					|| is_numeric($if_hours_24_today) && $if_hours_24_today >= 0
					|| is_numeric($if_not_hours_24_today) && $if_not_hours_24_today >= 0
					|| is_numeric($if_hours_24_tomorrow) && $if_hours_24_tomorrow >= 0
					|| is_numeric($if_not_hours_24_tomorrow) && $if_not_hours_24_tomorrow >= 0
					|| is_numeric($if_regular_today) && $if_regular_today >= 0
					|| is_numeric($if_not_regular_today) && $if_not_regular_today >= 0
					|| is_numeric($if_special_today) && $if_special_today >= 0
					|| is_numeric($if_not_special_today) && $if_not_special_today >= 0
					|| is_numeric($if_closure_today) && $if_closure_today >= 0
					|| is_numeric($if_not_closure_today) && $if_not_closure_today >= 0
					|| is_numeric($if_regular_tomorrow) && $if_regular_tomorrow >= 0
					|| is_numeric($if_not_regular_tomorrow) && $if_not_regular_tomorrow >= 0
					|| is_numeric($if_special_tomorrow) && $if_special_tomorrow >= 0
					|| is_numeric($if_not_special_tomorrow) && $if_not_special_tomorrow >= 0
					|| is_numeric($if_closure_tomorrow) && $if_closure_tomorrow >= 0
					|| is_numeric($if_not_closure_tomorrow) && $if_not_closure_tomorrow >= 0)
				{
					$maxes = array_keys(
						array(
							$if_open_now,
							$if_closed_now,
							$if_open_today,
							$if_closed_today,
							$if_open_later,
							$if_not_open_later,
							$if_open_tomorrow,
							$if_closed_tomorrow,
							$if_hours_24_today,
							$if_not_hours_24_today,
							$if_hours_24_tomorrow,
							$if_not_hours_24_tomorrow,
							$if_regular_today,
							$if_not_regular_today,
							$if_special_today,
							$if_not_special_today,
							$if_closure_today,
							$if_not_closure_today,
							$if_regular_tomorrow,
							$if_not_regular_tomorrow,
							$if_special_tomorrow,
							$if_not_special_tomorrow,
							$if_closure_tomorrow,
							$if_not_closure_tomorrow
						),
						max(
							array(
								$if_open_now,
								$if_closed_now,
								$if_open_today,
								$if_closed_today,
								$if_open_later,
								$if_not_open_later,
								$if_open_tomorrow,
								$if_closed_tomorrow,
								$if_hours_24_today,
								$if_not_hours_24_today,
								$if_hours_24_tomorrow,
								$if_not_hours_24_tomorrow,
								$if_regular_today,
								$if_not_regular_today,
								$if_special_today,
								$if_not_special_today,
								$if_closure_today,
								$if_not_closure_today,
								$if_regular_tomorrow,
								$if_not_regular_tomorrow,
								$if_special_tomorrow,
								$if_not_special_tomorrow,
								$if_closure_tomorrow,
								$if_not_closure_tomorrow
							)
						)
					);
					$max = $maxes[0];
					
					if ($max == 1)
					{
						if ($lv == 'else')
						{
							$if_open_now = $if_closed_now;
						}
						
						$if_closed_now = -1;
					}
					elseif ($max == 2)
					{
						if ($lv == 'else')
						{
							$if_closed_today = $if_open_today;
						}
						
						$if_open_today = -1;
					}
					elseif ($max == 3)
					{
						if ($lv == 'else')
						{
							$if_open_today = $if_closed_today;
						}
						
						$if_closed_today = -1;
					}
					elseif ($max == 4)
					{
						if ($lv == 'else')
						{
							$if_not_open_later = $if_open_later;
						}
						
						$if_open_later = -1;
					}
					elseif ($max == 5)
					{
						if ($lv == 'else')
						{
							$if_open_later = $if_not_open_later;
						}
						
						$if_not_open_later = -1;
					}
					elseif ($max == 6)
					{
						if ($lv == 'else')
						{
							$if_closed_tomorrow = $if_open_tomorrow;
						}
						
						$if_open_tomorrow = -1;
					}
					elseif ($max == 7)
					{
						if ($lv == 'else')
						{
							$if_open_tomorrow = $if_closed_tomorrow;
						}
						
						$if_closed_tomorrow = -1;
					}
					elseif ($max == 8)
					{
						if ($lv == 'else')
						{
							$if_not_hours_24_today = $if_hours_24_today;
						}
						
						$if_hours_24_today = -1;
					}
					elseif ($max == 9)
					{
						if ($lv == 'else')
						{
							$if_hours_24_today = $if_not_hours_24_today;
						}
						
						$if_not_hours_24_today = -1;
					}
					elseif ($max == 10)
					{
						if ($lv == 'else')
						{
							$if_not_hours_24_tomorrow = $if_hours_24_tomorrow;
						}
						
						$if_hours_24_tomorrow = -1;
					}
					elseif ($max == 11)
					{
						if ($lv == 'else')
						{
							$if_hours_24_tomorrow = $if_not_hours_24_tomorrow;
						}
						
						$if_not_hours_24_tomorrow = -1;
					}
					elseif ($max == 12)
					{
						if ($lv == 'else')
						{
							$if_not_regular_today = $if_regular_today;
						}
						
						$if_regular_today = -1;
					}
					elseif ($max == 13)
					{
						if ($lv == 'else')
						{
							$if_regular_today = $if_not_regular_today;
						}
						
						$if_not_regular_today = -1;
					}
					elseif ($max == 14)
					{
						if ($lv == 'else')
						{
							$if_not_special_today = $if_special_today;
						}
						
						$if_special_today = -1;
					}
					elseif ($max == 15)
					{
						if ($lv == 'else')
						{
							$if_special_today = $if_not_special_today;
						}
						
						$if_not_special_today = -1;
					}
					elseif ($max == 16)
					{
						if ($lv == 'else')
						{
							$if_not_closure_today = $if_closure_today;
						}
						
						$if_closure_today = -1;
					}
					elseif ($max == 17)
					{
						if ($lv == 'else')
						{
							$if_closure_today = $if_not_closure_today;
						}
						
						$if_not_closure_today = -1;
					}
					elseif ($max == 18)
					{
						if ($lv == 'else')
						{
							$if_not_regular_tomorrow = $if_regular_tomorrow;
						}
						
						$if_regular_tomorrow = -1;
					}
					elseif ($max == 19)
					{
						if ($lv == 'else')
						{
							$if_regular_tomorrow = $if_not_regular_tomorrow;
						}
						
						$if_not_regular_tomorrow = -1;
					}
					elseif ($max == 20)
					{
						if ($lv == 'else')
						{
							$if_not_special_tomorrow = $if_special_tomorrow;
						}
						
						$if_special_tomorrow = -1;
					}
					elseif ($max == 21)
					{
						if ($lv == 'else')
						{
							$if_special_tomorrow = $if_not_special_tomorrow;
						}
						
						$if_not_special_tomorrow = -1;
					}
					elseif ($max == 22)
					{
						if ($lv == 'else')
						{
							$if_not_closure_tomorrow = $if_closure_tomorrow;
						}
						
						$if_closure_tomorrow = -1;
					}
					elseif ($max == 23)
					{
						if ($lv == 'else')
						{
							$if_closure_tomorrow = $if_not_closure_tomorrow;
						}
						
						$if_not_closure_tomorrow = -1;
					}
					else
					{
						if ($lv == 'else')
						{
							$if_closed_now = $if_open_now;
						}
						
						$if_open_now = -1;
					}
				}
				continue;
			}

			if ($lv == 'if_open' || $lv == 'if_open_now' || $lv == 'if_not_closed' || $lv == 'if_not_closed_now')
			{
				$if_open_now = $i;
				continue;
			}
			
			if ($lv == 'if_closed' || $lv == 'if_closed_now' || $lv == 'if_not_open' || $lv == 'if_not_open_now')
			{
				$if_closed_now = $i;
				continue;
			}
			
			if ($lv == 'if_open_today' || $lv == 'if_not_closed_today')
			{
				$if_open_today = $i;
				continue;
			}
			
			if ($lv == 'if_closed_today' || $lv == 'if_not_open_today')
			{
				$if_closed_today = $i;
				continue;
			}

			if ($lv == 'if_open_later' || $lv == 'if_open_later_today')
			{
				$if_open_later = $i;
				continue;
			}

			if ($lv == 'if_not_open_later' || $lv == 'if_not_open_later_today')
			{
				$if_not_open_later = $i;
				continue;
			}

			if ($lv == 'if_open_tomorrow' || $lv == 'if_not_closed_tomorrow')
			{
				$if_open_tomorrow = $i;
				continue;
			}

			if ($lv == 'if_closed_tomorrow' || $lv == 'if_not_open_tomorrow')
			{
				$if_closed_tomorrow = $i;
				continue;
			}
			
			if ($lv == 'if_24_hours_today' || $lv == 'if_hours_24_today' || $lv == 'if_24_hours' || $lv == 'if_hours_24')
			{
				$if_hours_24_today = $i;
				continue;
			}
			
			if ($lv == 'if_not_24_hours_today' || $lv == 'if_not_hours_24_today' || $lv == 'if_not_24_hours' || $lv == 'if_not_hours_24')
			{
				$if_not_hours_24_today = $i;
				continue;
			}
			
			if ($lv == 'if_24_hours_tomorrow' || $lv == 'if_hours_24_tomorrow')
			{
				$if_hours_24_tomorrow = $i;
				continue;
			}
			
			if ($lv == 'if_not_24_hours_tomorrow' || $lv == 'if_not_hours_24_tomorrow')
			{
				$if_not_hours_24_tomorrow = $i;
				continue;
			}
			
			if ($lv == 'if_regular_today' || $lv == 'if_regular_today')
			{
				$if_regular_today = $i;
				continue;
			}
			
			if ($lv == 'if_regular' || $lv == 'if_regular_today')
			{
				$if_regular_today = $i;
				continue;
			}

			if ($lv == 'if_not_regular' || $lv == 'if_not_regular_today')
			{
				$if_not_regular_today = $i;
				continue;
			}

			if ($lv == 'if_special' || $lv == 'if_special_today')
			{
				$if_special_today = $i;
				continue;
			}

			if ($lv == 'if_not_special' || $lv == 'if_not_special_today')
			{
				$if_not_special_today = $i;
				continue;
			}

			if ($lv == 'if_closure' || $lv == 'if_closure_today')
			{
				$if_closure_today = $i;
				continue;
			}

			if ($lv == 'if_not_closure' || $lv == 'if_not_closure_today')
			{
				$if_not_closure_today = $i;
				continue;
			}

			if ($lv == 'if_regular_tomorrow')
			{
				$if_regular_tomorrow = $i;
				continue;
			}

			if ($lv == 'if_not_regular_tomorrow')
			{
				$if_not_regular_tomorrow = $i;
				continue;
			}

			if ($lv == 'if_special_tomorrow')
			{
				$if_special_tomorrow = $i;
				continue;
			}

			if ($lv == 'if_not_special_tomorrow')
			{
				$if_not_special_tomorrow = $i;
				continue;
			}

			if ($lv == 'if_closure_tomorrow')
			{
				$if_closure_tomorrow = $i;
				continue;
			}

			if ($lv == 'if_not_closure_tomorrow')
			{
				$if_not_closure_tomorrow = $i;
				continue;
			}

			if ((is_numeric($if_open_now) && $if_open_now >= 0
				|| is_numeric($if_closed_now) && $if_closed_now >= 0
				|| is_numeric($if_open_today) && $if_open_today >= 0
				|| is_numeric($if_closed_today) && $if_closed_today >= 0
				|| is_numeric($if_open_later) && $if_open_later >= 0
				|| is_numeric($if_not_open_later) && $if_not_open_later >= 0
				|| is_numeric($if_open_tomorrow) && $if_open_tomorrow >= 0
				|| is_numeric($if_closed_tomorrow) && $if_closed_tomorrow >= 0
				|| is_numeric($if_hours_24_today) && $if_hours_24_today >= 0
				|| is_numeric($if_not_hours_24_today) && $if_not_hours_24_today >= 0
				|| is_numeric($if_hours_24_tomorrow) && $if_hours_24_tomorrow >= 0
				|| is_numeric($if_not_hours_24_tomorrow) && $if_not_hours_24_tomorrow >= 0
				|| is_numeric($if_regular_today) && $if_regular_today >= 0
				|| is_numeric($if_not_regular_today) && $if_not_regular_today >= 0
				|| is_numeric($if_special_today) && $if_special_today >= 0
				|| is_numeric($if_not_special_today) && $if_not_special_today >= 0
				|| is_numeric($if_closure_today) && $if_closure_today >= 0
				|| is_numeric($if_not_closure_today) && $if_not_closure_today >= 0
				|| is_numeric($if_regular_tomorrow) && $if_regular_tomorrow >= 0
				|| is_numeric($if_not_regular_tomorrow) && $if_not_regular_tomorrow >= 0
				|| is_numeric($if_special_tomorrow) && $if_special_tomorrow >= 0
				|| is_numeric($if_not_special_tomorrow) && $if_not_special_tomorrow >= 0
				|| is_numeric($if_closure_tomorrow) && $if_closure_tomorrow >= 0
				|| is_numeric($if_not_closure_tomorrow) && $if_not_closure_tomorrow >= 0)
				&& (is_bool($closed_now) && $closed_now && is_numeric($if_open_now) && $if_open_now >= 0
				|| is_bool($open_now) && $open_now && is_numeric($if_closed_now) && $if_closed_now >= 0
				|| is_bool($today_closed) && $today_closed && is_numeric($if_open_today) && $if_open_today >= 0
				|| is_bool($tomorrow_closed) && $tomorrow_closed && is_numeric($if_open_tomorrow) && $if_open_tomorrow >= 0
				|| is_bool($today_closed) && !$today_closed && is_numeric($if_closed_today) && $if_closed_today >= 0
				|| is_bool($tomorrow_closed) && !$tomorrow_closed && is_numeric($if_closed_tomorrow) && $if_closed_tomorrow >= 0
				|| is_bool($today_hours_24) && !$today_hours_24 && is_numeric($if_hours_24_today) && $if_hours_24_today >= 0
				|| is_bool($today_hours_24) && $today_hours_24 && is_numeric($if_not_hours_24_today) && $if_not_hours_24_today >= 0
				|| is_bool($tomorrow_hours_24) && !$tomorrow_hours_24 && is_numeric($if_hours_24_tomorrow) && $if_hours_24_tomorrow >= 0
				|| is_bool($tomorrow_hours_24) && $tomorrow_hours_24 && is_numeric($if_not_hours_24_tomorrow) && $if_not_hours_24_tomorrow >= 0
				|| is_bool($open_later) && !$open_later && is_numeric($if_open_later) && $if_open_later >= 0
				|| (is_bool($open_now) && $open_now || is_bool($open_later) && $open_later) && is_numeric($if_not_open_later) && $if_not_open_later >= 0
				|| $today_hours_type != NULL && is_numeric($if_regular_today) && $if_regular_today >= 0
				|| $today_hours_type == NULL && is_numeric($if_not_regular_today) && $if_not_regular_today >= 0
				|| $today_hours_type != 'special' && is_numeric($if_special_today) && $if_special_today >= 0
				|| $today_hours_type == 'special' && is_numeric($if_not_special_today) && $if_not_special_today >= 0
				|| $today_hours_type != 'closure' && is_numeric($if_closure_today) && $if_closure_today >= 0
				|| $today_hours_type == 'closure' && is_numeric($if_not_closure_today) && $if_not_closure_today >= 0
				|| $tomorrow_hours_type != NULL && is_numeric($if_regular_tomorrow) && $if_regular_tomorrow >= 0
				|| $tomorrow_hours_type == NULL && is_numeric($if_not_regular_tomorrow) && $if_not_regular_tomorrow >= 0
				|| $tomorrow_hours_type != 'special' && is_numeric($if_special_tomorrow) && $if_special_tomorrow >= 0
				|| $tomorrow_hours_type == 'special' && is_numeric($if_not_special_tomorrow) && $if_not_special_tomorrow >= 0
				|| $tomorrow_hours_type != 'closure' && is_numeric($if_closure_tomorrow) && $if_closure_tomorrow >= 0
				|| $tomorrow_hours_type == 'closure' && is_numeric($if_not_closure_tomorrow) && $if_not_closure_tomorrow >= 0))
			{
				$text[$i] = NULL;
				continue;
			}
			
			if ($lv == 'now' || $lv == 'current' || $lv == 'current_time' || $lv == 'currenttime')
			{
				$text[$i] = $now;
				continue;
			}
			
			if ($lv == 'today' || $lv == 'today_name' || $lv == 'today_day_name')
			{
				$text[$i] = $today_name;
				continue;
			}
			
			if ($lv == 'tomorrow' || $lv == 'tomorrow_name' || $lv == 'tomorrow_day_name')
			{
				$text[$i] = $tomorrow_name;
				continue;
			}
			
			if ($lv == 'hours_today' || $lv == 'hours_tomorrow')
			{
				$text[$i] = ($lv == 'hours_tomorrow') ? $tomorrow_text : $today_text;
				continue;
			}
			
			if ($lv == 'today_start')
			{
				$text[$i] = $today_start_text;
				continue;
			}
			
			if ($lv == 'today_end')
			{
				$text[$i] = $today_end_text;
				continue;
			}
			
			if ($lv == 'today_next')
			{
				$text[$i] = $today_next_text;
				continue;
			}
			
			if ($lv == 'tomorrow_start')
			{
				$text[$i] = $tomorrow_start_text;
				continue;
			}
			
			if ($lv == 'tomorrow_end')
			{
				$text[$i] = $tomorrow_end_text;
				continue;
			}
			
			if ($lv == 'days_status' || $lv == 'days_status_padded' || $lv == 'days_change' || $lv == 'days_change_padded' || $lv == 'days' || $lv == 'days_padded')
			{
				$text[$i] = ($lv == 'days_padded' || $lv == 'days_status_padded' || $lv == 'days_change_padded') ? str_pad(floor($seconds_to_change / DAY_IN_SECONDS), 2, '0', STR_PAD_LEFT) : floor($seconds_to_change / DAY_IN_SECONDS);
				continue;
			}
			
			if ($lv == 'hours' || $lv == 'hours_padded')
			{
				$text[$i] = ($lv == 'hours_padded') ? str_pad(floor($seconds_to_change / HOUR_IN_SECONDS), 2, '0', STR_PAD_LEFT) : floor($seconds_to_change / HOUR_IN_SECONDS);
				continue;
			}
			
			if ($lv == 'hours_divisor' || $lv == 'hours_divisor_padded')
			{
				$text[$i] = ($lv == 'hours_divisor_padded') ? str_pad((floor($seconds_to_change / HOUR_IN_SECONDS) % HOUR_IN_SECONDS), 2, '0', STR_PAD_LEFT) : (floor($seconds_to_change / HOUR_IN_SECONDS) % HOUR_IN_SECONDS);
				continue;
			}
			
			if ($lv == 'minutes' || $lv == 'minutes_padded')
			{
				$text[$i] = ($lv == 'minutes_padded') ? str_pad(floor($seconds_to_change / MINUTE_IN_SECONDS), 2, '0', STR_PAD_LEFT) : floor($seconds_to_change / MINUTE_IN_SECONDS);
				continue;
			}
			
			if ($lv == 'minutes_divisor' || $lv == 'minutes_divisor_padded')
			{
				$text[$i] = ($lv == 'minutes_divisor_padded') ? str_pad((floor($seconds_to_change / MINUTE_IN_SECONDS) % MINUTE_IN_SECONDS), 2, '0', STR_PAD_LEFT) : (floor($seconds_to_change / MINUTE_IN_SECONDS) % MINUTE_IN_SECONDS);
				continue;
			}
			
			if ($lv == 'seconds' || $lv == 'seconds_padded')
			{
				$text[$i] = ($lv == 'seconds_padded') ? str_pad($seconds_to_change, 2, '0', STR_PAD_LEFT) : $seconds_to_change;
				continue;
			}
			
			if ($lv == 'seconds_divisor' || $lv == 'seconds_divisor_padded')
			{
				$text[$i] = ($lv == 'seconds_divisor_padded') ? str_pad(($seconds_to_change % MINUTE_IN_SECONDS), 2, '0', STR_PAD_LEFT) : ($seconds_to_change % MINUTE_IN_SECONDS);
				continue;
			}
			
			if ($lv == 'space' || $lv == 'nbsp')
			{
				$text[$i] = ' ';
				continue;
			}
			
			if ($lv == 'comma')
			{
				$text[$i] = ',';
				continue;
			}
			
			if ($lv == 'semicolon' || $lv == 'semi_colon')
			{
				$text[$i] = ';';
				continue;
			}

			if ($lv == 'colon')
			{
				$text[$i] = ':';
				continue;
			}

			if ($lv == 'query' || $lv == 'question' || $lv == 'querymark' || $lv == 'questionmark' || $lv == 'question_mark' || $lv == 'query_mark')
			{
				$text[$i] = '?';
				continue;
			}

			if ($lv == 'exclamation' || $lv == 'exclamationmark' || $lv == 'exclamation_mark')
			{
				$text[$i] = '!';
				continue;
			}

			if ($lv == 'fullstop' || $lv == 'full_stop' || $lv == 'stop' || $lv == 'period' || $lv == 'dot' || $lv == 'point')
			{
				$text[$i] = '.';
				continue;
			}
			
			if ($lv == 'percent' || $lv == 'percentage')
			{
				$text[$i] = '%';
				continue;
			}
			
			if ($lv != NULL)
			{
				$text[$i] = '%' . $lv . '%';
				continue;
			}
		}
		
		$text = array_filter($text, 'strlen');
		$text = implode('', $text);
		
		return $text;
	}
	
	public function phrasing_content($html)
	{
		// Check if HTML string only contains phrasing content
		
		if (preg_match('#</?(?:div|p)(?:\s*|[^a-z][^>]*)>#i', $html))
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	public function sentence_case($string, $force = FALSE, $add_spaces = TRUE)
	{
		// Set text to use sentence case
		
		$ret = '';
		$sentences = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		
		foreach ($sentences as $key => $sentence)
		{
			if ($add_spaces)
			{
				$sentence = trim($sentence);
			}
			
			if ($force)
			{
				$sentence = strtolower($sentence);
			}
			
			$ret .= (($key & 1) == 0) ? ucfirst($sentence) : $sentence . (($add_spaces) ? ' ' : '');
		}
		
		return trim($ret);
	}
	
	public function loaded()
	{
		// Load languages
		
		load_plugin_textdomain('opening-hours', FALSE, basename(dirname(__FILE__)) . '/languages');

		return TRUE;
	}
	
	public function widget()
	{
		// Initiate widget
		
		register_widget('we_are_open_widget');
	}
	
}

new we_are_open;
