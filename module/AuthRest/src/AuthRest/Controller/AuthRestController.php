<?php

namespace AuthRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Crypt\Password\Bcrypt;

class AuthRestController extends AbstractRestfulController
{
    protected $authservice;

    /**
     * Login api
     *
     * @return object
     */
    public function loginAction()
    {
        $response;
        $request = $this->getRequest();

            if($request->isPost()) {

                try {

                    $data = $request->getContent();
                    $json = json_decode($data, true);
        
                    $bcrypt = new Bcrypt(array(
                        'salt' => '$2y$05$KkFmCjGPJiC1jdt.SFcJ5uDXkF1yYCQFgiQIjjT6p.z7QIHyU1elW',
                        'cost' => 5
                    ));
                    $securePass = $bcrypt->create($json['password_enlasa']);
        
                    $this->getAuthService()
                            ->getAdapter()
                            ->setIdentity($json['user_enlasa'])
                            ->setCredential($securePass);
                    
                    $result = $this->getAuthService()->authenticate();
        
                    if($result->isValid()) {
        
                        // Data to user
                        $userInfo = $this->getAuthService()->getAdapter()->getResultRowObject(array(
                            'user_id',
                            'name',
                            'surname',
                            'lastname',
                            'email',
                            'app_access',
                            'canlogin'
                        ));
                        
                        // Response login success
                        $response = new JsonModel(array(
                            "response" => "Credenciales correctas",
                            "status" => "success",
                            "data" => json_decode(json_encode($userInfo), true),
                            "acces_enlasa" => $userInfo->app_access,
                            "id_user_enlasa" => $userInfo->user_id,
                            "token_enlasa" => bin2hex(openssl_random_pseudo_bytes(64))
                        ));
                    } else {
                        $response = new JsonModel(array(
                            "response" => "Credenciales invalidas",
                            "status" => "fail"
                        ));
                    }
        
                    return $response;

                } catch (Exception $e) {
                    echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                }

            }

        
        

        
        return new JsonModel(array('data' => "Login API"));
    }

    public function getList()
    {
        $response = $this->getResponseWithHeader()
                         ->setContent( __METHOD__.' get the list of data');
        return $response;
    }

    public function get($id)
    {
        $response = $this->getResponseWithHeader()
                         ->setContent( __METHOD__.' get current data with id =  '.$id);
        return $response;
    }

    public function create($data)
    {
        $response = $this->getResponseWithHeader()
                         ->setContent( __METHOD__.' create new item of data :
                                                    <b>'.$data['name'].'</b>');
        return $response;
    }

    public function update($id, $data)
    {
        $response = $this->getResponseWithHeader()
                        ->setContent(__METHOD__.' update current data with id =  '.$id.
                                            ' with data of name is '.$data['name']) ;
       return $response;
    }

    public function delete($id)
    {
        $response = $this->getResponseWithHeader()
                        ->setContent(__METHOD__.' delete current data with id =  '.$id) ;
        return $response;
    }

    // configure response
    public function getResponseWithHeader()
    {
        $response = $this->getResponse();
        $response->getHeaders()
                 //make can accessed by *   
                 ->addHeaderLine('Access-Control-Allow-Origin','*')
                 //set allow methods
                 ->addHeaderLine('Access-Control-Allow-Methods','POST PUT DELETE GET');
         
        return $response;
    }

    private function getAuthService()
	{
		if (! $this->authservice) {
			$this->authservice = $this->getServiceLocator()->get('AuthService');
		}
        
        return $this->authservice;
	}

}
