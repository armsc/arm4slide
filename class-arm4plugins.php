<?php
/**
 * Class name: arm4plugins
 * Author: Armando Soriano
 * Year: 2013
 * Version: 1.0 (first release)
 * URL: http://arm4design.com
 * Description: This class manages the creation, setup and validation of the options page(s) and the global admin area in all arsplugin's plugins.
**/

/*!
 * @class arm4plugins
 * @abstract This class manages the creation, setup and validation of the options page(s) in all arm4plugin's plugins
 */
class arm4plugins
{
	/*! @const ARM4_PLUGIN_NAME string Plugin name */
	const ARM4_PLUGIN_NAME = 'arm4slide';
	/*! @const ARM4_PLUGIN_VER string Plugin version */
	const ARM4_PLUGIN_VER = '1.0';
	/*! @var DEFAULTS array The defaults values */
	private $defaults = array();
	
	/*!
	 * @function __construct
	 * @abstract Constructor. Prepares the class doing the calls to setup the options and defines the admin workspace.
	 */
	public function __construct() 
	{		
		// Actions & filters
		$this->actions();
		$this->filters();
	}
	/*!
	 * @function dataDefaults
	 * @abstract Defines default values for first initialization.
	 */
	public function dataDefaults()
	{
		// Stores defaults values
		$this->defaults = array(
			'plugin_name' => $this->ARM4_PLUGIN_NAME,
			'plugin_version' => $this->ARM4_PLUGIN_VER,
			'wrapper-id' => 'slider',
			'wrapper-width' => '800',
			'wrapper-height' => '350',
			'wrapper-texture' => '',
			'wrapper-background' => '',
			'wrapper-shadow-x' => '5',
			'wrapper-shadow-y' => '5',
			'wrapper-shadow-blur' => '5',
			'wrapper-shadow-color' => '#000',
			'wrapper-border-radius' => '20',
			'wrapper-padding' => '20',
			'slides-class' => 'slides',
			'slides-count' => '3',
			'slides-which' => 'post',
			'slides-which-cats' => array(),
			'slides-shadow-x' => '3',
			'slides-shadow-y' => '3',
			'slides-shadow-blur' => '3',
			'slides-shadow-color' => '#000',
			'slides-border-radius' => '20',
			'captions-class' => 'slides-captions',
			'title-pos' => 'right',
			'title-fontcolor' => '#FFF',
			'title-size' => '24',
			'title-font-shadow-x' => '',
			'title-font-shadow-y' => '',
			'title-font-shadow-blur' => '',
			'title-font-shadow-color' => '',
			'title-texture' => '',
			'title-background' => '#000',
			'title-shadow-x' => '',
			'title-shadow-y' => '',
			'title-shadow-blur' => '',
			'title-shadow-color' => '',
			'title-padding' => '20',
			'content-pos' => 'left',
			'content-width' => '60',
			'content-fontcolor' => '#BBB',
			'content-size' => '16',
			'content-font-shadow-x' => '',
			'content-font-shadow-y' => '',
			'content-font-shadow-blur' => '',
			'content-font-shadow-color' => '',
			'content-texture' => '',
			'content-background' => '#000',
			'content-shadow-x' => '',
			'content-shadow-y' => '',
			'content-shadow-blur' => '',
			'content-shadow-color' => '',
			'content-padding' => '20',
			'control-class' => 'slides-control',
			'control-type' => 'circles',
			'control-pos' => 'bottomright',
			/*'control-width' => '300',
			'control-height' => '50',*/
			'control-texture' => '',
			'control-background' => '',
			'control-color' => '',
			'control-shadow-x' => '',
			'control-shadow-y' => '',
			'control-shadow-blur' => '',
			'control-shadow-color' => '',
			'control-border-radius' => '',
			'control-margin' => '10',
			'control-padding' => '5',
			'firsttime' => '1200',
			'movetime' => '800',
			'movetype-out' => 'back:in',
			'movetype-in' => 'back:out',
			'titlemove' => '550',
			'titletype' => 'elastic:in',
			'contentmove' => '650',
			'contenttype' => 'elastic:out',
			'pausein' => '600',
			'pauseout' => '350',
			'readmore' => '',
			'link' => '',
			'auto' => '8000',
		);
	}
	/*!
	 * @function actions
	 * @abstract Calls for the needed actions hooks.
	 */
	public function actions()
	{		
		// Creates the option page(s)
		add_action( 'admin_menu', array( $this, 'addOptionsMenu' ) );
		// Calls for initialization on admin_init
		add_action( 'admin_init', array( $this, 'optionsInit' ) );
		add_action( 'admin_init', array( $this, 'textReplacements' ) );
		// Calls for styles and scripts initialization
		add_action( 'admin_enqueue_scripts', array($this,'adminStylesLoad') );
		add_action( 'admin_enqueue_scripts', array($this,'adminScriptsLoad') );
		//add_action( 'wp_enqueue_scripts', array($this,'stylesLoad') );
		add_action( 'wp_enqueue_scripts', array($this,'scriptsLoad') );
		// Internationalization
		add_action( 'plugins_loaded', array($this,'internationalization') );
	}
	/*! @function filters
	 *  @abstract Calls for applying the neccesary filters
	 */
	public function filters()
	{
		// Calls for new contextual help
		add_filter( 'contextual_help', array( $this, 'help') );
	}
	/*!	@function optionsInit
	 * 	@abstract Initializes wp_options data row.
	 */
	public function optionsInit()
	{		
		$this->options = get_option('arm4slide');

		if( $this->options == null ) {
			$this->dataDefaults();
			$this->options = $this->defaults;
			update_option( 'arm4slide', $this->options );
		}
	
		register_setting( 'arm4slide', 'arm4slide', array( $this, 'validation' ) );
	}
	/*!	@function addOptionsMenu
	 * 	@abstract Creates the main options page. Also gets the data from plugin definition.
	 */
	public function addOptionsMenu()
	{
		// Gets the data for plugin build. Moved from __construct() in order to delay the gettext string load.
		$this->data = $this->pluginDefinition();

		add_theme_page(
			$this->data['name'],
			__('Slider','arm4plugins'),
			'manage_options',
			$this->data['slug'],
			array( $this, 'pageRender' )
		);
	}
	/*!	@function pageRender
	 * 	@abstract HTML render of the main options page.
	 */
	public function pageRender()
	{
		// Html options page render?>
		<div class="wrap">
			<div id="icon-arm4-config" class="icon32"><br/></div>
			<h2><?php _e('Slider configuration','arm4plugins'); ?></h2>
			<?php
				// 'Correctly updated' message, if any
				if( isset( $_GET['settings-updated'] ) ) {
					echo '<div class="updated"><p>'.__('Correctly options update','arm4plugins').'</p></div>';
				}
			?>
			<form id="<?php echo $this->data['slug']; ?>" method="POST" action="options.php" style="display:none">

		<?php	// nonce for verification
				wp_nonce_field('options.php', 'arm4plugins_nonce');

				// Settings init
				settings_fields('arm4slide');
					
				// Sections creation
				foreach( $this->data['sections'] as $section ) {
					add_settings_section(
						$section['slug'],
						$section['name'],
						$this->sectionRender($section),
						$this->data['slug'] );
					foreach( $section['fields'] as $field ) {
						add_settings_field(
							$field['slug'],
							$field['slug'],
							$this->fieldRender($field),
							$section['slug'],
							$section['slug'] );
					}
				}	?>

			<!-- CODE SHOWER -->
				<?php $code = '<?php if( class_exists("arm4plugins") && isset($arm4slide) ) :	$arm4slide->doSlider();	endif; ?>' ?>
				
				<div id="arm4slide_render_code">
					<p><?php _e("Please copy this text and place it where you want that the slider will be rendered. And don't forget to save changes!",'arm4plugins') ?></p>
					<textarea cols="40" rows="2"><?php echo $code; ?></textarea>
				</div>
				
				<tr valign="top">
					<th scope="row">
						<p id="arm4slide_submit" class="submit">
							<input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>"/>
						</p>
					</th>
				</tr>
				
			</form>
			
		</div>	
		<?php 
	}
	/*!	@function sectionRender
	 * 	@abstract HTML render of each subpage section.
	 * 	@param section array - the section options stored as an array.
	 */
	public function sectionRender($section)
	{
		// Catchs the values
		$this->section = $section;
		// and print the header
		echo '<h3 id="'.$this->section['slug'].'">'.$this->section['name'].'</h3>';	
	}
	/*!	@function pageRender
	 * 	@abstract HTML render of each field in every subpage section.
	 * 	@param field array - the field options stored as an array.
	 */
	public function fieldRender($field)
	{
		// Catchs the values
		$this->field = $field;

		// Gets custom posts, if any
		$post_types = get_post_types(
			array(
				'public' => true,
				'_builtin' => false
			),
			'objects',
			'and'
		);

		// Gets categories
		$cats = get_categories(
			array(
				//'type'         => 'product',
				'child_of'     => 0,
				'parent'       => '',
				'orderby'      => 'name',
				'order'        => 'ASC',
				'hide_empty'   => 1,
				'hierarchical' => 1,
				'exclude'      => '',
				'include'      => '',
				'number'       => '',
				'taxonomy'     => 'category',
				'pad_counts'   => 1 ));
?>
		<table class="form-table <?php echo $this->section['slug']; ?>" >
			<tr valign="top">
				<th scope="row">
					<label for="<?php echo $this->field['slug']; ?>">
						<?php echo $this->field['label']; ?>						
					</label>
				</th>
				<td>
				<?php
					switch($this->field['type']) :
						
						case 'text':?>
								<input id="<?php echo $this->field['slug'];?>" class="left" type="<?php echo $this->field['type']; ?>" name="arm4slide[<?php echo $this->field['slug']; ?>]" value="<?php echo $this->options[$this->field['slug']]; ?>"/>
						<?php break;
						
						case 'radio':
							foreach( $this->field['input'] as $radio ) : ?>
								<input class="opt-radio" type="<?php echo $this->field['type']; ?>" name="arm4slide[<?php echo $this->field['slug']; ?>]" value="<?php echo $radio['value']; ?>" <?php checked( $radio['value'], $this->options[$this->field['slug']] ); ?> />
								<?php echo $radio['name'];
							endforeach;
						break;
						
						case 'checkbox':
							foreach( $this->field['input'] as $checkbox ) : ?>
								<input class="opt-checkbox" type="<?php echo $this->field['type']; ?>" name="arm4slide[<?php echo $this->field['slug']; ?>]" value="<?php echo $checkbox['value']; ?>" <?php checked( $checkbox['value'], $this->options[$this->field['slug']] ); ?> />
								<?php echo $checkbox['name'];
							endforeach;
 						break;

 						case 'file':?>
							<input id="<?php echo $this->field['slug']; ?>" type="text" name="arm4slide[<?php echo $this->field['slug']; ?>]" value="<?php echo $this->options[$this->field['slug']]; ?>"/>
							<input id="<?php echo $this->field['slug'].'-button'; ?>" class="button-secondary" type="button" name="ars_options[<?php echo $this->field['slug']; ?>]" value="<?php _e('Upload'); ?>"/>
				<?php	break;

						case 'select':	?>
							<select id="<?php echo $this->field['slug']; ?>" name="arm4slide[<?php echo $this->field['slug']; ?>]">

						<?php foreach( $this->field['options'] as $option ): ?>
								<option value="<?php echo $option['value']; ?>" <?php selected( $this->options[$this->field['slug']], $option['value'] ); ?> ><?php echo $option['name']; ?></option>
						<?php endforeach; 
							  if( $this->field['slug'] == 'slides-which' ):
								foreach( $post_types as $post_type ): ?> 
									<option value="<?php echo $post_type->name; ?>" <?php selected( $this->options[$this->field['slug']], $post_type->name ); ?> ><?php echo strtolower($post_type->label); ?></option>
						<?php	endforeach;
							  endif; ?>
							  
							</select>
				<?php       if( $this->field['slug'] == 'slides-which' ):
								$i=0;	?>
								<span class="which_msg"><?php _e('Which: ','arm4plugins'); ?></span>
						<?php	foreach( $cats as $cat ):
									if( count( $this->options['slides-which-cats'] ) > 0 ):
										$checkit = $this->options['slides-which-cats'][$i];
									endif;	?>
									<input class="opt-checkbox which_input" type="checkbox" name="arm4slide[slides-which-cats][]" value="<?php echo $cat->slug; ?>" <?php if( count( $this->options['slides-which-cats'] ) > 0 ) { checked( $cat->slug, $checkit ); } ?> /><span class="which_input"><?php echo $cat->name; ?></span>
						<?php		$i++;
								endforeach; ?>
					<?php	endif;
						break;
				  
					endswitch;
				?>
				</td>
			</tr>
		</table>
<?php
	}
	/*!	@function validation
	 * 	@abstract Validates input data as needed.
	 *  @param input Auto variable defined by wordpress options API on click 'Save changes' button.
	 */
	public function validation()
	{
		if ( !empty($_POST) && wp_verify_nonce($_POST['arm4plugins_nonce'],'options.php')
			 && check_admin_referer('options.php','arm4plugins_nonce') ) {

			$input = $_POST['arm4slide'];

			if( !isset($input['readmore']) ) {
				$input['readmore'] = '';
			}
			if( !isset($input['link']) ) {
				$input['link'] = '';
			}
			
			// options unification
			$this->options = array_merge($this->options,$input);

			// Sanitizes text fields over UTF-8
			foreach( $this->data['sections'] as $section) {
				foreach( $section['fields'] as $field ) {
					// Sanitizes text fields
					if( $field['type'] === 'text' ) {
						$this->options[$field['slug']] = sanitize_text_field( $this->options[$field['slug']] );
					}
				}
			}

			if( $this->options['auto'] == '0' ) {
				$this->options['auto'] = '';
			}
		
			return $this->options;
		}
	}
	/*!	@function adminStylesLoad
	 * 	@abstract Loads the admin stylesheet files.
	 */
	public function adminStylesLoad()
	{
		wp_register_style( 'arm4slide_admin_styles', plugins_url('/styles/admin_styles.css',__FILE__) );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'arm4slide_admin_styles' );
	}
	/*!	@function stylesLoad
	 * 	@abstract Loads the stylesheet files.
	 */
	public function stylesLoad()
	{
		// Not necessary at the moment
	}
	/*!	@function adminScriptsLoad
	 * 	@abstract Admin javascript stuff. Defines Google Maps client key, loads js files for admin
	 * 		screens, creates and loads the neccesary js variables.
	 */
	public function adminScriptsLoad()
	{
		wp_enqueue_script( 'mootools', plugins_url('/js/mootools-core.js',__FILE__) );
		wp_enqueue_script( 'mootools-more', plugins_url('/js/mootools-more-wait-measure.js',__FILE__), array('mootools') );
		wp_enqueue_script( 'arm4slide_admin_scripts', plugins_url('/js/admin_js.js',__FILE__), array( 'jquery', 'wp-color-picker', 'mootools', 'mootools-more', 'media-upload', 'thickbox' ) );

		// JS vars passed to the admin scripts enviroment
		$jsadmin = array(
			'pluginDir' => plugins_url('arm4slide'),
			'controlTypeTitles' => __('Will show each slide title','arm4plugins'),
			'controlTypeThumbs' => __('Will show each featured thumbnail','arm4plugins'),
			'slidesLocalizatedName' => __('slides','arm4plugins'),
		);
		wp_localize_script( 'arm4slide_admin_scripts', 'vars', $jsadmin );
	}
	/*!	@function scriptsLoad
	 * 	@abstract Loads the front end javascript files.
	 */
	public function scriptsLoad()
	{
		wp_enqueue_script( 'mootools', plugins_url('/js/mootools-core.js',__FILE__) );
		wp_enqueue_script( 'mootools-more', plugins_url('/js/mootools-more-wait-measure.js',__FILE__), array('mootools') );
		wp_enqueue_script( 'arm4slide', plugins_url('/js/class-arm4slide.js',__FILE__), array( 'mootools', 'mootools-more' ) );
		wp_enqueue_script( 'arm4slide_frontend', plugins_url('/js/frontend_js.js',__FILE__), array( 'arm4slide', 'jquery') );

		$opts = get_option('arm4slide');

		// JS vars passed to the frontend scripts enviroment
		$jsfront = array(
			'wrapper_id' => $opts['wrapper-id'],
			'wrapper_width' => $opts['wrapper-width'],
			'wrapper_height' => $opts['wrapper-height'],
			'wrapper_texture' => $opts['wrapper-texture'],
			'wrapper_background' => $opts['wrapper-background'],
			'wrapper_shadow_x' => $opts['wrapper-shadow-x'],
			'wrapper_shadow_y' => $opts['wrapper-shadow-y'],
			'wrapper_shadow_blur' => $opts['wrapper-shadow-blur'],
			'wrapper_shadow_color' => $opts['wrapper-shadow-color'],
			'wrapper_border_radius' => $opts['wrapper-border-radius'],
			'wrapper_padding' => $opts['wrapper-padding'],
			'slides_class' => $opts['slides-class'],
			'slides_shadow_x' => $opts['slides-shadow-x'],
			'slides_shadow_y' => $opts['slides-shadow-y'],
			'slides_shadow_blur' => $opts['slides-shadow-blur'],
			'slides_shadow_color' => $opts['slides-shadow-color'],
			'slides_border_radius' => $opts['slides-border-radius'],
			'captions_class' => $opts['captions-class'],
			'title_pos' => $opts['title-pos'],
			'title_fontcolor' => $opts['title-fontcolor'],
			'title_size' => $opts['title-size'],
			'title_font_shadow_x' => $opts['title-font-shadow-x'],
			'title_font_shadow_y' => $opts['title-font-shadow-y'],
			'title_font_shadow_blur' => $opts['title-font-shadow-blur'],
			'title_font_shadow_color' => $opts['title-font-shadow-color'],
			'title_texture' => $opts['title-texture'],
			'title_background' => $opts['title-background'],
			'title_shadow_x' => $opts['title-shadow-x'],
			'title_shadow_y' => $opts['title-shadow-y'],
			'title_shadow_blur' => $opts['title-shadow-blur'],
			'title_shadow_color' => $opts['title-shadow-color'],
			'title_padding' => $opts['title-padding'],
			'content_pos' => $opts['content-pos'],
			'content_width' => $opts['content-width'],
			'content_fontcolor' => $opts['content-fontcolor'],
			'content_size' => $opts['content-size'],
			'content_font_shadow_x' => $opts['content-font-shadow-x'],
			'content_font_shadow_y' => $opts['content-font-shadow-y'],
			'content_font_shadow_blur' => $opts['content-font-shadow-blur'],
			'content_font_shadow_color' => $opts['content-font-shadow-color'],
			'content_texture' => $opts['content-texture'],
			'content_background' => $opts['content-background'],
			'content_shadow_x' => $opts['content-shadow-x'],
			'content_shadow_y' => $opts['content-shadow-y'],
			'content_shadow_blur' => $opts['content-shadow-blur'],
			'content_shadow_color' => $opts['content-shadow-color'],
			'content_padding' => $opts['content-padding'],
			'control_class' => $opts['control-class'],
			'control_type' => $opts['control-type'],
			'control_pos' => $opts['control-pos'],
			/*'control_width' => $opts['control-width'],
			'control_height' => $opts['control-height'],*/
			'control_texture' => $opts['control-texture'],
			'control_background' => $opts['control-background'],
			'control_color' => $opts['control-color'],
			'control_shadow_x' => $opts['control-shadow-x'],
			'control_shadow_y' => $opts['control-shadow-y'],
			'control_shadow_blur' => $opts['control-shadow-blur'],
			'control_shadow_color' => $opts['control-shadow-color'],
			'control_border_radius' => $opts['control-border-radius'],
			'control_padding' => $opts['control-padding'],
			'control_margin' => $opts['control-margin'],
			'anim_firsttime' => $opts['firsttime'],
			'anim_movetime' => $opts['movetime'],
			'anim_movetype_out' => $opts['movetype-out'],
			'anim_movetype_in' => $opts['movetype-in'],
			'anim_title_move' => $opts['titlemove'],
			'anim_type_move' => $opts['titletype'],
			'anim_content_move' => $opts['contentmove'],
			'anim_content_type' => $opts['contenttype'],
			'anim_pause_in' => $opts['pausein'],
			'anim_pause_out' => $opts['pauseout'],
			'readmore' => $opts['readmore'],
			'link' => $opts['link'],
			'auto' => $opts['auto'],
		);
		wp_localize_script( 'arm4slide_frontend', 'slider', $jsfront );
		// other vars
		$othervars = array(
			'pluginURL' => plugins_url('',__FILE__)
		);
		wp_localize_script( 'arm4slide', 'vars', $othervars );
	}
	/*!	@function help
	 * 	@abstract Defines contextual help.
	 */
	public function help()
	{
		$screen = get_current_screen();
		$screen_id = $screen->id;

		switch( $screen_id ) {
			case 'appearance_page_arm4slide':
				$screen->add_help_tab( array(
					'title' => __('Container options','arm4plugins'),
					'id' => 'arm4slide-container',
					'content' => "<p>".__('Here is where you can change your slider container options.','arm4plugins')."</p><p><strong>".__('Name','arm4plugins')." - </strong>".__('This will be the ID attribute of your slider. You can change it in order to avoid CSS conflicts.','arm4plugins')."</p><p><strong>".__('Width','arm4plugins')." - </strong>".__('Sets the width of the container.','arm4plugins')."</p><p><strong>".__('Height','arm4plugins')." - </strong>".__('Sets the height of the slides container, it means that this measure will be increased with the control panel height and container padding options.','arm4plugins')."</p><p><strong>".__('Padding','arm4plugins')." - </strong>".__('The padding value of the main container.','arm4plugins')."</p><p><strong>".__('Texture','arm4plugins')." - </strong>".__('You can upload an image in order to use it as texture. Translucent textures would be modificated using background color. See below.','arm4plugins')."</p><p><strong>".__('Background color','arm4plugins')." - </strong>".__('Defines the background color of the main container. Useful for theme integration. Useful for translucent texture complement.','arm4plugins')."</p><p><strong>".__('Box shadow','arm4plugins')." - </strong>".__('Sets the box shadow of the main container. Negative and zero values are allowed for both horizontal and vertical positions.','arm4plugins')."</p><p><strong>".__('Border radius','arm4plugins')." - </strong>".__('Sets the radius for borders.','arm4plugins')."</p>"
				) );
				$screen->add_help_tab( array(
					'title' => __('Slides options','arm4plugins'),
					'id' => 'arm4slide-slides',
					'content' => "<p>".__('Here is where you can change your slides options.','arm4plugins')."</p><p><strong>".__('Name','arm4plugins')." - </strong>".__('This will be the CLASS attribute of your slides. You can change it in order to avoid CSS conflicts.','arm4plugins')."</p><p><strong>".__('Get slides from','arm4plugins')." - </strong>".__('Sets the source from the slider gets the slides to be shown. In this list must appear at least two options: last entries and categories. In categories will be shown the names of your non-empty categories. Also will be shown in this list your custom post types.','arm4plugins')."</p><p><strong>".__('Number of slides','arm4plugins')." - </strong>".__('Sets the number of slides to be showed in the slider. It gets it in DESCENDANT order of publish date.','arm4plugins')."</p><p><strong>".__('Box shadow','arm4plugins')." - </strong>".__('Sets the box shadow of each slide. Negative and zero values are allowed for both horizontal and vertical positions.','arm4plugins')."</p><p><strong>".__('Border radius','arm4plugins')." - </strong>".__('Sets the radius for borders.','arm4plugins')."</p>"
				) );
				$screen->add_help_tab( array(
					'title' => __('Captions options','arm4plugins'),
					'id' => 'arm4slide-captions',
					'content' => "<p>".__('Here is where you can change your slide captions options.','arm4plugins')."</p><p><strong>".__('Name','arm4plugins')." - </strong>".__('This will be the CLASS attribute of your slide captions. You can change it in order to avoid CSS conflicts.','arm4plugins')."</p><p><strong>".__('Position','arm4plugins')." - </strong>".__('Sets the position of both title and content layers. Title is always on top and content is always on bottom, but you can change from left to right and viceversa independently.','arm4plugins')."</p><p><strong>".__('Font color','arm4plugins')." - </strong>".__('Sets the font color of both title and content.','arm4plugins')."</p><p><strong>".__('Font size','arm4plugins')." - </strong>".__('The font size of the each one.','arm4plugins')."</p><p><strong>".__('Padding','arm4plugins')." - </strong>".__('The padding value of title and content layers. Essential for good looking.','arm4plugins')."</p><p><strong>".__('Texture','arm4plugins')." - </strong>".__('You can upload an image in order to use it as texture. Translucent textures would be modificated using background color. See below.','arm4plugins')."</p><p><strong>".__('Background color','arm4plugins')." - </strong>".__('Defines the background color of both title and content layers. Useful for theme integration. Useful for translucent texture complement.','arm4plugins')."</p><p><strong>".__('Box shadow','arm4plugins')." - </strong>".__('Sets the box shadow of the main container. Negative and zero values are allowed for both horizontal and vertical positions.','arm4plugins')."</p><p><strong>".__('Content area size','arm4plugins')." - </strong>".__('Sets the width of the content area relatively to the total width of the slide.','arm4plugins')."</p>"
				) );
				$screen->add_help_tab( array(
					'title' => __('Control panel options','arm4plugins'),
					'id' => 'arm4slide-control',
					'content' => "<p>".__('Here is where you can change your control panel options.','arm4plugins')."</p><p><strong>".__('Name','arm4plugins')." - </strong>".__('This will be the CLASS attribute of your panel controls items. You can change it in order to avoid CSS conflicts.','arm4plugins')."</p><p><strong>".__('Type','arm4plugins')." - </strong>".__('Sets the type of the control panel items. In the case of circles and squares, left image is for non-select and right image is for selected. In the case of thumbnails, active control will be shown with a little shadow. In the case of titles, each active will be shown with a littel text shadow. In the last two cases, you can select the color of the shadow through the control panel main color selection.','arm4plugins')."</p><p><strong>".__('Position','arm4plugins')." - </strong>".__('You can select the position of the control panel between the given options.','arm4plugins')."</p><p><strong>".__('Texture','arm4plugins')." - </strong>".__('You can upload an image in order to use it as texture. Translucent textures would be modificated using background color. See below.','arm4plugins')."</p><p><strong>".__('Background color','arm4plugins')." - </strong>".__('Defines the background color of the main container. Useful for theme integration. Useful for translucent texture complement.','arm4plugins')."</p><p><strong>".__('Main color','arm4plugins')." - </strong>".__('Sets the color to be used as shadow color if you choose the titles or thumbnails control panel type.','arm4plugins')."</p><p><strong>".__('Box shadow','arm4plugins')." - </strong>".__('Sets the box shadow of the main container. Negative and zero values are allowed for both horizontal and vertical positions.','arm4plugins')."</p><p><strong>".__('Border radius','arm4plugins')." - </strong>".__('Sets the radius for borders.','arm4plugins')."</p><p><strong>".__('Margin from slides','arm4plugins')." - </strong>".__('Sets the margin between the slides container and the panel container.','arm4plugins')."</p><p><strong>".__('Padding','arm4plugins')." - </strong>".__('The padding value of the control panel container.','arm4plugins')."</p>"
				) );
				$screen->add_help_tab( array(
					'title' => __('Animation options','arm4plugins'),
					'id' => 'arm4slide-animation',
					'content' => "<p>".__('Here is where you can change the animation values. About the values of this section, you must know that ms = milisecond, and 1000 ms = 1 second (more or less).','arm4plugins')."</p><p><strong>".__('First appearance','arm4plugins')." - </strong>".__('Defines the time that gets the caption of the first slide in be showed.')."</p><p><strong>".__('Time between slides','arm4plugins')." - </strong>".__('The time that takes the slider in show the next slide.','arm4plugins')."</p><p><strong>".__('Slide animation type','arm4plugins')." - </strong>".__('Sets both outgoing and incoming slide animations. Test all with the ball and select your preferred one.','arm4plugins')."</p><p><strong>".__('Title and content animation time','arm4plugins')." - </strong>".__('Sets the time that takes the slider in show each one of those.','arm4plugins')."</p><p><strong>".__('Title and content animation type','arm4plugins')." - </strong>".__('Sets the animation type for both titles and contents.','arm4plugins')."</p><p><strong>".__('Pauses','arm4plugins')." - </strong>".__('Sets the pause time between the captions items animations.','arm4plugins')."</p>"
				) );
				$screen->add_help_tab( array(
					'title' => __('Other options','arm4plugins'),
					'id' => 'arm4slide-other',
					'content' => "<p><strong>".__('Show "Read more"','arm4plugins')." - </strong>".__('If selected, a link with the same properties of the title ones will be shown on the top of the slide, in the opposite side of the title.')."</p><p><strong>".__('Link to whole slider','arm4plugins')." - </strong>".__('This creates a top layer linking to the title link in each slide.','arm4plugins')."</p><p><strong>".__('Auto movement','arm4plugins')." - </strong>".__('Defines the time in move to the next slide. If empty or zero value, auto movement will be disabled. Take care about this value, can cause inconsistencies if lower than the other animation time values.','arm4plugins')."</p>"
				) );
			break;
		}
 
	}
	/*!	@function textReplacements
	 * 	@abstract Replaces some text from texture uploadings.
	 */
	public function textReplacements()
	{
		// Calls for wordpress global variable $pagenow, that stores the current page name.
		global $pagenow;
		// See $this->uploadLogoText()
		if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
			add_filter( 'gettext', array( $this, 'uploadTextureText' ), 20, 3 );
		}
	}
	/*!	@function uploadLogoText
	 * 	@abstract Replaces the text upload thickbox text for logo uploading. this function has three
	 * 		parameters by definition. There are passed but the only neccesary its the second one.
	 * 	@param translated_text string - gettext filter variable that stores the translated text
	 * 	@param untranslated_text string - gettext filter variable that stores the untranslated text
	 * 	@param domamin string - gettext filter variable that stores the text domain
	 */
	public function uploadTextureText( $translated_text, $untranslated_text, $domain )
	{		
		if ( $untranslated_text === 'Insert into Post' ) {
			$referer = strpos( wp_get_referer(), 'arm4slide' );
			if ( $referer != '' ) {
				return __('Use as texture', 'arm4plugins' );
			}
		}
		return $translated_text;
	}
	/*! @function doSlider
	 *  @abstract The function that 'do' the slider using the parameters stored through the setting page.
	 */
	public function doSlider()
	{
		$options = get_option('arm4slide');
	
		// depends of the slides selection method
		switch( $options['slides-which'] ):
			case 'cats':
				// categories id management
				foreach( $options['slides-which-cats'] as $i => $cat_name ) {
					$options['slides-which-cats'][$i] = get_cat_ID($cat_name);
				}
				$categories = implode(',',$options['slides-which-cats']);
				// post type retrieving
				$post_types = get_post_types(
					array(
						'public' => true,
					),
					'names',
					'and'
				);
				$args = array(
					'numberposts'     => $options['slides-count'],
					'category'	      => $categories,
					'orderby'         => 'post_date',
					'order'           => 'DESC',
					'post_status'     => 'publish',
					'post_type'		  =>  $post_types,
					'suppress_filters' => true );
				$slides = get_posts($args);
				break;
			case 'manual':
				
				break;
			default:
				$args = array(
					'numberposts'     => $options['slides-count'],
					'orderby'         => 'post_date',
					'order'           => 'DESC',
					'post_type'       => $options['slides-which'],
					'post_status'     => 'publish',
					'suppress_filters' => true );
				$slides = get_posts($args);
		endswitch;

		if( $options['readmore'] == '1' ) :
			switch( $options['title-pos'] ):
				case 'left':
					$more_pos = 'right';
				break;
				case 'right':
					$more_pos = 'left';
				break;
			endswitch;
		endif;
?>
		<aside id="<?php echo $options['wrapper-id']; ?>">

		<?php if( $options['control-pos'] == 'topleft' || $options['control-pos'] == 'topright' ): ?>

				<div id="<?php echo $options['control-class'].'-wrapper';?>">
					<?php $this->doSliderControls($slides); ?>
				</div>
				
		<?php endif; ?>

			<div id="<?php echo $options['wrapper-id'].'-inner'; ?>">

<?php
		foreach( $slides as $post ): setup_postdata($post);	?>

				<div class="<?php echo $options['slides-class']; ?>">

					<div class="<?php echo $options['slides-class'].'_attach'; ?>">
						<?php echo get_the_post_thumbnail($post->ID, 'full'); ?>
					</div>
					
					<div class="<?php echo $options['captions-class']; ?>">

						<!-- readmore link -->
						<?php if( isset($more_pos) ): ?>
							<a class="readmore" href="<?php echo site_url($post->post_name); ?>" style="float:<?php echo $more_pos; ?>;"><?php _e('Read more','arm4plugins'); ?></a>
						<?php endif; ?>
						
						<h2 class="<?php echo $options['captions-class'].'-title'; ?> ">
							<a href="<?php echo site_url($post->post_name); ?>" title="<?php echo $post->post_title; ?>">
								<?php echo $post->post_title; ?>
							</a>
						</h2>
						
						<div class="<?php echo $options['captions-class'].'-content'; ?>">
							<?php echo $post->post_excerpt; ?>
						</div>
					</div>
					
				</div>
			
<?php 	endforeach; ?>

			</div>

		<?php if( $options['control-pos'] == 'bottomright' || $options['control-pos'] == 'bottomleft' ): ?>

				<div id="<?php echo $options['control-class'].'-wrapper';?>">
					<?php $this->doSliderControls($slides); ?>
				</div>
				
		<?php endif; ?>
		
		</aside>
<?php
	}
	/*! @function doSliderControls
	 *  @abstract The function that 'do' the slider controls using the parameters stored through the setting page.
	 */
	public function doSliderControls( $slides )
	{
		$this->slides = $slides;
		
		$options = get_option('arm4slide');
		
		switch( $options['control-type'] ):
			case 'titles':
				foreach( $this->slides as $post ): ?>
					<div class="<?php echo $options['control-class'] ?>">
						<a class="<?php echo $options['control-class'].'-'.$options['control-type'] ?>" href="" title="">
							<?php echo $post->post_title; ?>
						</a>
					</div>
		<?php	endforeach;
			break;
			case 'thumbs':
				foreach( $this->slides as $post ): ?>
					<div class="<?php echo $options['control-class'] ?>">
						<a class="<?php echo $options['control-class'].'-'.$options['control-type'] ?>" href="" title="">
							<?php echo get_the_post_thumbnail( $post->ID, array(50,50) ); ?>
						</a>
					</div>
		<?php	endforeach;
			break;
			case 'circles':
				foreach( $this->slides as $post ): ?>
					<div class="<?php echo $options['control-class'] ?>">
						<a class="<?php echo $options['control-class'].'-'.$options['control-type'] ?>" href="" title="">
						</a>
					</div>
		<?php	endforeach;
			break;
			case 'squares':
				foreach( $this->slides as $post ): ?>
					<div class="<?php echo $options['control-class'] ?>">
						<a class="<?php echo $options['control-class'].'-'.$options['control-type'] ?>" href="" title="">
						</a>
					</div>
		<?php	endforeach;
			break;
		endswitch;
	}
	/*!	@function internationalization
	 * 	@abstract Defines i18n domain and languages location.
	 */
	public function internationalization()
	{
		load_plugin_textdomain( 'arm4plugins', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}
	/*!	@function pluginDefinition
	 * 	@abstract Defines the plugin and all its options.
	 */
	public function pluginDefinition()
	{
		$arm4slide_page = array(
			'name' => 'arm4slide',
			'slug' => 'arm4slide',
			'infotext' => __('Slider options','arm4plugins'),
			'sections' => array(
				array(
					'name' => __('Container options','arm4plugins'),
					'slug' => 'arm4slide-wrapper',
					'fields' => array(
						array(
							'label' => __('Container name','arm4plugins'),
							'slug' => 'wrapper-id',
							'type' => 'text'
						),
						array(
							'label' => __('Container width','arm4plugins'),
							'slug' => 'wrapper-width',
							'type' => 'text',
						),
						array(
							'label' => __('Container height','arm4plugins'),
							'slug' => 'wrapper-height',
							'type' => 'text',
						),
						array(
							'label' => __('Container padding','arm4plugins'),
							'slug' => 'wrapper-padding',
							'type' => 'text',
						),
						array(
							'label' => __('Container background texture','arm4plugins'),
							'slug' => 'wrapper-texture',
							'type' => 'file'
						),
						array(
							'label' => __('Container background color','arm4plugins'),
							'slug' => 'wrapper-background',
							'type' => 'text'
						),
						array(
							'label' => __('Box shadow horizontal position','arm4plugins'),
							'slug' => 'wrapper-shadow-x',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow vertical position','arm4plugins'),
							'slug' => 'wrapper-shadow-y',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow blur size','arm4plugins'),
							'slug' => 'wrapper-shadow-blur',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow color','arm4plugins'),
							'slug' => 'wrapper-shadow-color',
							'type' => 'text',
						),
						array(
							'label' => __('Border radius size','arm4plugins'),
							'slug' => 'wrapper-border-radius',
							'type' => 'text'
						)
					)
				),
				array(
					'name' => __('Slides options','arm4plugins'),
					'slug' => 'arm4slide-slides',
					'fields' => array(
						array(
							'label' => __('Slides name','arm4plugins'),
							'slug' => 'slides-class',
							'type' => 'text'
						),
						array(
							'label' => __('Get slides from','arm4plugins'),
							'slug' => 'slides-which',
							'type' => 'select',
							'options' => array(
								array(
									'value' => 'post',
									'name' => __('last entries','arm4plugins')
								),
								/*array(
									'value' => 'page',
									'name' => __('pages','arm4plugins')
								),*/
								array(
									'value' => 'cats',
									'name' => __('categories','arm4plugins')
								),
								/*array(
									'value' => 'manual',
									'name' => __('manualmente','arm4plugins')
								)*/
							)
						),
						array(
							'label' => __('Number of slides','arm4plugins'),
							'slug' => 'slides-count',
							'type' => 'text'
						),
						array(
							'label' => __('Box shadow horizontal position','arm4plugins'),
							'slug' => 'slides-shadow-x',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow vertical position','arm4plugins'),
							'slug' => 'slides-shadow-y',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow blur size','arm4plugins'),
							'slug' => 'slides-shadow-blur',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow color','arm4plugins'),
							'slug' => 'slides-shadow-color',
							'type' => 'text',
						),
						array(
							'label' => __('Border radius size','arm4plugins'),
							'slug' => 'slides-border-radius',
							'type' => 'text'
						),
					)
				),
				array(
					'name' => __('Captions options','arm4plugins'),
					'slug' => 'arm4slide-captions',
					'fields' => array(
						array(
							'label' => __('Captions name','arm4plugins'),
							'slug' => 'captions-class',
							'type' => 'text'
						),
						array(
							'label' => __('Captions title position','arm4plugins'),
							'slug' => 'title-pos',
							'type' => 'select',
							'options' => array(
								array(
									'value' => 'left',
									'name' => __('Left','arm4plugins')
								),
								array(
									'value' => 'right',
									'name' => __('Right','arm4plugins')
								)
							)
						),
						array(
							'label' => __('Title font color','arm4plugins'),
							'slug' => 'title-fontcolor',
							'type' => 'text',
						),
						array(
							'label' => __('Title font size','arm4plugins'),
							'slug' => 'title-size',
							'type' => 'select',
							'options' => array(
								array(
									'value' => '12',
									'name' => '12px'
								),
								array(
									'value' => '14',
									'name' => '14px'
								),
								array(
									'value' => '16',
									'name' => '16px'
								),
								array(
									'value' => '18',
									'name' => '18px'
								),
								array(
									'value' => '20',
									'name' => '20px'
								),
								array(
									'value' => '22',
									'name' => '22px'
								),
								array(
									'value' => '24',
									'name' => '24px'
								),
								array(
									'value' => '26',
									'name' => '26px'
								),
								array(
									'value' => '28',
									'name' => '28px'
								),
								array(
									'value' => '30',
									'name' => '30px'
								)
							)
						),
						array(
							'label' => __('Title font shadow horizontal position','arm4plugins'),
							'slug' => 'title-font-shadow-x',
							'type' => 'text',
						),
						array(
							'label' => __('Title font shadow vertical position','arm4plugins'),
							'slug' => 'title-font-shadow-y',
							'type' => 'text',
						),
						array(
							'label' => __('Title font shadow blur size','arm4plugins'),
							'slug' => 'title-font-shadow-blur',
							'type' => 'text',
						),
						array(
							'label' => __('Title font shadow color','arm4plugins'),
							'slug' => 'title-font-shadow-color',
							'type' => 'text',
						),
						array(
							'label' => __('Title padding','arm4plugins'),
							'slug' => 'title-padding',
							'type' => 'text',
						),
						array(
							'label' => __('Title background texture','arm4plugins'),
							'slug' => 'title-texture',
							'type' => 'file'
						),
						array(
							'label' => __('Title background color','arm4plugins'),
							'slug' => 'title-background',
							'type' => 'text'
						),
						array(
							'label' => __('Box shadow horizontal position','arm4plugins'),
							'slug' => 'title-shadow-x',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow vertical position','arm4plugins'),
							'slug' => 'title-shadow-y',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow blur size','arm4plugins'),
							'slug' => 'title-shadow-blur',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow color','arm4plugins'),
							'slug' => 'title-shadow-color',
							'type' => 'text',
						),
						array(
							'label' => __('Captions content position','arm4plugins'),
							'slug' => 'content-pos',
							'type' => 'select',
							'options' => array(
								array(
									'value' => 'left',
									'name' => __('Left','arm4plugins')
								),
								array(
									'value' => 'right',
									'name' => __('Right','arm4plugins')
								)
							)
						),
						array(
							'label' => __('Content area size','arm4plugins'),
							'slug' => 'content-width',
							'type' => 'text',
						),
						array(
							'label' => __('Content font color','arm4plugins'),
							'slug' => 'content-fontcolor',
							'type' => 'text',
						),
						array(
							'label' => __('Content font size','arm4plugins'),
							'slug' => 'content-size',
							'type' => 'select',
							'options' => array(
								array(
									'value' => '12',
									'name' => '12px'
								),
								array(
									'value' => '14',
									'name' => '14px'
								),
								array(
									'value' => '16',
									'name' => '16px'
								),
								array(
									'value' => '18',
									'name' => '18px'
								),
								array(
									'value' => '20',
									'name' => '20px'
								),
								array(
									'value' => '22',
									'name' => '22px'
								),
								array(
									'value' => '24',
									'name' => '24px'
								)
							)
						),
						array(
							'label' => __('Content font shadow horizontal position','arm4plugins'),
							'slug' => 'content-font-shadow-x',
							'type' => 'text',
						),
						array(
							'label' => __('Content font shadow vertical position','arm4plugins'),
							'slug' => 'content-font-shadow-y',
							'type' => 'text',
						),
						array(
							'label' => __('Content font shadow blur size','arm4plugins'),
							'slug' => 'content-font-shadow-blur',
							'type' => 'text',
						),
						array(
							'label' => __('Content font shadow color','arm4plugins'),
							'slug' => 'content-font-shadow-color',
							'type' => 'text',
						),
						array(
							'label' => __('Content padding','arm4plugins'),
							'slug' => 'content-padding',
							'type' => 'text',
						),
						array(
							'label' => __('Content background texture','arm4plugins'),
							'slug' => 'content-texture',
							'type' => 'file'
						),
						array(
							'label' => __('Content background color','arm4plugins'),
							'slug' => 'content-background',
							'type' => 'text'
						),
						array(
							'label' => __('Box shadow horizontal position','arm4plugins'),
							'slug' => 'content-shadow-x',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow vertical position','arm4plugins'),
							'slug' => 'content-shadow-y',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow blur size','arm4plugins'),
							'slug' => 'content-shadow-blur',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow color','arm4plugins'),
							'slug' => 'content-shadow-color',
							'type' => 'text',
						)
					)
				),
				array(
					'name' => __('Control panel options','arm4plugins'),
					'slug' => 'arm4slide-control',
					'fields' => array(
						array(
							'label' => __('Control panel name','arm4plugins'),
							'slug' => 'control-class',
							'type' => 'text'
						),
						array(
							'label' => __('Control panel type','arm4plugins'),
							'slug' => 'control-type',
							'type' => 'select',
							'options' => array(
								array(
									'value' => 'titles',
									'name' => __('Titles','arm4plugins')
								),
								array(
									'value' => 'thumbs',
									'name' => __('Thumbnails','arm4plugins')
								),
								array(
									'value' => 'circles',
									'name' => __('Circles','arm4plugins')
								),
								array(
									'value' => 'squares',
									'name' => __('Squares','arm4plugins')
								)
							)
						),
						array(
							'label' => __('Control panel position','arm4plugins'),
							'slug' => 'control-pos',
							'type' => 'select',
							'options' => array(
								array(
									'value' => 'topleft',
									'name' => __('Top left','arm4plugins')
								),
								array(
									'value' => 'topright',
									'name' => __('Top right','arm4plugins')
								),
								array(
									'value' => 'bottomleft',
									'name' => __('Bottom left','arm4plugins')
								),
								array(
									'value' => 'bottomright',
									'name' => __('Bottom right','arm4plugins')
								)
							)
						),
						/*array(
							'label' => __('Anchura del panel de control','arm4plugins'),
							'slug' => 'control-width',
							'type' => 'text',
						),
						array(
							'label' => __('Altura del panel de control','arm4plugins'),
							'slug' => 'control-height',
							'type' => 'text',
						),*/
						array(
							'label' => __('Control panel background texture','arm4plugins'),
							'slug' => 'control-texture',
							'type' => 'file'
						),
						array(
							'label' => __('Control panel background color','arm4plugins'),
							'slug' => 'control-background',
							'type' => 'text'
						),
						array(
							'label' => __('Control panel main color','arm4plugins'),
							'slug' => 'control-color',
							'type' => 'text'
						),
						array(
							'label' => __('Box shadow horizontal position','arm4plugins'),
							'slug' => 'control-shadow-x',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow vertical position','arm4plugins'),
							'slug' => 'control-shadow-y',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow blur size','arm4plugins'),
							'slug' => 'control-shadow-blur',
							'type' => 'text',
						),
						array(
							'label' => __('Box shadow color','arm4plugins'),
							'slug' => 'control-shadow-color',
							'type' => 'text',
						),
						array(
							'label' => __('Border radius size','arm4plugins'),
							'slug' => 'control-border-radius',
							'type' => 'text',
						),
						array(
							'label' => __('Control panel margin from slides','arm4plugins'),
							'slug' => 'control-margin',
							'type' => 'text',
						),
						array(
							'label' => __('Control panel padding','arm4plugins'),
							'slug' => 'control-padding',
							'type' => 'text',
						)
					)
				),
				array(
					'name' => __('Animation options','arm4plugins'),
					'slug' => 'arm4slide-animation',
					'fields' => array(
						array(
							'label' => __('First caption appearance time','arm4plugins'),
							'slug' => 'firsttime',
							'type' => 'text'
						),
						array(
							'label' => __('Transition time between slides','arm4plugins'),
							'slug' => 'movetime',
							'type' => 'text'
						),
						array(
							'label' => __('Outgoing slide animation type','arm4plugins'),
							'slug' => 'movetype-out',
							'type' => 'select',
							'options' => array(
								array(
									'value' => 'linear',
									'name' => 'Linear'
								),
								array(
									'value' => 'sine:in',
									'name' => 'Sine:in'
								),
								array(
									'value' => 'sine:out',
									'name' => 'Sine:out'
								),
								array(
									'value' => 'sine:in:out',
									'name' => 'Sine:in:out'
								),
								array(
									'value' => 'circ:in',
									'name' => 'Circ:in'
								),
								array(
									'value' => 'circ:out',
									'name' => 'Circ:out'
								),
								array(
									'value' => 'circ:in:out',
									'name' => 'Circ:in:out'
								),
								array(
									'value' => 'expo:in',
									'name' => 'Expo:in'
								),
								array(
									'value' => 'expo:out',
									'name' => 'Expo:out'
								),
								array(
									'value' => 'expo:in:out',
									'name' => 'Expo:in:out'
								),
								array(
									'value' => 'back:in',
									'name' => 'Back:in'
								),
								array(
									'value' => 'back:out',
									'name' => 'Back:out'
								),
								array(
									'value' => 'back:in:out',
									'name' => 'Back:in:out'
								),
								array(
									'value' => 'bounce:in',
									'name' => 'Bounce:in'
								),
								array(
									'value' => 'bounce:out',
									'name' => 'Bounce:out'
								),
								array(
									'value' => 'bounce:in:out',
									'name' => 'Bounce:in:out'
								),
								array(
									'value' => 'elastic:in',
									'name' => 'Elastic:in'
								),
								array(
									'value' => 'elastic:out',
									'name' => 'Elastic:out'
								),
								array(
									'value' => 'elastic:in:out',
									'name' => 'Elastic:in:out'
								)
							)
						),
						array(
							'label' => __('Incoming slide animation type','arm4plugins'),
							'slug' => 'movetype-in',
							'type' => 'select',
							'options' => array(
								array(
									'value' => 'linear',
									'name' => 'Linear'
								),
								array(
									'value' => 'sine:in',
									'name' => 'Sine:in'
								),
								array(
									'value' => 'sine:out',
									'name' => 'Sine:out'
								),
								array(
									'value' => 'sine:in:out',
									'name' => 'Sine:in:out'
								),
								array(
									'value' => 'circ:in',
									'name' => 'Circ:in'
								),
								array(
									'value' => 'circ:out',
									'name' => 'Circ:out'
								),
								array(
									'value' => 'circ:in:out',
									'name' => 'Circ:in:out'
								),
								array(
									'value' => 'expo:in',
									'name' => 'Expo:in'
								),
								array(
									'value' => 'expo:out',
									'name' => 'Expo:out'
								),
								array(
									'value' => 'expo:in:out',
									'name' => 'Expo:in:out'
								),
								array(
									'value' => 'back:in',
									'name' => 'Back:in'
								),
								array(
									'value' => 'back:out',
									'name' => 'Back:out'
								),
								array(
									'value' => 'back:in:out',
									'name' => 'Back:in:out'
								),
								array(
									'value' => 'bounce:in',
									'name' => 'Bounce:in'
								),
								array(
									'value' => 'bounce:out',
									'name' => 'Bounce:out'
								),
								array(
									'value' => 'bounce:in:out',
									'name' => 'Bounce:in:out'
								),
								array(
									'value' => 'elastic:in',
									'name' => 'Elastic:in'
								),
								array(
									'value' => 'elastic:out',
									'name' => 'Elastic:out'
								),
								array(
									'value' => 'elastic:in:out',
									'name' => 'Elastic:in:out'
								)
							)
						),
						array(
							'label' => __('Title animation time','arm4plugins'),
							'slug' => 'titlemove',
							'type' => 'text'
						),
						array(
							'label' => __('Title animation type','arm4plugins'),
							'slug' => 'titletype',
							'type' => 'select',
							'options' => array(
								array(
									'value' => 'linear',
									'name' => 'Linear'
								),
								array(
									'value' => 'sine:in',
									'name' => 'Sine:in'
								),
								array(
									'value' => 'sine:out',
									'name' => 'Sine:out'
								),
								array(
									'value' => 'sine:in:out',
									'name' => 'Sine:in:out'
								),
								array(
									'value' => 'circ:in',
									'name' => 'Circ:in'
								),
								array(
									'value' => 'circ:out',
									'name' => 'Circ:out'
								),
								array(
									'value' => 'circ:in:out',
									'name' => 'Circ:in:out'
								),
								array(
									'value' => 'expo:in',
									'name' => 'Expo:in'
								),
								array(
									'value' => 'expo:out',
									'name' => 'Expo:out'
								),
								array(
									'value' => 'expo:in:out',
									'name' => 'Expo:in:out'
								),
								array(
									'value' => 'back:in',
									'name' => 'Back:in'
								),
								array(
									'value' => 'back:out',
									'name' => 'Back:out'
								),
								array(
									'value' => 'back:in:out',
									'name' => 'Back:in:out'
								),
								array(
									'value' => 'bounce:in',
									'name' => 'Bounce:in'
								),
								array(
									'value' => 'bounce:out',
									'name' => 'Bounce:out'
								),
								array(
									'value' => 'bounce:in:out',
									'name' => 'Bounce:in:out'
								),
								array(
									'value' => 'elastic:in',
									'name' => 'Elastic:in'
								),
								array(
									'value' => 'elastic:out',
									'name' => 'Elastic:out'
								),
								array(
									'value' => 'elastic:in:out',
									'name' => 'Elastic:in:out'
								)
							)
						),
						array(
							'label' => __('Content animation time','arm4plugins'),
							'slug' => 'contentmove',
							'type' => 'text'
						),
						array(
							'label' => __('Content animation type','arm4plugins'),
							'slug' => 'contenttype',
							'type' => 'select',
							'options' => array(
								array(
									'value' => 'linear',
									'name' => 'Linear'
								),
								array(
									'value' => 'sine:in',
									'name' => 'Sine:in'
								),
								array(
									'value' => 'sine:out',
									'name' => 'Sine:out'
								),
								array(
									'value' => 'sine:in:out',
									'name' => 'Sine:in:out'
								),
								array(
									'value' => 'circ:in',
									'name' => 'Circ:in'
								),
								array(
									'value' => 'circ:out',
									'name' => 'Circ:out'
								),
								array(
									'value' => 'circ:in:out',
									'name' => 'Circ:in:out'
								),
								array(
									'value' => 'expo:in',
									'name' => 'Expo:in'
								),
								array(
									'value' => 'expo:out',
									'name' => 'Expo:out'
								),
								array(
									'value' => 'expo:in:out',
									'name' => 'Expo:in:out'
								),
								array(
									'value' => 'back:in',
									'name' => 'Back:in'
								),
								array(
									'value' => 'back:out',
									'name' => 'Back:out'
								),
								array(
									'value' => 'back:in:out',
									'name' => 'Back:in:out'
								),
								array(
									'value' => 'bounce:in',
									'name' => 'Bounce:in'
								),
								array(
									'value' => 'bounce:out',
									'name' => 'Bounce:out'
								),
								array(
									'value' => 'bounce:in:out',
									'name' => 'Bounce:in:out'
								),
								array(
									'value' => 'elastic:in',
									'name' => 'Elastic:in'
								),
								array(
									'value' => 'elastic:out',
									'name' => 'Elastic:out'
								),
								array(
									'value' => 'elastic:in:out',
									'name' => 'Elastic:in:out'
								)
							)
						),
						array(
							'label' => __('Pause time between the incoming title and the content one','arm4plugins'),
							'slug' => 'pausein',
							'type' => 'text'
						),
						array(
							'label' => __('Pause time between the outgoing title and the content one','arm4plugins'),
							'slug' => 'pauseout',
							'type' => 'text'
						)
					)
				),
				array(
					'name' => __('Other options','arm4plugins'),
					'slug' => 'arm4slide-misc',
					'fields' => array(
						array(
							'label' => __('Show "Read more"','arm4plugins'),
							'slug' => 'readmore',
							'type' => 'checkbox',
							'input' => array(
								array(
									'value' => true,
									'name' => ''
								)
							)
						),
						array(
							'label' => __('Link to whole slider','arm4plugins'),
							'slug' => 'link',
							'type' => 'checkbox',
							'input' => array(
								array(
									'value' => true,
									'name' => ''
								)
							)
						),
						array(
							'label' => __('Auto movement','arm4plugins'),
							'slug' => 'auto',
							'type' => 'text'
						)
					)
				)
			)
		);
		return $arm4slide_page;
	}
// Self class helpers
	/*!	@function __get
	 * 	@abstract Getter magic method that returns self class constants.
	 * 	@param name string - the constant
	 */
	public function __get($name) 
	{
		if(defined("self::$name")) {
			return constant("self::$name");
		}
		trigger_error ("$name  isn't defined");
	}
}
?>
