<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://optimisation.io
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       WP Image Compression
 * Plugin URI:        https://optimisation.io
 * Description:       WP Image Compression is a quick and easy way to not only resize your images, but compress them as well for optimimum performance going forward.
 * Version:           1.3.01
 * Author:            pigeonhut, optimisation.io
 * Author URI:        https://optimisation.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-image-compression
 * Domain Path:       /languages
 */
define('WPIMAGE_VERSION', '2.3.2');
define('WPIMAGE_SCHEMA_VERSION', '1.1');

define('WPIMAGE_DEFAULT_MAX_WIDTH', 1024);
define('WPIMAGE_DEFAULT_MAX_HEIGHT', 1024);
define('WPIMAGE_DEFAULT_BMP_TO_JPG', 1);
define('WPIMAGE_DEFAULT_PNG_TO_JPG', 0);
define('WPIMAGE_DEFAULT_QUALITY', 90);

define('WPIMAGE_SOURCE_POST', 1);
define('WPIMAGE_SOURCE_LIBRARY', 2);
define('WPIMAGE_SOURCE_OTHER', 4);

define('WPIMAGE_AJAX_MAX_RECORDS', 265);
define('STRIPE_APIKEY', 'sk_test_BQokikJOvBiI2HlWgH4olfQ2');

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

load_plugin_textdomain('wp-image-compression', false, 'wp-image-compression/languages/');
register_activation_hook(__FILE__, 'wpimagecompression_install');

function wpimagecompression_install()
{
    $table_name = $wpdb->prefix . "wp_image_compression_settings";
    $sqle = "DROP TABLE IF EXISTS $table_name;";
    $sql = "CREATE TABLE $table_name (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
         `total_size_optimized` float DEFAULT NULL,
         `total_image_optimized` int(11) DEFAULT NULL,
         `total_allowed` int(11) NOT NULL DEFAULT '1000',
         `created` datetime DEFAULT NULL,
         `modified` datetime DEFAULT NULL,
         PRIMARY KEY (`id`)
            );";
    dbDelta($sqle);
    dbDelta($sql);
}

/**
 * import supporting libraries
 */
include_once plugin_dir_path(__FILE__) . 'libs/utils.php';
include_once plugin_dir_path(__FILE__) . 'settings.php';
include_once plugin_dir_path(__FILE__) . 'ajax.php';
if (!class_exists('NMRichReviewsAdminHelper')) {
    require_once plugin_dir_path(__FILE__) . 'lib/admin-view-helper-functions.php';
}

//including stripe stuffs
// Stripe singleton
require(dirname(__FILE__) . '/lib/stripe/Stripe.php');

// Utilities
require(dirname(__FILE__) . '/lib/stripe/Util/AutoPagingIterator.php');
require(dirname(__FILE__) . '/lib/stripe/Util/RequestOptions.php');
require(dirname(__FILE__) . '/lib/stripe/Util/Set.php');
require(dirname(__FILE__) . '/lib/stripe/Util/Util.php');

// HttpClient
require(dirname(__FILE__) . '/lib/stripe/HttpClient/ClientInterface.php');
require(dirname(__FILE__) . '/lib/stripe/HttpClient/CurlClient.php');

// Errors
require(dirname(__FILE__) . '/lib/stripe/Error/Base.php');
require(dirname(__FILE__) . '/lib/stripe/Error/Api.php');
require(dirname(__FILE__) . '/lib/stripe/Error/ApiConnection.php');
require(dirname(__FILE__) . '/lib/stripe/Error/Authentication.php');
require(dirname(__FILE__) . '/lib/stripe/Error/Card.php');
require(dirname(__FILE__) . '/lib/stripe/Error/InvalidRequest.php');
require(dirname(__FILE__) . '/lib/stripe/Error/Permission.php');
require(dirname(__FILE__) . '/lib/stripe/Error/RateLimit.php');

// Plumbing
require(dirname(__FILE__) . '/lib/stripe/ApiResponse.php');
require(dirname(__FILE__) . '/lib/stripe/JsonSerializable.php');
require(dirname(__FILE__) . '/lib/stripe/StripeObject.php');
require(dirname(__FILE__) . '/lib/stripe/ApiRequestor.php');
require(dirname(__FILE__) . '/lib/stripe/ApiResource.php');
require(dirname(__FILE__) . '/lib/stripe/SingletonApiResource.php');
require(dirname(__FILE__) . '/lib/stripe/AttachedObject.php');
require(dirname(__FILE__) . '/lib/stripe/ExternalAccount.php');

// Stripe API Resources
require(dirname(__FILE__) . '/lib/stripe/Account.php');
require(dirname(__FILE__) . '/lib/stripe/AlipayAccount.php');
require(dirname(__FILE__) . '/lib/stripe/ApplePayDomain.php');
require(dirname(__FILE__) . '/lib/stripe/ApplicationFee.php');
require(dirname(__FILE__) . '/lib/stripe/ApplicationFeeRefund.php');
require(dirname(__FILE__) . '/lib/stripe/Balance.php');
require(dirname(__FILE__) . '/lib/stripe/BalanceTransaction.php');
require(dirname(__FILE__) . '/lib/stripe/BankAccount.php');
require(dirname(__FILE__) . '/lib/stripe/BitcoinReceiver.php');
require(dirname(__FILE__) . '/lib/stripe/BitcoinTransaction.php');
require(dirname(__FILE__) . '/lib/stripe/Card.php');
require(dirname(__FILE__) . '/lib/stripe/Charge.php');
require(dirname(__FILE__) . '/lib/stripe/Collection.php');
require(dirname(__FILE__) . '/lib/stripe/CountrySpec.php');
require(dirname(__FILE__) . '/lib/stripe/Coupon.php');
require(dirname(__FILE__) . '/lib/stripe/Customer.php');
require(dirname(__FILE__) . '/lib/stripe/Dispute.php');
require(dirname(__FILE__) . '/lib/stripe/Event.php');
require(dirname(__FILE__) . '/lib/stripe/FileUpload.php');
require(dirname(__FILE__) . '/lib/stripe/Invoice.php');
require(dirname(__FILE__) . '/lib/stripe/InvoiceItem.php');
require(dirname(__FILE__) . '/lib/stripe/Order.php');
require(dirname(__FILE__) . '/lib/stripe/OrderReturn.php');
require(dirname(__FILE__) . '/lib/stripe/Plan.php');
require(dirname(__FILE__) . '/lib/stripe/Product.php');
require(dirname(__FILE__) . '/lib/stripe/Recipient.php');
require(dirname(__FILE__) . '/lib/stripe/Refund.php');
require(dirname(__FILE__) . '/lib/stripe/SKU.php');
require(dirname(__FILE__) . '/lib/stripe/Source.php');
require(dirname(__FILE__) . '/lib/stripe/Subscription.php');
require(dirname(__FILE__) . '/lib/stripe/SubscriptionItem.php');
require(dirname(__FILE__) . '/lib/stripe/ThreeDSecure.php');
require(dirname(__FILE__) . '/lib/stripe/Token.php');
require(dirname(__FILE__) . '/lib/stripe/Transfer.php');
require(dirname(__FILE__) . '/lib/stripe/TransferReversal.php');
//


