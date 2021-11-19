<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://mortenmosa.com
 * @since      1.0.0
 *
 * @package    Mosa_Forms
 * @subpackage Mosa_Forms/includes
 */

use WordPlate\Acf\Fields\FlexibleContent;
use WordPlate\Acf\Fields\Group;
use WordPlate\Acf\Fields\Layout;
use WordPlate\Acf\Fields\Repeater;
use WordPlate\Acf\Fields\Select;
use WordPlate\Acf\Fields\Tab;
use WordPlate\Acf\Fields\Textarea;
use WordPlate\Acf\Fields\TrueFalse;
use WordPlate\Acf\Fields\WysiwygEditor;
use WordPlate\Acf\Fields\Image;
use WordPlate\Acf\Fields\Text;
use WordPlate\Acf\Location;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mosa_Forms
 * @subpackage Mosa_Forms/includes
 * @author     Morten Sassi <dev@mortenmosa.com>
 */
class Mosa_Forms
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Mosa_Forms_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('MOSA_FORMS_VERSION')) {
            $this->version = MOSA_FORMS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'mosa-forms';

        $this->load_dependencies();
        $this->set_locale();
        $this->register_form_cpt();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Mosa_Forms_Loader. Orchestrates the hooks of the plugin.
     * - Mosa_Forms_i18n. Defines internationalization functionality.
     * - Mosa_Forms_Admin. Defines all hooks for the admin area.
     * - Mosa_Forms_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * Composer deps
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/autoload.php';

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-mosa-forms-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-mosa-forms-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-mosa-forms-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-mosa-forms-public.php';

        $this->loader = new Mosa_Forms_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Mosa_Forms_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Mosa_Forms_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register CPT and fields
     */
    private function register_form_cpt()
    {
        add_action('init', function () {
            register_extended_post_type('mosa_form', [
                'supports' => ['title'],
                'show_in_rest' => true
            ], [
                'singular' => __('Form', 'mosa'),
                'plural' => __('Forms', 'mosa'),
            ]);

            register_extended_post_type('mosa_form_signup', [
                'supports' => ['title'],
            ], [
                'singular' => __('Signup', 'mosa'),
                'plural' => __('Signups', 'mosa'),
            ]);
        });

        add_action('acf/init', function () {
            register_extended_field_group([
                'title' => __('Form Settings', 'mosa'),
                'style' => 'default',
                'show_in_rest' => true,
                'id' => $this->plugin_name . 'form-fields',
                'key' => $this->plugin_name . 'form-fields',
                'fields' => [
                    Text::make(__('Title', 'mosa'), 'title'),
                    Tab::make(__('Form', 'mosa')),
                    Repeater::make(__('Steps', 'mosa'), 'steps')
                        ->layout('block')
                        ->buttonLabel(__('Add step', 'mosa'))
                        ->fields([
                            Text::make(__('Title', 'mosa'), 'title'),
                            FlexibleContent::make(__('Steps', 'mosa'), 'fields')
                                ->buttonLabel(__('Add field', 'mosa'))
                                ->layouts([
                                    Layout::make(__('Input', 'mosa'), 'input')
                                        ->fields([
                                            Text::make(__('Label', 'mosa'), 'label')
                                                ->wrapper([
                                                    'width' => '50%'
                                                ]),
                                            Select::make(__('Type', 'mosa'), 'type')
                                                ->choices([
                                                    'text' => __('Text', 'mosa'),
                                                    'email' => __('Email', 'mosa'),
                                                    'tel' => __('Phone', 'mosa'),
                                                    'file' => __('File', 'mosa')
                                                ])
                                                ->defaultValue('text')
                                                ->stylisedUi()
                                                ->wrapper([
                                                    'width' => '25%'
                                                ]),
                                            TrueFalse::make(__('Required', 'mosa'), 'is_required')
                                                ->stylisedUi()
                                                ->wrapper([
                                                    'width' => '25%'
                                                ])
                                        ]),
                                    Layout::make(__('Textarea', 'mosa'), 'textarea')
                                        ->fields([
                                            Text::make(__('Label', 'mosa'), 'label')
                                        ]),
                                    Layout::make(__('Select', 'mosa'), 'select')
                                        ->fields([
                                            Text::make(__('Label', 'mosa'), 'label')
                                                ->wrapper([
                                                    'width' => '75%'
                                                ]),
                                            TrueFalse::make(__('Required', 'mosa'), 'is_required')
                                                ->stylisedUi()
                                                ->wrapper([
                                                    'width' => '25%'
                                                ]),
                                            Repeater::make(__('Choices', 'mosa'), 'choices')
                                                ->buttonLabel(__('Add Choice', 'mosa'))
                                                ->fields([
                                                    Text::make(__('Choice', 'mosa'), 'choice')
                                                ])
                                        ]),
                                    Layout::make(__('Checkbox', 'mosa'), 'checkbox')
                                        ->fields([
                                            Text::make(__('Label', 'mosa'), 'label')
                                                ->wrapper([
                                                    'width' => '50%'
                                                ]),
                                            TrueFalse::make(__('Required', 'mosa'), 'is_required')
                                                ->stylisedUi()
                                                ->wrapper([
                                                    'width' => '50%'
                                                ]),
                                            WysiwygEditor::make(__('Description', 'mosa'), 'description')
                                                ->delay()
                                                ->mediaUpload(0)
                                                ->tabs('visual')
                                                ->toolbar('mosa_forms_toolbar'),
                                        ]),
                                    Layout::make(__('Cards', 'mosa'), 'cards')
                                        ->fields([
                                            Text::make(__('Label', 'mosa'), 'label'),
                                            Repeater::make(__('Choices', 'mosa'), 'choices')
                                                ->buttonLabel(__('Add Choice', 'mosa'))
                                                ->layout('block')
                                                ->fields([
                                                    Text::make(__('Choice', 'mosa'), 'title'),
                                                    WysiwygEditor::make(__('Description', 'mosa'), 'description')
                                                        ->delay()
                                                        ->mediaUpload(0)
                                                        ->tabs('visual')
                                                        ->toolbar('mosa_forms_toolbar'),
                                                    Group::make(__('Pricing Table', 'mosa'), 'pricing_table')
                                                        ->fields([
                                                            Text::make(__('Headline', 'mosa'), 'headline'),
                                                            Repeater::make(__('Pricing', 'mosa'), 'pricing')
                                                                ->buttonLabel(__('Add Price', 'mosa'))
                                                                ->fields([
                                                                    Text::make(__('Title', 'mosa'), 'title'),
                                                                    Repeater::make(__('Price', 'mosa'), 'price')
                                                                        ->fields([
                                                                            Text::make(__('Audience', 'mosa'), 'audience'),
                                                                            Text::make(__('Price', 'mosa'), 'value'),
                                                                        ])
                                                                ])
                                                        ]),
                                                ])
                                        ])
                                ])
                        ]),
                    Tab::make(__('Compliance', 'mosa')),
                    Repeater::make(__('User Opt Check', 'mosa'), 'compliance_opt_check')
                        ->buttonLabel(__('Add Check', 'mosa'))
                        ->layout('block')
                        ->fields([
                            TrueFalse::make(__('Required', 'mosa'), 'is_required')
                                ->stylisedUi()
                                ->wrapper([
                                    'width' => '25%'
                                ]),
                            WysiwygEditor::make(__('Opt-In Text', 'mosa'), 'text')
                                ->delay()
                                ->mediaUpload(0)
                                ->tabs('visual')
                                ->toolbar('mosa_forms_toolbar')
                                ->wrapper([
                                    'width' => '75%'
                                ]),
                        ]),
                    Tab::make(__('Mail', 'mosa')),
                    Group::make(__('Mail Settings', 'mosa'), 'mail_settings')
                    ->layout('block')
                    ->fields([
                        Text::make(__('Subject', 'mosa'), 'subject'),
                        Text::make(__('Receiver', 'mosa'), 'recipient'),
                        WysiwygEditor::make(__('Success Mail', 'mosa'), 'body')
                            ->delay()
                            ->mediaUpload(0)
                            ->tabs('visual')
                            ->toolbar('mosa_forms_toolbar')
                    ]),
                    Tab::make(__('Messages', 'mosa')),
                    Group::make(__('Form Messages', 'mosa'), 'form_messages')
                        ->layout('block')
                        ->fields([
                            Text::make(__('Success', 'mosa'), 'success'),
                            Text::make(__('Warning', 'mosa'), 'warning'),
                            Text::make(__('Error', 'mosa'), 'error'),
                        ])
                ],
                'location' => [
                    Location::if('post_type', 'mosa_form')
                ],
            ]);
        });

        add_shortcode('mosa_form', [$this, 'mosa_form_shortcode']);
    }

    /**
     * @return WP_Error
     */
    private function mosa_form_error_handler()
    {
        return new WP_Error('no_id', __('Please pass a form ID', 'mosa'));
    }

    /**
     * @param $parameters
     * @return false|string
     *
     * Create vue wrapper
     */
    public function mosa_form_shortcode($parameters)
    {
        $error_handler = $this->mosa_form_error_handler();

        if (!$parameters || !$parameters['id']) {
            echo $error_handler->get_error_message();
            return false;
        }

        $app_id = $this->get_plugin_name() . '-app-' . $parameters['id'];

        wp_enqueue_script($this->plugin_name);

        return '<div id="' . $app_id . '" data-form-id="' . $parameters['id'] . '"></div>';
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Mosa_Forms_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Mosa_Forms_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Mosa_Forms_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

}
