<?php

namespace wptrebrets\inc;


class Commands
{

    public function getProperties($expired = null)
    {
        $properties = array();

        if ($expired === true) {
            $args = array (
                'post_type' => array( 'property' ),
                'posts_per_page' => 900,
                'meta_query'             => array(
                    array(
                        'key'       => 'expired',
                        'value'     => 'true',
                        'compare'   => '!=',
                    ),
                ),
            );
        } else {
            $args = array (
                'post_type' => array( 'property' ),
                'posts_per_page' => 900,
            );
        }

        $property_query = new \WP_Query( $args );

        if ( $property_query->have_posts() ) {
            while ( $property_query->have_posts() ) {
                $property_query->the_post();
                $properties[] = get_post_meta(get_the_ID(), 'mls', true);
            }
        }

        // Restore original post data
        wp_reset_postdata();

        return $properties;
    }

    public function getInitial()
    {
        $feed = new \wptrebrets\inc\Feed();
        $feed->start();
        $feed->initialSearch();
        $save = new \wptrebrets\inc\Save($feed);
    }

    public function getDaily()
    {
        $feed = new \wptrebrets\inc\Feed();
        $feed->start();
        $feed->dailySearch();
        $save = new \wptrebrets\inc\Save($feed);
    }

    public function verifyCurrent()
    {
        $current = self::getProperties(true);
        $expired = array();

        $checking = new Feed();
        $checking->start('meta');
        $results = $checking->metaSearch($current);
        $results = $results->toArray();

        //Loop through results, look for unavailable
        foreach ($results as $result) {

            if($result['Status'] !== "A") {
                $expired[] = $result['Ml_num'];
            }

        }

        return $expired;

    }

    public function changeExpired($expired)
    {
        if (is_array($expired) && count($expired) > 1) {
            $args = array (
                'post_type'              => array( 'property' ),
                'meta_query'             => array(
                    array(
                        'key'       => 'mls',
                        'value'     => $expired,
                        'compare'   => 'IN',
                    ),
                ),
            );

            $expired_query = new WP_Query( $args );

            if ( $expired_query->have_posts() ) {
                while ( $expired_query->have_posts() ) {
                    $expired_query->the_post();

                    $id = get_the_ID();

                    update_post_meta($id, 'expired', 'true');
                    $title = get_the_title($id);

                    $property = array(
                        'ID'           => $id,
                        'post_title'   => $title . ' - SOLD',
                    );

                    wp_update_post( $property );
                }
            }

            wp_reset_postdata();
        }
    }
}