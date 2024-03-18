<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PlanDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Create a new Plan.
     *
     *
     * @OA\Post (path="/api/plan",
     *     tags={"Category"},
     *     security={{ "apiAuth": {} }},
     *
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="name",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="Doel Rana",
     *     ),
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="description",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="This is just description",
     *     ),
     *     @OA\Parameter(
     *     in="query",
     *     name="price",
     *     required=true,
     *     @OA\Schema(type="string"),
     *     example="100",
     *     ),
     *
     *     @OA\Parameter(
     *     in="query",
     *     name="currency",
     *     required=true,
     *     @OA\Schema(type="string"),
     *     example="USD",
     *     ),
     *
     *     @OA\Parameter(
     *     in="query",
     *     name="interval",
     *     required=true,
     *     @OA\Schema(type="string"),
     *     example="month",
     *     ),
     *
     *     @OA\Parameter(
     *     in="query",
     *     name="interval_duration",
     *     required=true,
     *     @OA\Schema(type="string"),
     *     example="1",
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Category created successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *          )
     *      )
     * )
     */


    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'name'              => 'required',
            'description'       => 'required',
            'price'             => 'required',
            'currency'          => 'required',
            'interval'          => 'required',
            'interval_duration' => 'required',
        ]);

        if ($validateData->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validateData->errors()
            ], 422);
        }
        $created_by = auth()->user()->id;
        $updated_by = auth()->user()->id;
        $uid        = 'PLN-' . time() . rand(10, 99);

        $data = [
            'name'              => $request->name,
            'description'       => $request->description,
            'price'             => $request->price,
            'currency'          => $request->currency,
            'interval'          => $request->interval,
            'interval_duration' => $request->interval_duration,
            'created_by'        => $created_by,
            'updated_by'        => $updated_by,
            'uid'               => $uid
        ];


        $plan         = Plan::create($data);
        $plan_details = [
            'name'       => $request->name,
            'feature'    => $request->feature,
            'value'      => $request->value,
            'currency'   => $request->currency,
            'plan_id'    => $plan->id,
            'created_by' => $created_by,
            'updated_by' => $updated_by,
        ];
        PlanDetails::create($plan_details);
        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully!'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateData = Validator::make($request->all(), [
            'name'              => 'required',
            'description'       => 'required',
            'price'             => 'required',
            'currency'          => 'required',
            'interval'          => 'required',
            'interval_duration' => 'required',
            'created_by'        => 'required',
            'updated_by'        => 'required',
        ]);

        if ($validateData->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validateData->errors()
            ], 422);
        }

        Plan::find($id)->update($request->only(['name', 'description', 'price', 'currency', 'interval', 'interval_duration', 'created_by', 'updated_by']));
        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
