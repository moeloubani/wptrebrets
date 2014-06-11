<?php
/**
 * Created by PhpStorm.
 * User: moeloubani
 * Date: 2014-06-10
 * Time: 5:13 AM
 */

namespace wptrebrets\inc;


class Update extends Save {

    protected $post;
    protected $photos;
    protected $mls;
    protected $wpdb;

    function __construct($mls, $wpdb, $photos)
    {
        $this->mls = $mls;
        $this->wpdb = $wpdb;
        $this->photos = $photos;
    }


    public function photos(Array $photos)
    {
        $upload_dir = wp_upload_dir();


        $mls_array = explode(',', $this->mls);

        foreach ($mls_array as $property) {

            $first_letter = substr($property, 0, 1);
            $numeric = substr($property, 1);
            $dir = $upload_dir['basedir'] . '/wptreb/' . $first_letter . '/' . $numeric;

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $n = 1;

            foreach ($photos as $photo) {
                file_put_contents($dir.'/'.$n.'.jpg', $photo['Data']);
                $n++;
            }


        }


    }

    public function posts(Array $post)
    {
        //assign database variable
        $wpdb = $this->wpdb;

        //get post data
        $property_formatted = array();

        //loop through properties found
        foreach ($post as $property) {

            //reassign fields to ones that look good

            $property_formatted['price'] = $property['Lp_dol'];
            $property_formatted['mls'] = $property['Ml_num'];
            $property_formatted['address'] = $property['Addr'];
            $property_formatted['bathrooms'] = $property['Bath_tot'];
            $property_formatted['bedrooms'] = $property['Br'];
            $property_formatted['province'] = $property['County'];
            $property_formatted['broker'] = $property['Rltr'];
            $property_formatted['rooms'] = $property['Rms'];
            $property_formatted['rentorsale'] = $property['S_r'];
            $property_formatted['status'] = $property['Status'];
            $property_formatted['postal_code'] = $property['Zip'];
            $property_formatted['city'] = $property['Area'];
            $property_formatted['last_updated_text'] = $property['Timestamp_sql'];
            $property_formatted['last_updated_photos'] = $property['Pix_updt'];
            $property_formatted['description'] = $property['Ad_text'];

            //set up arguments before entering post to wp
            $post_args = array(
                'post_title' => $property_formatted['address'],
                'post_content' => $property_formatted['description'],
                'post_status' => 'publish',
                'post_type' => 'wptrebs_property',
            );

            //check if post title already exists
            $query = $wpdb->prepare(
                'SELECT ID FROM ' . $wpdb->posts . '
                WHERE post_title = %s
                AND post_type = \'wptrebs_property\'',
                $property_formatted['address']
            );

            $wpdb->query( $query );

            //if the post title exists then update, if not create new post
            if ( $wpdb->num_rows ) {
                $post_id = $wpdb->get_var( $query );
                self::update($post_id, $property_formatted);
            } else {

                //insert post and return new post id
                $posted_property = wp_insert_post($post_args);

                //add post meta using the new post id and good looking array
                foreach ($property_formatted as $key => $value) {
                    if (!empty($value)) {
                        add_post_meta( $posted_property, $key, $value, true ) || update_post_meta( $posted_property, $key, $value );
                    }
                }

                //create photos
                self::photos($this->photos);
            }




        }

        //store first photo as featured

        //store remaining photos in array in custom field
    }

} 