add_action( 'wp_ajax_stripe_payment', 'stripe_payment' );
add_action( 'wp_ajax_nopriv_stripe_payment', 'stripe_payment' );

/**
 * Fired with the WordPress upload dialog is displayed
 */

/**
 * Inspects the request and determines where the upload came from
 * @return WPIMAGE_SOURCE_POST | WPIMAGE_SOURCE_LIBRARY | WPIMAGE_SOURCE_OTHER
 */
function wpimages_get_source()
{

    $id     = array_key_exists('post_id', $_REQUEST) ? $_REQUEST['post_id'] : '';
    $action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

    // a post_id indicates image is attached to a post
    if ($id > 0) {
        return WPIMAGE_SOURCE_POST;
    }

    // post_id of 0 is 3.x otherwise use the action parameter
    if ($id === 0 || $action == 'upload-attachment') {
        return WPIMAGE_SOURCE_LIBRARY;
    }

    // we don't know where this one came from but $_REQUEST['_wp_http_referer'] may contain info
    return WPIMAGE_SOURCE_OTHER;
}

/**
 * Given the source, returns the max width/height
 *
 * @example:  list($w,$h) = wpimages_get_max_width_height(WPIMAGE_SOURCE_LIBRARY);
 * @param int WPIMAGE_SOURCE_POST | WPIMAGE_SOURCE_LIBRARY | WPIMAGE_SOURCE_OTHER
 */
function wpimages_get_max_width_height($source)
{
    $w = wpimages_get_option('wpimages_max_width', WPIMAGE_DEFAULT_MAX_WIDTH);
    $h = wpimages_get_option('wpimages_max_height', WPIMAGE_DEFAULT_MAX_HEIGHT);

    switch ($source) {
        case WPIMAGE_SOURCE_POST:
            break;
        case WPIMAGE_SOURCE_LIBRARY:
            $w = wpimages_get_option('wpimages_max_width_library', $w);
            $h = wpimages_get_option('wpimages_max_height_library', $h);
            break;
        default:
            $w = wpimages_get_option('wpimages_max_width_other', $w);
            $h = wpimages_get_option('wpimages_max_height_other', $h);
            break;
    }

    return array($w, $h);
}

/**
 * Handler after a file has been uploaded.  If the file is an image, check the size
 * to see if it is too big and, if so, resize and overwrite the original
 * @param Array $params
 */
function wpimages_handle_upload($params)
{
    /* debug logging... */
    // file_put_contents ( "debug.txt" , print_r($params,1) . "\n" );
    // if "noresize" is included in the filename then we will bypass wpimage scaling
    if (strpos($params['file'], 'noresize') !== false) {
        return $params;
    }

    // if preferences specify so then we can convert an original bmp or png file into jpg
    if ($params['type'] == 'image/bmp' && wpimages_get_option('wpimages_bmp_to_jpg', WPIMAGE_DEFAULT_BMP_TO_JPG)) {
        $params = wpimages_convert_to_jpg('bmp', $params);
    }

    if ($params['type'] == 'image/png' && wpimages_get_option('wpimages_png_to_jpg', WPIMAGE_DEFAULT_PNG_TO_JPG)) {
        $params = wpimages_convert_to_jpg('png', $params);
    }

    // make sure this is a type of image that we want to convert and that it exists
    // @TODO when uploads occur via RPC the image may not exist at this location
    $oldPath = $params['file'];

    if ((!is_wp_error($params)) && file_exists($oldPath) && in_array($params['type'], array('image/png', 'image/gif', 'image/jpeg'))) {

        // figure out where the upload is coming from
        $source = wpimages_get_source();

        list($maxW, $maxH) = wpimages_get_max_width_height($source);

        list($oldW, $oldH) = getimagesize($oldPath);

        /* HACK: if getimagesize returns an incorrect value (sometimes due to bad EXIF data..?)
        $img = imagecreatefromjpeg ($oldPath);
        $oldW = imagesx ($img);
        $oldH = imagesy ($img);
        imagedestroy ($img);
        // */

        /* HACK: an animated gif may have different frame sizes.  to get the "screen" size
        $data = ''; // TODO: convert file to binary
        $header = unpack('@6/vwidth/vheight', $data );
        $oldW = $header['width'];
        $oldH = $header['width'];
        // */

        if (($oldW > $maxW && $maxW > 0) || ($oldH > $maxH && $maxH > 0)) {
            $quality = wpimages_get_option('wpimages_quality', WPIMAGE_DEFAULT_QUALITY);

            list($newW, $newH) = wp_constrain_dimensions($oldW, $oldH, $maxW, $maxH);

            // this is wordpress prior to 3.5 (image_resize deprecated as of 3.5)
            $resizeResult = wpimages_image_resize($oldPath, $newW, $newH, false, null, null, $quality);

            /* uncomment to debug error handling code: */
            // $resizeResult = new WP_Error('invalid_image', __(print_r($_REQUEST)), $oldPath);
            // regardless of success/fail we're going to remove the original upload
            unlink($oldPath);

            if (!is_wp_error($resizeResult)) {
                $newPath = $resizeResult;

                // remove original and replace with re-sized image
                rename($newPath, $oldPath);
            } else {
                // resize didn't work, likely because the image processing libraries are missing
                $params = wp_handle_upload_error($oldPath, sprintf(__("Oh Snap! Wp image resizer was unable to resize this image "
                    . "for the following reason: '%s'
                    .  If you continue to see this error message, you may need to either install missing server"
                    . " components or disable the Wp image resizer plugin."
                    . "  If you think you have discovered a bug, please report it on the Wp image resizer support forum.", 'wpimage'), $resizeResult->get_error_message()));
            }
        }
    }

    return $params;
}

/**
 * read in the image file from the params and then save as a new jpg file.
 * if successful, remove the original image and alter the return
 * parameters to return the new jpg instead of the original
 *
 * @param string 'bmp' or 'png'
 * @param array $params
 * @return array altered params
 */
