<?php
namespace Nanga;

final class ContactSettings
{

    protected $config;

    private function __construct()
    {
        $defaultConfig = [
            'notification'   => get_option('options_nanga_contact_notification'),
            'notificationTo' => get_option('options_nanga_contact_notification_to'),
        ];
        $this->config  = $defaultConfig;
        add_action('acf/init', [$this, 'settingsPage']);
        add_action('acf/init', [$this, 'settingsFields']);
        /*
        add_action('acf/input/admin_head', function () {
            remove_meta_box('submitdiv', 'acf_options_page', 'side');
        }, 11);
        add_filter('get_user_option_screen_layout_toplevel_page_contact-settings', '__return_true');
        */
    }

    public static function instance()
    {
        static $instance = false;
        if ($instance === false) {
            $instance = new static();
        }

        return $instance;
    }

    public function setConfiguration($config)
    {
        if ( ! empty($config)) {
            $this->config = array_replace($this->config, $config);
        }
    }

    public function getConfiguration()
    {
        return $this->config;
    }

    public function getNotification()
    {
        return $this->config['notification'];
    }

    public function getNotificationTo()
    {
        return $this->config['notificationTo'];
    }

    public function settingsPage()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page([
                'capability' => 'manage_options',
                'icon_url'   => 'dashicons-hammer',
                'menu_slug'  => 'contact-settings',
                'menu_title' => 'Contact Form',
                'page_title' => 'Contact Form Configuration',
                'position'   => false,
                'redirect'   => false,
            ]);
        }
    }

    public function settingsFields()
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key'                   => 'group_nanga_contact_settings',
                'title'                 => '&nbsp;',
                'fields'                => [
                    [
                        'key'               => 'field_nanga_contact_notification_to',
                        'label'             => 'Send notification to',
                        'name'              => 'nanga_contact_notification_to',
                        'type'              => 'email',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => [
                            'width' => '50',
                            'class' => '',
                            'id'    => '',
                        ],
                        'default_value'     => '',
                        'placeholder'       => '',
                        'prepend'           => '',
                        'append'            => '',
                    ],
                    [
                        'key'               => 'field_nanga_contact_notification',
                        'label'             => 'Notification',
                        'name'              => 'nanga_contact_notification',
                        'type'              => 'true_false',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => [
                            'width' => '50',
                            'class' => '',
                            'id'    => '',
                        ],
                        'message'           => '',
                        'default_value'     => 0,
                        'ui'                => 1,
                        'ui_on_text'        => 'Yes',
                        'ui_off_text'       => 'No',
                    ],
                    [
                        'key'               => 'field_nanga_contact_help',
                        'label'             => 'Usage/Help',
                        'name'              => '',
                        'type'              => 'message',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => [
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ],
                        'message'           => 'You can find usage instructions here: <a href="https://github.com/Mallinanga/nanga-contact" target="_blank">https://github.com/Mallinanga/nanga-contact</a>',
                        'new_lines'         => 'br',
                        'esc_html'          => 0,
                    ],
                ],
                'location'              => [
                    [
                        [
                            'param'    => 'options_page',
                            'operator' => '==',
                            'value'    => 'contact-settings',
                        ],
                    ],
                ],
                'menu_order'            => 0,
                'position'              => 'acf_after_title',
                'style'                 => 'default',
                'label_placement'       => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen'        => '',
                'active'                => 1,
                'description'           => '',
            ]);
        }
    }

    private function __clone()
    {
    }

    private function __sleep()
    {
    }

    private function __wakeup()
    {
    }
}
