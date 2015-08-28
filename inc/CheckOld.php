<?php

namespace wptrebrets\inc;


class CheckOld {

    static function data($title, $added, $status)
    {
        global $user_ID, $wpdb;

        $query = $wpdb->prepare(
            'SELECT ID FROM ' . $wpdb->posts . '
            WHERE post_title = %s
            AND post_type = \'property\'',
            $title
        );
        $wpdb->query( $query );

        if ( $wpdb->num_rows ) {
            $post_id = $wpdb->get_var( $query );
            $timestamp = get_the_time('U', $post_id);

            $added = strtotime($added);

            //Delete if status is no longer available
            if ($status !== 'A') {
                wp_delete_post( $post_id, true);
                $result = array('delete' => $post_id);
            } elseif ($timestamp < $added) {
                $result = array('update' => $post_id);
            } elseif ($timestamp > $added) {
                $result = array(
                    'posted' => $timestamp
                );
            }
        } else {
            //Mark as new property
            $result = array(
                'new' => 1
            );
        }
        return $result;
    }
} 