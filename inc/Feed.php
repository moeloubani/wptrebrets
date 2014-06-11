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
    }


    public function start()
    {
        $this->retsfeed = new \PHRETS;

    }

    public function connect ()
    {
        $connect = $this->retsfeed->Connect($this->url, $this->login, $this->password);
        return $connect;
    }

    public function search ()
    {
        $this->search = $this->retsfeed->SearchQuery(
            'Property', // Resource
            'ResidentialProperty',// Class
            '((lp_dol=1500000+))', // DMQL
            //'((ml_num='.$this->mls.'))', // DMQL
            array(
                'Format' => 'COMPACT-DECODED',
                'Select' => $this->fields,
                'Count' => 1,
                'Limit' => $this->limit
            )
        );
    }

    public function photos()
    {
        $photos = $this->retsfeed->GetObject('Property', 'Photo', $this->mls);

        return $photos;
    }

    public function show()
    {
        if ($this->retsfeed->TotalRecordsFound() > 0) {
            while ($data = $this->retsfeed->FetchRow($this->search)) {
                $results[] = $data;
            }
        } else {
            $results = '0 Records Found.';
        }

        return $results;
    }

    public function close(Array $photos)
    {
        $this->retsfeed->FreeResult($photos);
        $this->retsfeed->Disconnect();
    }


}