<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;

/**
 * Class AviaryPlugin
 * @package Grav\Plugin
 */
class AviaryPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onAssetsInitialized' => ['onAssetsInitialized', 0]
        ];
    }

    public function onAssetsInitialized() {

        $this->grav['assets']->addJs('user/plugins/aviary/js/dropzone.js', ['loading' => 'defer']);
        $this->grav['assets']->addJs('user/plugins/aviary/js/aviary.js', ['loading' => 'defer', 'priority' => 0]);
        $this->grav['assets']->addCss('user/plugins/aviary/css/aviary.css');
        if ($this->grav['uri']->scheme() == 'http://') {
            $this->grav['assets']->addJs('http://feather.aviary.com/imaging/v3/editor.js',['loading' => 'defer']);
        }else {
            $this->grav['assets']->addJs('https://dme0ih8comzn4.cloudfront.net/imaging/v3/editor.js', ['loading' => 'defer']);
        }

    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {

            $this->enable([
                'onAssetsInitialized' => ['onAssetsInitialized', 0]
            ]);

        }
    }


}
