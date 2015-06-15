<?php namespace DenverArt\ActivityFields\Components;

use Cms\Classes\ComponentBase;
use DMA\Friends\Models\Activity;
use DenverArt\ActivityFields\Classes\ExtendedActivity;
use DMA\Friends\Models\Category;
use RainLab\User\Models\User;
use Auth;
use DB;

class Explore extends ComponentBase
{
    /**
     * {@inheritDoc}
     */
    public function componentDetails()
    {
        return [
            'name' => 'Explore Component',
            'description' => 'Allows the user to explore a full list of available activities',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function defineProperties()
    {
        return [
            'containerclass' => [
                'title'             => 'Container Class',
                'description'       => 'Optional CSS class for Activity List container div',
                'type'              => 'string',
                'default'           => '',
                'validationPattern' => '^[a-zA-Z_- ]*$',
                'validationMessage' => 'Class must be a valid CSS class identifier.',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function onRun()
    {
        $this->getResults();
        $this->page['containerclass'] = $this->property('containerclass');
    }

    /**
     * {@inheritDoc}
     */
    public function onUpdate()
    {
        $filters = post('filters');
        $this->getResults($filters);

        // Render only the activitylist partial and not the full default partial
        // Avoids AJAX producing a load of nested div#activity-catalog elements
        return [
            '#activity-catalog' => $this->renderPartial('@activitylist'),
        ];
    }

    /**
     * Produce a collection of Activities based on recommendations and filters
     */
    private function getResults($filterstr = null)
    {
        $user = Auth::getUser();
        $perpage = 12;
        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $restrictions = [];

        if ($filterstr && $filterstr != 'all') {
            $filters = json_decode($filterstr, true);
            if ($filters && is_array($filters['categories'])) {
                $results = Activity::isActive()->notIgnored($user)->notComplete($user)->startedNotExpired()->byCategory($filters['categories'])->paginate($perpage);
            }
            else {
                $results = Activity::isActive()->notIgnored($user)->notComplete($user)->startedNotExpired()->paginate($perpage);
            }
        }
        else {
            $results = Activity::isActive()->notIgnored($user)->notComplete($user)->startedNotExpired()->paginate($perpage);
        }

        foreach ($results as $index => $result) {
            $string = '';
            $type = $result->time_restriction;
            $time = $result->time_restriction_data;
            if ($type == 1) {
                $days = [];
                foreach ($time['days'] as $key => $value) {
                    if ($value > 0) {
                        $days[] .= $dayNames[$key-1];
                    }
                }

                $len = count($days);

                switch($len) {
                    case 0:
                        break;
                    case 1:
                        $string = $days[0];
                        break;

                    case 2:
                        $string = implode(' and ', $days);
                        break;

                    default:
                        $last = array_pop($days);
                        $string = implode(', ', $days) . ' and ' . $last;
                }

                $string .= ' at ';
                $string .= strtolower($time['start_time'] . '–' . $time['end_time']);
            }
            elseif ($type == 2) {
                $start = date('M j, Y', strtotime($result->date_begin));
                $end = date('M j, Y', strtotime($result->date_end));

                if ($start == $end) {
                    $start = date('M j, Y g:i a', strtotime($result->date_begin));
                    $end = date('M j, Y g:i a', strtotime($result->date_end));
                    if ($start == $end) {
                        $string = $start;
                    }
                    else {
                        $end = date('g:i a', strtotime($result->date_end));
                    }
                }
                if (!$string) {
                    $string = $start . ' to ' . $end;
                }
            }

            $restrictions[$index] = $string;
        }

        $this->page['activities'] = $results;
        $this->page['restrictions'] = $restrictions;
        $this->page['hasLinks'] = $results->hasMorePages() || $results->currentPage() > 1;
        $this->page['links'] = $results->render();
    }
}