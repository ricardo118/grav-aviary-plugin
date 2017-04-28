<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

require __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
/**
 * Aviary image editor integration
 *
 * [Longer description]
 *
 * Class AviaryPlugin
 * @package Grav\Plugin
 * @return void
 * @license MIT License by ricardo118
 */

/**
 * Class AviaryPlugin
 * @package Grav\Plugin
 */
class AviaryPlugin extends Plugin
{
    /**
     * Route for Ajax-Endpoint
     * @var string
     */
    protected $route = 'admin/aviary-endpoint';

    /**
     * Route for Auth-Endpoint
     * @var string
     */
    protected $authRoute = 'aviary-authentication-endpoint';

    /**
     * Access Grav Configuration
     * @return array Grav resources, Symfony Filesystem
     */
    public function config()
    {
        $config = array();
        $config['locator'] = $this->grav['locator'];
        $config['filesystem'] = new Filesystem();
        return $config;
    }
    
    /**
     * [getSubscribedEvents description]
     * @return [type] [description]
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onAssetsInitialized' => ['onAssetsInitialized', 0]
        ];
    }

    /**
     * Access Plugin Configuration
     * @return array Config
     */
    public function getPluginConfig()
    {
        $aviaryConfig = [ //get the true or false configs
            'enable_admin' => $this->config->get('plugins.aviary.enable_admin'),
            'enable_site' => $this->config->get('plugins.aviary.enable_site'),
            'display_img_size' => $this->config->get('plugins.aviary.display_img_size'),
            'enable_cors' => $this->config->get('plugins.aviary.enable_cors'),
            'crop_strict' => $this->config->get('plugins.aviary.crop_strict')
        ];

        foreach ($aviaryConfig as $key => $value) { // set the true or false configs to strings
            if($value){
                $aviaryConfig[$key] = 'true';
            }else{
                $aviaryConfig[$key] = 'false';
            }
        }

        // Get all the non true/false configs after
        $aviaryConfig['language'] = $this->config->get('plugins.aviary.language');
        $aviaryConfig['all_tools'] = $this->config->get('plugins.aviary.all_tools');
        $aviaryConfig['theme_select'] = $this->config->get('plugins.aviary.theme_select');
        $aviaryConfig['img_quality'] = $this->config->get('plugins.aviary.img_quality');
        $aviaryConfig['crop_presets'] = $this->config->get('plugins.aviary.crop_presets');

        //setup the custom theme if needed
        if ($aviaryConfig['theme_select'] == 'custom'){
            $this->getCustomTheme();
            $aviaryConfig['theme_select'] = "minimum";
        }

        // return all the configs
        return $aviaryConfig;
    }

    public function getCropPresets($cropsArray)  // call this function only if presets not null
    {
        $presets = "[";
        foreach ($cropsArray as $key => $value) {
               $presets .= "['" . $key . "','" . "$value" . "'],";
        }
        $presets .= "]";

        return $presets;
    }

    //Get which theme we're using and if custom add the new asset.
    public function getCustomTheme()
    {
        $this->grav['assets']->addInlineCss($this->config->get('plugins.aviary.theme_custom_editor'), ['priority' => 0]);
    }

    // This function should only be called when all_tools is != to 'all'
    public function getCustomTools($pluginConfig)
    {
        // get the custom tools config here for performance
        $toolsConfig = [
            'enhance' => $this->config->get('plugins.aviary.enhance'),
            'effects' => $this->config->get('plugins.aviary.effects'),
            'frames' => $this->config->get('plugins.aviary.frames'),
            'overlays' => $this->config->get('plugins.aviary.overlays'),
            'stickers' => $this->config->get('plugins.aviary.stickers'),
            'orientation' => $this->config->get('plugins.aviary.orientation'),
            'crop' => $this->config->get('plugins.aviary.crop'),
            'resize' => $this->config->get('plugins.aviary.resize'),
            'lighting' => $this->config->get('plugins.aviary.lighting'),
            'color' => $this->config->get('plugins.aviary.color'),
            'focus' => $this->config->get('plugins.aviary.focus'),
            'vignette' => $this->config->get('plugins.aviary.vignette'),
            'blemish' => $this->config->get('plugins.aviary.blemish'),
            'whiten' => $this->config->get('plugins.aviary.whiten'),
            'redeye' => $this->config->get('plugins.aviary.redeye'),
            'draw' => $this->config->get('plugins.aviary.draw'),
            'colorsplash' => $this->config->get('plugins.aviary.colorsplash'),
            'text' => $this->config->get('plugins.aviary.text'),
            'meme' => $this->config->get('plugins.aviary.meme')
        ];

        //Set up the array to hold custom tools
        $customTools = [];
        if ($pluginConfig['all_tools'] == 'custom') {
            foreach ($toolsConfig as $key => $value) {
                if ($value) {
                    array_push($customTools, $key);
                }
            }
        }

        //Create a Basic Config here.
        if ($pluginConfig['all_tools'] == 'basic'){
            array_push($customTools,
                'enhance',
                'orientation',
                'crop',
                'resize'
            );
        }

        return json_encode($customTools);
    }

