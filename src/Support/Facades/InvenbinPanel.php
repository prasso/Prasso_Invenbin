<?php

namespace Faxt\Invenbin\Support\Facades;

use Illuminate\Support\Facades\Facade;

class InvenbinPanel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'invenbin';
    }
}