function wpimages_convert_to_jpg($type, $params)
{

    $img = null;

    if ($type == 'bmp') {
        include_once 'libs/imagecreatefrombmp.php';
        $img = imagecreatefrombmp($params['file']);
    } elseif ($type == 'png') {

        if (!function_exists('imagecreatefrompng')) {
            return wp_handle_upload_error($params['file'], 'wpimages_convert_to_jpg requires gd library enabled');
        }

        $img = imagecreatefrompng($params['file']);
    } else {
        return wp_handle_upload_error($params['file'], 'Unknown image type specified in wpimages_convert_to_jpg');
    }

    // we need to change the extension from the original to .jpg so we have to ensure it will be a unique filename
    $uploads     = wp_upload_dir();
    $oldFileName = basename($params['file']);
    $newFileName = basename(str_ireplace("." . $type, ".jpg", $oldFileName));
    $newFileName = wp_unique_filename($uploads['path'], $newFileName);

    $quality = wpimages_get_option('wpimages_quality', WPIMAGE_DEFAULT_QUALITY);

    if (imagejpeg($img, $uploads['path'] . '/' . $newFileName, $quality)) {
        // conversion succeeded.  remove the original bmp & remap the params
        unlink($params['file']);

        $params['file'] = $uploads['path'] . '/' . $newFileName;
        $params['url']  = $uploads['url'] . '/' . $newFileName;
        $params['type'] = 'image/jpeg';
    } else {
        unlink($params['file']);

        return wp_handle_upload_error($oldPath, __("Oh Snap! Wp image resizer was Unable to process the $type file.  "
            . "If you continue to see this error you may need to disable the $type-To-JPG "
            . "feature in Wp image convertor settings.", 'wpimage'));
    }

    return $params;
}

function stripe_payment()
{
    $email = $_POST['email'];
    $price = $_POST['price'];
    $token = $_POST['token'];
    $description = $_POST['description'];
    \Stripe\Stripe::setApiKey(STRIPE_APIKEY);
    $charge = \Stripe\Charge::create(array(
      "amount" => $price,
      "currency" => "gbp",
      "source" => $token, // obtained with Stripe.js
      "description" => $description
    ));

    if($charge->paid == true) {
        //add the credits
        global $wpdb;
        $exist = $wpdb->get_row("select * from ".$wpdb->prefix."image_compression_settings", ARRAY_A);
        if($amount == 1000){
           $add = 1000;
        }else if($amount == 4000) {
           $add = 5000;
        }else if($amount == 8000) {
            $add = 10000;
        }else if($amount == 15000){
            $add = 20000;
        }
        if ($exist) {
            $array = array();
            $array['total_allowed'] = $exist['total_allowed']+$add;
            $wpdb->update($wpdb->prefix."image_compression_settings", $array, array('id'=>$exist['id']));
        } else {
            $aray = array();
            $array['total_allowed'] = 100+$add;
            $wpdb->insert($wpdb->prefix."image_compression_settings", $array);
        }
        $return['type'] = 'success';
        $return['msg'] = 'Api updated successfully';
        echo json_encode($return);die;

    }else{
        //return error message
        $return['type'] = 'error';
        $return['msg'] = 'Sorry could not update';
        echo json_encode($return);die;

    }


}

/* add filters to hook into uploads */
add_filter('wp_handle_upload', 'wpimages_handle_upload');

/* add filters/actions to customize upload page */

// TODO: if necessary to update the post data in the future...
// add_filter( 'wp_update_attachment_metadata', 'wpimages_handle_update_attachment_metadata' );

