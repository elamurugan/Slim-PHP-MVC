<?phpclass App extends Controller{    public function _init()    {        $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);        removeMagicQuotes();        $this->configPrepare();        $this->__area = $this->getConfig('default/area');        $this->__module = $this->getConfig('default/frontend/module');        $this->__action = $this->getConfig('default/frontend/function');        $this->routing();    }    public function run()    {        global $__area, $app;        $this->_init();        $__area = $this->__area;        session_start();        session_name($__area);        $this->db = new Model();        $_controllerClass = ucfirst($this->__module) . 'Controller';        if (isset($_GET['url_request']) && !class_exists($_controllerClass)) {            $userExist = $this->db->checkUsernameExist($this->__module);            if ($userExist) {                $this->__module = 'users';                $_controllerClass = ucfirst($this->__module) . 'Controller';                $this->__action = "profileview";                $this->__resultData['current_user'] = $userExist;            } else {                $this->__module = 'error';                $_controllerClass = ucfirst($this->__module);            }        }        $app = new $_controllerClass();        $action = $this->__action . "Action";        if (!method_exists($app, $this->__action)) {            $this->__action = "error404";        }        $this->setBodyClass(strtolower($this->__module . "_" . $this->__action));        $modelClass = ucfirst($this->__module);        if ($modelClass != '' && class_exists($modelClass)) {            $this->model = new $modelClass;        }        $this->template = $app;        $this->_templateInit();        $app->$action();    }    public function routing()    {        if (isset($_GET['url_request'])) {            $urlParams = @explode("/", $_GET['url_request']);            if (isset($urlParams[0]) && $urlParams[0] != '') {                $this->__module = $urlParams[0];                array_shift($urlParams);                if ($this->__module == $this->adminRoutePath) {                    $this->__area = $this->getConfig('default/adminhtml/area');                    $this->__module = $this->getConfig('default/adminhtml/module');                    $this->__action = $this->getConfig('default/adminhtml/function');                    if (isset($urlParams[0]) && $urlParams[0] != '') {                        $this->__module = $urlParams[0];                        array_shift($urlParams);                        if (isset($urlParams[0]) && $urlParams[0] != '') {                            $this->__action = $urlParams[0];                            array_shift($urlParams);                        }                    }                } elseif (!$this->db->checkUrlRewriteAvailable($this->__module)) {                    if (isset($urlParams[0]) && $urlParams[0] != '') {                        $this->__action = $urlParams[0];                        array_shift($urlParams);                    } else {                        $this->__action = 'error404';                    }                }            }            foreach ($urlParams as $key => $val) {                if ($key % 2 == 0 && $val != '') {                    $this->__appParams['params'][$val] = @$urlParams[$key + 1];                }            }            if (isset($_REQUEST)) {                foreach ($_REQUEST as $key => $val) {                    $this->__appParams['params'][$key] = $val;                }            }        }    }}