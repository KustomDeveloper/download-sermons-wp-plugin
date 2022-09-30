<?php
/*
*  Plugin Name: Download Sermons
*  Description: Download sermon from Amazon S3 bucket 
*  Author: Michael Hicks
*  Version: 1.0
*/

/**
 * Bootstrap the plugin.
 */
require_once 'vendor/autoload.php';


/*
*  AWS Dependencies 
*/
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


/**
 *  Download Sermon from Amazon S3
 */
function download_sermon_audio($slug) { 
    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => 'us-east-1'
    ]);

    $bucket = 'rick-howard-sermons';
    
    try {
        // Get the object.
        $result = $s3->getObject([
            'Bucket' => $bucket,
            'Key'    => $slug
        ]);

        // Display the object in the browser.
        header("Content-Type: {$result['ContentType']}");
        echo $result['Body'];
    } catch (S3Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }

    // return $slug['slug'];
}

add_action('rest_api_init', function () {
    register_rest_route( 'download-sermon/v1', '/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods'  => 'GET',
        'callback' => 'download_sermon_audio'
    ));
});