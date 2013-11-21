<?php

namespace Yaf\Extras;

// Register custom renderer according to file's extension name
// use default Yaf Simple View as fallback if no renderer matched
class AdaptiveView implements \Yaf\View_Interface {
    private $path;
    private $renderers = array();
    private $data = array();

    public function __construct($path = null) {
        if ( isset($path) ) {
            $this->path = $path;
        } else {
            $config = \Yaf\Application::app()->getConfig();
            $this->path = $config['application.directory'] .'/views/';
        }
    }

    public function render($file, $data = null) {
        if ( is_array($data) ) {
            $this->data = array_merge($this->data, $data);
        }

        // render according to extname
        $extname = strtolower( pathinfo($file, PATHINFO_EXTENSION) );

        if ( isset($this->renderers[$extname]) ) {
            return $this->renderers[$extname]($file, $this->data);
        } else {
            return $this->renderDefault($file, $this->data);
        }
    }

    public function display($file, $data = null) {
        echo $this->render($file, $data);
    }

    public function assign($name, $value = null) {
        $this->data[$name] = $value;
    }

    public function getScriptPath() {
        return $this->path;
    }

    public function setScriptPath($path) {
        $this->path = $path;
    }


    // renderer registration
    public function on($extname, $renderer) {
        $extname = strtolower($extname);
        $this->renderers[$extname] = $renderer;
    }

    private function renderDefault($file, $data) {
        $view = new \Yaf\View\Simple($this->path);
        return $view->render($file, $data);
    }
}