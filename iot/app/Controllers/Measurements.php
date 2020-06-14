<?php namespace App\Controllers;

use App\Models\Measurement;
use App\Commands\FilterMeasurements;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Validation\Exceptions\ValidationException;
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
     * @return CodeIgniter\HTTP\Response
	 */
    private function getAll()
    {
        try
        {
            $result = $this->model->findAllOrById();
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
	 * Responds to GET /measurements/? to filtered 
     * Measurement Events data.
     * 
     * @return CodeIgniter\HTTP\Response
	 */
    private function getFiltered()
    {
        // Get URL parameters.
        try 
        {
            $params = $this->getQueryParameters();
            $filterMeasurements = new FilterMeasurements($params);
        }
        catch (ValidationException $e)
        {
            return $this->failValidationError();
        }
        try 
        {            
            $result = $this->model->findFiltered(
                $filterMeasurements->siteName,
                $filterMeasurements->name,
                $filterMeasurements->fromDatetime,
                $filterMeasurements->toDatetime
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
     * @return CodeIgniter\HTTP\Response
	 */
    public function index()
    {
        // If no query parameters supplied, get all measurements.
        if (empty($this->request->uri->getQuery()))
        {
            return $this->getAll();
        }       
        // else run filtered query.
        else
        {
            return $this->getFiltered();
        }
    }

    /**
	 * Responds to GET /measurements/{id} to return a single 
     * Measurement Event by Id.
     * 
     * @return CodeIgniter\HTTP\Response
	 */
    public function show($id = null)
    {
        try
        {
            return $this->respond($this->model->findAllOrById($id));
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
     * @return CodeIgniter\HTTP\Response
	 */
    public function create()
    {
        try 
        {
            $this->model->add($this->request->getJSON());
            return $this->respondCreated();
        } 
        catch (Exception $e) 
        {
            return $this->failServerError();
        }
    }
}