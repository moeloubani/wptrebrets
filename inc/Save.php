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

    public function posts()
    {
        //get post data

        //create post

        //organize data into custom fields

        //store first photo as featured

        //store remaining photos in array in custom field
    }
}