<?php namespace DenverArt\ActivityFields;

use Backend;
use Auth;
use Illuminate\Support\Facades\Event;
use DMA\Friends\Models\Activity as Activity;
use Rainlab\User\Models\User as User;
use DenverArt\ActivityFields\Models\ExtraFields as Fields;
use DMA\Friends\Components\ActivityCatalog as ActivityCatalog;
use Illuminate\Foundation\AliasLoader;

/**
 * Activity Fileds plugin information file
 *
 * @package DenverArt\ActivityFields
 * @author Matt Popke
 */
class Plugin extends \System\Classes\PluginBase
{
    /**
     * @var array plugin dependencies
     */
    public $require = ['DMA.Friends'];

    /**
     * Return information about this plugin
     * 
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Friends Activities Extensions',
            'description' => 'Adds additional fields to friends activities.',
            'author' => 'Denver Art Museum',
            'icon' => 'icon-database'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        // Extend Activity model to support extra fields & ratings
        Activity::extend(function($model) {
            $model->hasOne['activity_fields'] = ['DenverArt\ActivityFields\Models\ExtraFields'];
            $model->hasMany['ratings']        = ['DenverArt\ActivityFields\Models\Rating'];

            $model->addDynamicMethod('scopeNotIgnored', function($query, $user) {
                $query = $query->whereHas('ratings', function ($q) {
                    $q->where('user_id', $user->getKey())
                      ->where('rating', 0);
                });
            });
        });

        ActivityCatalog::extend(function($model) {
            $model->addDynamicMethod('getResults', function($filterstr = null) {
                $user = Auth::getUser();
                $perpage = 10;

                if ($filterstr && $filterstr != 'all') {
                    $filters = json_decode($filterstr, true);
                    if ($filters && is_array($filters['categories'])) {
                        $results = Activity::isActive()->byNotIgnored($user)->byCategory($filters['categories'])->paginate($perpage);
                    }
                    else {
                        $results = Activity::isActive()->byNotIgnored($user)->paginate($perpage);
                    }
                }
                else {
                    $results = Activity::isActive()->byNotIgnored($user)->paginate($perpage);
                }

                $this->page['activities'] = $results;
                $this->page['links'] = $results['links'];
            });
        });

        // Extend User model to support ratings
        User::extend(function ($model) {
            $model->hasMany['ratings'] = ['DenverArt\ActivityFields\Models\Rating'];
        });

        // Extend Activity fields
        $context = $this;
        Event::listen('backend.form.extendFields', function($widget) use ($context) {
            $context->extendedActivityFields($widget);
        });

        // Extend Activity admin listing table columns
        Event::listen('backend.list.extendColumns', function($widget) {
            if (!$widget->getController() instanceof \DMA\Friends\Controllers\Activities) return;

            $widget->addColumns([
                'duration' => [
                    'label'         => 'Duration',
                    'relation'      => 'activity_fields',
                    'sortable'      => true,
                    'select'        => '@duration',
                    'searchable'    => true
                ],
                'location' => [
                    'label'         => 'Location',
                    'relation'      => 'activity_fields',
                    'sortable'      => true,
                    'select'        => '@location',
                    'searchable'    => true
                ],
            ]);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function registerComponents()
    {
        return [
            'DenverArt\ActivityFields\Components\ActivityInteractions' => 'ActivityInteractions',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function registerFormWidgets()
    {
        return [
            'DenverArt\ActivityFields\FormWidgets\TimePicker' => [
                'label' => 'TimePicker',
                'code'  => 'timepicker',
            ],
        ];   
    }

    /**
     * Extend Activity fields when editing Friends Activities
     * @param  [type] $widget [description]
     */
    private function extendedActivityFields($widget)
    {
        if (!$widget->getController() instanceof \DMA\Friends\Controllers\Activities) return;
        if ($widget->getContext() != 'update') return;

        // Make sure the extended fields exist for this Activity
        if (!Fields::getFromActivity($widget->model)) return;

        $option_list = Fields::getOptions();

        $widget->addFields([
            'activity_fields[duration]' => [
                'label' => 'Estimated Duration',
                'tab'   => 'Configuration',
                'span'  => 'left',
                'type'  => 'dropdown',
                'options'   => $option_list['duration'],
            ],
            'activity_fields[engagement]' => [
                'label' => 'Engagement Level',
                'tab'   => 'Configuration',
                'span'  => 'left',
                'type'  => 'dropdown',
                'options'   => $option_list['engagement'],
            ],
            'activity_fields[location]' => [
                'label' => 'Location',
                'tab'   => 'Configuration',
                'span'  => 'left',
            ],
            'activity_fields[start_time]' => [
                'label' => 'Start Time',
                'tab'   => 'Configuration',
                'span'  => 'left',
                'type' => 'timepicker',
            ],
            'activity_fields[ticketed]' => [
                'label' => 'Not included in General Admission',
                'tab'   => 'Configuration',
                'span'  => 'left',
                'type'  => 'checkbox',
            ],
        ], 'secondary');
    }
}
