<?php

namespace App\Query;

use Illuminate\Database\Eloquent\Builder;

class QueryBuilder extends Builder
{
    public function getAdmin() : self
    {
        return $this->where('role','admin');
    }
}