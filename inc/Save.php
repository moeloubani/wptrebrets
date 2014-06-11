<?php

namespace wptrebrets\inc;

class Save
{
    protected $post;
    protected $photos;
    protected $mls;

    function __construct($mls)
    {
        $this->mls = $mls;
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
        //get post data
        $property_formatted = array();

        foreach ($post as $property) {

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

            $post_args = array(
                'post_title' => $property_formatted['address'],
                'post_content' => $property_formatted['description'],
                'post_status' => 'publish',
                'post_type' => 'wptrebs_property',
            );

            $posted_property = wp_insert_post($post_args);

            foreach ($property_formatted as $key => $value) {
                if (!empty($value)) {
                    add_post_meta( $posted_property, $key, $value, true ) || update_post_meta( $posted_property, $key, $value );
                }
            }


        }


        //create post

        //organize data into custom fields

        //store first photo as featured

        //store remaining photos in array in custom field
    }
}