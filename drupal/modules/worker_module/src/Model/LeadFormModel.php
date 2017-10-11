<?php

namespace Drupal\worker_module\Model;

/**
 * Undocumented class
 */
class LeadFormModel
{

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $_id;
    
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $_attempts;
    
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $_body;

    /**
     * Undocumented function
     */
    private function __construct(){}

    static public function create(){
        return new LeadForm();
    }

    /**
     * Undocumented function
     *
     * @param [type] $id
     * 
     * @return void
     */
    public function withId($id) 
    {
        $this->_id;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param [type] $attempts
     * 
     * @return void
     */
    public function withAttempts($attempts)
    { 
        $this->_attemps = $attempts;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param [type] $body
     * 
     * @return void
     */
    public function withBody($body)
    {
        $this->_body;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getSalesforceJsonData(){
        return json_encode(
            [
                "id" => $this->_id,
                "attemps" => $this->_attempts,
                "body" => $this->_body
            ]
        );
    }
}