<?php namespace DenverArt\ActivityFields\Models;

use Model;
use DMA\Friends\Models\Activity as Activity;
use Rainlab\User\Models\User as User;

/**
 * 
 */
class Rating extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'dam_rating';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['activity_id','user_id'];

    public $rules = [];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'activity'  => ['DMA\Friends\Models\Activity'],
        'user'      => ['Rainlab\User\Models\User'],
    ];

    public function rate($rating)
    {
        $this->rating = $rating;
        $user = User::find($this->user_id);
        $user->touch();
    }

}
