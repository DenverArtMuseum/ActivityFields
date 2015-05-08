<?php namespace DenverArt\ActivityFields\Components;

use Cms\Classes\ComponentBase;
use DB;
use Auth;
use Flash;
use Postman;
use DenverArt\ActivityFields\Models\Rating as Rating;
use DMA\Friends\Models\Activity as Activity;

class ActivityInteractions extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'          => 'Rate Activity AJAX handler',
            'description'   => 'Component for handling activity rating requests'
        ];
    }

    public function onRate()
    {
        $messages = '';

        // Get submit data
        $json = post('rating');
        if ($json && !empty($json)) {
            $data = json_decode($json, true);
        }
        else {
            // error
            return [];
        }

        // Get User
        $user = Auth::getUser();

        // verify activity from id
        $activity_id = $data['activity'];
        $activity = Activity::find($activity_id);
        if ( !$activity ) {
            // error
            //return [];
            $messages = 'Activity check broken. <br/>';
        }

        // Rate activity (call to Rating model)
        $rating = Rating::firstOrNew(array('activity_id' => $activity_id, 'user_id' => $user->id));
        $rating->rate(0);

        // construct flash message
        $messages .= 'Message';
        /*
        Postman::send('activity_rating_successful', function(NotificationMessage $notification) use ($user, $activity) {
            $nofication->to($user, $user->name);
            $notification->message('You rated activity: ' . $activity->title);
        }, ['flash']);
        */
        Flash::success('You rated activity: ' . $activity->title);
        
        // return flash message
        return [
            '#flashMessages' => $this->renderPartial('@flashMessages'),
        ];
    }
}
