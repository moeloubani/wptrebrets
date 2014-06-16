<?php

namespace wptrebrets\inc;

class Save
{
    protected $post;
    protected $photos;
    protected $mls;
    protected $upload_dir;

    function __construct($mls, $photos)
    {
        $this->mls = $mls;
        $this->photos = $photos;

        $upload_dir = wp_upload_dir();
        $this->upload_dir = $upload_dir;
    }

    public function getDirectory($property) {

        $first_letter = substr($property, 0, 1);
        $numeric = substr($property, 1);
        $dir = $this->upload_dir['basedir'] . '/wptreb/' . $first_letter . '/' . $numeric;

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }


    public function photos(Array $photos)
    {

        $properties = explode(',', $this->mls);
        $all_photos = array();

        foreach ($properties as $property) {

            $dir = self::getDirectory($property);

            $n = 1;

            foreach ($photos as $photo) {

                file_put_contents($dir.'/'.$n.'.jpg', $photo['Data']);

                $all_photos[$property][] = $dir.'/'.$n.'.jpg';

                $n++;
            }
        }

    }

    public function posts(Array $post)
    {

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

            $update_check = CheckOld::data($property_formatted['address'], $property_formatted['last_updated_text'], $property_formatted['status']);

            if (is_array($update_check)) {
                dd($update_check);
                if (isset($update_check['update'])) {
                    $update = new Update($property_formatted['mls'], $update_check['update'], get_post_meta($update_check['update'], 'wptrebs_photos', true));
                }
                break;
            } else {

                //set up arguments before entering post to wp
                $post_args = array(
                    'post_title' => $property_formatted['address'],
                    'post_content' => $property_formatted['description'],
                    'post_status' => 'publish',
                    'post_type' => 'wptrebs_property',
                );


                //insert post and return new post id
                $posted_property = wp_insert_post($post_args);

                //add post meta using the new post id and good looking array
                foreach ($property_formatted as $key => $value) {
                    if (!empty($value)) {
                        add_post_meta($posted_property, 'wptrebs_' . $key, $value, true) || update_post_meta($posted_property, 'wptrebs_' . $key, $value);
                    }
                }

                self::photosMeta($property_formatted['mls'], $posted_property);

            }


        }
    }

    public function photosMeta($mls, $id) {
        $dir = self::getDirectory($mls);

        //get list of photos
        $all_photos = array();

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    $all_photos[$mls][] = $dir . $file;
                }
                closedir($dh);

                foreach ($all_photos[$mls] as $key => $value) {
                    if (strpos($value, 'jpg') == false) {
                        unset($all_photos[$mls][$key]);
                    }
                }

                $all_photos[$mls] = array_values($all_photos[$mls]);

                add_post_meta($id, 'wptrebs_photos', $all_photos[$mls], true ) || update_post_meta( $id, 'wptrebs_photos', $all_photos[$mls] );
            }
        }
    }

}