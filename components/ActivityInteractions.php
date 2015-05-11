<?php namespace DenverArt\ActivityFields\Components;

use Cms\Classes\ComponentBase;
use DB;
use Auth;
use Flash;
use Postman;
use DenverArt\ActivityFields\Models\Rating as Rating;
use DMA\Friends\Models\Activity as Activity;
use DMA\Friends\Activities\ActivityCode;

class ActivityInteractions extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'          => 'Rate Activity AJAX handler',
            'description'   => 'Component for handling activity rating requests'
        ];
    }

    protected function validateSubmission($json) {
        $err_str = 'Error in request: ';

        if (!$json || $json == '') {
            Flash::error($err_str . 'No form data submitted.');
        }
        else {
            $data = json_decode($json, true);

            if (!$data) {
                Flash::error($err_str . 'Data submitted is invalid JSON');
            }
            else {
                if (!isset($data['activity'])) {
                    Flash::error($err_str . 'No activity submitted.');
                }
                else {
                    $activity = Activity::find($data['activity']);

                    if (!$activity) {
                        Flash::error($err_str . 'Submitted activity does not exist.');
                    }
                    else {
                        return [
                            'activity' => $activity,
                            'data'     => $data,
                        ];
                    }
                }
            }
        }

        return false;
    }

    public function onRate()
    {
        // Get submit data
        $json = post('rating');

        // Make sure the submission we've recieved is a valid request
        $data = $this->validateSubmission($json);

        if ($data) { // if false, do nothing. Flash messages created in validate function
            if (!isset($data['data']['rating'])) {
                Flash::error('No rating submitted.');
            }
            else {
                // Get User
                $user = Auth::getUser();

                // Rate activity
                $rating_val = $data['data']['rating'];
                $rating = Rating::firstOrCreate(array('activity_id' => $data['activity']->id, 'user_id' => $user->id));
                $rating->rate($rating_val);
                $user->forceSave();

                // construct flash message
                if ($rating_val > 0) {
                    $message = '<strong>You rated activity:</strong> ' . $data['activity']->title;            
                }
                else {
                    $message = 'Rover has buried this activity. You can still find it in the catalog of activities.<br />'
                        . '<strong>You removed:</strong> ' . $data['activity']->title;
                }
                Flash::success($message);
            }
        }

        // return flash message
        return [
            '#flashMessages' => $this->renderPartial('@flashMessages'),
        ];
    }

    public function onComplete()
    {
        // Get submit data
        $json = post('completion');

        // Validate json submission
        $data = $this->validateSubmission($json);

        if ($data) {
            $params['code'] = $data['activity']->activity_code;
            $user = Auth::getUser();

            $activity = ActivityCode::process($user, $params);

            Flash::success('Completed ' . $activity->title);
        }

        // return flash message
        return [
            '#flashMessages' => $this->renderPartial('@flashMessages'),
        ];
    }

}
