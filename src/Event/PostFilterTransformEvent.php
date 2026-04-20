<?php

declare(strict_types=1);

namespace App\Event;

use App\Model\Search\SearchFilter;
use Symfony\Contracts\EventDispatcher\Event;

class PostFilterTransformEvent extends Event
{
    private SearchFilter $filter;

    public function __construct(SearchFilter $filter)
    {
        $this->filter = $filter;
    }

    public function getFilter(): SearchFilter
    {
        return $this->filter;
    }
}
