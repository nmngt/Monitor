<?php

namespace App\Checks;

use NGT\Monitor\Checks\CheckBase;

class AppleTouchIcon extends CheckBase
{
    public $name = 'Apple-Touch-Icon';

    protected $xpath = '/html/head/link[@rel="apple-touch-icon"]';

    protected $errorMessage = 'No apple touch icon meta header was found.';
    protected $successMessage = 'Apple touch icon meta header was found';
    protected $returnValue = '';

    public function validate()
    {
        $icons = $this->getDomElementFromBodyByXpath($this->xpath);

        $foundItems = [];
        foreach ($icons as $icon) {
            $size = empty($icon['sizes']) ? 'default' : (string) $icon['sizes'][0];
            if (isset($foundItems[$size])) {
                $this->errorMessage = 'Multiple apple-touch-icon entries for size "'.$size.'" found.';

                return false;
            }
            $foundItems[$size] = $icon;
        }

        if (! empty($icons)) {
            $this->returnValue = sizeof($icons);
            return true;
        } else {
            return false;
        }
    }
}
