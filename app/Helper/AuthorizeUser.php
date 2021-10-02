<?php

namespace App\Helper;

Trait AuthorizeUser {

    public function authorizeUser(string $key): bool
    {
        return \Gate::allows($key) ? true : false;
    }
}