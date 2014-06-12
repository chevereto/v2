<?php
/* --------------------------------------------------------------------

  Chevereto
  http://chevereto.com/

  @version	2.6.0
  @author	Rodolfo Berríos A. <http://rodolfoberrios.com/>
			<inbox@rodolfoberrios.com>

  Copyright (c) Rodolfo Berrios <inbox@rodolfoberrios.com>
  
  Licensed under the MIT license
  http://opensource.org/licenses/MIT

  --------------------------------------------------------------------- */

/**
 * Theme
 * http://chevereto.com/themes/
 */
$config['theme'] = 'Peafowl';  // Place the case-sensitive folder name of your theme located in /includes/themes/


/**
 * Default language display
 * If you need to add a new language just create a new xx.php file under includes/lang/ then change 'en' to 'xx'
 */
$config['lang'] = 'en'; // en - English | es - Spanish | etc...


/**
 * Auto language
 * If is set to true, the script will set the language according to the visitor language.
 * default: true
 */
$config['auto_lang'] = true; // Values: true | false


/**
 * Site details
 */ 
$config['site_name']		= 'Chevereto';
$config['doctitle']			= 'Chevereto image hosting script';
$config['meta_description'] = 'Chevereto is a free image hosting service powered by Chevereto';
$config['meta_keywords']	= 'images, photos, image hosting, photo hosting, free image hosting';


/**
 * Database details (MySQL)
 */ 
$config['db_host'] = 'localhost'; // localhost is mostly the default on all servers. Do not indicate port here!
$config['db_port'] = ''; // Some servers needs to indicate the port of the database hostname - default: don't set it
$config['db_name'] = 'chevereto'; // Database must exists
$config['db_user'] = 'root'; // Database user with access to the above database name
$config['db_pass'] = '';


/**
 * Maintenance mode 
 * Sets the maintenance mode status on/off
 * @Since 2.5
 */
$config['maintenance'] = false;


/**
 * Google Analytics
 * Enables Google Analytics tracking code in the public areas. Set up your account at http://www.google.com/analytics/
 * Tip: To add more tracking systems use the header.php and footer.php template files.
 * @Since 2.5
 */
$config['google_analytics_tracking_id'] = ''; // Something like UA-25384365-1


/**
 * Minify files (JS/CSS)
 * Set on/off the minify mode. If you turn on minify_files (true) the system will use the minified version of the
 * JS and CSS files. If the minify file doesn't exist the system will try to create it for you.
 * @Since 2.4.2
 * Default: true
 */
$config['minify'] = true; // Values: true | false


/**
 * Private mode
 * Set on/off the private mode. If you turn on the private_mode (true) only the admin and the people with the
 * user password will have access to upload images
 * @Since 2.2
 * Default: false
 */
$config['private_mode'] = false; // Values: true | false


/**
 * User password
 * If you set this value and the private_mode is true, only the people with this password can upload images
 * This MUST not be the same as 'admin_password'
 * Note 1: This users can't enter the admin area (file manager)
 * Note 2: Admin can always upload images using their own password
 * @Since 2.2
 */
$config['user_password'] = '';


/**
 * Admin folder
 * This will be the folder of the admin area (file manager)
 * @Since 2.4.2
 * Default: 'admin'
 */
$config['admin_folder'] = 'admin';

/**
 * Admin password
 * This will be the password to enter the admin area (file manager)
 * This MUST not be the same as 'user_password'
 */
$config['admin_password'] = 'password';


/**
 * API settings
 * Sets the API configurable values
 */
$config['api_key']	= 'my_api_key'; // Whatever you want. Default: my_api_key
$config['api_mode'] = 'private'; // Values: public | private - Public: No restrictions, Private: Need to parse the API key.
 

/**
 * Storage
 * Select where do you want to store the uploads. This don't affect the actual files, just the new uploads.
 * Default: datefolders
 * @Since 2.2
 */
$config['storage'] = 'datefolders'; // Values: datefolders | direct -> datefolders (example: /YYYY-MM-DD/file.ext) | direct (/image/file.ext)


/**
 * File naming set-up
 * Set up how you will like to name the uploaded files.
 * 
 * 'original' : Try to keep the original file name. If the file already exists the 'mixed' method will be used for that file.
 * 'random'   : Use absolute random names for the uploaded files.
 * 'mixed'    : Keeps part of the original file name including random chars at the end of the file.
 *
 * Default: original
 * @Since 2.5
 */
$config['file_naming'] = 'original';


/**
 * Folders set-up
 * This are the main folders used by the script.
 * -> If you change this you also must change the folders name.
 */
$config['folder_images'] = 'images';
$config['folder_thumbs'] = 'images/thumbs'; // Just for legacy concerns, this is actually not used anymore since 2.1


/**
 * Virtual folders set-up
 * This are the virtual folders used by the scripts, they MUST be setted.
 * -> This folders are used by PHP, they aren't real folders but they may conflic with real folders. Try to don't have this folders as real folders.
 */
$config['virtual_folder_image']		= 'image'; // used on image viewer ie: (http://mysite.com/image/<ID>)
$config['virtual_folder_uploaded']	= 'uploaded'; // used on multiupload image process ie:(http://mysite.com/uploaded)


/**
 * Allowed Minimum-Maximum values
 * This values are globally afected by PHP configuration located in php.ini.
 * This limits have a maximum setted by yout hosting company, Chevereto can't override them.
 */
$config['max_filesize'] = "2 MB"; // Allowed maximum image size to be uploaded - default: 2 MB

$config['thumb_width']	= 100; // Thumb width in pixels - default: 100
$config['thumb_height'] = 90; // Thumb height in pixels - default: 90

