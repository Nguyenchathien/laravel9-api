<?php

namespace App\Repositories\Interfaces;
use App\Repositories\EloquentRepositoryInterface;

interface UserRepositoryInterface extends EloquentRepositoryInterface 
{
    public function findByEmail($email);
    public function findByMail($email);
}