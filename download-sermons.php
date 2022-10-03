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
*  Load Scripts
*/
function download_sermon_scripts() {
    wp_enqueue_script ( 'download-sermons-main-js', plugin_dir_url( __FILE__ ) . 'js/main.js', 'jQuery', null, true );
}
add_action( 'wp_enqueue_scripts', 'download_sermon_scripts' );

/*
*  AWS Dependencies 
*/
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/*
*  Add Env Variables 
*/
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 *  Download Sermon from Amazon S3
 */
function download_sermon_audio($slug) { 
    $s3 = new S3Client([
        'credentials' => array(
            'key'    => $_ENV['S3_ACCESS_KEY'],
            'secret' => $_ENV['S3_SECRET_KEY']
        ),
        'version' => $_ENV['S3_VERSION'],
        'region'  => $_ENV['S3_REGION']
    ]);

    try {
        // Get the object.
        $result = $s3->getObject([
            'Bucket' => $_ENV['S3_BUCKET'],
            'Key'    => $slug['slug']
        ]);

        // Display the object in the browser.
        header("Content-Disposition: attachment; Content-Type: {$result['ContentType']}");
        echo $result['Body'];
    } catch (S3Exception $e) {
        return $e->getMessage();
    }
}

add_action('rest_api_init', function () {
    register_rest_route( 'download-sermon/v1', '/(?P<slug>[\s\S]+\.mp3)', array(
        'methods'  => 'GET',
        'callback' => 'download_sermon_audio'
    ));
});