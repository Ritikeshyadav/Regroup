<?php

namespace App\Services\APIs;

use App\Models\Colors;
use App\Models\IconMaster;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class SpaceApiService
{
    /**
     * Created By : Chandan Yadav
     * Created At : 21 March 2024
     * Use : To show space  list
     */
    public function fetchSpaceService()
    {
        try {
            $spaceData = Space::select('id', 'name', 'description')->get();
            if ($spaceData == null) {
                Log::info('Space data not found.');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'), [], 200);
            }
            $responseData['result'] = $spaceData;
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'), $responseData, 201);
        } catch (Exception $ex) {
            Log::error('fetch space function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 21 March 2024
     * Use : To add space  list
     */
    public function addSpaceService(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'icon_xid' => 'required',
                'color_xid' => 'required',
                'description' => 'required'
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            $icon_xid = IconMaster::where('id', $request->icon_xid)->first();

            if (!$icon_xid) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 403);
            }

            $color_xid = Colors::where('id', $request->color_xid)->first();

            if (!$color_xid) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 403);
            }


            $spaceData = Space::updateOrCreate(
                [
                    'id' => $request->id // Check if record with this ID exists
                ],
                [
                    'name' => $request->name,
                    'icon_xid' => $icon_xid->id,
                    'color_xid' => $color_xid->id,
                    'description' => $request->description,
                    'is_private' => 1,

                ]
            );
            DB::commit();
            $responseData['space'] = $spaceData;
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), $responseData, 201);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('add space service function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }


    /**
     * Created By : Chandan Yadav
     * Created At : 21 March 2024
     * Use : To delete space 
     */
    public function deleteSpaceService(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return jsonResponseWithErrorMessageApi($validator->errors(), 422);
            }

            $space = Space::find($request->id);

            if (!$space) {
                return jsonResponseWithErrorMessageApi(__('success.data_not_found'), 404);
            }

            $space->delete();
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.data_deleted'), $space, 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Delete Space function failed: ' . $ex->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'), 500);
        }
    }
}
