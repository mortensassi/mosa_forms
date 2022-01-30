<?php

/**
 * The fields-specific functionality of the plugin.
 *
 * @link       mortensassi.com
 * @since      1.0.0
 *
 * @package    Mosa_Forms
 * @subpackage Mosa_Forms/admin
 */

use Schrittweiter\Acf\Fields\FlexibleContent;
use Schrittweiter\Acf\Fields\Layout;
use WordPlate\Acf\Location;

/**
 * The fields-specific functionality of the plugin.
 *
 * Defines all fields used by plugin for form building and data storage
 *
 * @package    Mosa_Forms
 * @subpackage Mosa_Forms/admin
 * @author     Morten Sassi <mail@mortensassi.com>
 */
class Mosa_Forms_Fields {

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
     * @var string
     */
    private $fields;

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

        $this->fields = __DIR__ . '/acf-fields/';

	}

    /**
     * set acf url for build-in plugin use
     *
     * @param $url
     * @return string
     */
    public function get_acf_url($url) {
        return MOSA_FORMS_ACF_URL;
    }

    /**
     * hide acf from adminarea if shipped with plugin
     *
     * @return false
     */
    public function hide_acf_adminarea() {
        return false;
    }

    /**
     * Register mosa-forms fields by ACF Flexible Content Fields
     * Autoload layouts based on folder structure
     */
    public function register_acf_fields() {

        $fieldTypes = [];


        register_extended_field_group([
            'title' => __('Advanced field settings', 'mosa-forms'),
            'key' => 'mosa_forms_builder_field_settings',
            'id' => 'mosa_forms_builder_field_settings',
            'style' => 'default',
            'fields' =>
                array_merge(
                    require $this->fields . 'fields/settings/settings.php',
                    require $this->fields . 'fields/settings/attributes.php'
                )
            ,
            'location' => [
                Location::if('post_type', '!=','all'),
            ]
        ]);

        foreach (glob($this->fields . 'fields/field.*.php') as $file) {

            $layout = str_replace('field.', '', basename($file, '.php'));
            $key = str_replace('field.', 'layout_', basename($file, '.php'));

            $fieldData = get_file_data($file, array(
                    'title' => 'Title',
                    'category' => 'Category',
                )
            );
            $filename = basename($file);

            // set default additional validation empty
            // additonal validation can be set inside of a field type

            $additonalValidation = [];

            // # create layout types based on defined options

            $fields[$fieldData['title']] = Layout::make($fieldData['title'], $key)
                ->layout('block')
                ->modalSize('full')
                ->category(explode(',',$fieldData['category']))
                ->settingsClone(['group_9e84faae'])
                ->fields(
                    array_merge(
                        require $this->fields . 'fields/default/field.php',
                        require $this->fields . 'fields/' . $filename,
                        require $this->fields . 'fields/default/appearance.php',
                        require $this->fields . 'fields/default/validation.php',
                    )
                );

        }

        // # sort layouts by title

        ksort($fields);

        register_extended_field_group([
            'title' => __('Form Builder', 'mosa-forms'),
            'key' => 'mosa_forms_builder',
            'id' => 'mosa_forms_builder',
            'style' => 'default',
            'position' => 'acf_after_title',
            'instruction_placement' => 'tooltip',
            'menu_order' => 50,
            'fields' => [
                FlexibleContent::make(__('Fields', 'schrittweiter'), 'builder')
                    ->buttonLabel(__('add field', 'schrittweiter'))
                    ->modalSelection('full','Add field',4,true)
                    ->stylisedButton()
                    ->templates()
                    ->thumbnails()
                    ->settings()
                    ->addActions(['title', 'toggle', 'copy', 'close'])
                    ->emptyMessage(__('Add your first field and start creating your form .', 'schrittweiter'))
                    ->layouts($fields)
            ],
            'location' => [
                Location::if('post_type', 'mosa-form')
            ]
        ]);

    }
}
