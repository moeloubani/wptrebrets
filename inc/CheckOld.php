<?php
/**
 * Created by PhpStorm.
 * User: moeloubani
 * Date: 2014-06-12
 * Time: 8:37 AM
 */

namespace wptrebrets\inc;


class CheckOld {

    public function __construct() {

    }

    public static function photos() {

    }

    static function data($title, $added, $status) {
        global $user_ID, $wpdb;

        $query = $wpdb->prepare(
            'SELECT ID FROM ' . $wpdb->posts . '
            WHERE post_title = %s
            AND post_type = \'wptrebs_property\'',
            $title
        );
        $wpdb->query( $query );

        //dd($wpdb);

        if ( $wpdb->num_rows ) {
            $post_id = $wpdb->get_var( $query );
            $timestamp = get_the_time('U', $post_id);

            $added = strtotime($added);

            if ($status !== 'A') {
                wp_delete_post( $post_id, true);
                $result = array('delete' => $post_id);
            } elseif ($timestamp < $added) {
                $result = array('update' => $post_id);
                dd($result);
            } elseif ($timestamp > $added) {
                $result = array(
                    'posted' => $timestamp
                );
            }
        } else {
            $result = array(
                'new' => 1
            );
        }
        return $result;
    }
} 