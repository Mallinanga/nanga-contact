<?php
namespace Nanga;

class Contact
{

    private function __construct()
    {
        $contactSettings = ContactSettings::instance();
        add_action('init', [$this, 'registerFeedback']);
        add_action('add_meta_boxes', [$this, 'feedbackMetaboxes']);
        add_action('admin_menu', [$this, 'feedbackDisableMetaboxes']);
        add_filter('get_user_option_screen_layout_feedback', '__return_true');
        add_action('admin_menu', [$this, 'feedbackMenu']);
        add_action('wp_enqueue_scripts', [$this, 'assets']);
        add_action('wp_ajax_nopriv_nanga_contact', [$this, 'handle']);
        add_action('wp_ajax_nanga_contact', [$this, 'handle']);
    }

    public static function instance()
    {
        static $instance = false;
        if ($instance === false) {
            $instance = new static();
        }

        return $instance;
    }

    public static function form($options = null)
    {
        $fields = [
            [
                'label'    => 'Name',
                'name'     => 'name',
                'required' => true,
                'type'     => 'text',
            ],
            [
                'label'    => 'Email',
                'name'     => 'email',
                'required' => true,
                'type'     => 'email',
            ],
            [
                'label'    => 'Message',
                'name'     => 'comment',
                'required' => true,
                'type'     => 'textarea',
            ],
            [
                'label' => 'Send',
                'type'  => 'submit',
            ],
        ];
        if ( ! empty($options)) {
            $fields = $options['fields'];
        }
        $fieldsMarkup = '';
        foreach ($fields as $field) {
            $fieldsMarkup .= '<div class="form__field form__field--' . $field['type'] . '" data-type="' . $field['type'] . '">';
            if ($field['type'] == 'text') {
                $fieldsMarkup .= '<label for="' . strtolower($field['name']) . '">' . $field['label'] . '</label>';
                $fieldsMarkup .= '<input type="text" id="' . strtolower($field['name']) . '" name="' . strtolower($field['name']) . '" placeholder="' . $field['label'] . '"' . ($field['required'] ? ' required="required"' : null) . '>';
            }
            if ($field['type'] == 'email') {
                $fieldsMarkup .= '<label for="' . strtolower($field['name']) . '">' . $field['label'] . '</label>';
                $fieldsMarkup .= '<input type="email" id="' . strtolower($field['name']) . '" name="' . strtolower($field['name']) . '" placeholder="' . $field['label'] . '"' . ($field['required'] ? ' required="required"' : null) . '>';
            }
            if ($field['type'] == 'textarea') {
                $fieldsMarkup .= '<label for="' . strtolower($field['name']) . '">' . $field['label'] . '</label>';
                $fieldsMarkup .= '<textarea id="' . strtolower($field['name']) . '" name="' . strtolower($field['name']) . '" placeholder="' . $field['label'] . '" rows="8"' . ($field['required'] ? ' required="required"' : null) . '></textarea>';
            }
            if ($field['type'] == 'submit') {
                $fieldsMarkup .= '<button type="submit">' . $field['label'] . '</textarea>';
            }
            $fieldsMarkup .= '</div>';
        }

        return '<form class="nanga-contact" method="POST"><div class="form__message"></div><div class="form__fields">' . $fieldsMarkup . '</div>' . wp_nonce_field('nanga_contact', 'nanga_contact', false, false) . '</form>';
    }

