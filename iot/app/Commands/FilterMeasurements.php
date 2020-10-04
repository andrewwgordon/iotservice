<?php namespace App\Commands;

use CodeIgniter\Validation\Exceptions\ValidationException;
use DateTime;

/**
 * Class FilterMeasuremwents
 *
 * API Command Class to validate API parameters
 *
 * @package App\Config
 */
class FilterMeasurements
{
    /**
	 * Name of site measurement is registered.
	 *
	 * @var string
	 */
    public $siteName;

    /**
	 * Id of measurement location
	 *
	 * @var integer
	 */
    public $measurementLocationId;

    /**
	 * DateTime from query in POSIX time.
	 *
	 * @var integer
	 */
    public $fromDatetime;

    /**
	 * DateTime to query in POSIX time.
	 *
	 * @var integer
	 */
    public $toDatetime;

    /**
	 * Constructor takes an array of query parameters
     * and performs validation.
     * 
     * @param array
     * 
     * throws ValidationException   
	 */
    public function __construct($params)
    {
         // Verify query parameters are valid
         $queryParams = array('site_name','measurement_location_id','from_datetime','to_datetime');
         $providedParams = array_keys($params);
         if (!array_intersect($queryParams,$providedParams))
         {
             throw new ValidationException('Invalid Parameter');
         }
         // If null, set strings to SQL wildcard.
         if (is_null($params['site_name']))
         {
             $this->siteName = '%';
         }
         else
         {
             $this->siteName=$params['site_name'];
         }
         if (is_null($params['measurement_location_id']))
         {
            throw new ValidationException('Measurement Location Id required');
         }
         else
         {
             $this->measurementLocationId = $this->validateIsInteger($params['measurement_location_id']);
         }
         // If no from_datetime supplied, set to 0.
         if (is_null($params['from_datetime']))
         {
             $this->fromDatetime = 0;
         }
         // else validate supplied value is a number.
         else
         {
             $this->fromDatetime=$this->validateIsInteger($params['from_datetime']);
         }
         // If no to_datetime supplied set to now.
         if (is_null($params['to_datetime']))
         {
             $now= new DateTime();            
             $this->toDatetime=$now->getTimestamp();
         }
         // else validate supplied value is number
         else
         {
             $this->toDatetime=$this->validateIsInteger($params['to_datetime']);
         }
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
}