    public function getJsCode(){

        $opts = $this->getPluginConfig();

        if ($opts['all_tools'] == 'all'){
            $tools = "'all'\n";
        }else {
            $tools = $this->getCustomTools($opts);
        }

        if ($opts['crop_presets'] != null){
            $crops = $this->getCropPresets($opts['crop_presets']);
        }

        $js  = "editorConfig = {\n";
        $js .=     "apiKey: 'bf06a5ee072248539ec95c826d4366f1',\n";
        $js .=     "language: '" . $opts['language'] . "',\n";
        $js .=     "enableCORS: " . $opts['enable_cors'] . ",\n";
        $js .=     "displayImageSize: " . $opts['display_img_size'] . ",\n";
        $js .=     "theme: '" . $opts['theme_select'] . "',\n";
        $js .=     "cropPresetsStrict: " . $opts['crop_strict'] . ",\n";
        $js .=     "jpgQuality: " . $opts['crop_strict'] . ",\n";
        $js .=     "tools: " . $tools . ",\n";
        if(isset($crops)){
            $js .=     "cropPresets: " .  $crops . "\n";
        }
        $js .= "};";

        return $js;
    }

    /**
     * [onAssetsInitialized description]
     */
    public function onAssetsInitialized()
    {
        $js = $this->getJsCode();
        $this->grav['assets']->addJs('user/plugins/aviary/js/dropzone.js', ['loading' => 'defer']);
        $this->grav['assets']->addJs('user/plugins/aviary/js/aviary.js', ['loading' => 'defer', 'priority' => 0]);
        $this->grav['assets']->addInlineJs($js, ['loading' => 'defer']);
        $this->grav['assets']->addCss('user/plugins/aviary/css/aviary.css');

        if ($this->grav['uri']->scheme() == 'http://') {
            $this->grav['assets']->addJs('http://feather.aviary.com/imaging/v3/editor.js');
        } else {
            $this->grav['assets']->addJs('https://dme0ih8comzn4.cloudfront.net/imaging/v3/editor.js');
        }
    }

    /**
     * Initialize the plugin
     * @return void
     */
    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            $this->enable([
                'onPagesInitialized' => ['pluginEndpoint', 0],
                'onAssetsInitialized' => ['onAssetsInitialized', 0]
            ]);
        }
        $this->enable([
            'onPagesInitialized' => ['pluginAuthEndpoint', 0]
        ]);
    }

    /**
     * Ajax-Endpoint to handle Auth
     * @return obj Authentication
     * @throws \Exception
     */
    public function getAuth()
    {
        $key = 'bf06a5ee072248539ec95c826d4366f1';
        $secret = '933f2342-7bc7-4b76-a3e5-4d75619ab245';
        $salt = rand(0,1000);
        $timestamp = time();
        $sig = $key . $secret . $timestamp . $salt;
        $sig = sha1($sig);

        $authObj = array(
            "salt" => $salt,
            "timestamp" => $timestamp,
            "encryptionMethod" => 'sha1',
            "signature" => $sig
        );

        return $authObj;
    }

    /**
     * Ajax-Endpoint to handle File-operations
     * @param Event $e RocketTheme\Toolbox\Event\Event
     * @return string Prints state of operation
     * @throws \Exception
     */
    public function pluginEndpoint(Event $e)
    {
        $uri = $this->grav['uri'];
        
        if (strpos($uri->path(), $this->config->get('plugins.aviary.route') . '/' . $this->route) === false) {
            return;
        }
        
        if (!isset($_POST['data'])) {
            return 'No POST-data.';
        } else {
            $data = $_POST['data'];
        }

        $config = $this->config();
        $target = $config['locator']->base . '' . $data['uploadPath'] . '/' . $data['imgName'];

        // Save edited image
        try {
            $config['filesystem']->copy($data['remotePath'], $target, true);
        } catch (\FileNotFoundException $e) {
            throw new \Exception($e);
        } catch (\IOException $e) {
            throw new \Exception($e);
        }

        print $target . ' replaced.';
        exit();
    }
    /**
     * Auth-Endpoint for Aviary
     * @param Event $e RocketTheme\Toolbox\Event\Event
     * @return string Prints Auth-data
     */
    public function pluginAuthEndpoint(Event $e)
    {
        $uri = $this->grav['uri'];
        if (strpos($uri->path(), $this->config->get('plugins.aviary.authRoute') . '/' . $this->authRoute) === false) {
            return;
        }
        $authObj = $this->getAuth();
        echo json_encode($authObj);
        exit();
    }
}
