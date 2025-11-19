<?php
defined( 'ABSPATH' ) or die( 'Something went wrong.' );

// Generic settings page for all modules except users-login
?>
<p class="movelogin-get-pro"><?php echo MoveLogin_Settings::get_pro_version_string(); ?></p>
<img src="<?php echo esc_url( MOVELOGIN_ADMIN_IMAGES_URL . 'placeholder.jpg' ); ?>" alt="<?php echo esc_attr( $this->modulenow ); ?>" style="max-width: 100%; max-height: 100%; height: auto; width: auto;" />

