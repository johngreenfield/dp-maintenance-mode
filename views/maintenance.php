<?php
/**
 * Construction mode template that's shown to logged out users.
 *
 * @package   dp-maintenance-mode
 * @copyright Copyright (c) 2024, John Greenfield
 * @license   GPL3+
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <!-- HTML5 Shiv -->
        <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv-printshiv.js"></script>
        <![endif]-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="format-detection" content="telephone=yes" />
        <meta name="HandheldFriendly" content="true" /> 
        <meta name="theme-color" content="#414A4F" />
        <meta name="mobile-web-app-capable" content="yes">
        <meta name='viewport' content='width=device-width, initial-scale=1' />
        <meta name="language" content="en-gb" />
        <meta name="rating" content="general" />
        
        <link rel="profile" href="http://gmpg.org/xfn/11" />

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Quicksand" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="<?php echo plugins_url( 'assets/css/maintenance.css', dirname( __FILE__ ) ); ?>" />

        <title>
            <?php $title = get_option('dpmm-site-title') ? $this->get_title() : get_bloginfo('name') . ' | Under Construction'; echo $title; ?>
        </title>
    </head>

    <body>
        <!-- Header -->
        <header>
            <nav>
                <a href="<?php echo home_url( '/' ); ?>" class="site-link">
                    <?php 
                    $image_id = get_option( 'dpmm-image-id' );
                    if(intval( $image_id ) > 0) {
                        $image = wp_get_attachment_image_src( $image_id, 'full', true );
                        echo '<img src="' . $image[0] . '" alt="' . get_bloginfo('name') . '" id="site-logo" />';
                    } else {
                        echo '<h1>' . get_bloginfo('name') . '</h1>'; 
                    } ?>
                </a>
            </nav>
        </header>

        <!-- Main Content -->
        <section class="hero">
            <?php echo $this->get_content(); ?>
        </section>

        <!-- Footer -->
        <footer>
            <?php $socials = get_option('dpmm-social-profiles'); ?>
            <?php if(!count($socials) === 0): ?>
            <ul>
            <?php 
                foreach($socials as $name => $option):
                    foreach($option as $link):
                        echo '<li><a href="' . $link . '"><i class="fa fa-' . $name . '"></i></a></li>';
                    endforeach;
                endforeach; 
                ?>
            </ul>
            <?php endif; ?>
            <p>Copyright <?php echo date('Y') . ' ' . get_bloginfo('name'); ?></p>
            <?php /* No attribution required here, you can change or remove this messages. */ ?>
            <p class="attribution"><?php _e('Made by <a href="https://johngreenfield.dev/" target="_blank">John Greenfield</a> with &#10084; in Cymru.', DPMM_PLUGIN_DOMAIN) ?></p>
        </footer>
        <?php echo $this->get_snippet(); ?>
    </body>
</html>