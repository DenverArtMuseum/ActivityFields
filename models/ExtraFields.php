<?php namespace DenverArt\ActivityFields\Models;

use Model;
use DMA\Friends\Models\Activity;

/**
 * ExtraFields Model
 *
 * This extends the Activity model in the DMA Friends plugin to add
 * additional information to each Activity
 */
class ExtraFields extends Model 
{
    // Define possible key => values of dropdown for Activity duration
    public static $durationOptions = [
        '10–15m'    => '10–15 minutes',
        '20–30m'    => '20–30 minutes',
        '45m'       => '45 minutes',
        '1h+'       => '1 hour or more',
    ];

    // Define database table for this model
    public $table = 'dam_activity_fields';

    // Don't need timestamps because the Activity will have them
    public $timestamps = false;

    // Everything is guarded, nothing is mass-editable
    protected $guarded = ['*'];
    protected $fillable = [];

    // Primary Key is the Activity ID
    protected $primaryKey = 'activity_id';

    // Each table entry belongs to an Activity
    public $belongsTo = [
        'user' => ['DMA\Friends\Models\Activity', 'foreignKey' => 'activity_id']
    ];

    /**
     * Automatically creates a activity_fields entry for an activity if there isn't already one defined
     * @param  DMA\Friends\Models\Activity $user
     * @return DenverArt\ActivityFields\Models\ExtraFields
     */
    public static function getFromActivity($activity = null)
    {
        // If no Activity to associate with, null out
        if (!$activity)
            return null;

        // If no activity_fields relation currently defined for this activity create one
        if (!$activity->activity_fields) {
            $meta = new static;            
            Activity::find($activity->getKey())->activity_fields()->save($meta);
            $activity = Activity::find($activity->getKey());
        }

        // Return appropriate activity_fields set
        return $activity->activity_fields;
    }

    /**
     * Accessor for Start Time, truncates the seconds off
     */
    public function getStartTimeAttribute($value) {
        return substr($value,0,5);
    }

    /**
     * Return options for fields so that admin forms can use them
     *
     * So far, duration is the only field that needs this, but easily expanded
     */
    public static function getOptions()
    {
        return [
            'duration' => self::$durationOptions,
        ];
    }

}