if (!class_exists('Wp_Image_compression')) {

    class Wp_Image_compression
    {

        private $id;
        private $compression_settings      = array();
        private $thumbs_data               = array();
        private $backup_before_compression = 0;

        public function __construct()
        {
            $plugin_dir_path = dirname(__FILE__);
            require_once $plugin_dir_path . '/lib/wp-image-compression.php';
            $this->compression_settings      = unserialize(get_option('_wpimage_options'));
            $this->backup_before_compression = $this->compression_settings['backup_before_compression'];
            add_action('admin_menu', array(&$this, 'admin_init'));
            add_action('admin_enqueue_scripts', array(&$this, 'my_enqueue'));
            add_action('wp_ajax_wpimage_request', array(&$this, 'wpimage_media_library_ajax_callback'));
            add_action('manage_media_custom_column', array(&$this, 'fill_media_columns'), 10, 2);
            add_filter('manage_media_columns', array(&$this, 'add_media_columns'));
            add_filter('wp_generate_attachment_metadata', array(&$this, 'optimize_thumbnails'));
            add_action('add_attachment', array(&$this, 'wpimage_media_uploader_callback'));
            add_action('admin_post_submit-compression-form', array(&$this, 'saveCompressionSettings'));
        }

        /*
         *  Adds wpimage fields and settings to Settings->Media settings page
         */

        public function admin_init()
        {
            add_menu_page(__('Compression', 'wpc'), __('Compression', 'wpc'), 'manage_options', 'wp-convertor', array(&$this, 'wpimages_settings_page_form'), '');
        }

        public function compressionTabs()
        {
            $pluginDirectory = trailingslashit(plugins_url(basename(dirname(__FILE__))));
            wp_register_style('compression-css', $pluginDirectory . 'css/compression.css');
            wp_enqueue_style('compression-css');
            $my_plugin_tabs = array(
                //'wp-convertor' => 'Image Dimension',
            );
            echo $this->admin_tabs_callmeback($my_plugin_tabs);
        }

        public function callCustomCompression()
        {
            session_start();
            global $wpdb;
            $get_option_details = unserialize(get_option('_wpimage_options'));
            $verify_checked     = $get_option_details['api_lossy'];
            if ($verify_checked == 'lossy') {
                $lossyCheked = 'checked="checked"';
            } else {
                $lossyCheked = '';
            }
            if ($verify_checked == 'lossless') {
                $losslessChecked = 'checked="checked"';
            } else {
                $losslessChecked = '';
            }

            $out = '
                <form id="compres" method="post" action="' . get_admin_url() . 'admin-post.php">
                    <fieldset>
                        <input type=\'hidden\' name=\'action\' value=\'submit-compression-form\' />
            <div>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th>Choose Amount to upgrade</th>
                        <td>
                        <select  id="stripepackage">
                          <option value="1000">&pound;10 for 1,000 image optimisation</option>
                          <option value="4000">&pound;40 for 5,000 image optimisation</option>
                          <option value="8000">&pound;80 for 10,000 image optimisation</option>
                          <option value="15000">&pound;150 for 20,000 image optimisation</option>
                        </select>
                        </td>
                    </tr>
                    <tr>
                      <td><button id="stripe_button" class="button-primary">Upgrade</button></td>
                    </tr>
                        <tr>
                            <th scope="row">Backup Image before Compression:</th>
                            <td>
                                <div style="display:inline-block;">';
            if ($get_option_details['backup_before_compression'] == 1) {
                $out .= '<label for="backup_before_compression"><input type="radio" name="_wpimage_options[backup_before_compression]" value="1" checked="checked"> Yes</label>';
            } else {
                $out .= '<label for="backup_before_compression"><input type="radio" name="_wpimage_options[backup_before_compression]" value="1"> Yes</label>';
            }

            $out .= '</div>
                                <div style="display:inline-block;">';
            if ($get_option_details['backup_before_compression'] == 0) {
                $out .= '<label for="backup_before_compression"><input type="radio" name="_wpimage_options[backup_before_compression]" value="0" checked="checked"> No</label>';
            } else {
                $out .= '<label for="backup_before_compression"><input type="radio" name="_wpimage_options[backup_before_compression]" value="0"> No</label>';
            }

            $out .= '</div>
                            </td>
                        </tr>
            </tbody>
            </table>
            </div>
            <input class="button-primary" type="submit" value="Submit" name="submit" />
            <!--<span><i>(On submitting this form, it will update the customer <b>subscription date</b> and process a <b>recurring payment</b>, please be sure before submitting)</i></span>-->
            </fieldset>
            </form>
           ';
            return $out;
        }

        public function saveCompressionSettings()
        {
            session_start();
            global $wpdb;
            if (isset($_POST['submit'])) {
                //$settings = $this->compression_settings;
                $status = $this->get_api_status();

                if ($status !== false) {
                    $value = serialize($_POST['_wpimage_options']);
                    update_option('_wpimage_options', $value);
                    $_SESSION['status'] = 'updated';
                } else {
                    $value                                   = serialize($_POST['_wpimage_options']);
                    update_option('_wpimage_options', $value);
                    $_SESSION['status'] = 'failed';
                }
                wp_redirect(admin_url('admin.php?page = wp-convertor'));
            }
        }

        public function wpimages_settings_page_form()
        {
            $this->compressionTabs();
            ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery("body").addClass("wps-admin-page");
                    // binds form submission and fields to the validation engine
                    jQuery(".wps-postbox-container .handlediv, .wps-postbox-container .hndle").on("click", function (n) {
                        return n.preventDefault(), jQuery(this).parent().toggleClass("closed");
                    });
                });
            </script>

            <div class="wrap">
            <h2><!--Wordpress admin notices will appear here --></h2>
            <header>
    <div class="cg-col-left">
        <h2>Compresion</h2>
        Simple, effective Caching Plugin
</div>

<div class="cg-col-right"><strong>Help us build a better product</strong>
<p><a target="blank" href="https://wordpress.org/plugins/wp-image-compression/">Rate us on WordPress.org</a></p>
<div class="cg-stars">

</div>
</div>
<div style="clear:both"></div>
    </header>
            <style type="text/css">

.cg-container .postbox, .cg-container .stuffbox {

    margin-bottom: 0;

}
.cg-stars {
    float: right;
    height:19px;
    background: hsla(0, 0%, 0%, 0) url("/wp-content/plugins/wp-image-compression/images/stars.jpg") repeat scroll 0 0;
    width: 100px;
}
.cg-container table tr td  input[type="radio"] {
    display: block;
    float: left;
    margin: 0 7px;
    width: 10px;
}
.cg-container table tr td  p {
    float: left;
    font-size: 12px;
    margin: 0 auto;
}
#wpbody-content .cg-container .metabox-holder {

    padding-top: 0;
}
.cg-container .nav-tab {

    background: hsl(220, 60%, 34%) none repeat scroll 0 0;
    border: medium none;
    border-radius: 2px 2px 0 0;
    color: hsl(0, 0%, 100%);
    display: block;
    float: left;
    font-size: 14px;
    line-height: 24px;
    margin: 0 auto;
       padding: 10px 25px;
}
.cg-container .nav-tab.nav-tab-active {
    background: hsl(13, 92%, 56%) none repeat scroll 0 0;
}
.wrap .cg-container h2.nav-tab-wrapper, .cg-container h1.nav-tab-wrapper, .cg-container h3.nav-tab-wrapper {
    border-bottom: medium none;

}
.cg-container .postbox {
    background: hsl(0, 0%, 93%) none repeat scroll 0 0;
    border: medium none;
    box-shadow: none;
    min-width: auto;
}
.cg-container #poststuff {
     min-width: auto;
    padding-top: 0;
}
.cg-container .postbox .hndle a {
    background: hsl(13, 92%, 56%) none repeat scroll 0 0;
    border-radius: 1px;
    color: hsl(0, 0%, 100%);
    padding: 0 10px 3px;
    text-decoration: none;
}
.cg-container table tr td input, .cg-container table tr td select, .cg-container table tr td textarea {
       border-radius: 1px;
    box-shadow: none;
    font-size: 12px;
    padding: 10px 18px;
    width: 100%;
}
.cg-container .postbox .hndle, .cg-container .stuffbox .hndle {

    background: hsl(0, 0%, 98%) none repeat scroll 0 0;
    border-bottom: medium none;
    color: #1010010;
}
.wp-core-ui .cg-container .button-primary.btn-sec {
    background: hsl(0, 0%, 20%) none repeat scroll 0 0;
    border-color: hsl(0, 0%, 7%);
    margin:0 auto !important;
}
.cg-panel {
    background: hsl(13, 92%, 56%) none repeat scroll 0 0;
    margin-bottom: 10px;
    padding: 5px;
}
.cg-right-pane, .cg-left-pane {
    float: left;
    margin: 6px auto;
}
    .form label{width:100%;display: inline-block;}
    .button-primary{margin-top:30px !important;}
    .check_options{font-weight: bold;}


    ul.tabs{
            margin: 0px;
            padding: 0px;
            list-style: none;
        }

ul.tabs li {

    border-radius: 2px 2px 0 0;
    color: hsl(0, 0%, 100%);
    cursor: pointer;
    display: inline-block;
    float: left;
    margin: 0 1px;

}

.form-table span {
    display: block;
    margin-bottom: 5px !important;
}
.wp-core-ui .cg-container .button-primary {
    margin: 5px 15px 15px !important;
    background: hsl(13, 92%, 56%) none repeat scroll 0 0;
    border-bottom: 5px solid hsl(13, 80%, 49%);
    border-left: medium none !important;
    border-right: medium none !important;
    border-top: medium none !important;
    box-shadow: none;

    color: hsl(0, 0%, 100%);
    height: auto;
    padding: 5px 30px;
    text-decoration: none;
    text-shadow: unset;
}

