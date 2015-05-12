<?php namespace DenverArt\ActivityFields\Components;

use Cms\Classes\ComponentBase;
use DMA\Friends\Models\Activity;
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

        if ($filterstr && $filterstr != 'all') {
            $filters = json_decode($filterstr, true);
            if ($filters && is_array($filters['categories'])) {
                $results = Activity::isActive()->NotIgnored($user)->byCategory($filters['categories'])->paginate($perpage);
            }
            else {
                $results = Activity::isActive()->NotIgnored($user)->paginate($perpage);
            }
        }
        else {
            $results = Activity::isActive()->NotIgnored($user)->paginate($perpage);
        }

        $this->page['activities'] = $results;
        $this->page['hasLinks'] = $results->hasMorePages() || $results->currentPage() > 1;
        $this->page['links'] = $results->render();
    }
}