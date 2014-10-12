<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/6/14
 * Time: 5:41 PM
 */


namespace Vector;

class MongoProxy {

    protected $client; // = array();
    protected $database; // = array();
    protected $collection; // = array();
    protected $document; // = array();
    protected $cursor; // = array();

    function __construct($server, $name) {
        $this->client = new \MongoClient();
        $this->database = $this->client->$name;
        //$collection = $database->cartoons;
    }

    public function getDatabase()
    {
        return $this->$database;
    }

    public function setCollection($param)
    {

        $this->collection = $this->database->$param;
    }

    public function addDocument($document)
    {
        $this->collection->insert($document);
    }

    public function findAll()
    {
        $this->cursor = $this->collection->find();
        return $this->cursor;
    }










} 