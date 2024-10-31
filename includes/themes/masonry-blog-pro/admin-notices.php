<?php

/**
 * Add admin notice when active theme, just show one time
 *
 * @return bool|null
 */
if( ! function_exists( 'perfectwpthemes_toolkit_masonry_blog_pro_admin_notice' ) ) {

    function perfectwpthemes_toolkit_masonry_blog_pro_admin_notice() {

        global $current_user;

        $user_id = $current_user->ID;

        $admin_url = 'themes.php?page=' . 'masonry-blog-pro' . '-about';
        
        if ( !get_user_meta( $user_id, 'masonry-blog-pro' . '_notice_ignore' ) ) {
            ?>
            <div class="notice perfectwpthemes-toolkit-notice">

                <h1>
                    <?php
                    /* translators: %1$s: theme name, %2$s theme version */
                    printf( esc_html__( 'Welcome to %1$s - Version %2$s', 'perfectwpthemes-toolkit' ), 'Masonry Blog Pro', '1.0.0' );
                    ?>
                </h1>

                <p>
                    <?php
                    /* translators: %1$s: theme name, %2$s link */
                    printf( __( 'Welcome! Thank you for choosing %1$s! To fully take advantage of our premium plugin please make sure you visit our <a href="%2$s">Welcome page</a>', 'perfectwpthemes-toolkit' ), 'Masonry Blog Pro', esc_url( admin_url( $admin_url ) ) );
                    printf( '<a href="%1$s" class="notice-dismiss dashicons dashicons-dismiss dashicons-dismiss-icon"></a>', '?' . 'masonry-blog-pro' . '_notice_ignore=0' );
                    ?>
                </p>
                <p>
                    <a href="<?php echo esc_url( admin_url( $admin_url ) ); ?>" class="button button-primary button-hero" style="text-decoration: none;">
                        <?php
                        /* translators: %s theme name */
                        printf( esc_html__( 'Get started with %s', 'perfectwpthemes-toolkit' ), 'Masonry Blog Pro' )
                        ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }
}
add_action( 'admin_notices', 'perfectwpthemes_toolkit_masonry_blog_pro_admin_notice' );


if( !function_exists( 'perfectwpthemes_toolkit_masonry_blog_pro_notice_ignore' ) ) {

    function perfectwpthemes_toolkit_masonry_blog_pro_notice_ignore() {

        global $current_user;

        $user_id     = $current_user->ID;

        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset( $_GET[ 'masonry-blog-pro_notice_ignore' ] ) && '0' == $_GET[ 'masonry-blog-pro_notice_ignore' ] ) {

            add_user_meta( $user_id, 'masonry-blog-pro_notice_ignore', 'true', true );
        }
    }
}
add_action( 'admin_init', 'perfectwpthemes_toolkit_masonry_blog_pro_notice_ignore' );