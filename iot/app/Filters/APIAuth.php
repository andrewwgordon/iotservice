<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;
use Exception;

class APIAuth implements FilterInterface
{
    protected $response;
    protected $apiKeys;

    public function __construct()
    {
        $this->response = Services::response();
        // Load API keys.
        try
        {
            $this->apiKeys = file(APPPATH . '.passwd',FILE_IGNORE_NEW_LINES);
        }
        catch (Exception $e)
        {
            log_message('error','File access issue');
            $this->response->setStatusCode(500);
            return $this->response;
        }
    }

    public function before(RequestInterface $request)
    {
        $authenticated = false;
        try 
        {
            $apiValue=$request->getHeader('X-Api-Key')->getValue();
        }
        catch (Exception $e)
        {
            // Do nothing.
        }
        if (!is_null($apiValue))
        {   
           if (in_array($apiValue,$this->apiKeys)) 
           {
               $authenticated=true;
           }
        }        
        if (!$authenticated) 
        {
            $this->response->setBody('Unauthorized');
            $this->response->setStatusCode(401);
            return $this->response;
        }
    }   

    public function after(RequestInterface $request, ResponseInterface $response)
    {
        // Do nothing.
    }
}