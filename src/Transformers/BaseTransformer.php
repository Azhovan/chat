<?php

namespace Chat\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class BaseTransformer extends TransformerAbstract
{

    /**
     * @param string $date
     * @return string
     */
    protected function formatDate(string $date): string
    {
        return Carbon::parse($date)->format('Y-m-d h:i:s');
    }

}