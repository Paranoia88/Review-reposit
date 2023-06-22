<?php
/**
 * Plugin Name: User Registration Form With Review
 * Plugin URI: #
 * Description: Creates a user registration form with email, password, first name, last name, review text area, and review rating fields.
 * Version: 1.0
 * Author: Utsav
 * Text Domain: review-registration-plugin
 * Domain Path: /languages
 * PHP version: 7.4.33
 * WP version: 6.2.2
 **/


class ReviewRegistrationPlugin {
        public function __construct() {
            add_action('plugins_loaded', array($this, 'loadTextdomain'));
            add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
            add_action('wp_enqueue_scripts', array($this, 'register_bootstrap_files'));
            add_action('wp_ajax_registration', array($this, 'registerUser'));
            add_action('wp_ajax_nopriv_registration', array($this, 'registerUser'));
            add_shortcode('complete_ajx_registration_form', array($this, 'registrationFormShortcode'));
            add_action('user_register', array($this, 'send_registration_email'), 10, 1);
            add_action('wp_ajax_registration', array($this,'my_ajax_registration_callback'));
            add_action('wp_ajax_nopriv_registration', array($this,'my_ajax_registration_callback'));

            
        }

        // Load plugin text domain for localization
        public function loadTextdomain() {
            load_plugin_textdomain('review-registration-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
        }

        // Enqueue scripts and styles securely
        public function enqueueScripts() {
            wp_enqueue_style('form-style', plugin_dir_url(__FILE__) . 'assets/css/style.css', array(), '2.0', "");
            wp_enqueue_script('registration-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), '1.0', true);
            wp_localize_script('registration-script', 'registration_ajax_object',array(
                $this,
                ['ajax_url' => admin_url('admin-ajax.php')],
                ['nonce' => wp_create_nonce('registration_ajax_nonce')]
            ));        
        }
    
    
        // Enqueue Bootstrap CSS and JavaScript
        function register_bootstrap_files() {
            wp_enqueue_style('bootstrap-css', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css');
            wp_enqueue_script('bootstrap-js', plugin_dir_url(__FILE__) . 'assets/js/bootstrap.min.js', array('jquery'), '5.0.0', true);
        }


        // AJAX user registration
        public function registerUser() {
        check_ajax_referer('registration_ajax_nonce', 'security');
       

        $form_data = $_POST['form_data'];   
    
        
            
         // Parse the form data
        parse_str($form_data, $parsed_data);

       
        // Store the form data in respective tables
        $username = sanitize_text_field($parsed_data['username']);
        $email = sanitize_email($parsed_data['email']);
        $password = sanitize_text_field($parsed_data['password']);
        $first_name = sanitize_text_field($parsed_data['first_name']);
        $last_name = sanitize_text_field($parsed_data['last_name']);
        $review = sanitize_textarea_field($parsed_data['review']);
        $review_rating = intval($parsed_data['review_rating']);
        
     
        error_log(print_r("Status code"));
        
        // Perform data validation

        if (empty($username) || empty($email) || empty($password)) {
            wp_send_json_error(json_encode(esc_html__('All fields are required.', 'review-registration-plugin')));
        }

        if (!is_email($email)) {
            wp_send_json_error(esc_html__('Invalid email address.', 'review-registration-plugin'));
        }


        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            wp_send_json_error(esc_html__('Invalid Username format.', 'review-registration-plugin'));
        }
        
        if (!is_email($email)) {
            wp_send_json_error(esc_html__('Invalid email format.', 'review-registration-plugin'));
        }
        
        // Validate $password (Example: Minimum length of 8 characters)
        if (strlen($password) < 8) {
            wp_send_json_error(esc_html__('Password should be minimun of length 8 characters.', 'review-registration-plugin'));
        }
        
        // Validate $first_name and $last_name
        if (empty($first_name) || empty($last_name)) {
            wp_send_json_error(esc_html__('First name and last name are required.', 'review-registration-plugin'));
        } elseif (!preg_match('/^[a-zA-Z\'-]+$/', $first_name) || !preg_match('/^[a-zA-Z\'-]+$/', $last_name)) {
            wp_send_json_error(esc_html__('Invalid name format', 'review-registration-plugin'));
        }
        
        // Validate $review
        if (empty($review)) {
            wp_send_json_error(esc_html__('Review is required', 'review-registration-plugin'));
        } elseif (strlen($review) > 1000) {
            wp_send_json_error(esc_html__('Review exceeds the maximum allowed length', 'review-registration-plugin'));
        
        }
        
        // Validate $review_rating (Example: Must be between 1 and 5)
        if ($review_rating < 1 || $review_rating > 5) {
            wp_send_json_error(esc_html__('Review rating must be between 1 and 5', 'review-registration-plugin'));
        }
        

        error_log(print_r('Hello'));
            
        // Extract the username from the email using the custom filter hook
        $username = apply_filters('extract_username_from_email', '', $email);

        // Perform your database operations to store the data
        // Replace the following code with your own logic

        // Store in wp_users table
        
        $user_id = wp_insert_user(array(
            'user_login' => $username,
            'user_email' => $email,
            'user_pass' => $password,
        ));


        // Store additional data in wp_usermeta table
        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'last_name', $last_name);
        update_user_meta($user_id, 'review', $review);
        update_user_meta($user_id, 'rating', $review_rating);
    
        // Return a response
        return wp_send_json_success('Form data stored successfully.');
        
    }

    



    // Custom filter hook for extracting username from email
    function extract_username_from_email($username, $email) {
        // Implement the logic to extract the username from the email
        // For example, you can use regex or string manipulation functions

        // Here's a simple example using explode():
        $email_parts = explode('@', $email);
        $extracted_username = $email_parts[0];

        // Apply any additional username transformation logic if needed
        // For example, removing special characters or converting to lowercase

        // Apply the custom filter to modify the username if necessary
        $filtered_username = $extracted_username;

        // Return the final username
        return $filtered_username;
    }

  

    // Render the registration form
    public function renderForm() {
        ob_start();

        // Include the template file
        $file = ABSPATH . 'wp-content/plugins/review/ajax-form.php';

        include_once($file);

        return ob_get_clean();
    }

    // Shortcode for displaying the registration form
    public function registrationFormShortcode() {
        return $this->renderForm();
    }

    // functions.php or your custom plugin file
    function send_registration_email($user_id) {
        // Get the user's email
        $user_info = get_userdata($user_id);
        $user_email = $user_info->user_email;

        // Set email subject and message
        $subject = 'Welcome to our site!';
        $message = 'Thank you for registering on our site.';

        // Send the email
        wp_mail($user_email, $subject, $message);
    }
    

}

// Initialize the plugin
new ReviewRegistrationPlugin();
 // Set user's display name
               
