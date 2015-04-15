<?php namespace DenverArt\ActivityFields\FormWidgets;

use Backend\Classes\FormWidgetBase;

/**
 * TimePicker Form Widget
 *
 * This is used to control the input values on text inputs corresponding to
 * time column types in the ExtraFields model/table. 
 */
class TimePicker extends FormWidgetBase
{
    /**
     * {@inheritDoc}
     */
    public function widgetDetails()
    {
        return [
            'name'          => 'Time Picker',
            'description'   => 'form widget for time of day input fields'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->vars['name']     = $this->formField->getName();
        $this->vars['value']    = $this->getLoadValue();

        return $this->makePartial('widget');
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addJS('jquery.timepicker.js');
    }
}