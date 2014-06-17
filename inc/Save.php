<?php

namespace wptrebrets\inc;

class Save
{
    protected $post;
    protected $photos;
    protected $mls;
    protected $upload_dir;
    protected $feed;

    public function __construct(Feed $feed)
    {
        $this->upload_dir = wp_upload_dir();
        $this->feed = $feed;
        $this->mls = $feed->mls;
        self::posts($feed->show());

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

    public function getDirectoryURL($property) {

        $first_letter = substr($property, 0, 1);
        $numeric = substr($property, 1);
        $upload_dir = wp_upload_dir();
        $dir = $upload_dir['url'] . '/wptreb/' . $first_letter . '/' . $numeric;

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }


    public function photos(Array $photos, $id, $property)
    {

        $all_photos = array();

        $dir = self::getDirectory($property);

        $n = 1;
        $last = false;

        foreach ($photos as $key => $photo) {
            $filename = $n.'.jpg';

            if ($key == count($photos) - 1) {
                $last = true;
            }

            if (!file_exists($dir.'/'.$n.'.jpg')) {
                file_put_contents($dir.'/'.$n.'.jpg', $photo['Data']);
                self::addPhotoToWordPress($filename, $dir, $id, $property, $last);
            }

            $all_photos[$property][] = $dir.'/'. $filename;



            $n++;
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

            $update_check = '';
            $update_check = CheckOld::data($property_formatted['address'], $property_formatted['last_updated_text'], $property_formatted['status']);

            if (is_array($update_check)) {

                if (isset($update_check['update'])) {
                    $update = new Update($property_formatted['mls'], $update_check['update'], get_post_meta($update_check['update'], 'wptrebs_photos', true));
                    $update->posts($property);
                } elseif (isset($update_check['new'])) {
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

                    $this->feed->mls = $property_formatted['mls'];
                    $photos = $this->feed->photos();

                    self::photos($photos, $posted_property, $property_formatted['mls']);
                }

            }


        }


    }

    protected static function addPhotoToWordPress($filename, $dir, $parent_post_id, $mls, $last) {

        // $filename should be the path to a file in the upload directory.
        $filename = $dir . '/' . $filename;

        // Check the type of tile. We'll use this as the 'post_mime_type'.
        $filetype = wp_check_filetype( basename( $filename ), null );

        // Get the path to the upload directory.
        $wp_upload_dir = $dir;

        // Prepare an array of post data for the attachment.
        $attachment = array(
            'guid'           => self::getDirectoryURL($mls) .'/'. basename( $filename ),
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Insert the attachment.
        $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        // Generate the metadata for the attachment, and update the database record.
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        if ($last == true) {
            add_post_meta($parent_post_id, '_thumbnail_id', $attach_id);
        }
    }

}