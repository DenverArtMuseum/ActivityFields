<?php namespace DenverArt\ActivityFields\Components;

use Cms\Classes\ComponentBase;

class PopupHelp extends ComponentBase
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
            'topic' => [
                'title'             => 'Help Topic',
                'description'       => 'name of the partial used for this help bubble',
                'type'              => 'string',
                'default'           => '@help',
            ],
        ];
    }

    public function onRun()
    {
        // Inject javascript
        $this->addJS('components/popuphelp/assets/js/popuphelp.js');

        $this->page['partial'] = $this->property('topic');
    }

    public function onHelp() {
        return [
            '#popup-help-message' => $this->renderPartial($this->property('topic')),
        ];
    }

}
