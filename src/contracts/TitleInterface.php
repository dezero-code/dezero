<?php
/**
 * Title contract for models
 */

namespace dezero\contracts;

interface TitleInterface
{
    /**
     * Title used for this model
     */
    public function title() : string;
}
