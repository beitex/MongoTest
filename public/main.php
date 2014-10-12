<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/7/14
 * Time: 9:33 AM
 */

/**
 * Set Auto Loading
 */

require_once realpath(__DIR__ . '/../autoload.php');

use Vector\MongoProxy;

$vector = new MongoProxy('mongodb://localhost:27017','beitex');

$vector->setCollection('baby');

// add a record
$document = array( "title" => "Calvin and Hobbes", "author" => "Bill Watterson" );
$vector->addDocument($document);

// add another record, with a different "shape"
$document = array( "title" => "XKCD", "online" => true, 'status'=>array(20, 30, 40) );
$vector->addDocument($document);

// find everything in the collection
$cursor = $vector->findAll();

// iterate through the results
foreach ($cursor as $document) {
    echo $document["title"] . "\n";



}


