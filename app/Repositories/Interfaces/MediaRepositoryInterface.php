<?php

namespace App\Repositories\Interfaces;
use App\Repositories\EloquentRepositoryInterface;

interface MediaRepositoryInterface extends EloquentRepositoryInterface
{
    public function importFile($file);
    public function getMedia($recordItemId);
    public function getMedias($recordId);
    public function getImages($recordId);
}