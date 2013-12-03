<?php
/*******************
 * PostapiController class file to Access the Posts
 * @uses Controller
 * @author Jay <jay@helix>
 * @Date <22/08/2013>
 */

class PostapiController extends Controller
{
    
    /*******************
     * API Key
     * Used for Authentication
     */
    Const POSTAPI_KEY = 'TESTHELIX';

    private $format = 'json';
 
    /*******************
     * filters
     * @return array action filters
     */
    public function filters()
    {
            return array();
    }
 
    /******************
     * actionIndex
     */
    public function actionIndex()
    {
        $head='Post API Controller';
        $message='API is running successfully ...';
        $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">                    
                </head>
                <body>
                    <h3>'.$head.'</h3>
                    <p>' . $message . '</p>
                </body>
            </html>';

        echo $body;
    } 
    
    /******************
     * actionList
     * Display All the Post Present in the Table
     */
    public function actionList()
    {
        $this->_userAutehntication();
        switch($_GET['model'])
        {
            case 'posts': 
                $models = Post::model()->findAll();
                break;  
            default: 
                $this->_sendResponse(501, sprintf('Error: Mode <b>list</b> is not implemented for model <b>%s</b>',$_GET['model']) );
                exit; 
        }
        if(is_null($models)) {
            $this->_sendResponse(200, sprintf('No Post found for model <b>%s</b>', $_GET['model']) );
        } else {
            $rows = array();
            foreach($models as $model)
                $rows[] = $model->attributes;

            $this->_sendResponse(200, CJSON::encode($rows));
        }
    } 
    
    
    /******************
     * Shows a single item
     * actionView
     */
    public function actionView()
    {
        $this->_userAutehntication();
        /*
        *  Check if id was submitted via GET method        
        */
        if(!isset($_GET['id']))
            $this->_sendResponse(500, 'Error: Parameter <b>id</b> is missing' );

        switch($_GET['model'])
        {
            /* Find respective model    */
            case 'posts': 
                $model = Post::model()->findByPk($_GET['id']);
                break; 
            default:  
                $this->_sendResponse(501, sprintf('Mode <b>view</b> is not implemented for model <b>%s</b>',$_GET['model']) );
                exit;  
        }
        if(is_null($model)) {
            $this->_sendResponse(404, 'No Post found with id '.$_GET['id']);
        } else {
            $this->_sendResponse(200, $this->_getEncodedData($_GET['model'], $model->attributes));
        }
    } 
    
     
    /*******************
     * actionCreate
     * Creates a new item
     */
    public function actionCreate()
    {
        $this->_userAutehntication();

        switch($_GET['model'])
        {
            /*
             * Get an instance of the respective model
             */
            case 'posts':   
                $model = new Post;                    
                break;   
            default:   
                $this->_sendResponse(501, sprintf('Mode <b>create</b> is not implemented for model <b>%s</b>',$_GET['model']) );
                exit;   
        }
            /*
             * Assign POST values to attributes             
             */ 
        foreach($_POST as $var=>$value) {
            /*
             * Check if the model have this attribute
             */ 
            if($model->hasAttribute($var)) {
                $model->$var = $value;
            } else {
                /* Error : model don't have this attribute */
                $this->_sendResponse(500, sprintf('Parameter <b>%s</b> is not allowed for model <b>%s</b>', $var, $_GET['model']) );
            }
        }
        /*
         * save the model
         */ 
        if($model->save()) {            
            $this->_sendResponse(200, $this->_getEncodedData($_GET['model'], $model->attributes) );
        } else {
            /*
             * Errors occurred
             */
            $message = "<h1>Error</h1>";
            $message .= sprintf("Couldn't create model <b>%s</b>", $_GET['model']);
            $message .= "<ul>";
            foreach($model->errors as $attribute=>$attribute_errors) {
                $message .= "<li>Attribute: $attribute</li>";
                $message .= "<ul>";
                foreach($attribute_errors as $attr_error) {
                    $message .= "<li>$attr_error</li>";
                }        
                $message .= "</ul>";
            }
            $message .= "</ul>";
            $this->_sendResponse(500, $message );
        }

        var_dump($_REQUEST);
    }       
      
    /*******************
     * actionUpdate
     * Update a single item
     * 
     */
    public function actionUpdate()
    {
        $this->_userAutehntication();

        /*
         * Receive all the PUT parameters
         */
        parse_str(file_get_contents('php://input'), $put_params);

        switch($_GET['model'])
        {
            /* Find respective model */
            case 'posts':   
                $model = Post::model()->findByPk($_GET['id']);                    
                break;   
            default:   
                $this->_sendResponse(501, sprintf('Error: Mode <b>update</b> is not implemented for model <b>%s</b>',$_GET['model']) );
                exit;   
        }
        if(is_null($model))
            $this->_sendResponse(400, sprintf("Error: Didn't find any model <b>%s</b> with ID <b>%s</b>.",$_GET['model'], $_GET['id']) );
        
        /*
         * assign PUT parameters to attributes
         */ 
        foreach($put_params as $var=>$value) {
            /*
             * Check if the model have this attribute
             */ 
            if($model->hasAttribute($var)) {
                $model->$var = $value;
            } else {
                /* Error : model don't have this attribute */
                $this->_sendResponse(500, sprintf('Parameter <b>%s</b> is not allowed for model <b>%s</b>', $var, $_GET['model']) );
            }
        }
        /*
         *save the model
         */
        if($model->save()) {
            $this->_sendResponse(200, sprintf('The model <b>%s</b> with id <b>%s</b> has been updated.', $_GET['model'], $_GET['id']) );
        } else {
            $message = "<h1>Error</h1>";
            $message .= sprintf("Couldn't update model <b>%s</b>", $_GET['model']);
            $message .= "<ul>";
            foreach($model->errors as $attribute=>$attribute_errors) {
                $message .= "<li>Attribute: $attribute</li>";
                $message .= "<ul>";
                foreach($attribute_errors as $attr_error) {
                    $message .= "<li>$attr_error</li>";
                }        
                $message .= "</ul>";
            }
            $message .= "</ul>";
            $this->_sendResponse(500, $message );
        }
    }   
      
