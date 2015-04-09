<?php namespace DenverArt\ActivityFields;

use Backend;
use Illuminate\Support\Facades\Event;
use DMA\Friends\Models\Activity as Activity;
use DenverArt\ActivityFields\Models\ExtraFields as Fields;
use Illuminate\Foundation\AliasLoader;




class Plugin extends \System\Classes\PluginBase
{
    public $require = ['DMA.Friends'];

    public function pluginDetails()
    {
        return [
            'name' => 'Friends Activities Extensions',
            'description' => 'Adds some new fields to friends activities.',
            'author' => 'Denver Art Museum',
            'icon' => 'icon-database'
        ];
    }

    public function boot()
    {
        // Extend Activity model to support extra fields
        Activity::extend(function($model) {
            $model->hasOne['activity_fields'] = ['DenverArt\ActivityFields\Models\ExtraFields'];
        });

        // Extend Activity fields
        $context = $this;
        Event::listen('backend.form.extendFields', function($widget) use ($context) {
            $context->extendedActivityFields($widget);
        });

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

    private function extendedActivityFields($widget)
    {
        if (!$widget->getController() instanceof \DMA\Friends\Controllers\Activities) return;
        if ($widget->getContext() != 'update') return;

        // Make sure the extended fields exist for this Activity
        if (!Fields::getFromActivity($widget->model)) return;

        $widget->addFields([
            'activity_fields[duration]' => [
                'label' => 'Estimated Duration',
                'tab'   => 'Configuration',
                'span'  => 'left',
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

    public function registerFormWidgets()
    {
        return [
            'DenverArt\ActivityFields\FormWidgets\TimePicker' => [
                'label' => 'TimePicker',
                'alias' => 'timepicker',
            ],
        ];   
    }
    /*
    public function registerComponents()
    {
        return [
            '' => ''
        ];
    }
    */
}