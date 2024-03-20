<?php

namespace App\Http\Controllers;

use App\Facades\StripePaymentGatewayFacade;
use App\Http\Requests\Plan\StorePlanDetailsRequest;
use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePlanDetailsRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;
use App\Models\Plan;
use App\Models\PlanDetails;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * Plan List.
     *
     * @OA\Get(
     *     path="/api/plan",
     *     tags={"Plan"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter(
     *          in="query",
     *          name="pagination",
     *          required=true,
     *
     *          @OA\Schema(type="number"),
     *          example="10"
     *      ),
     *
     *      @OA\Parameter(
     *          in="query",
     *          name="get_all",
     *          required=false,
     *
     *          @OA\Schema(type="boolean")
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="data", type="json", example={}),
     *               @OA\Property(property="links", type="json", example={}),
     *               @OA\Property(property="meta", type="json", example={}),
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     */
    public function index()
    {
        $plans = Plan::with('planDetails')->paginate(self::PER_PAGE);
        return response()->json(['success' => true, 'data' => $plans]);
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
     *     tags={"Plan"},
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
    public function store(StorePlanRequest $planRequest, StorePlanDetailsRequest $planDetailsRequest)
    {
        try {
            DB::beginTransaction();
            $plan = Plan::create($planRequest->only([
                'name',
                'description',
                'amount',
                'currency',
                'interval',
                'interval_duration',
            ]));

            PlanDetails::create([
                'plan_id' => $plan->id,
                'feature' => $planDetailsRequest->feature,
                'features_count' => $planDetailsRequest->features_count,
                'value' => $planDetailsRequest->value,
            ]);

            $response = StripePaymentGatewayFacade::createPrice($plan);
            $plan->update(['stripe_price_id' => $response->id]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Plan created successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

    }

    /**
     * Retrieve a specific plan.
     *
     * @OA\Get(
     *     path="/api/plan/{id}",
     *     tags={"Plan"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="get singe plan by plan id",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *    @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="errors", type="json", example={"message": {"Plan retrieved successfully."}}),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plan not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Plan not found")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $plan = Plan::find($id);
            if ($plan) {
                $plan->load('planDetails');
                return response()->json(['success' => true, 'data' => $plan], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Plan not exist'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve plan'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update this plan.
     *
     *
     * @OA\Post (path="/api/plan/{id}",
     *     tags={"Plan"},
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
    public function update(UpdatePlanRequest $updatePlanRequest, UpdatePlanDetailsRequest $updatePlanDetailsRequest, string $id)
    {
        $plan = Plan::find($id);
        if (empty($plan)) {
            return response()->json(['success' => false, 'message' => 'Plan does not exist'], 500);
        }
        try {
            DB::beginTransaction();

            $plan->update($updatePlanRequest->only(['name',
                'description',
                'amount',
                'currency',
                'interval',
                'interval_duration',
            ]));

            PlanDetails::where('plan_id', $plan->id)->update([
                'plan_id' => $plan->id,
                'feature' => $updatePlanDetailsRequest->feature,
                'features_count' => $updatePlanDetailsRequest->features_count,
                'value' => $updatePlanDetailsRequest->value,

            ]);
            $response = StripePaymentGatewayFacade::updatePrice($plan);
            // $plan->update(['stripe_price_id' => $response->id]);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Plan updated successfully!'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the plan and related data.
     *
     * @OA\Delete (
     *     path="/api/plan/{id}",
     *     tags={"Plan"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the plan to be deleted",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Plan and related data deleted successfully")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Plan not found")
     *         ),
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $plan = Plan::find($id);
        if ($plan) {
            $plan->planDetails()->delete();
            $plan->delete();
            return response()->json(['success' => true, 'message' => 'Plan and related data deleted successfully'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Plan not exist'], 500);
        }
    }


}
