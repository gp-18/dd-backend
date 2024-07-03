<?php

namespace App\Http\Controllers;

use App\Imports\ExcelUserDataImport;
use App\Models\ExcelUserOps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserIncentive;
use App\Models\EmailTemplate;

class ExcelUserOpsController extends Controller
{
    /**
     * THIS IS THE FUNCTION USED TO STORE THE USER DATA FROM EXCEL SHEET
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/store/excel (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function UserExcelStore(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'file' => 'required|file|mimes:csv,xls,xlsx',
            ]);

            if ($request->hasFile('file')) {

                $filePath = $request->file('file')->store('public/files');

                Excel::import(new ExcelUserDataImport, $request->file('file'));

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
     * THIS IS THE FUNCTION USED TO STORE THE USER DATA MANUALLY
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/store/manual (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function UserManualStore(Request $request)
    {
        try {

            $validate = $request->validate([
                'bo_name'   => 'required |string',
                'abm_name'  => 'required |string',
                'rsm_name'  => 'required |string',
                'nsm_name'  => 'required |string',
                'gpm_name'  => 'required |string',
                'bo_email'  => 'required |email',
                'abm_email' => 'required |email',
                'rsm_email' => 'required |email',
                'nsm_email' => 'required |email',
                'gpm_email' => 'required |email',
            ]);

            $user = ExcelUserOps::create($request->only('bo_name', 'abm_name', 'rsm_name', 'nsm_name', 'gpm_name', 'bo_email', 'abm_email', 'rsm_email', 'nsm_email', 'gpm_email'));

            return response()->json([
                'message' => 'User created successfully',
                'status'  => 'success',
                'user'    => $user,
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'errors'  => $e->getMessage(),
                'message' => 'Error Occured while Creating A user',
                'status'  => 'Failed'
            ], 422);
        }
    }

    /**
     * THIS IS THE FUNCTION USED TO FETCH ALL THE USER DATA AND ONLY ADMIN CAN DO THE FILTER CAN SEE THE DELETED USER
     * @method GET
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route allusers (PROTECTED ROUTE)
     * @middleware (auth:sanctum)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getAllUsers(Request $request)
    {
        try {

            $request->validate([
                'per_page'     => 'nullable|integer|min:1',
                'page'         => 'nullable|integer|min:1',
                'show_deleted' => 'nullable|boolean',
            ]);

            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);

            $user = Auth::user();

            if ($user->role == 'A' && $request->input('show_deleted', false)) {
                $userData = ExcelUserOps::onlyTrashed()
                    ->paginate($perPage, ['*'], 'page', $currentPage);
            } else {
                $userData = ExcelUserOps::paginate($perPage, ['*'], 'page', $currentPage);
            }

            return response()->json([
                'message'     => 'Fetching all the users',
                'status'      => 'Success',
                'users'       => $userData->items(),
                'pagination'  => [
                    'current_page' => $userData->currentPage(),
                    'per_page'     => $userData->perPage(),
                    'total'        => $userData->total(),
                    'last_page'    => $userData->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'errors'  => $e->getMessage(),
                'message' => 'Error occurred while fetching users',
                'status'  => 'Failed',
            ], 500);
        }
    }

    /**
     * THIS IS THE FUNCTION USED TO FETCH PARTICULAR USER DATA
     * @method GET
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/show/{id} (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getParticularUser(string $id)
    {
        try {

            $user = ExcelUserOps::findorFail($id);

            return response()->json([
                'message' => 'Retrieving user information successfully',
                'status'  => 'Success',
                'user'    => $user,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message'   => 'Error Occured while retrieving user information',
                'status'    => 'Failed',
                'data'      => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * THIS IS THE FUNCTION USED TO UPDATA PARTICULAR USER DATA
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/update/{id} (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function UserUpdate(Request $request, string $id)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'bo_name'   => 'required|string',
                'abm_name'  => 'required|string',
                'rsm_name'  => 'required|string',
                'nsm_name'  => 'required|string',
                'gpm_name'  => 'required|string',
                'bo_email'  => 'required|email|unique:excel_user_ops,bo_email,' . $id,
                'abm_email' => 'required|email',
                'rsm_email' => 'required|email',
                'nsm_email' => 'required|email',
                'gpm_email' => 'required|email',
            ]);

            $userData = ExcelUserOps::findOrFail($id);

            // Save original bo_name and bo_email
            $originalBoName = $userData->bo_name;
            $originalBoEmail = $userData->bo_email;

            // Update user data
            $userData->update($request->only(
                'bo_name',
                'abm_name',
                'rsm_name',
                'nsm_name',
                'gpm_name',
                'bo_email',
                'abm_email',
                'rsm_email',
                'nsm_email',
                'gpm_email'
            ));


            $relatedIncentives = UserIncentive::where('user_id', $id);

            if ($relatedIncentives->exists()) {

                if ($userData->bo_name !== $originalBoName) {
                    $relatedIncentives->update(['bo_name' => $userData->bo_name]);
                }

                if ($userData->bo_email !== $originalBoEmail) {
                    $relatedIncentives->update(['bo_email' => $userData->bo_email]);
                }
            }

            return response()->json([

                'message' => 'User updated successfully',
                'status'  => 'Success',
                'user'    => $userData,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error occurred while updating user information',
                'status'  => 'Failed',
                'data'    => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * THIS IS THE FUNCTION USED FOR SOFT DELETE AND PERMANENT DELETE OF THE USER
     * @method DELETE
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/delete/{id} (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function UserDelete(Request $request, string $id)
    {
        try {

            $request->validate([
                'force_delete' => 'sometimes | boolean'
            ]);

            $userData = ExcelUserOps::findOrFail($id);

            if ($request->input('force_delete')) {

                $userData->forceDelete();
            } else {

                $userData->delete();
            }

            return response()->json([
                'message' => 'User deleted successfully',
                'status'  => 'Success',
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error Occured while deleting user information',
                'status'  => 'Failed',
                'data'    => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * THIS IS THE FUNCTION USED FOR RESTORING THE DELETED USER
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/restore/{id} (PROTECTED ROUTE)
     * @middleware (auth:sanctum && check.role:A (ADMIN))
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function UserRestore(string $id)
    {
        try {

            $userData = ExcelUserOps::onlyTrashed()->findOrFail($id);
            $userData->restore();

            $userIncentives = UserIncentive::onlyTrashed()->where('user_id', $id);

            if ($userIncentives->exists()) {
                $userIncentives->restore();
            }

            return response()->json([
                'message' => 'User restored successfully',
                'status' => 'Success',
                'user' => $userData,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error occurred while restoring user information',
                'status' => 'Failed',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * THIS IS THE FUNCTION USED FOR GETTING THE TOTAL NUMBER OF USERS AND TEMPLATES
     * @method GET
     * @author PARTH GUPTA
     * @route total-user-templates (PROTECTED ROUTE)
     * @middleware (auth:sanctum )
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function totalUserAndTemplates()
    {
        try {

            $totalBo        = ExcelUserOps::whereNotNull('bo_name')->distinct('bo_name')->count('bo_name');
            $totalAbm       = ExcelUserOps::whereNotNull('abm_name')->distinct('abm_name')->count('abm_name');
            $totalRsm       = ExcelUserOps::whereNotNull('rsm_name')->distinct('rsm_name')->count('rsm_name');
            $totalNsm       = ExcelUserOps::whereNotNull('nsm_name')->distinct('nsm_name')->count('nsm_name');
            $totalGpm       = ExcelUserOps::whereNotNull('gpm_name')->distinct('gpm_name')->count('gpm_name');
            $totalTemplates = EmailTemplate::count();

            $totalUserAndTemplates = [
                'total_bo'        => $totalBo,
                'total_abm'       => $totalAbm,
                'total_rsm'       => $totalRsm,
                'total_nsm'       => $totalNsm,
                'total_gpm'       => $totalGpm,
                'total_templates' => $totalTemplates,
            ];

            return response()->json([
                'total_user_and_templates' => $totalUserAndTemplates,
                'status' => 'Success',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => $e->getMessage(),
                'message' => 'Error occurred while fetching total user and template count',
                'status' => 'Failed',
            ], 500);
        }
    }
}
