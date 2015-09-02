<?php

namespace wptrebrets\inc;

class Feed
{
    private $retsfeed;
    public $limit;
    public $mls;
    public $url;
    public $login;
    public $password;
    protected $search;

    /**
     * Get the options and assign to class
     *
     */
    function __construct()
    {
        $this->limit = wptrebrets_get_option('rets_limit');
        $this->login = wptrebrets_get_option('rets_username');
        $this->password = wptrebrets_get_option('rets_password');
        $this->url = wptrebrets_get_option('rets_url');
    }


    /**
     * Creates a config object and uses it to create a PHRETS session
     *
     * @param string $type type of call - meta or normal (default is normal)
     * @return \PHRETS\Session
     * @throws \PHRETS\Exceptions\MissingConfiguration if the username/password isn't set in the settings
     */
    public function start($type = null)
    {
        $login = $this->login;

        $config = new \PHRETS\Configuration;
        $config->setLoginUrl($this->url)
            ->setUsername($login)
            ->setPassword($this->password)
            ->setRetsVersion('1.7');

        $this->retsfeed = new \PHRETS\Session($config);
        $connect = $this->retsfeed->Login();

        return $this->retsfeed;
    }

    /**
     * Runs the search based on the options above.
     */
    public function initialSearch ($search = null)
    {
        $this->search = $this->retsfeed->Search(
            'Property', // Resource
            'ResidentialProperty',// Class
            '((status=A))', // DMQL
            [
                'Count' => 1, // count and records
                'Format' => 'COMPACT-DECODED',
                'Limit' => $this->limit,
            ]
        );
    }


    /**
     * Runs the search daily to get newly posted properties
     */
    public function dailySearch ($search = null)
    {
        //Today's date as yyyy-mm-dd
        $date = new \DateTime();
        $date->add(\DateInterval::createFromDateString('yesterday'));
        $yesterday = $date->format('Y-m-d');

        $this->search = $this->retsfeed->Search(
            'Property', // Resource
            'ResidentialProperty',// Class
            '((status=A),(timestamp_sql='.$yesterday.'+))', // DMQL
            [
                'Count' => 1, // count and records
                'Format' => 'COMPACT-DECODED',
                'Limit' => 25,
            ]
        );
    }

    /**
     * Runs the meta search to check property status
     */
    public function metaSearch ($mls)
    {
        if(is_array($mls) && count($mls) > 1) {
            $mls = implode(',', $mls);
        }

        $result = $this->retsfeed->Search(
            'Property', // Resource
            'ResidentialProperty',// Class
            '(ml_num='.$mls.')', // DMQL
            [
                'Count' => 1, // count and records
                'Format' => 'COMPACT-DECODED',
                'Limit' => 9999,
            ]
        );

        return $result;
    }

    /**
     * Gets the photos from the feed
     *
     * @return mixed
     */
    public function photos($mls)
    {
        $photos = $this->retsfeed->GetObject('Property', 'Photo', $mls);

        return $photos;
    }


    /**
     * Returns the results of the search or an error (?) if nothing is found
     *
     * @return mixed
     */
    public function get()
    {
        if ($this->search->getReturnedResultsCount() > 0) {
            $results = $this->search->toArray();
        } else {
            $results = '0 Records Found.';
        }

        return $results;
    }

    /**
     * Returns the results of the search or an error (?) if nothing is found
     *
     * @return mixed
     */
    public function checkExpired()
    {
        if ($this->search->getReturnedResultsCount() > 0) {
            $results = $this->search->toArray();
        } else {
            $results = '0 Records Found.';
        }

        return $results;
    }


}