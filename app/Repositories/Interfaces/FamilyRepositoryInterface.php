<?php

namespace App\Repositories\Interfaces;
use App\Repositories\EloquentRepositoryInterface;

interface FamilyRepositoryInterface extends EloquentRepositoryInterface
{
    public function accepted($id, array $data);

    public function getListRequests($email);

    public function getListRequestsJoin($email);

    public function sendAgain($id, $email);

    public function removeFamily($id, $email);

    public function addAgain($id, $email);

    public function findFamily($userId, $email);

    public function findFamilyValid($userId, $email);
    // public function getAll();

    // public function getDetail($id);

    // public function create(array $data);

    // public function update($id, array $data);
    
    // public function delete($id);
}