table tbody tr td {
    color: hsl(0, 0%, 0%);
    font-size: 12px;
    font-weight: 600;
    padding: 5px;
}
ul.tabs li.current {
    background: hsl(13, 92%, 56%) none repeat scroll 0 0;
    color: hsl(0, 0%, 100%);
}

        .tab-content{
            display: none;
            background: #eee;
            clear: both;
            padding: 15px;
        }

        .tab-content.current{
            display: inherit;
        }
        .wrap {
    margin: 10px 20px 0 2px;
      max-width: 1160px;

}
        .wrap header {
    background: hsl(220, 60%, 34%) none repeat scroll 0 0;
    color: hsl(0, 0%, 100%);
    padding: 15px;
}
.wrap header small {
    background: hsl(13, 92%, 56%) none repeat scroll 0 0;
    border-radius: 1px;
    font-size: 13px;
    padding: 2px 13px 3px;
}
.cg-pane-head {
    text-align: right;
}
.wrap header h2 {
      color: hsl(0, 0%, 92%);
    font-size: 20px;
       margin: 10px auto;
    font-weight: 600;
}
.cg-pane-head h2 {
    color: hsl(0, 0%, 100%);
    line-height: 22px;
    text-align: center;
}
.wp-core-ui .wrap .notice.is-dismissible {
    color: hsl(0, 0%, 6%);

}
.cg-tab-wrap {
    float: left;
    padding: 0 15px;
    width: 57%;
}
.cg-pane-small {
    float: left;
    padding: 0 15px;
    width: 36%;
}
.cg-container {
      background: hsl(0, 0%, 100%) none repeat scroll 0 0;
    border: 1px solid hsl(0, 0%, 85%);
    box-shadow: 0 0 2px 1px hsla(0, 0%, 0%, 0.1);
    padding: 15px  ;
}
.cg-container .form-table th {
      font-size: 12px;
    font-weight: 600;
    line-height: 1.3;
    padding: 0 15px;
    text-align: left;
    vertical-align: middle;
    width: 195px;
}
.cg-container .form-table td {
    font-size: 12px;
     padding: 8px 10px;
}
table.form-table {

    margin-top: 0;

}
.cg-container fieldset, .cg-container table {
    border: 0 none;
    width: 100%;
}
.cg-container .postbox .inside {
    margin: 0 auto;
    padding: 0;

}
.cg-container .right-side .form-table th {
    width: auto;
}
table tr:nth-child(2n+1) {
    background: hsl(0, 0%, 87%) none repeat scroll 0 0;
}
.cg-container #poststuff .inside {
    margin: 0 auto;
}
.cg-col-right {
   float: right;
    margin: 10px auto;
    text-align: right;
    width: 40%;
}
.cg-col-left {
    float: left;
    width: 60%;
}
.form-table tr td:first-child {
    width: 140px;
}
.cg-col-right a {
    background: hsl(13, 92%, 56%) none repeat scroll 0 0;
    border-radius: 1px;
    color: hsl(0, 0%, 100%);
    font-size: 12px;
    padding: 1px 10px 3px;
    text-decoration: none;
}
.cg-col-right > p {
    margin: 5px auto;
}
.cg-pane-head img {
      border: 2px solid hsl(0, 0%, 98%);
    width: 100%;
}
.cg-pane-head > a {
    text-decoration: none;
}

.cg-panel h3 {
    color: hsl(0, 0%, 100%);
    font-size: 20px;
    text-align: center;
}

.cg-panel.cg-featured-panel.cg-stye-1 {
    background: hsl(220, 60%, 34%) none repeat scroll 0 0;
}

@media(max-width:768px){

.cg-tab-wrap {

    width: 96%;
}

.cg-col-left, .cg-col-right, .cg-pane-small  {

    width: 100%;
}
.cg-col-right a {

    display: block;

}
}
</style>
<div class="cg-container">
                <div id="poststuff" class="metabox-holder ppw-settings">
                    <div class="left-side">
                        <?php
NMRichReviewsAdminHelper::render_container_open('content-container');
            NMRichReviewsAdminHelper::render_postbox_open('Image convertor settings');
            ?>
                        <form method="post" action="options.php">

                            <?php settings_fields('wpimage-settings-group');?>
                            <table class="form-table">

                                <tr valign="middle">
                                    <th  scope="row"><?php _e("Images uploaded within a Page/Post", 'wpimage');?></th>
                                    <td colspan="3">Fit within <input type="text" style="width: 65px;" name="wpimages_max_width" value="<?php echo get_option('wpimages_max_width', WPIMAGE_DEFAULT_MAX_WIDTH); ?>" />
                                        x <input type="text" style="width: 65px;" name="wpimages_max_height" value="<?php echo get_option('wpimages_max_height', WPIMAGE_DEFAULT_MAX_HEIGHT); ?>" /> pixels width/height <?php _e(" (or enter 0 to disable)", 'wpimage');?>
                                    </td>
                                </tr>

                                <tr valign="middle">
                                    <th  scope="row"><?php _e("Images uploaded directly to the Media Library", 'wpimage');?></th>
                                    <td colspan="3">Fit within <input type="text" style="width: 65px;" name="wpimages_max_width_library" value="<?php echo get_option('wpimages_max_width_library', WPIMAGE_DEFAULT_MAX_WIDTH); ?>" />
                                        x <input type="text" style="width: 65px;" name="wpimages_max_height_library" value="<?php echo get_option('wpimages_max_height_library', WPIMAGE_DEFAULT_MAX_HEIGHT); ?>" /> pixels width/height <?php _e(" (or enter 0 to disable)", 'wpimage');?>
                                    </td>
                                </tr>

                                <tr valign="middle">
                                    <th scope="row"><?php _e("Images uploaded elsewhere (Theme headers, backgrounds, logos, etc)", 'wpimage');?></th>
                                    <td colspan="3">Fit within <input type="text" style="width: 65px;" name="wpimages_max_width_other" value="<?php echo get_option('wpimages_max_width_other', WPIMAGE_DEFAULT_MAX_WIDTH); ?>" />
                                        x <input type="text" style="width: 65px;" name="wpimages_max_height_other" value="<?php echo get_option('wpimages_max_height_other', WPIMAGE_DEFAULT_MAX_HEIGHT); ?>" /> pixels width/height <?php _e(" (or enter 0 to disable)", 'wpimage');?>
                                    </td>
                                </tr>


                                <tr  valign="middle">
                                <td>
                                    <span scope="row"><?php _e("JPG image quality (Default 90)", 'wpimage');?><br>
                                    </span>
                                     <select name="wpimages_quality">
                                            <?php
