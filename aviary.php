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
     * [onAssetsInitialized description]
     */
    public function onAssetsInitialized()
    {

        $this->grav['assets']->addJs('user/plugins/aviary/js/dropzone.js', ['loading' => 'defer']);
        $this->grav['assets']->addJs('user/plugins/aviary/js/aviary.js', ['loading' => 'defer', 'priority' => 0]);
        $this->grav['assets']->addCss('user/plugins/aviary/css/aviary.css');
        if ($this->grav['uri']->scheme() == 'http://') {
            $this->grav['assets']->addJs('http://feather.aviary.com/imaging/v3/editor.js', ['loading' => 'defer']);
        } else {
            $this->grav['assets']->addJs('https://dme0ih8comzn4.cloudfront.net/imaging/v3/editor.js', ['loading' => 'defer']);
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
}
