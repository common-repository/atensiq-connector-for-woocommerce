<?php
namespace WCAT;

class Settings {

	var $settings;
	var $sections;
	var $page_slug;
	var $settings_group;
	var $lang;

	function __construct($settings, $sections){
        $this->settings = wp_parse_args($settings, array(
            'prefix' => 'wcat_',
            'menu_page' => 'woocommerce',
            'menu_title' => __('Atensiq', 'wcat'),
            'page_title' => __('Atensiq Restaurant menu & Connector for WooCommerce', 'wcat'),
            'btn_title' => __('Update', 'wcat')
        ));
        
        $this->sections = $sections;
        if(!$this->sections) return;
        
		$this->page_slug = $this->settings['prefix'] . 'page';
		$this->settings_group = $this->settings['prefix'] . 'settings_group';
		//$this->lang = $this->define_lang();
		$this->lang = '_en';
        
		add_action('plugins_loaded', array($this, 'define_lang'));
		add_action('admin_menu', array($this, 'add_submenu_page'), 100);
		add_action('admin_init', array($this, 'add_fields'));
	}

	function define_lang(){
		if(defined('ICL_LANGUAGE_CODE')){
			return $this->lang = '_' . ICL_LANGUAGE_CODE;
		}else{
			return $this->lang = '_' . substr(get_locale(), 0, 2);
		}
		return $this->lang = '_en';
	}

	function get_option_full_id($id, $lang=null){
		$lang = isset($lang) ? '_' . $lang : $this->lang; 
		return $this->settings['prefix'] . $id . $lang;
	}

	private function get_section_full_id($id){
		return $this->settings['prefix'] . 'section_' . $id;
	}

	function get_option($name, $lang=null){
		$value = get_option($this->get_option_full_id($name, $lang));
		if($value == '' || $value === false){
			foreach($this->sections as $section){
				if(isset($section['fields'][$name]['default']) && $section['fields'][$name]['default'] != ''){
					return $section['fields'][$name]['default'];
				}
			}
		}
		return $value;
	}

	function add_submenu_page(){
		add_submenu_page(
			$this->settings['menu_page'],
			$this->settings['page_title'],
			$this->settings['menu_title'],
			'manage_options',
			$this->page_slug,
			array($this, 'display_options_page')
		);
	}

	function display_options_page(){ ?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h1><?php echo $this->settings['page_title']; ?></h1>
			<?php do_action($this->settings['prefix'] . 'before_form'); ?>
			<form action="options.php" method="POST" enctype="multipart/form-data">
				<?php
				do_settings_sections($this->page_slug);
				settings_fields($this->settings_group);
				submit_button($this->settings['btn_title']);
				?>
			</form>
			<?php do_action($this->settings['prefix'] . 'after_form'); ?>
		</div>
		<?php
	}

	function add_fields(){
		foreach($this->sections as $section_id => $section){
			add_settings_section(
				$this->get_section_full_id($section_id),
				$section['title'],
				array($this, 'display_section'),
				$this->page_slug
			);
            //do_action($this->settings['prefix'] . 'after_section_' . $section_id);
			foreach($section['fields'] as $field_id => $field){
				add_settings_field(
					$this->get_option_full_id($field_id),
					$field['title'],
					array($this, 'display_field'),
					$this->page_slug,
					$this->get_section_full_id($section_id),
					array('field_id' => $field_id, 'field_config' => $field)
				);
                //do_action($this->settings['prefix'] . 'after_field_' . $section_id);
				register_setting(
					$this->settings_group,
					$this->get_option_full_id($field_id)
				);
			}
		}
	}

	function display_section($section){
		if(isset($this->sections[$section['id']]['description']) && $this->sections[$section['id']]['description'] != ''){ ?>
			<?php echo wpautop($this->section[$section['id']]['description']); ?>
		<?php
		}
        do_action($section['id'] . '_after');
	}

	function display_field($args){
		$option_name = $this->get_option_full_id($args['field_id']);
		switch($args['field_config']['type']){
			case 'text': ?>
				<input name="<?php echo $option_name; ?>" type="text" class="regular-text" value="<?php echo $this->get_option($args['field_id']); ?>" />
				<?php break;
			case 'textarea': ?>
				<textarea name="<?php echo $option_name; ?>" class="large-text" cols="50" rows="5"><?php echo $this->get_option($args['field_id']); ?></textarea>
				<?php break;
			case 'select': ?>
				<select name="<?php echo $option_name; ?>">
					<?php foreach($args['field_config']['options'] as $option){ ?>
					<option value="<?php echo $option; ?>" <?php selected($this->get_option($args['field_id']), $option); ?>><?php echo $option; ?></option>
					<?php } ?>
				</select>
				<?php break;
			case 'checkbox': ?>
				<input name="<?php echo $option_name; ?>" type="checkbox" value="1" <?php checked($this->get_option($args['field_id']), 1); ?>/>
				<?php break;
		}
		if(isset($args['field_config']['description']) && $args['field_config']['description'] != ''){ ?>
			<p class="description"><?php echo $args['field_config']['description']; ?></p>
		<?php
		}
	}
}
