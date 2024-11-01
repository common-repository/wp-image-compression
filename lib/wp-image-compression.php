<?php
include 'Cloudinary.php';
include 'Uploader.php';
class Wpimage {

    protected $auth = array();

    public function __construct() {

        \Cloudinary::config(array("cloud_name"=>'cdn-hosting-io', "api_key" => '211466787232616', "api_secret" => 'MR1qKhLKwOMKIlYKte_CR-kxgKo'));
    }


    public function upload($opts = array()) {
     	global $wpdb;
        $exist = $wpdb->get_row("select * from ".$wpdb->prefix."image_compression_settings", ARRAY_A);
        if($exist && $exist['total_allowed'] <= 0) {
            return false;
        }

        if (!isset($opts['file'])) {
            return array(
                "success" => false,
                "error" => "File parameter was not provided"
            );
        }


        if (!file_exists($opts['file'])) {
            return array(
                "success" => false,
                "error" => "File `" . $opts['file'] . "` does not exist"
            );
        }
        if (function_exists('curl_file_create')) {
            $file = sprintf('%s', $opts['file']);
            //$file = curl_file_create($opts['file'], 'image/jpeg', $opts['file']);
        } else {
            $file = sprintf('%s', $opts['file']);
        }
        unset($opts['file']);
		// $file = str_replace($_SERVER['DOCUMENT_ROOT'],'http://'.$_SERVER['HTTP_HOST'],$file);
        if ($opts['backup_before_compression']) {
            //backup the image first
            $upload_dir = wp_upload_dir();
            $user_dirname = $upload_dir['basedir'].'/optimisationio_media_backup';
            if ( ! file_exists( $user_dirname ) ) {
                wp_mkdir_p( $user_dirname );
            }
            copy($file, $user_dirname.'/'.basename($file));
        }
	    $img = filesize($file);
        $options = array();
        $source = wpimages_get_source();

        list($maxW, $maxH) = wpimages_get_max_width_height($source);
        if ($maxW > 0) {
            $options['width'] = $maxW;
        }
        if ($maxH > 0) {
            $options['height'] = $maxH;
        }
        if ($opts['wpimages_quality']) {
            $options['quality'] = $opts['wpimages_quality'];
        }
        $options['crop'] = 'limit';
        try{
            $response = \Cloudinary\Uploader::upload($file, $options);
        }catch(Exception $e){
            //no compression
            return false;
        }

        // Array
        // (
        //     [public_id] => sample_spreadsheet.xls
        //     [version] => 1371999373
        //     [signature] => 11bae88d112f76184ed0700891f16bd278a6c06d
        //     [resource_type] => raw
        //     [created_at] => 2013-06-23T14:55:21Z
        //     [bytes] => 6144
        //     [type] => upload
        //     [url] => http://res.cloudinary.com/demo/raw/upload/v1371999373/sample_spreadsheet.xls
        //     [secure_url] => https://res.cloudinary.com/demo/raw/upload/v1371999373/sample_spreadsheet.xls
        // )
        if ($response) {
            $bytes = get_headers($response['url'],1);
             if (!$exist) {
                $array = array();
                $array['total_size_optimized'] = $bytes['Content-Length']/1000;
                $array['total_image_optimized'] = 1;
                $array['total_allowed'] = 100-1;
                $array['created'] = date('Y-m-d H:i:s');
                $wpdb->insert($wpdb->prefix."image_compression_settings", $array);
             } else {
                $array = array();
                $array['total_size_optimized'] = $exist['total_size_optimized'] + $bytes['Content-Length']/1000;
                $array['total_image_optimized'] = $exist['total_image_optimized']+1;
                $array['total_allowed'] = $exist['total_allowed']-1;
                $array['modified'] = date('Y-m-d H:i:s');
                $wpdb->update($wpdb->prefix."image_compression_settings", $array, array('id'=>$exist['id']));
             }

            $response['original_size'] = $img;
            $response['bytes'] = $bytes['Content-Length'];
            $response['saved_bytes'] = $response['original_size'] - $bytes['Content-Length'];
        }

        return $response;
    }

    public function deleteResource($public_id)
    {
        \Cloudinary\Uploader::destroy($public_id);
    }

    public function status() {
        $response = \Cloudinary\Uploader::upload('file.jpg', array());
        echo 3423;die;
        print_r($response);die;
    }

    private function request($data, $url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 400);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FAILONERROR, 0);
        $response = json_decode(curl_exec($curl), true);
        $error = curl_errno($curl);
        curl_close($curl);
        if ($error > 0) {
            throw new RuntimeException(sprintf('cURL returned with the following error code: "%s"', $error));
        }
        return $response;
    }

}
