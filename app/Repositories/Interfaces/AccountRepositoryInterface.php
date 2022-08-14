<?php

namespace App\Repositories\Interfaces;
use Illuminate\Http\Request;

use App\Repositories\EloquentRepositoryInterface;

interface AccountRepositoryInterface extends EloquentRepositoryInterface {
    public function Search(Request $request);
}