<?php
/**
 * Created by PhpStorm.
 * User: moeloubani
 * Date: 2014-06-12
 * Time: 8:37 AM
 */

namespace wptrebrets\inc;


class CheckOld {

    public function __construct($title) {

    }

    static function test($title) {
        global $user_ID, $wpdb;

        $query = $wpdb->prepare(
            'SELECT ID FROM ' . $wpdb->posts . '
            WHERE post_title = %s
            AND post_type = \'wptrebs_property\'',
            $title
        );
        $wpdb->query( $query );

        if ( $wpdb->num_rows ) {
            $post_id = $wpdb->get_var( $query );



            return $post_id;
        } else {
            return false;
        }
    }
} 