$config['min_resize_size'] = 16; // Min. resize value (pixels)
$config['max_resize_size'] = 1280; // Max. resize value (pixels) - 0: No limit


/**
 * multiupload
 * Multiupload feature for local file uploads.
 */
$config['multiupload'] = true;
$config['multiupload_limit'] = '10'; // Maximun number images on multiupload queue values. No-limit: 0


/**
 * over_resize
 * Chevereto can resize images but you can limit if it can over resize images like turning a 100x100 into 1280x1280
 * default: false
 */
$config['over_resize'] = false; // Values: true | false


/**
 * flood_protection
 * Switch for enable/disable the flood protection based on IP
 * default: true
 */
$config['flood_protection'] = true; // Values: true | false

/**
 * flood_report_email
 * Enter a email address if you want to recieve flood reports.
 * The email will be sent using the php mail() function
 * Note: This could mean a lot of emails, it depends on how many people use your website.
 */
$config['flood_report_email'] = ''; // Values: '' | somemail@mail.com

/**
 * Flood limits
 * This are the limits for the uploads / time period per user
 * Admin user will always bypass this limits
 * no limit: 0
 */
$config['max_uploads_per_minute'] = 15; // Default: 15
$config['max_uploads_per_hour']   = 30; // Default: 30 
$config['max_uploads_per_day'] 	  = 50; // Default: 50
$config['max_uploads_per_week']   = 200; // Default: 200
$config['max_uploads_per_month']  = 500; // Default: 500


/**
 * error_reporting
 * Switch for enable/disable the PHP error reporting
 * -> When this is on TRUE the upload process won't trigger the redirect and the debug will be output
 * default: false
 */
$config['error_reporting'] = false; // Values: true | false


/**
 * short_url
 * Short URL allows the ability of shrink the long image URL to something like
 * http://tinyurl.com/c9a8pc using a short_url_service.
 * default: true
*/
$config['short_url'] = true; // Values: true | false


/**
 * short_url_service
 * Remember $config['short_url'] There we set up wich service we want to use.
 * default: tinyurl
*/
$config['short_url_service'] = 'tinyurl'; // Values: tinyurl | google | isgd | bitly | custom
/*
 * if using google put your own API key in the 'short_url_keypass' config. Get your API key here: https://code.google.com/apis/console
 * More info here: https://developers.google.com/url-shortener/v1/getting_started
 */


/**
 * custom_short_url_api
 * If you set short_url_service to custom you need to specify the API url for your custom service
 * including user login, output format (raw text) and at the end the empty url param (see the example)
 * Note: Your service must have raw text output via GET.
*/
$config['custom_short_url_api'] = ''; // Example: http://yoursite.com/yourls-api.php?format=simple&action=shorturl&username=<USERNAME>&password=<PASSWORD>&url=


/**
 * custom_short_url_service
 * The name of your custom short url service
*/
$config['custom_short_url_service'] = 'MyService'; 


/**
 * short_url_user & short_url_keypass
 * In case that the cut URL service API needs user:pass or id:key
 * Note: This is not valid for custom short_url_service
*/
$config['short_url_user'] = ''; // user/id  example: $config['short_url_user'] = 'bitlyuser'
$config['short_url_keypass'] = ''; // pass/key example: $config['short_url_keypass'] = '123456'


/**
 * short_url_image
 * What image resource will be the shorted url in bitly, tinyurl, etc?
 * default: shorturl
*/
$config['short_url_image'] = 'shorturl'; // Values: shorturl | direct | viewer


/**
 * facebook_app_id
 * The id of your facebook app id (useful to place facebook social plugins all over the site)
 * default: none
*/
$config['facebook_app_id'] = '263511280400003'; // Get your app id on http://developers.facebook.com/apps


/**
 * facebook_comments
 * Enable (true) or disable (false) facebook comments on your theme
 * default: none
*/
$config['facebook_comments'] = true; // Values: true | false


/**
 * watermark_enable
 * Enable (true) or disable (false) watermark image.
 * default: false
*/
$config['watermark_enable'] = false; // Values: true | false

/**
 * watermark_image
 * Location of the watermark PNG file relative to the Chevereto directory
 * Note: It must be a PNG file!
*/
$config['watermark_image'] = 'content/system/watermark.png'; // Example content/system/watermark.png

/**
 * watermark_position
 * Relative position of the watermark image
 * First word sets the horizontal position and second word sets the vertical position
 * default: right bottom
*/
$config['watermark_position'] = 'right bottom'; // Values: (right,center,bottom) (top,center,bottom)

/**
 * watermark_margin
 * Marging from the border of the image to the watermark image
 * default: 10
*/
$config['watermark_margin'] = 10; // Values: numeric

/**
 * watermark_opacity
 * Opacity of the watermak on the watermarked image
 * default: 30
*/
$config['watermark_opacity'] = 30; // Values: 0 -> 100


///////////////////////////////////////////////////////////////////////////////////
//////////////   EDIT BELOW THIS ONLY IF YOU KNOW WHAT YOU'RE DOING    ////////////
///////////////////////////////////////////////////////////////////////////////////

/**
 * Script paths
 * Use 'auto' to let Chevereto set the paths. If you have issues you should define this values manually.
 * Default: 'auto'
 * @since 2.4.2
 */
$config['root_dir']		= 'auto'; // Values: auto | /home/user/public_html/chevereto/
$config['relative_dir']	= 'auto'; // Values: auto | /chevereto/

/*** CloudFlare IP workaround ***/
// Uncomment this line if you are using CloudFlare
//$_SERVER['REMOTE_ADDR'] = (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"]; 

?>