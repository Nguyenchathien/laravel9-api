<?php

use App\Http\Controllers\V1\Auth\AppleController;
use App\Http\Controllers\V1\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\HospitalController;
use App\Http\Controllers\V1\PeopleController;
use App\Http\Controllers\V1\FolderController;
use App\Http\Controllers\V1\KeywordController;
use App\Http\Controllers\V1\DoctorController;
use App\Http\Controllers\V1\FamilyController;
use App\Http\Controllers\V1\Auth\GoogleController;
use App\Http\Controllers\V1\Auth\ConfirmPasswordController;
use App\Http\Controllers\V1\ScheduleController;
use App\Http\Controllers\V1\TagController;
use App\Http\Controllers\V1\AccountController;
use App\Http\Controllers\V1\ShareController;
use App\Http\Controllers\V1\RecordController;
use App\Http\Controllers\V1\RecordTagController;
use App\Http\Controllers\V1\RecordKeywordController;
use App\Http\Controllers\V1\RecordItemController;
use App\Http\Controllers\V1\MediaController;
use App\Http\Controllers\V1\FavoriteController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'as' => 'v1.', 'namespace' => 'V1'], function () {
    Route::group(['middleware' => ['cors']], function () {

        Route::post('/google/create', [GoogleController::class, 'create'])->name('google.register.api');
        Route::post('/apple/create', [AppleController::class, 'create'])->name('apple.register.api');

    });
    Route::group(['middleware' => ['cors', 'json.response']], function () {
        Route::post('/upload-pdf', [FolderController::class, 'uploadPdf'])->name('upload.pdf');

        Route::post('/concat_audio', [FolderController::class, 'concat'])->name('audio.concat');

        // AUTH APIs
        Route::post('/login', [AuthController::class, 'login'])->name('login.api');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout.api');
        Route::post('/register', [AuthController::class, 'register'])->name('register.api');
        Route::post('/confirm-code', [AuthController::class, 'confirmCode'])->name('confirm.code.api');

        Route::post('/accounts/update-temail', [AccountController::class, 'updateTEmail'])->name('accounts.update.temail');

        // CHANGE PASSWORD
        Route::post('/change-password', [ConfirmPasswordController::class, 'index'])->name('change.password.api');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('change.forgotPassword.api');
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {

        // HOSPITAL APIs
        Route::get('/hospitals', [HospitalController::class, 'index'])->name('hospitals.api');
        Route::post('/hospitals', [HospitalController::class, 'store'])->name('hospitals.store.api');
        Route::get('/hospitals/{id}', [HospitalController::class, 'detail'])->name('hospitals.detail.api');
        Route::put('/hospitals/{id}', [HospitalController::class, 'update'])->name('hospitals.update.api');
        Route::delete('/hospitals/{id}', [HospitalController::class, 'delete'])->name('hospitals.delete.api');

        Route::post('/update-hospital', [HospitalController::class, 'updateHospital'])->name('hospitals.updateHospital.api');

        // SCHEDULE APIs
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.api');
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store.api');
        Route::get('/schedules/{id}', [ScheduleController::class, 'getScheduleDetail'])->name('schedules.detail.api');
        Route::put('/schedules/{id}', [ScheduleController::class, 'update'])->name('schedules.update.api');
        Route::delete('/schedules/{id}', [ScheduleController::class, 'delete'])->name('schedules.delete.api');
        Route::get('/schedules-list/{date}', [ScheduleController::class, 'getSchedule'])->name('schedules.getSchedule.api');
        Route::post('/update-schedule', [ScheduleController::class, 'updateSchedule'])->name('schedules.updateSchedule.api');

        // DOCTOR APIs
        Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.api');
        Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store.api');
        Route::get('/doctors/{id}', [DoctorController::class, 'detail'])->name('doctors.detail.api');
        Route::put('/doctors/{id}', [DoctorController::class, 'update'])->name('doctors.update.api');
        Route::delete('/doctors/{id}', [DoctorController::class, 'delete'])->name('doctors.delete.api');

        Route::post('/update-doctor', [DoctorController::class, 'updateDoctor'])->name('doctors.updateDoctor.api');

        // FAMILY APIs
        Route::get('/family', [FamilyController::class, 'index'])->name('family.api');
        Route::post('/family', [FamilyController::class, 'store'])->name('family.store.api');
        Route::get('/family/{id}', [FamilyController::class, 'detail'])->name('family.detail.api');
        Route::put('/family/{id}', [FamilyController::class, 'update'])->name('family.update.api');
        Route::delete('/family/{id}', [FamilyController::class, 'delete'])->name('family.delete.api');
        Route::post('/send-request', [FamilyController::class, 'sendRequest'])->name('family.sendRequest.api');
        Route::get('/received-request', [FamilyController::class, 'receivedRequest'])->name('family.receivedRequest.api');
        Route::get('/accepted-request', [FamilyController::class, 'acceptedRequest'])->name('family.acceptedRequest.api');
        Route::post('/send-request-to-join', [FamilyController::class, 'sendRequestToJoin'])->name('family.sendRequestToJoin.api');
        Route::get('/received-join-request', [FamilyController::class, 'receivedJoinRequest'])->name('family.receivedJoinRequest.api');
        Route::get('/accepted-join-request', [FamilyController::class, 'acceptedJoinRequest'])->name('family.acceptedJoinRequest.api');

        Route::post('/send-request-to-share', [ShareController::class, 'sendShareRequest'])->name('family.sendShareRequest.api');
        Route::get('/received-share-request', [ShareController::class, 'receivedShareRequest'])->name('family.receivedShareRequest.api');
        Route::get('/accepted-share-request', [ShareController::class, 'acceptedShareRequest'])->name('family.acceptedShareRequest.api');
        Route::get('/family/{id}/search', [FamilyController::class, 'searchRecordInFamily'])->name('family.searchRecordInFamily.api');
        Route::get('/list-sharing/{id}', [ShareController::class, 'getListSharing'])->name('family.getListSharing.api');
        Route::get('/list-requests', [FamilyController::class, 'listReceivedRequest'])->name('family.listReceivedRequest.api');
        Route::get('/list-requests-join', [FamilyController::class, 'listReceivedRequestJoin'])->name('family.listReceivedRequestJoin.api');
        Route::get('/accept-request/{id}', [FamilyController::class, 'acceptedRequestFamily'])->name('family.acceptedRequestFamily.api');
        Route::get('/denied-request/{id}', [FamilyController::class, 'deniedRequestFamily'])->name('family.deniedRequestFamily.api');
        Route::get('/remove-request/{id}', [FamilyController::class, 'remove'])->name('family.remove.api');
        Route::post('/send-join-request', [FamilyController::class, 'sendJoinRequest'])->name('family.sendJoinRequest.api');
        Route::get('/received-shared-record', [ShareController::class, 'receivedSharedRecord'])->name('family.receivedSharedRecord.api');

        Route::get('/list-family', [FamilyController::class, 'listFamily'])->name('family.listFamily.api');
        Route::get('/family/{id}/schedules', [ScheduleController::class, 'listScheduleFamily'])->name('family.listScheduleFamily.api');

        Route::post('/send-request-invite', [FamilyController::class, 'sendRequestInvite'])->name('family.sendRequestInvite.api');
        Route::get('/accept-request-invite/{id}', [FamilyController::class, 'acceptedRequestInvite'])->name('family.acceptedRequestInvite.api');

        Route::post('/send-request-join', [FamilyController::class, 'sendRequestJoin'])->name('family.sendRequestJoin.api');
        Route::get('/accept-request-join/{id}', [FamilyController::class, 'acceptedRequestJoin'])->name('family.acceptedRequestJoin.api');

        Route::get('/accept-request-record/{id}', [ShareController::class, 'acceptedShareRecord'])->name('family.acceptedShareRecord.api');
        // FOLDER APIs
        Route::get('/folders', [FolderController::class, 'index'])->name('folders.api');
        Route::post('/folders', [FolderController::class, 'store'])->name('folders.store.api');
        Route::get('/folders/{id}', [FolderController::class, 'getFolderDetail'])->name('folders.getFolderDetail.api');
        Route::put('/folders/{id}', [FolderController::class, 'update'])->name('folders.update.api');
        Route::delete('/folders/{id}', [FolderController::class, 'delete'])->name('folders.delete.api');
        Route::put('/folders/{id}/delete', [FolderController::class, 'deleteFolder'])->name('folders.deleteFolder.api');

        Route::get('/folders/{id}/records', [FolderController::class, 'getListRecordsInFolder'])->name('folders.getListRecordsInFolder.api');
        Route::post('/delete-folders', [FolderController::class, 'deleteMultiple'])->name('folders.deleteMultiple.api');
        Route::get('/folders/{id}/search', [FolderController::class, 'searchRecordsInFolder'])->name('folders.searchRecordsInFolder.api');

        // MEDICINE APIs
        Route::get('/medicines', [KeywordController::class, 'index'])->name('medicines.api');
        Route::post('/medicines', [KeywordController::class, 'store'])->name('medicines.store.api');
        Route::get('/medicines/{id}', [KeywordController::class, 'detail'])->name('medicines.detail.api');
        Route::put('/medicines/{id}', [KeywordController::class, 'update'])->name('medicines.update.api');
        Route::delete('/medicines/{id}', [KeywordController::class, 'delete'])->name('medicines.delete.api');

        Route::post('/update-medicine', [KeywordController::class, 'updateMedicine'])->name('medicines.updateMedicine.api');


        //TAGS APIs
        Route::get('/tags', [TagController::class, 'index'])->name('tags.api');
        Route::post('/tags', [TagController::class, 'store'])->name('tags.store.api');
        Route::get('/tags/{id}', [TagController::class, 'getTagDetail'])->name('tags.detail.api');
        Route::put('/tags/{id}', [TagController::class, 'update'])->name('tags.update.api');
        Route::delete('/tags/{id}', [TagController::class, 'delete'])->name('tags.delete.api');

        //ACCOUNT APIs

        Route::get('/accounts/search', [AccountController::class, 'searchAccount'])->name('accounts.searchAccount.api');
        Route::get('/accounts/{id}', [AccountController::class, 'detail'])->name('accounts.detail.api');
        Route::put('/accounts/{id}', [AccountController::class, 'update'])->name('accounts.update.api');
        Route::delete('/accounts/{id}', [AccountController::class, 'delete'])->name('accounts.delete.api');

        Route::post('/accounts/update-avatar', [AccountController::class, 'updateAvatar'])->name('accounts.updateAvatar.temail');

        //SHARES APIs
        Route::post('/shares', [ShareController::class, 'share'])->name('shares.share.api');
        Route::delete('/shares/{id}', [ShareController::class, 'delete'])->name('shares.delete.api');

        // KEYWORD
        Route::post('/create-keyword', [KeywordController::class, 'createKey'])->name('keyword.createKey.api');
        Route::put('/update-keyword/{id}', [KeywordController::class, 'updateKey'])->name('keyword.updateKey.api');
        Route::delete('/delete-keyword/{id}', [KeywordController::class, 'deleteKey'])->name('keyword.deleteKey.api');
        Route::get('/list-keyword', [KeywordController::class, 'listKey'])->name('keyword.listKey.api');

        //RECORD
        Route::post('/add-keywords', [RecordKeywordController::class, 'createRecordKeyWords'])->name('record-keyword.createRecordKeyWords.api');
        Route::post('/delete-keywords', [RecordKeywordController::class, 'deleteRecordKeyword'])->name('record-keyword.deleteRecordKeyword.api');
        Route::post('/control-keywords', [RecordKeywordController::class, 'controlRecordKeyword'])->name('record-keyword.controlRecordKeyword.api');
        Route::post('/control-keywords/update', [RecordKeywordController::class, 'controlRecordKeywordUpdate'])->name('record-keyword.controlRecordKeywordUpdate.api');
        Route::post('/import-audio', [RecordController::class, 'importAudio'])->name('records.importAudio.api');
        Route::get('/records/search', [RecordController::class, 'searchRecord'])->name('records.searchRecord.api');
        Route::get('/records', [RecordController::class, 'index'])->name('records.index.api');
        Route::post('/records', [RecordController::class, 'store'])->name('records.store.api');
        Route::get('/records/{id}', [RecordController::class, 'detail'])->name('records.detail.api');
        Route::post('/records/{id}', [RecordController::class, 'update'])->name('records.update.api');
        Route::delete('/records/{id}', [RecordController::class, 'delete'])->name('records.delete.api');
        Route::put('/save-record/{id}', [RecordController::class, 'save'])->name('records.save.api');
        Route::post('/import', [RecordController::class, 'import'])->name('records.import.api');
        Route::put('/hideandshow/{id}', [RecordController::class, 'hideAndShow'])->name('records.hideAndShow.api');
        Route::post('/create-record', [RecordController::class, 'createRecord'])->name('records.createRecord.api');
        Route::get('/list-records', [RecordController::class, 'list'])->name('records.list.api');
        Route::get('/list-records-no-paginate', [RecordController::class, 'listNoPagination'])->name('records.listNoPagination.api');
        Route::get('/list-records/{folder}', [RecordController::class, 'getListRecordByFolder'])->name('records.folder.list.api');
        Route::get('/records-duration', [RecordController::class, 'durationInAMonth'])->name('record.durationInAMonth.api');
        Route::get('/records-duration', [RecordController::class, 'durationInAMonth'])->name('record.durationInAMonth.api');
        Route::post('/import-audio', [RecordController::class, 'importAudio'])->name('records.importAudio.api');


        Route::get('/list-records-by-hospital/{hospital}', [RecordController::class, 'listRecordsByHospital'])->name('records.listRecordsByHospital.api');
        Route::get('/list-records-by-doctor/{doctor}', [RecordController::class, 'listRecordsByDoctor'])->name('records.listRecordsByDoctor.api');
        Route::get('/list-records-by-keyword/{keyword}', [RecordKeywordController::class, 'listRecordsByKeyword'])->name('records.listRecordsByKeyword.api');
        Route::get('/list-records-by-family/{family}', [RecordController::class, 'listRecordsByFamily'])->name('records.listRecordsByFamily.api');
        Route::get('/list-records-by-medicine/{medicine}', [RecordController::class, 'listRecordsByMedicine'])->name('records.listRecordsByMedicine.api');

        Route::get('/list-records-is-shared', [ShareController::class, 'getListSharedRecords'])->name('records.getListSharedRecords.api');


        Route::get('/record/search', [RecordController::class, 'searchRecords'])->name('records.searchRecords.api');

        //RECORD_ITEM APIs
        Route::get('/record-item', [RecordItemController::class, 'index'])->name('record-item.api');
        Route::post('/record-item', [RecordItemController::class, 'store'])->name('record-item.store.api');
        Route::get('/record-item/{id}', [RecordItemController::class, 'detail'])->name('record-item.detail.api');
        Route::post('/record-item/{id}', [RecordItemController::class, 'update'])->name('record-item.update.api');
        Route::delete('/record-item/{id}', [RecordItemController::class, 'delete'])->name('record-item.delete.api');
        Route::get('/list-items/{id}', [RecordItemController::class, 'getItem'])->name('record-item.getItem.api');
        Route::get('/list-items-visible/{id}', [RecordItemController::class, 'getItemVisible'])->name('record-item.getItemVisible.api');
        Route::post('/record-tag', [RecordTagController::class, 'store'])->name('record-tag.store.api');

        //MEDIA APIs
        Route::get('/medias', [MediaController::class, 'index'])->name('medias.api');
        Route::post('/medias', [MediaController::class, 'store'])->name('medias.store.api');
        Route::get('/medias/{id}', [MediaController::class, 'detail'])->name('medias.detail.api');
        Route::put('/medias/{id}', [MediaController::class, 'update'])->name('medias.update.api');
        Route::delete('/medias/{id}', [MediaController::class, 'delete'])->name('medias.delete.api');
        Route::post('/create-media', [MediaController::class, 'storeMedia'])->name('medias.storeMedia.api');
        Route::post('/record-image', [MediaController::class, 'storeImage'])->name('medias.storeImage.api');
        Route::post('/import-media', [MediaController::class, 'storeImportMedia'])->name('medias.storeImportMedia.api');

        //FAVORITE APIs
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.api');
        Route::post('/favorites', [FavoriteController::class, 'store'])->name('favorites.store.api');
        Route::delete('/favorites/{id}', [FavoriteController::class, 'delete'])->name('favorites.delete.api');
        Route::get('/liked/{id}', [FavoriteController::class, 'liked'])->name('favorites.liked.api');
        Route::post('/speech-to-text', [RecordController::class, 'speechToText']);

        //PAYMENT
        Route::post('/payment', [PaymentController::class, 'payment'])->name('payment.api');
        Route::get('/payment/info', [PaymentController::class, 'getPaymentInfo'])->name('payment.info.api');

    });
Route::post('/speech-to-text', [RecordController::class, 'speechToText']);
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
});
