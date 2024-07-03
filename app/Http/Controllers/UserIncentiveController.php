<?php

namespace App\Http\Controllers;

use App\Imports\ExcelUserIncentive;
use App\Mail\IncentiveMail;
use App\Models\ExcelUserOps;
use App\Models\UserIncentive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UserIncentiveController extends Controller
{
    /**
     * THIS IS THE FUNCTION USED TO STORE THE USER INCENTIVE FROM EXCEL SHEET
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/incentive/store/excel (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function IncentiveStoreExcel(Request $request)
    {

        try {

            $request->validate([
                'file' => 'required|file|mimes:csv,xls,xlsx',
            ]);

            if ($request->hasFile('file')) {

                $filePath = $request->file('file')->store('public/files');

                Excel::import(new ExcelUserIncentive, $request->file('file'));

                return response()->json([
                    'message' => 'File uploaded and stored successfully',
                    'file_path' => $filePath,
                ], 200);
            } else {

                return response()->json([
                    'message' => 'No file was uploaded',
                ], 400);
            }
        } catch (\Exception $e) {

            return response()->json([
                'errors'  => $e->getMessage(),
                'message' => 'Validation failed. Please check the file and try again.'
            ], 422);
        }
    }

    /**
     * THIS IS THE FUNCTION USED TO STORE THE USER INCENTIVE MANUALLY
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/incentive/store/manual (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function IncentiveStoreManual(Request $request)
    {
        try {

            $request->validate([
                'bo_name'                   => 'required |string',
                'bo_email'                  => 'required |email',
                'headquarter'               => 'required |string',
                'april_may_june_target'     => 'required |integer',
                'july_aug_sept_target'      => 'required |integer',
                'oct_nov_dec_target'        => 'required |integer',
                'april_may_june_incentive'  => 'required |integer',
                'july_aug_sept_incentive'   => 'required |integer',
                'oct_nov_dec_incentive'     => 'required |integer',
            ]);

            $user = ExcelUserOps::where('bo_email', $request->only('bo_email'))->first();

            if (!$user) {

                return response()->json([
                    'errors'  => 'User not found',
                    'message' => 'First Create User and then Enter the Targets and Inncentive',
                    'status'  => 'Failed'
                ], 422);
            }

            $incentive = UserIncentive::create($request->only(['bo_name', 'bo_email', 'headquarter', 'april_may_june_target', 'july_aug_sept_target', 'oct_nov_dec_target', 'april_may_june_incentive', 'july_aug_sept_incentive', 'oct_nov_dec_incentive']) + ['user_id' => $user->id]);

            return response()->json([
                'message' => 'User Incentive Store Successfully',
                'data'    => $incentive,
                'status'  => 'Success'
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'errors'  => $e->getMessage(),
                'message' => ' Please try again.',
                'status'  => 'Failed'
            ], 422);
        }
    }

    /**
     * THIS IS THE FUNCTION USED TO GET ALL THE USER INCENTIVE AND MAKE SURE THAT ONLY ADMIN CAN SEE THE DELETED USER INCENTIVE
     * @method GET
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route alluserincentive (PROTECTED ROUTE)
     * @middleware (auth:sanctum)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUserIncentive(Request $request)
{
    try {
        $request->validate([
            'show_deleted' => 'nullable|boolean'
        ]);

        $user = Auth::user();

        if ($user->role == 'A' && $request->input('show_deleted', false)) {
            $userIncentive = UserIncentive::with(
                'user:id,abm_email,rsm_email,nsm_email,gpm_email'
            )->onlyTrashed()->get();
        } else {
            $userIncentive = UserIncentive::with(
                'user:id,abm_email,rsm_email,nsm_email,gpm_email'
            )->get();
        }

        return response()->json([
            'message' => 'Fetching all the users',
            'status' => 'Success',
            'users' => $userIncentive,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'errors' => $e->getMessage(),
            'message' => 'Please try again.',
        ], 422);
    }
}


    /**
     * THIS IS THE FUNCTION USED TO GET PARTICULAR USER INCENTIVE
     * @method GET
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/incentive/show/{id} (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getParticularUserIncentiveDetails($id)
    {

        try {

            $userInnective = UserIncentive::with(
                'user:id,abm_name,rsm_name,nsm_name,gpm_name'
            )->findOrFail($id);

            return response()->json([
                'data'    => $userInnective,
                'status'  => 'Success'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'errors'  => $e->getMessage(),
                'message' => 'Please try again.',
            ], 422);
        }
    }

    /**
     * THIS IS THE FUNCTION USED TO GET UPDATE PARTICULAR USER INCENTIVE
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/incentive/update/{id} (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateParticularUserIncentiveDetails(Request $request, $id)
    {

        try {

            $request->validate([
                'april_may_june_target'     => 'required |integer',
                'july_aug_sept_target'      => 'required |integer',
                'oct_nov_dec_target'        => 'required |integer',
                'april_may_june_incentive'  => 'required |integer',
                'july_aug_sept_incentive'   => 'required |integer',
                'oct_nov_dec_incentive'     => 'required |integer',
            ]);

            $userIncentive = UserIncentive::findOrFail($id);

            $userIncentive->update($request->all());

            return response()->json([
                'data'    => $userIncentive,
                'status'   => 'Success'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'errors'  => $e->getMessage(),
                'message' => 'Please try again.',
            ], 422);
        }
    }

    /**
     * THIS IS THE FUNCTION USED TO GET DELETE PARTICULAR USER INCENTIVE
     * @method DELETE
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/incentive/delete/{id} (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteUserIncentiveDetails(Request $request, $id)
    {
        try {

            $request->validate([
                'force_delete' => 'sometimes | boolean'
            ]);

            $userIncentive = UserIncentive::findOrFail($id);

            if ($request->input('force_delete')) {

                $userIncentive->forceDelete();
            } else {

                $userIncentive->delete();
            }

            return response()->json([
                'data'    => $userIncentive,
                'status'  => 'Success'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'errors'  => $e->getMessage(),
                'message' => 'Please try again.',
            ], 422);
        }
    }

    /**
     * THIS IS THE FUNCTION USED TO RESTORE PARTICULAR USER INCENTIVE
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/incentive/restore/{id} (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function restoreUserIncentiveDetails($id)
    {
        try {

            $userIncentive = UserIncentive::withTrashed()->findOrFail($id);

            $user = ExcelUserOps::withTrashed()->findOrFail($userIncentive->user_id);

            if ($user->trashed()) {

                return response()->json([
                    'message' => 'Cannot restore user incentive because the corresponding user is soft-deleted.',
                    'status'  => 'Failed',
                ], 409);
            }

            $userIncentive->restore();

            return response()->json([
                'data'   => $userIncentive,
                'status' => 'Success',
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error occurred while restoring user incentive',
                'status'  => 'Failed',
                'data'    => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * THIS IS THE FUNCTION USED TO SENG MAIL TO PARTICULAR USER WITH INCENTIVE
     * @method GET
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/incentive/send/{id} (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendIncentiveMail(Request $request, $id)
    {
        try {

            $userIncentiveDetails = UserIncentive::with('user:id,abm_email,rsm_email,nsm_email,gpm_email')
                ->findOrFail($id);

            $ccEmails = [];
            $bccEmails = [];

            if ($userIncentiveDetails->user->abm_email) {
                $ccEmails[] = $userIncentiveDetails->user->abm_email;
            }
            if ($userIncentiveDetails->user->rsm_email) {
                $ccEmails[] = $userIncentiveDetails->user->rsm_email;
            }
            if ($userIncentiveDetails->user->nsm_email) {
                $ccEmails[] = $userIncentiveDetails->user->nsm_email;
            }
            if ($userIncentiveDetails->user->gpm_email) {
                $ccEmails[] = $userIncentiveDetails->user->gpm_email;
            }

            Mail::to($userIncentiveDetails->bo_email)->cc($ccEmails)
                ->bcc($bccEmails)->send(new IncentiveMail($userIncentiveDetails));

            return response()->json([
                'data'    => $userIncentiveDetails,
                'status'  => 'Success'
            ], 200);
            
        } catch (\Exception $e) {

            return response()->json([
                'errors'  => $e->getMessage(),
                'message' => 'Please try again.',
                'status'  => 'Failed'
            ], 422);
        }
    }
}
