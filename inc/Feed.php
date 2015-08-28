<?php

namespace wptrebrets\inc;

class Feed
{
    private $retsfeed;
    public $fields;
    public $limit;
    public $mls;
    public $url;
    public $login;
    public $password;
    protected $search;

    function __construct($fields, $limit, $login, $mls, $password, $url)
    {
        $this->fields = $fields;
        $this->limit = $limit;
        $this->login = $login;
        $this->mls = $mls;
        $this->password = $password;
        $this->url = $url;
        self::start();
    }


    public function start()
    {
        $config = new \PHRETS\Configuration;
        $config->setLoginUrl($this->url)
            ->setUsername($this->login)
            ->setPassword($this->password)
            ->setRetsVersion('1.7');

        $this->retsfeed = new \PHRETS\Session($config);
        $connect = $this->retsfeed->Login();
        self::search();
    }

    public function search ()
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

    public function photos()
    {
        $photos = $this->retsfeed->GetObject('Property', 'Photo', $this->mls);
        return $photos;
    }

    public function show()
    {
        if ($this->search->getReturnedResultsCount() > 0) {
            $results = $this->search->toArray();
        } else {
            $results = '0 Records Found.';
        }

        return $results;
    }


}