    public function registerFeedback()
    {
        $labels       = [
            'add_new'            => false,
            'add_new_item'       => false,
            'all_items'          => 'Feedback',
            'edit_item'          => 'Review Feedback',
            'menu_name'          => 'Feedback',
            'name'               => 'Feedback',
            'name_admin_bar'     => 'Feedback',
            'new_item'           => false,
            'not_found'          => 'No Feedback found',
            'not_found_in_trash' => 'No Feedback found in Trash',
            'search_items'       => 'Search Feedback',
            'singular_name'      => 'Feedback',
            'update_item'        => 'Update Feedback',
            'view_item'          => 'View Feedback',
            'view_items'         => 'View Feedback',
        ];
        $capabilities = [
            'create_posts'       => false,
            'delete_post'        => 'delete_post',
            'delete_posts'       => 'delete_posts',
            'edit_others_posts'  => 'edit_others_posts',
            'edit_post'          => 'edit_post',
            'edit_posts'         => 'edit_posts',
            'publish_posts'      => 'publish_posts',
            'read_post'          => 'read_post',
            'read_private_posts' => 'read_private_posts',
        ];
        $args         = [
            'can_export'          => true,
            'capabilities'        => $capabilities,
            'capability_type'     => 'post',
            'delete_with_user'    => false,
            'description'         => 'Feedback from Contact Form',
            'exclude_from_search' => true,
            'has_archive'         => false,
            'hierarchical'        => false,
            'label'               => 'Feedback',
            'labels'              => $labels,
            'map_meta_cap'        => true,
            'menu_icon'           => 'dashicons-megaphone',
            'menu_position'       => 69,
            'public'              => false,
            'publicly_queryable'  => false,
            'rewrite'             => false,
            'show_in_admin_bar'   => false,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => false,
            'show_in_rest'        => false,
            'show_ui'             => current_user_can('edit_pages'),
            'supports'            => false,
        ];
        register_post_type('feedback', $args);
    }

    public function feedbackMetaboxes()
    {
        add_meta_box('feedback_content', 'Feedback Details', [$this, 'feedbackMetaboxRender'], 'feedback', 'normal', 'high');
    }

    public function feedbackMetaboxRender($post)
    {
        echo '<p><strong>' . __('Date') . ':</strong><br>' . date(get_option('date_format'), strtotime($post->post_date)) . '<br>' . $post->post_content . '</p>';
    }

    public function feedbackDisableMetaboxes()
    {
        remove_meta_box('commentstatusdiv', 'feedback', 'normal');
        remove_meta_box('slugdiv', 'feedback', 'normal');
        remove_meta_box('submitdiv', 'feedback', 'side');
    }

    public function feedbackMenu()
    {
        remove_submenu_page('edit.php?post_type=feedback', 'post-new.php?post_type=feedback');
    }

    public function assets()
    {
        wp_enqueue_script('nanga-contact', get_template_directory_uri() . '/vendor/nanga/nanga-contact/assets/js/nanga-contact.js', ['jquery'], null, true);
        wp_localize_script('nanga-contact', 'nangaContact', ['endpoint' => admin_url('admin-ajax.php')]);
    }

    public function handle()
    {
        parse_str($_REQUEST['fields'], $fields);
        if ( ! $this->validate($fields)) {
            wp_send_json_error(apply_filters('nanga_contact_error_message', 'Something went wrong.'));
        }
        unset($fields['nanga_contact']);
        unset($fields['_wp_http_referer']);
        $title = 'Feedback from ' . date('d/m/Y');
        if (isset($fields['email'])) {
            $title = 'Feedback from ' . $fields['email'];
        }
        $content = '';
        foreach ($fields as $key => $value) {
            $content .= '<strong>' . __(ucwords(str_replace('_', ' ', $key))) . ':</strong><br>';
            $content .= sanitize_text_field($value) . '<br>';
        }
        $feedback = wp_insert_post([
            'comment_status' => 'closed',
            'post_content'   => $content,
            'post_status'    => 'publish',
            'post_title'     => $title,
            'post_type'      => 'feedback',
        ]);
        $this->notify($title, $content);
        add_post_meta($feedback, 'nanga_contact_feedback_from_email', $fields['email'], true);
        wp_send_json_success(apply_filters('nanga_contact_thanks_message', 'Thanks for contacting us.'));
    }

    private function validate($fields)
    {
        if ( ! wp_verify_nonce($fields['nanga_contact'], 'nanga_contact')) {
            return false;
        }
        if ( ! isset($fields['email'])) {
            return false;
        }
        if ( ! filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        if (empty($fields['comment'])) {
            return false;
        }

        return true;
    }

    private function notify($subject, $body)
    {
        $contactSettings = ContactSettings::instance();
        if ($contactSettings->getNotification()) {
            add_filter('wp_mail_from', function () {
                return 'noreply@' . @parse_url(site_url(), PHP_URL_HOST);
            });
            add_filter('wp_mail_from_name', function () {
                return get_bloginfo('name');
            });
            wp_mail($contactSettings->getNotificationTo(), $subject, $body, ['Content-Type:text/html;charset=UTF-8']);
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
