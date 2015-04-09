<?php namespace DenverArt\ActivityFields\FormWidgets;

use Backend\Classes\FormWidgetBase;

class TimePicker extends FormWidgetBase
{
    public function widgetDetails()
    {
        return [
            'name'          => 'Time Picker',
            'description'   => 'form widget for time of day input fields'
        ];
    }

    public function render()
    {
        $data = $this->getLoadValue();

        $this->vars['name']     = $this->formField->getName();
        $this->vars['value']    = $data;

        return $this->makePartial('widget');
    }


    public function loadAssets()
    {
        $this->addJS('jquery.timepicker.js');
    }
}