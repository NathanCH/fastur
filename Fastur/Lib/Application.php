<?php
namespace Fastur\Lib;
use Fastur\Controller\Home;

class Application {

    private $url_controller = null,
            $url_action = null,
            $url_parameter_1 = null,
            $url_parameter_2 = null,
            $url_parameter_3 = null;

    /**
     *  "Start" the application:
     *  Analyze the URL elements and calls the according controller/method or the fallback
     *
     *  @author panique@web.de
     */
        public function __construct() {

            // Start session.
            session_start();

            // Create array with URL parts in $url
            $this->splitUrl();

            // Check for controller: does such a controller exist ?
            if (file_exists('./Fastur/Controller/' . $this->url_controller . '.php')) {

                // Append controller action to class location.
                $controller_action = $this->url_controller;
                $class_location = '\\Fastur\\Controller\\' . $controller_action;

                $this->url_controller = new $class_location;

                // Check for method: does such a method exist in the controller?
                if (method_exists($this->url_controller, $this->url_action)) {

                    // Call the method and pass the arguments to it.
                    if (isset($this->url_parameter_3)) {
                        // Will translate to something like $this->home->method($param_1, $param_2, $param_3);
                        $this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2, $this->url_parameter_3);
                    } elseif (isset($this->url_parameter_2)) {
                        // Will translate to something like $this->home->method($param_1, $param_2);
                        $this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2);
                    } elseif (isset($this->url_parameter_1)) {
                        // Will translate to something like $this->home->method($param_1);
                        $this->url_controller->{$this->url_action}($this->url_parameter_1);
                    } else {
                        // If no parameters given, just call the method without parameters, like $this->home->method();
                        $this->url_controller->{$this->url_action}();
                    }
                } else {
                    // Default/fallback: call the index() method of a selected controller.
                    $this->url_controller->index();
                }
            } else {

                $index = new Home();
                $index->index();
            }
        }

    /**
     *  Get and split the URL
     *
     *  @author panique@web.de
     */
        private function splitUrl() {

            if (isset($_GET['url'])) {

                // Split URL.
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);

                // Put URL parts into according properties.
                $this->url_controller = (isset($url[0]) ? $url[0] : null);
                $this->url_action = (isset($url[1]) ? str_replace('-', '_', $url[1]) : null);
                $this->url_parameter_1 = (isset($url[2]) ? $url[2] : null);
                $this->url_parameter_2 = (isset($url[3]) ? $url[3] : null);
                $this->url_parameter_3 = (isset($url[4]) ? $url[4] : null);
            }
        }

}