$q = get_option('wpimages_quality', WPIMAGE_DEFAULT_QUALITY);

            for ($x = 10; $x <= 100; $x = $x + 10) {
                echo "<option" . ($q == $x ? " selected='selected'" : "") . ">$x</option>";
            }
            ?>
                                        </select>
                                       </td>
                                             <td>
                                                 <span scope="row"><?php _e("Convert BMP To JPG", 'wpimage');?></span>
                                  <select name="wpimages_bmp_to_jpg">
                                            <option <?php
if (get_option('wpimages_bmp_to_jpg', WPIMAGE_DEFAULT_BMP_TO_JPG) == "1") {
                echo "selected='selected'";
            }
            ?> value="1"><?php _e("Yes", 'wpimage');?></option>
                                                        <option <?php
if (get_option('wpimages_bmp_to_jpg', WPIMAGE_DEFAULT_BMP_TO_JPG) == "0") {
                echo "selected='selected'";
            }
            ?> value="0"><?php _e("No", 'wpimage');?></option>
                                        </select>

                                             </td>
                                              <td>
                                                  <span scope="row"><?php _e("Convert PNG To JPG", 'wpimage');?></span>
                                    <select name="wpimages_png_to_jpg">
                                            <option <?php
if (get_option('wpimages_png_to_jpg', WPIMAGE_DEFAULT_PNG_TO_JPG) == "1") {
                echo "selected='selected'";
            }
            ?> value="1"><?php _e("Yes", 'wpimage');?></option>
                                            <option <?php
if (get_option('wpimages_png_to_jpg', WPIMAGE_DEFAULT_PNG_TO_JPG) == "0") {
                echo "selected='selected'";
            }
            ?> value="0"><?php _e("No", 'wpimage');?></option>
                                        </select>

                                              </td>


                                </tr>





                            </table>

                            <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes')?>" /></p>

                        </form>
                        <?php
