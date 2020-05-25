<?php namespace App\Controllers;

use App\Models\Measurement;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Validation\Exceptions\ValidationException;
use DateTime;
use Exception;

/**
 * Class Measurements
 *
 * REST API Controller for Measurement data. 
 * Supports:
 *      GET /measurements
 *      GET /measurements/{id}
 *      GET /measurements?site_name={}&name={}&from_datetime={}&to_datetime={}
 *      POST /measurements
 *
 * @package App\Controllers
 */
class Measurements extends ResourceController
{
    /**
	 * Set format of returned data to JSON.
	 *
	 * @var string
	 */
    protected $format = 'json';
    
    /**
	 * Instance of Measurement Model.
	 *
	 * @var App\Models\Measurement
	 */
    protected $model;

    /**
	 * Constructor.
	 */
    public function __construct()
    {
        // Create instance of Measurement Model.
        $this->model = new Measurement();
    }

    /**
	 * Responds to GET /measurements to return all 
     * Measurement Events data.
     * 
     * @return HTTPResponse
	 */
    private function getAllMeasurements()
    {
        try
        {
            $result = $this->model->getMeasurements();
            if (!empty($result))
            {
                return $this->respond($result);
            }
            else
            {
                return $this->failNotFound();
            }
        }
        catch (Exception $e)
        {
            return $this->failServerError();
        }
    }

    /**
	 * Gets URL Parameters
     * 
     * @return array
	 */
    private function getQueryParameters()
    {
        $url = parse_url($_SERVER['REQUEST_URI']);
        parse_str($url['query'], $params);
        return $params;
    }

    /**
	 * Validates variable is an integer.
     * 
     * @param string
     * 
     * @return integer
     * 
     * throws ValidationException   
	 */
    private function validateIsInteger($i)
    {
        if(ctype_digit($i))
        {
            return intval($i);
        }
        else
        {
            throw new ValidationException('Invalid Integer');
        }
    }

     /**
	 * Performs validation on query parameters.
     * 
     * @param array
     * 
     * @return array
	 */
    private function validateFilterParameters($params)
    {
        // Verify query parameters are valid
        $queryParams = array('site_name','name','from_datetime','to_datetime');
        $providedParams = array_keys($params);
        if (!array_intersect($queryParams,$providedParams))
        {
            throw new ValidationException('Invalid Parameter');
        }
        // If null, set strings to SQL wildcard.
        if (is_null($params['site_name']))
        {
            $params['site_name'] = '%';
        }
        if (is_null($params['name']))
        {
            $params['name'] = '%';
        }
        // If no from_datetime supplied, set to 0.
        if (is_null($params['from_datetime']))
        {
            $params['from_datetime'] = 0;
        }
        // else validate supplied value is a number.
        else
        {
            $params['from_datetime']=$this->validateIsInteger($params['from_datetime']);
        }
        // If no to_datetime supplied set to now.
        if (is_null($params['to_datetime']))
        {
            $now= new DateTime();            
            $params['to_datetime']=$now->getTimestamp();
        }
        // else validate supplied value is number
        else
        {
            $params['to_datetime']=$this->validateIsInteger($params['to_datetime']);
        }
        return $params;
    }

    /**
	 * Responds to GET /measurements/? to filtered 
     * Measurement Events data.
     * 
     * @return HTTPResponse
	 */
    private function getFilteredMeasurements()
    {
        // Get URL parameters.
        try 
        {
            $params = $this->validateFilterParameters($this->getQueryParameters());
        }
        catch (ValidationException $e)
        {
            return $this->failValidationError();
        }
        try 
        {            
            $result = $this->model->filterMeasurements(
                $params['site_name'],
                $params['name'],
                $params['from_datetime'],
                $params['to_datetime']
            );
            if (!empty($result))
            {
                return $this->respond($result);
            }
            else
            {
                return $this->failNotFound();
            }

        }
        catch (Exception $e)
        {
            return $this->failServerError();
        }
    }

     /**
	 * Responds to GET /measurements to return all 
     * Measurement Events data.
     * 
     * @return HTTPResponse
	 */
    public function index()
    {
        // If no query parameters supplied, get all measurements.
        if (empty($this->request->uri->getQuery()))
        {
            return $this->getAllMeasurements();
        }       
        // else run filtered query.
        else
        {
            return $this->getFilteredMeasurements();
        }
    }

    /**
	 * Responds to GET /measurements/{id} to return a single 
     * Measurement Event by Id.
     * 
     * @return HTTPResponse
	 */
    public function show($id = null)
    {
        try
        {
            return $this->respond($this->model->getMeasurements($id));
        }
        catch (Exception $e)
        {
            return $this->failServerError();
        }

    }

    /**
	 * Responds to POST /measurements to create a single 
     * Measurement Event.
     * 
     * @return HTTPResponse
	 */
    public function create()
    {
        try 
        {
            $this->model->insert($this->request->getJSON());
            return $this->respondCreated();
        } 
        catch (Exception $e) 
        {
            return $this->failServerError();
        }
    }
}