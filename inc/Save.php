<?php

namespace wptrebrets\inc;

class Save
{
    protected $post;
    protected $photos;
    protected $mls;
    protected $upload_dir;
    protected $feed;

    /**
     * Takes a Feed object from a completed search and saves it
     *
     * @param Feed $feed
     */
    public function __construct(Feed $feed)
    {
        $this->upload_dir = wp_upload_dir();
        $this->feed = $feed;
        $this->mls = $feed->mls;
        self::posts($feed->get());
    }

    /**
     * Uses the filename and MLS number to generate a directory to save the files to, this returns the path
     *
     * @param array|object $property
     * @return string
     */
    public function getDirectory($property)
    {
        //Get the path to the upload directory and create it if it isn't there
        $first_letter = substr($property, 0, 1);
        $numeric = substr($property, 1);
        $dir = $this->upload_dir['basedir'] . '/wptreb/' . $first_letter . '/' . $numeric;

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    /**
     * Uses the filename and MLS number to get the URL to the stored images
     *
     * @param array|object $property
     * @return string
     */
    public function getDirectoryURL($property)
    {
        //Get the URL to the upload directory
        $first_letter = substr($property, 0, 1);
        $numeric = substr($property, 1);
        $upload_dir = wp_upload_dir();
        $dir = $upload_dir['url'] . '/wptreb/' . $first_letter . '/' . $numeric;

        return $dir;
    }


    /**
     * Loops through the images and stores them on the server - at the same time it links the images to the post so you
     * later use them for a gallery/slideshow
     *
     * @param $photos
     * @param $id
     * @param $property
     */
    public function photos($photos, $id, $property)
    {
        $all_photos = array();
        $dir = self::getDirectory($property);

        $n = 1;
        $last = false;

        //Here we get the photos and store them in directories separated by the first letter of the property
        foreach ($photos as $key => $photo) {
            $filename = $n.'.jpg';



            if ($key == count($photos) - 1) {
                $last = true;
            }

            if (!file_exists($dir.'/'.$n.'.jpg')) {
                file_put_contents($dir.'/'.$n.'.jpg', $photo->getContent());
                self::addPhotoToWordPress($filename, $dir, $id, $property, $last);
            }

            $all_photos[$property][] = $dir.'/'. $filename;
            $n++;
        }


    }

    /**
     * Saves the posts into WordPress
     *
     * @param array|object $post
     */
    public function posts($post)
    {

        /*
         * TODO: If this isn't an array it probably is an error message, need to handle it
         */
        if (is_array($post)) {
            //get post data
            $property_formatted = array();


            //loop through properties found
            foreach ($post as $property) {

                //reassign fields to ones that look good
                $property_formatted['property_price'] = $property['Lp_dol'];
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
                $property_formatted['property_area'] = $property['Sqft'];
                $property_formatted['full_dump'] = $property;

                $update_check = '';

                //This will check for old properties and see if they need to be updated
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
                            'post_type' => 'property',
                        );

                        //insert post and return new post id
                        $posted_property = wp_insert_post($post_args);

                        //add post meta using the new post id and good looking array
                        foreach ($property_formatted as $key => $value) {
                            if (!empty($value)) {
                                add_post_meta($posted_property, $key, $value, true) || update_post_meta($posted_property, $key, $value);
                            }
                        }

                        $photos = $this->feed->photos($property_formatted['mls']);

                        //Get the photos
                        self::photos($photos, $posted_property, $property_formatted['mls']);
                    }

                }


            }
        }

    }

    /**
     * Links the picture to a WordPress post
     *
     * @param $filename
     * @param $dir
     * @param $parent_post_id
     * @param $mls
     * @param $last
     */
    protected function addPhotoToWordPress($filename, $dir, $parent_post_id, $mls, $last)
    {

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