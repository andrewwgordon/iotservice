<?php namespace App\Models;

use CodeIgniter\Model;
use Exception;

/**
 * Class Measurement
 *
 * Model for Measurement data access.
 * Supports:
 *      getMeasurements - returns all data
 *      getMeasurements(id) - returns a single measurement
 *      addMeasuremment - creates a new measurement
 *
 * @package App\Models
 */
class Measurement extends Model
{
     /**
	 * Defines database table for model.
	 *
	 * @var string
	 */
    protected $table         = 'measurement_event';

     /**
	 * Defines database table primary key.
	 *
	 * @var string
	 */
    protected $primaryKey    = 'id';

     /**
	 * Defines array of allowable fields for insert
	 *
	 * @var array
	 */
    protected $allowedFields = ['site_name','date_time','name','data_value','description'];

    /**
	 * Return all Measurements or single Measurement
     * 
     * @param integer $id
     * 
     * @return array
	 */
    public function getMeasurements($id=false)
    {
        if ($id==false) {
            return $this->findAll();
        } 
        else 
        {
            return $this->asArray()
                        ->where(['id'=>$id])
                        ->first();
        }
    }

    /**
	 * Return Measurements filtered by:
     *  
     * @param string  $siteName
     * @param string  $name
     * @param integer $fromPOSIXTime
     * @param integer $toPOSIXTime
     * 
     * @return array
     * */
    public function filterMeasurements($siteName,$name,$fromPOSIXTime,$toPOSIXTime)
    {
        $sql = 'SELECT *
                FROM   measurement_event
                WHERE  date_time >= :fromDateTime:
                AND    date_time <= :toDateTime:
                AND    lower(site_name) like lower(:siteName:)
                AND    lower(name) like lower(:name:)';
        $query = $this->db->query($sql,[
            'fromDateTime' => $fromPOSIXTime,
            'toDateTime'   => $toPOSIXTime,
            'siteName'     => $siteName,
            'name'         => $name
        ]);
        if (!$query)
        {
            log_message('error',$this->db->getError());
            throw new Exception("Database Error");
        }
        return $query->getResultArray();
    }

    /**
	 * Create a new Measurement entry.
     * 
     * @param array $measurement
     * 
	 */
    public function addMeasurement($measurement)
    {
        $this->insert($measurement);
    }
}