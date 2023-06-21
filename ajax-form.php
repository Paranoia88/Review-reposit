<form id="registration-form" class="needs-validation" novalidate>

        <div class="mb-3">
            <label for="username" class="form-label"><?php _e( 'Username:', 'review-registration-plugin' ); ?></label>
            <input type="text" id="username" name="username" class="form-control" required>
            <div class="invalid-feedback">
                <?php _e( 'Please enter a username.', 'review-registration-plugin' ); ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label"><?php _e( 'Email:', 'review-registration-plugin' ); ?></label>
            <input type="email" id="email" name="email" class="form-control" required>
            <div class="invalid-feedback">
                <?php _e( 'Please enter a valid email address.', 'review-registration-plugin' ); ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label"><?php _e( 'Password:', 'review-registration-plugin' ); ?></label>
            <input type="password" id="password" name="password" class="form-control" required>
            <div class="invalid-feedback">
                <?php _e( 'Please enter a password.', 'review-registration-plugin' ); ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="first_name" class="form-label">First Name:</label>
            <input type="text" name="first_name" id="first_name" class="form-control" required>
            
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name:</label>
            <input type="text" name="last_name" id="last_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="review" class="form-label">Review:</label>
            <textarea name="review" id="review" rows="4" cols="50" class="form-control"></textarea>
            <div class="invalid-feedback">
                <?php _e( 'Please enter Review.', 'review-registration-plugin' ); ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="review_rating" class="form-label">Review Rating (out of 5):</label>
            <input type="number" name="review_rating" id="review_rating" min="0" max="5" class="form-control" required>
            <div class="invalid-feedback">
                <?php _e( 'Please rate.', 'review-registration-plugin' ); ?>
            </div>
        </div>


    
    <?php wp_nonce_field( 'registration_ajax_nonce', 'security' ); ?>

    <button type="submit" class="btn btn-primary"><?php _e( 'Register', 'review-registration-plugin' ); ?></button>
</form>