NMRichReviewsAdminHelper::render_postbox_close();
            NMRichReviewsAdminHelper::render_container_close();

            NMRichReviewsAdminHelper::render_container_open('content-container');
            NMRichReviewsAdminHelper::render_postbox_open('About');
            $this->callback_about_us_compression();
            NMRichReviewsAdminHelper::render_postbox_close();
            NMRichReviewsAdminHelper::render_container_close();
            ?>
                    </div>
                </div>
                <?php $this->displayRightCompressionText();?>
                <div style="clear:both"></div>
            </div>
            <?php
}

        public function api_settings_compression()
        {

        }

        public function callback_about_us_compression()
        {
            $output = '<p style="padding:0 15px 15px" ><strong>Built and maintained by Optimisation.io, a Hosting.io brand dedicated to fast performance WordPress websites.
            Our other free plugins can be found on the <a href = "https://profiles.wordpress.org/pigeonhut/#content-plugins" target = "_blank">WordPress Plugin Repositiory/</a>
           Proudly made in Belfast, Northern Ireland.</p>';
            echo $output;
        }

        public function displayRightCompressionText()
        {
            ?>
            <div class="right-side">
                <?php
NMRichReviewsAdminHelper::render_container_open('content-container-right');
            NMRichReviewsAdminHelper::render_postbox_open('API Settings');
            echo $this->callCustomCompression();
            NMRichReviewsAdminHelper::render_postbox_close();
            NMRichReviewsAdminHelper::render_container_close();

            NMRichReviewsAdminHelper::render_container_open('content-container-right');

            ?>

        <div class="cg-pane-head">
            <a target="blank" href="https://wordpress.org/plugins/wp-disable/"><img src="https://res.cloudinary.com/dhnesdsyd/image/upload/v1491036438/wp-disable_wmhjmb.jpg" alt="" /></a>
        </div>

         <div class="cg-panel">
        <div class="cg-pane-head">
            <a target="blank" href="#"><h2>WordPress Cache<br>Coming Soon</h2></a>
        </div>
        </div>
         <div class="cg-pane-head">
           <a target="blank" href="https://optimisation.io/"><img src="http://res.cloudinary.com/dhnesdsyd/image/upload/q_auto/v1490964304/optimisation_noj4ri.jpg" alt="" /></a>
           <a target="blank" style="text-decoration: none;" href="https://optimisation.io">Still Need Help ? We also do manual optimisations.</a>
        </div>

            </div>
            <?php
}

        public function compress_what_we_do()
        {
            $output = '<img src = "https://optimisation.io/wp-content/uploads/2015/12/optimisation-after.png"/></a>';
            $output .= '<span style = "background: none repeat scroll 0 0 #FFA650;display:block;padding: 10px; color:#fff;">Looking for more performance, try our <a href="https://wordpress.org/plugins/wp-disable/" target = "_blank">WP Disable</a> plugin - Disable unused HTTP requests to help speed up your website<br>
            Stress and Settings free caching, try our <a href="#">WP Optimisation</a> plugin - Easy, fast WordPress Caching<br></span>';
            echo $output;
        }

        public function my_enqueue($hook)
        {
            if ($hook == 'options-media.php' || $hook == 'upload.php') {
                wp_enqueue_script('jquery');
                wp_enqueue_script('tipsy-js', plugins_url('/js/jquery.tipsy.js', __FILE__), array('jquery'));
                wp_enqueue_script('async-js', plugins_url('/js/async.js', __FILE__));
                wp_enqueue_script('ajax-script', plugins_url('/js/ajax.js', __FILE__), array('jquery'));
                wp_enqueue_style('wpimage_admin_style', plugins_url('css/admin.css', __FILE__));
                wp_enqueue_style('tipsy-style', plugins_url('css/tipsy.css', __FILE__));
                wp_enqueue_style('modal-style', plugins_url('css/jquery.modal.css', __FILE__));
                wp_localize_script('ajax-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
                wp_enqueue_script('modal-js', plugins_url('/js/jquery.modal.min.js', __FILE__), array('jquery'));
             }
             wp_enqueue_script('stripe-js', 'https://checkout.stripe.com/checkout.js');
             wp_enqueue_script('stripe-custom-js', plugins_url('/js/stripe.js', __FILE__),20, 1, true);
        }

        public function show_wp_image_optimizer()
        {
            echo '<a href = "https://optimisation.io">Optimisation.io</a> for API settings';
        }

        public function get_api_status()
        {
            return true;
        }

        /**
         *  Handles optimizing already-uploaded images in the  Media Library
         */
        public function wpimage_media_library_ajax_callback()
        {

            $image_id = (int) $_POST['id'];
            $type     = false;
            if (isset($_POST['type'])) {
                $type = $_POST['type'];
            }

            $this->id = $image_id;

            if (wp_attachment_is_image($image_id)) {
//error_reporting(E_ALL);
                //ini_set("display_errors",1);
                $image_path = get_attached_file($image_id);
//$image_path = wp_get_attachment_url($image_id);
                $settings = $this->compression_settings;

                $status = $this->get_api_status();

                if ($status === false) {
                    $kv['error'] = 'There is a problem with your credentials. Please check them in the wp-image.co.uk settings section of Media Settings, and try again.';
                    update_post_meta($image_id, '_wpimage_size', $kv);
                    echo json_encode(array('error' => $kv['error']));
                    exit;
                }
                // if (isset($status['active'])) {
                if ($status == true) {

                } else {
                    echo json_encode(array('error' => 'Your API is inactive. Please visit your account settings'));
                    die();
                }

                $result = $this->optimize_image($image_path, $this->backup_before_compression);

                $kv = array();

                if ($result && $result['url']) {

                    $compressed_url        = $result['url'];
                    $savings_percentage    = (int) $result['saved_bytes'] / (int) $result['original_size'] * 100;
                    $kv['original_size']   = self::pretty_kb($result['original_size']);
                    $kv['compressed_size'] = self::pretty_kb($result['bytes']);
                    $kv['saved_bytes']     = self::pretty_kb($result['saved_bytes']);
                    $kv['savings_percent'] = round($savings_percentage, 2) . '%';
                    $kv['backup_before_compression'] = $result['backup_before_compression'];
                    $kv['success']         = true;
                    $kv['meta']            = wp_get_attachment_metadata($image_id);

                    if ($this->replace_image($image_path, $compressed_url)) {

                        // get metadata for thumbnails
                        $image_data = wp_get_attachment_metadata($image_id);
                        $this->optimize_thumbnails($image_data);

                        // store compressed info to DB
                        update_post_meta($image_id, '_wpimage_size', $kv);

                        // Compress thumbnails, store that data too
                        $compressed_thumbs_data = get_post_meta($image_id, '_compressed_thumbs', true);
                        if (!empty($compressed_thumbs_data)) {
                            $kv['thumbs_data'] = $compressed_thumbs_data;
                        }

                        $this->delete_resources($result['public_id']);

                        echo json_encode($kv);
                    } else {
                        echo json_encode(array('error' => 'Could not overwrite original file. Please ensure that your files are writable by plugins.'));
                        exit;
                    }
                } else {

                    // error or no optimization
                    if (file_exists($image_path)) {

                        $kv['original_size'] = self::pretty_kb(filesize($image_path));
                        $kv['error']         = $result['error'];
                        $kv['type']          = $result['type'];

                        if ($kv['error'] == 'This image can not be optimized any further') {
                            $kv['compressed_size'] = 'No savings found';
                            $kv['no_savings']      = true;
                        }

                        update_post_meta($image_id, '_wpimage_size', $kv);
                    } else {
                        // file not found
                    }
                    echo json_encode($result);
                }
            }
            die();
        }

        /**
         *  Handles optimizing images uploaded through any of the media uploaders.
         */
        public function wpimage_media_uploader_callback($image_id)
        {
            $this->id = $image_id;

            if (wp_attachment_is_image($image_id)) {

                $settings                  = $this->compression_settings;
                $backup_before_compression = $settings['backup_before_compression'];
                $image_path                = get_attached_file($image_id);
                $result                    = $this->optimize_image($image_path, $backup_before_compression);

                if ($result && isset($result['url'])) {

                    $compressed_url                  = $result['url'];
                    $savings_percentage              = (int) $result['saved_bytes'] / (int) $result['original_size'] * 100;
                    $kv['original_size']             = self::pretty_kb($result['original_size']);
                    $kv['compressed_size']           = self::pretty_kb($result['bytes']);
                    $kv['saved_bytes']               = self::pretty_kb($result['saved_bytes']);
                    $kv['savings_percent']           = round($savings_percentage, 2) . '%';
                    $kv['backup_before_compression'] = $settings['backup_before_compression'];
                    $kv['success']                   = true;
                    $kv['meta']                      = wp_get_attachment_metadata($image_id);

                    if ($this->replace_image($image_path, $compressed_url)) {
                        update_post_meta($image_id, '_wpimage_size', $kv);
                        $this->delete_resources($result['public_id']);
                    } else {
                        // writing image failed
                    }
                } else {

                    // error or no optimization
                    if (file_exists($image_path)) {

                        $kv['original_size'] = self::pretty_kb(filesize($image_path));
                        $kv['error']         = $result['error'];
                        $kv['type']          = $result['type'];

                        if ($kv['error'] == 'This image can not be optimized any further') {
                            $kv['compressed_size'] = 'No savings found';
                            $kv['no_savings']      = true;
                        }

                        update_post_meta($image_id, '_wpimage_size', $kv);
                    } else {
                        // file not found
                    }
                }
            }
        }

        public function show_credentials_validity()
        {

            $settings   = $this->compression_settings;
            $status = $this->get_api_status();
            $url    = admin_url() . 'images/';

            if ($status !== false && isset($status['active'])) {
                $url .= 'yes.png';
                echo '<p class = "apiStatus">Your credentials are valid <span class = "apiValid" style = "background:url(' . "'$url') no-repeat 0 0" . '"></span></p>';
            } else {
                $url .= 'no.png';
                echo '<p class = "apiStatus">There is a problem with your credentials <span class = "apiInvalid" style = "background:url(' . "'$url') no-repeat 0 0" . '"></span></p>';
            }
        }

        public function validate_options($input)
        {
            $valid              = array();
            $error              = '';
            $valid['api_lossy'] = $input['api_lossy'];

            if (!function_exists('curl_exec')) {
                $error = 'cURL not available. Wp image compression requires cURL in order to communicate with wp-image.co.uk servers. <br /> Please ask your system administrator or host to install PHP cURL, or contact support@wp-image.co.uk for advice';
            } else {
                $status = $this->get_api_status();

                if ($status !== false) {

                    if (isset($status['active'])) {
                        if ($status['plan_name'] === 'Developers') {
                            $error = 'Developer API credentials cannot be used with this plugin.';
                        } else {
                            $valid['api_key']    = $input['api_key'];
                            $valid['api_secret'] = $input['api_secret'];
                        }
                    } else {
                        $error = 'There is a problem with your credentials. Please check them from your wp-image.co.uk account.';
                    }
                } else {
                    $error = 'Please enter a valid wp-image.co.uk API key and secret';
                }
            }

            if (!empty($error)) {
                add_settings_error(
                    'media', 'api_key_error', $error, 'error'
                );
            }
            return $valid;
        }


        public function show_lossy()
        {
            $options = get_option('_wpimage_options');
            $value   = isset($options['api_lossy']) ? $options['api_lossy'] : 'lossy';

            $html = '<input type="radio" id="wpicompressor_lossy" name="_wpimage_options[api_lossy]" value="lossy"' . checked('lossy', $value, false) . '/>';
            $html .= '<label for="wpicompressor_lossy">Lossy</label>';

            $html .= '<input style="margin-left:10px;" type="radio" id="wpimage_lossless" name="_wpimage_options[api_lossy]" value="lossless"' . checked('lossless', $value, false) . '/>';
            $html .= '<label for="wpimage_lossless">Lossless</label>';

            echo $html;
        }

        public function add_media_columns($columns)
        {
            $columns['original_size']   = 'Original Size';
            $columns['compressed_size'] = 'compressed Size';
            return $columns;
        }

        public function fill_media_columns($column_name, $id)
        {

            $original_size = filesize(get_attached_file($id));
            $original_size = self::pretty_kb($original_size);

            $options = get_option('_wpimage_options');
            $type    = isset($options['api_lossy']) ? $options['api_lossy'] : 'lossy';

            if (strcmp($column_name, 'original_size') === 0) {
                if (wp_attachment_is_image($id)) {

                    $meta = get_post_meta($id, '_wpimage_size', true);

                    if (isset($meta['original_size'])) {
                        echo $meta['original_size'];
                    } else {
                        echo $original_size;
                    }
                } else {
                    echo $original_size;
                }
            } else if (strcmp($column_name, 'compressed_size') === 0) {

                if (wp_attachment_is_image($id)) {

                    $meta = get_post_meta($id, '_wpimage_size', true);

                    // Is it optimized? Show some stats
                    if (isset($meta['compressed_size']) && empty($meta['no_savings'])) {
                        $compressed_size = $meta['compressed_size'];
                        if ($meta['savings_percent'] >= 0) {
                            $savings_percentage = $meta['savings_percent'];
                            echo '<strong>' . $compressed_size . '</strong><br /><br /><small>Savings:&nbsp;' . $savings_percentage . '</small>';

                            $thumbs_data  = get_post_meta($id, '_compressed_thumbs', true);
                            $thumbs_count = count($thumbs_data);

                            if (!empty($thumbs_data)) {
                                echo '<br /><small>' . $thumbs_count . ' thumbs optimized' . '</small>';
                            }
                        } else {
                            echo '<small>No further optimization required</small>';
                        }
                        // Were there no savings, or was there an error?
                    } else {
                        $image_url = wp_get_attachment_url($id);
                        $filename  = basename($image_url);
                        echo '<div class="buttonWrap"><button data-setting="' . $type . '" type="button" class="wpimage_req" data-id="' . $id . '" id="wpimageid-' . $id . '" data-filename="' . $filename . '" data-url="' . $image_url . '">Optimize This Image</button><span class="wpimageSpinner"></span></div>';
                        if (!empty($meta['no_savings'])) {
                            echo '<div class="noSavings"><strong>No savings found</strong><br /><small>Type:&nbsp;' . $meta['type'] . '</small></div>';
                        } else if (isset($meta['error'])) {
                            $error = $meta['error'];
                            echo '<div class="wpimageErrorWrap"><a class="wpimageError" title="' . $error . '">Failed! Hover here</a></div>';
                        }
                    }
                } else {
                    echo 'n/a';
                }
            }
        }

        public function replace_image($image_path, $compressed_url)
        {
            $rv = false;
            $ch = curl_init($compressed_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $result = curl_exec($ch);
            $rv     = file_put_contents($image_path, $result);
            return $rv !== false;
        }

        public function optimize_image($image_path, $backup)
        {
            $settings = $this->compression_settings;
            $wpimage  = new Wpimage();

            $params = array(
                "file"                      => $image_path,
                "origin"                    => "wp",
                'backup_before_compression' => $backup,
                'wpimages_quality'          => $settings['wpimages_quality'],
            );
            $data = $wpimage->upload($params);
            return $data;
        }

        public function delete_resources($public_id)
        {
            $settings = $this->compression_settings;
            $wpimage  = new Wpimage();
            $wpimage->deleteResource($public_id);

        }

        public function optimize_thumbnails($image_data)
        {

            $image_id = $this->id;
            if (empty($image_id)) {
                global $wpdb;
                $post     = $wpdb->get_row($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %s LIMIT 1", $image_data['file']));
                $image_id = $post->post_id;
            }

            $path_parts = pathinfo($image_data['file']);

            // e.g. 04/02, for use in getting correct path or URL
            $upload_subdir = $path_parts['dirname'];

            $upload_dir = wp_upload_dir();

            // all the way up to /uploads
            $upload_base_path = $upload_dir['basedir'];
            $upload_full_path = $upload_base_path . '/' . $upload_subdir;

            $sizes = array();

            if (isset($image_data['sizes'])) {
                $sizes = $image_data['sizes'];
            }

            if (!empty($sizes)) {

                $thumb_path = '';

                $thumbs_optimized_store = array();
                $this_thumb             = array();

                foreach ($sizes as $key => $size) {

                    $thumb_path = $upload_full_path . '/' . $size['file'];

                    if (file_exists($thumb_path) !== false) {

                        $result = $this->optimize_image($thumb_path, $this->backup_before_compression);

                        if (!empty($result) && isset($result) && isset($result['url'])) {
                            $compressed_url = $result["url"];
                            if ($this->replace_image($thumb_path, $compressed_url)) {
                                $this_thumb               = array('thumb' => $key, 'file' => $size['file'], 'original_size' => $result['original_size'], 'compressed_size' => $result['bytes'], 'backup_before_compression' => $this->backup_before_compression);
                                $thumbs_optimized_store[] = $this_thumb;
                            }
                            //deleting the image from clould
                            $this->delete_resources($result['public_id']);
                        }
                    }
                }
            }
            if (!empty($thumbs_optimized_store)) {
                update_post_meta($image_id, '_compressed_thumbs', $thumbs_optimized_store, false);
            }
            return $image_data;
        }

        public static function pretty_kb($bytes)
        {
            return round(($bytes / 1024), 2) . ' kB';
        }

        public function admin_tabs_callmeback($tabs, $current = null)
        {
            if (is_null($current)) {
                if (isset($_GET['page'])) {
                    $current = $_GET['page'];
                }
            }
            $content = '';
            $content .= '<h2 class="nav-tab-wrapper">';
            foreach ($tabs as $location => $tabname) {
                if ($current == $location) {
                    $class = ' nav-tab-active';
                } else {
                    $class = '';
                }
                $content .= '<a class="nav-tab' . $class . '" href="?page=' . $location . '">' . $tabname . '</a>';
            }
            $content .= '</h2>';
            return $content;
        }

    }

}
new Wp_Image_compression();
