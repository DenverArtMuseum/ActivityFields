<?php namespace DenverArt\ActivityFields\Models;

use Model;
use DMA\Friends\Models\Activity;

/**
 * ExtraFields Model
 */
class ExtraFields extends Model 
{
    public static $durationOptions = [
        '10–15m',
        '20–30m',
        '45m',
        '1h+',
    ];

    public $table = 'dam_activity_fields';

    public $timestamps = false;

    protected $guarded = ['*'];
    protected $fillable = [];
    protected $primaryKey = 'activity_id';

    public $belongsTo = [
        'user' => ['DMA\Friends\Models\Activity', 'foreignKey' => 'activity_id']
    ];

    /**
     * Automatically creates a activity_fields entry for an acitivity if there isn't already one defined
     * @param  DMA\Friends\Models\Actibity $user
     * @return DenverArt\ActivityFields\Models\ExtraFields
     */
    public static function getFromActivity($activity = null)
    {
        if (!$activity)
            return null;

        if (!$activity->activity_fields) {

            $meta = new static;            
            Activity::find($activity->getKey())->activity_fields()->save($meta);
            $activity = Activity::find($activity->getKey());
            
        }

        return $activity->activity_fields;
    }

    public static function getOptions()
    {
        return [
            'duration' => self::$durationOptions,
        ];
    }

}