    /*******************
     * actionDelete
     * Deletes a single item
     */
    public function actionDelete()
    {
        $this->_userAutehntication();

        switch($_GET['model'])
        {
            /* Load the respective model */
            case 'posts':   
                $model = Post::model()->findByPk($_GET['id']);                    
                break;   
            default:   
                $this->_sendResponse(501, sprintf('Error: Mode <b>delete</b> is not implemented for model <b>%s</b>',$_GET['model']) );
                exit;   
        }
        /* Find the model */
        if(is_null($model)) {
            // Error : model not found
            $this->_sendResponse(400, sprintf("Error: Didn't find any model <b>%s</b> with ID <b>%s</b>.",$_GET['model'], $_GET['id']) );
        }

        /* Delete the model */
        $response = $model->delete();
        if($response>0)
            $this->_sendResponse(200, sprintf("Model <b>%s</b> with ID <b>%s</b> has been deleted.",$_GET['model'], $_GET['id']) );
        else
            $this->_sendResponse(500, sprintf("Error: Couldn't delete model <b>%s</b> with ID <b>%s</b>.",$_GET['model'], $_GET['id']) );
    }
    
    /*******************
     * ****** End Actions ********
     *
     */
      
      
    /*******************
     * Start of the Private Methodes
     *
     */
      
    /*******************
     * _sendResponse
     * Sends the API response 
     * 
     * @param int $statusCode 
     * @param string $body 
     * @param string $content_type 
     */
    private function _sendResponse($statusCode = 200, $body = '', $content_type = 'text/html')
    {
        $statusCode_header = 'HTTP/1.1 ' . $statusCode . ' ' . $this->_getStatusCodeMessage($statusCode);
        /* set the status */
        header($statusCode_header);
        /* set the content type */
        header('Content-type: ' . $content_type);
        
        if($body != '')
        {
            /* send the body */
            echo $body;
            exit;
        }
        /* Create the body if it is empty */
        else
        {            
            $message = '';
            switch($statusCode)
            {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }

            /* Get servers signature  */
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
            
            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
                        <html>
                            <head>
                                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                                <title>' . $statusCode . ' ' . $this->_getStatusCodeMessage($statusCode) . '</title>
                            </head>
                            <body>
                                <h1>' . $this->_getStatusCodeMessage($statusCode) . '</h1>
                                <p>' . $message . '</p>
                                <hr />
                                <address>' . $signature . '</address>
                            </body>
                        </html>';

            echo $body;
            exit;
        }
    }            
    
    /*******************
     * _getStatusCodeMessage
     * Gets the message for a status code
     * 
     * @param mixed $statusCode
     * The list of Status codes can be found in the API documentation
     */
    private function _getStatusCodeMessage($statusCode)
    {
        $codes = Array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        return (isset($codes[$statusCode])) ? $codes[$statusCode] : '';
    }
    
    /*******************
     *_userAutehntication
     * Checks if a request is authorized
     */
    private function _userAutehntication()
    {
        /* Check if the USERNAME and PASSWORD HTTP headers is set*/
        if(!(isset($_SERVER['HTTP_X_'.self::POSTAPI_KEY.'_USERNAME']) and isset($_SERVER['HTTP_X_'.self::POSTAPI_KEY.'_PASSWORD']))) {
            /* Error: Unauthorized User */
            $this->_sendResponse(401);
        }
        $username = $_SERVER['HTTP_X_'.self::POSTAPI_KEY.'_USERNAME'];
        $password = $_SERVER['HTTP_X_'.self::POSTAPI_KEY.'_PASSWORD'];        
        
        /* Find the user */
        $user=User::model()->find('LOWER(username)=?',array(strtolower($username)));
        if($user===null) {
            /* Error: Unauthorized User, username doesn't exist */
            $this->_sendResponse(401, 'Error: Not a valid User');
        } else if(!$user->validatePassword($password)) {
            /* Error: Unauthorized User, Wrong Password */
            $this->_sendResponse(401, 'Error: Password is Wrong');
        }
    }   
      
    /*******************
     *_getEncodedData
     * Returns the json or xml encoded array
     * 
     * @param mixed $model 
     * @param mixed $array Data to be encoded
     */
    private function _getEncodedData($model, $array)
    {
        if(isset($_GET['format']))
            $this->format = $_GET['format'];

        if($this->format=='json')
        {
            return CJSON::encode($array);
        }
        elseif($this->format=='xml')
        {
            $data = '<?xml version="1.0">';
            $data .= "\n<$model>\n";
            foreach($array as $key=>$value)
                $data .= "    <$key>".utf8_encode($value)."</$key>\n"; 
            $data .= '</'.$model.'>';
            return $data;
        }
        else
        {
            return;
        }
    } 
}

?>
