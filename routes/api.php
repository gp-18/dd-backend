<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ExcelUserOpsController;
use App\Http\Controllers\UserIncentiveController;
use App\Http\Controllers\MailTemplateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// PUBLIC API ROUTES
Route::controller(AuthenticationController::class)->prefix('user')->group(function () {

    // ROUTE FOR USER RESGISTRATION
    Route::post('register', 'UserRegister');

    // ROUTE FOR USER LOGIN
    Route::post('login', 'UserLogin');
});

// PROTECTED API ROUTES
Route::middleware('auth:sanctum')->group(function () {

    Route::controller(AuthenticationController::class)->prefix('user')->group(function () {

        // ROUTE FOR USER CHANGE PASSWORD 
        Route::post('changepassword', 'changePassword');

        // ROUTE FOR USER LOGOUT
        Route::post('logout', 'UserLogout');

        // ROUTE FOR GETTING USER DETAILS
        Route::get('details', 'UserDetails');
    });

    // ROUTE TO SEE THE ALL THE USER LIST (NORMAL USER AND ADMIN CAN SEE THE LIST OF USERS)
    Route::get('allusers', [ExcelUserOpsController::class, 'getAllUsers']);

    // ROUTE TO SEE THE ALL THE USER INCENTIVE (NORMAL USER AND ADMIN CAN SEE THE USER INCENTIVE )
    Route::get('alluserincentive', [UserIncentiveController::class, 'getUserIncentive']);

    // normal user can see the mail templates
    Route::get('templates', [MailTemplateController::class, 'getTemplates']);

    // get all the users and total number of mail templates are there
    Route::get('totalUserAndTemplates',[ExcelUserOpsController::class, 'totalUserAndTemplates']);

    // ROUTE THAT CAN BE ACCESSED BY ADMIN (A = ADMIN)
    Route::middleware(['check.role:A'])->group(function () {

        Route::controller(ExcelUserOpsController::class)->prefix('user')->group(function () {

            // ROUTE FOR STORING USERS FROM EXCEL FILE
            Route::post('store/excel', 'UserExcelStore');

            // ROUTE FOR STORING USERS MANUALLY 
            Route::post('store/manual', 'UserManualStore');

            // ROUTE FOR GETTING PARTICULAR USER DETAILS
            Route::get('show/{id}', 'getParticularUser');

            // ROUTE FOR UPDATING PARTICULAR USER DETAILS
            Route::post('update/{id}', 'UserUpdate');

            // ROUTE FOR DELETING PARTICULAR USER DETAILS
            Route::delete('delete/{id}', 'UserDelete');

            // ROUTE FOR RESTORING PARTICULAR USER
            Route::post('restore/{id}', 'UserRestore');
        });

        Route::controller(UserIncentiveController::class)->prefix('user/incentive')->group(function () {

            // ROUTE FOR STORING USER INCENTIVES FROM EXCEL FILE
            Route::post('store/excel', 'IncentiveStoreExcel');

            // ROUTE FOR STORING USER INCENTIVES MANUALLY
            Route::post('store/manual', 'IncentiveStoreManual');

            // ROUTE FOR GETTING PARTICULAR USER INCENTIVE DETAILS
            Route::get('show/{id}', 'getParticularUserIncentiveDetails');

            // ROUTE FOR UPDATING PARTICULAR USER INCENTIVE DETAILS
            Route::post('update/{id}', 'updateParticularUserIncentiveDetails');

            // ROUTE FOR DELETING USER INCENTIVE DETAILS
            Route::delete('delete/{id}', 'deleteUserIncentiveDetails');

            // ROUTE FOR RESTORING USER INCENTIVE DETAILS
            Route::post('restore/{id}', 'restoreUserIncentiveDetails');

            // ROUTE FOR SENDING MAIL TO THE USER WITH INCENTIVE DETAILS
            Route::get('send/{id}', 'sendIncentiveMail');
        });

        // mail templates to be stored in
        Route::post('/templates', [MailTemplateController::class, 'saveTemplate']);
       
        // delete mail template
        Route::delete('/templates/{id}', [MailTemplateController::class, 'deleteTemplate']);

        // mail send
        Route::post('/send-mail-template', [MailTemplateController::class, 'sendMailTemplate']);

    });
});
