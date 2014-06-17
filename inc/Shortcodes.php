<?php
class Shortcodes
{
    private $var = 'foo';

    protected static $instance = NULL;

    public static function get_instance()
    {
        // create an object
        NULL === self::$instance and self::$instance = new self;
        return self::$instance; // return the object
    }

    // Add Shortcode
    public function FeedShortcode( $atts ) {

        // Attributes
        extract( shortcode_atts(
                array(
                    'number' => '4',
                    'sort' => 'date_listed',
                ), $atts )
        );

        $args = array(
            'posts_per_page' => $number,
            'post_type' => 'wptrebs_property',
            'orderby' => 'meta_value',
            'meta_key' => 'wptrebs_last_updated_text'

        );

        $query = new WP_Query($args);

        if ( $query->have_posts() ) {
            echo '<ul class="wptrebs_feed">';
            while ( $query->have_posts() ) {
                $query->the_post(); ?>
                <li class="wptrebs_feed_item">
                    <div class="price"><span>$</span><?php echo number_format(get_post_meta(get_the_ID(), 'wptrebs_price', true)); ?></div>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('wptrebs_feed_img'); ?></a>
                </li>
                <?php
            } // end while
            echo '</ul>';
            echo '<div class="wptrebs_clear"></div>';
        } // end if
    }

}

add_shortcode( 'wptrebs_feed', array( Shortcodes::get_instance(), 'FeedShortcode' ) );