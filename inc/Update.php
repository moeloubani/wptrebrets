<?php

namespace wptrebrets\inc;


class Update extends Save {

    protected $post;
    protected $photos;
    protected $mls;

    /**
     * @param Feed $mls
     * @param $id
     * @param $photos
     */
    function __construct($mls, $id, $photos)
    {
        $this->mls = $mls;
        $this->id = $id;
        $this->photos = $photos;
    }

    /**
     * @param array $property
     */
    public function posts(Array $property)
    {

        //get post data
        $property_formatted = array();

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
        $property_formatted['full_dump'] = $property;

        //set up arguments before entering post to wp
        $post_args = array(
            'post_content' => $property_formatted['description'],
            'ID' => $this->id,
            'post_type' => 'property'
        );

        //insert post and return new post id
        $posted = wp_update_post($post_args, true);

        //add post meta using the new post id and good looking array
        foreach ($property_formatted as $key => $value) {
            if (!empty($value)) {
                add_post_meta($this->id, 'wptrebs_' . $key, $value, true) || update_post_meta($this->id, 'wptrebs_' . $key, $value);
            }
        }

    }

} 