<?php

namespace App\Providers;

use App\Repositories\Eloquent\PaymentTransactionRepository;
use App\Repositories\Eloquent\ShareFamilyRepository;
use App\Repositories\Interfaces\PaymentTransactionRepositoryInterface;
use App\Repositories\Interfaces\ShareFamilyRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Eloquent\HospitalRepository;
use App\Repositories\Interfaces\HospitalRepositoryInterface;
use App\Repositories\Eloquent\PeopleRepository;
use App\Repositories\Interfaces\PeopleRepositoryInterface;
use App\Repositories\Eloquent\FolderRepository;
use App\Repositories\Interfaces\FolderRepositoryInterface;
use App\Repositories\Eloquent\KeywordRepository;
use App\Repositories\Interfaces\KeywordRepositoryInterface;
use App\Repositories\Eloquent\FamilyRepository;
use App\Repositories\Interfaces\FamilyRepositoryInterface;
use App\Repositories\Eloquent\MediaKeywordRepository;
use App\Repositories\Interfaces\MediaKeywordRepositoryInterface;
use App\Repositories\Eloquent\RecordKeywordRepository;
use App\Repositories\Interfaces\RecordKeywordRepositoryInterface;
use App\Repositories\Eloquent\RecordTagRepository;
use App\Repositories\Interfaces\RecordTagRepositoryInterface;
use App\Repositories\Eloquent\ScheduleRepository;
use App\Repositories\Interfaces\ScheduleRepositoryInterface;
use App\Repositories\Eloquent\TagRepository;
use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Repositories\Eloquent\AccountRepository;
use App\Repositories\Interfaces\AccountRepositoryInterface;
use App\Repositories\Eloquent\ShareRepository;
use App\Repositories\Interfaces\ShareRepositoryInterface;
use App\Repositories\Eloquent\DoctorRepository;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use App\Repositories\Eloquent\RecordRepository;
use App\Repositories\Interfaces\RecordRepositoryInterface;
use App\Repositories\Eloquent\RecordItemRepository;
use App\Repositories\Interfaces\RecordItemRepositoryInterface;
use App\Repositories\Eloquent\MediaRepository;
use App\Repositories\Interfaces\MediaRepositoryInterface;
use App\Repositories\Eloquent\FavoriteRepository;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            EloquentRepositoryInterface::class,
            BaseRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            HospitalRepositoryInterface::class,
            HospitalRepository::class
        );

        $this->app->bind(
            PeopleRepositoryInterface::class,
            PeopleRepository::class
        );

        $this->app->bind(
            KeywordRepositoryInterface::class,
            KeywordRepository::class
        );

        $this->app->bind(
            MediaKeywordRepositoryInterface::class,
            MediaKeywordRepository::class
        );

        $this->app->bind(
            FolderRepositoryInterface::class,
            FolderRepository::class
        );

        $this->app->bind(
            ScheduleRepositoryInterface::class,
            ScheduleRepository::class
        );

        $this->app->bind(
            TagRepositoryInterface::class,
            TagRepository::class
        );

        $this->app->bind(
            AccountRepositoryInterface::class,
            AccountRepository::class
        );

        $this->app->bind(
            ShareRepositoryInterface::class,
            ShareRepository::class
        );

        $this->app->bind(
            DoctorRepositoryInterface::class,
            DoctorRepository::class
        );

        $this->app->bind(
            FamilyRepositoryInterface::class,
            FamilyRepository::class
        );

        $this->app->bind(
            RecordRepositoryInterface::class,
            RecordRepository::class
        );

        $this->app->bind(
            RecordItemRepositoryInterface::class,
            RecordItemRepository::class
        );

        $this->app->bind(
            MediaRepositoryInterface::class,
            MediaRepository::class
        );

        $this->app->bind(
            FavoriteRepositoryInterface::class,
            FavoriteRepository::class
        );
        $this->app->bind(
            RecordKeywordRepositoryInterface::class,
            RecordKeywordRepository::class
        );
        $this->app->bind(
            RecordTagRepositoryInterface::class,
            RecordTagRepository::class
        );
        $this->app->bind(
            ShareFamilyRepositoryInterface::class,
            ShareFamilyRepository::class
        );
        $this->app->bind(
            PaymentTransactionRepositoryInterface::class,
            PaymentTransactionRepository::class
        );

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
