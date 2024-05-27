<?php

namespace Faxt\Invenbin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ErpBaseModel extends Model
{
    use HasFactory;
    use HasTimestamps;
    use SoftDeletes;

    public $productListArray = [];

    /**
     * Flag to indicate whether the model should return single or multiple records format in API responses.
     * 
     * When set to true, the model will return detailed information suitable for a single record response.
     * When set to false, the model will return summary information suitable for multiple record responses.
     * 
     * Usage:
     * - Set $isSingleRecord to true when retrieving a single instance of the model for detailed representation.
     * - default: $isSingleRecord is false used when retrieving multiple instances of the model for summary representation.
     * 
     * Example:
     * // Retrieve a single instance of SalesInvoice with detailed information
     * $salesInvoice->isSingleRecord = true;
     * $singleRecordData = $salesInvoice->toArray();
     * 
     * // Retrieve multiple instances of SalesInvoice with summary information
     * $salesInvoice->isSingleRecord = false;
     * $multipleRecordData = $salesInvoice->toArray();
     * 
     * @var bool
     */
    public $isSingleRecord = false;

    protected $guarded = ['created_at', 'updated_at' ,'updated_by'];

    protected static function booted()
    {
       static::saving(function ($model) {
       
            if (Schema::connection("mysql")->hasColumn($model->getTable(),'updated_by'))
            {
                $user = \Auth::user();
                if (!$user){
                    throw ValidationException::withMessages([
                        'user' => [('Saving - failed. User is not authorized.')],
                    ]);
                }
                $model->updated_by = $user->id;
            }

            if (!$model->guid ) {
                if (Schema::connection("mysql")->hasColumn($model->getTable(),'guid'))
                {$model->guid = uuid_create();
                }
            }    
        });

        static::creating(function ($model) {
            $model->unguard(); 
             // Check if the model doesn't have an ID (assuming an auto-incrementing ID)
            // and if it has a "guid" field
            if (!$model->id ) {
                if (Schema::connection("mysql")->hasColumn($model->getTable(),'guid'))
                {$model->guid = uuid_create();
                }
            }           
            
        });
    }

    /**
     * Get the user that updated the model.
     *
     * @return BelongsTo|null
     */
    public function updatedBy(): ? BelongsTo
    {
        if (Schema::connection("mysql")->hasColumn($this->getTable(),'updated_by')) {
            return $this->belongsTo(User::class, 'updated_by');
        }

        return null;
    }
    /**
     * The attributes that should be mutated to dates using configured format.
     *
     * @var array
     */
    protected $dates = ['PDC_date','date','delivery_date','email_verified_at','two_factor_confirmed_at',
            'failed_at','last_used_at','expires_at','scheddule_date_time',
            'start_date','end_date'];

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = $this->formatDate($value);

    }   
    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] = $this->formatDate($value);
    }

    public function setPdcDateAttribute($value)
    {
        $this->attributes['PDC_date'] = $this->formatDate($value);
    }

    public function setDateAttribute($value)
        {
            $this->attributes['date'] = $this->formatDate($value);
        }
    
        public function setDeliveryDateAttribute($value)
        {
            Log::info('setting delivery date correct format');
            $this->attributes['delivery_date'] = $this->formatDate($value);
        }
    
        public function setEmailVerifiedAtAttribute($value)
        {
            $this->attributes['email_verified_at'] = $this->formatDate($value);
        }
    
        public function setTwoFactorConfirmedAtAttribute($value)
        {
            $this->attributes['two_factor_confirmed_at'] = $this->formatDate($value);
        }
    
        public function setFailedAtAttribute($value)
        {
            $this->attributes['failed_at'] = $this->formatDate($value);
        }
    
        public function setLastUsedAtAttribute($value)
        {
            $this->attributes['last_used_at'] = $this->formatDate($value);
        }
    
        public function setExpiresAtAttribute($value)
        {
            $this->attributes['expires_at'] = $this->formatDate($value);
        }
    
        public function setSchedduleDateTimeAttribute($value)
        {
            $this->attributes['scheddule_date_time'] = $this->formatDate($value);
        }
  
        public function setStartDateAttribute($value)
        {
            $this->attributes['start_date'] = $this->formatDate($value);
        }
        
        
        public function setEndDateAttribute($value)
        {
            $this->attributes['end_date'] = $this->formatDate($value);
        }
    
protected function formatDate($value)
{
     if (strpos($value, '/') == false) {
        return $value;
     }

    // Split the date into day, month, and year components
    $dateComponents = explode('/', $value);
    
    // Ensure that we have exactly three components
    if (count($dateComponents) !== 3) {
        return null; // or handle the error as needed
    }
    
    // Extract day, month, and year from the components using the configured date format
    $dateFormat = config('app.date_format');
    $formatComponents = explode('/', $dateFormat);
    
    if (count($formatComponents) !== 3) {
        return null; // or handle the error as needed
    }
    
    $dayIndex = array_search('d', $formatComponents);
    $monthIndex = array_search('m', $formatComponents);
    $yearIndex = array_search('Y', $formatComponents);
    
    $day = $dateComponents[$dayIndex];
    $month = $dateComponents[$monthIndex];
    $year = $dateComponents[$yearIndex];
    
    // Validate day, month, and year
    if (!checkdate($month, $day, $year)) {
        return null; // or handle the error as needed
    }
    
    // Create the date object
    $date = \DateTime::createFromFormat($dateFormat, "$day/$month/$year");
    // Format and return the date
    return $date->format('Y/m/d');
}
    

    /**
     * Define a mutator for all timestamp fields.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function serializeDate($value)
    {
        return $value->format(config('app.date_format'));
    }


    /**
     * Convert the model instance to an array.
     * 
     * @return array
     */
    public function toArray()
    {
        $toArray = parent::toArray();

        if (!$this->isSingleRecord) {
            $toArray = $this->summaryArray();
        }

        return $toArray;
    }

    /**
     * Generate array for summary representation of the model.
     * This method should be implemented in the child classes.
     * 
     * @return array
     */
    protected function summaryArray()
    {
        // Default implementation returns the parent toArray() method
        return parent::toArray();
    }
}
