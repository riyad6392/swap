<?php

namespace App\Http\Controllers;

use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Requests\Shipment\UpdateShipmentRequest;
use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    const PER_PAGE = 10;

    public function index()
    {
        $shipments = Shipment::query();

        if (request()->has('get_all')) {

            return response()->json(['success' => true, 'data' => $shipments->get()]);
        }

        $shipments = $shipments->paginate($request->pagination ?? self::PER_PAGE);

        return response()->json(['success' => true, 'data' => $shipments]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Create Shipping info.
     *
     * @OA\Post(
     *     path="/api/shipping",
     *     tags={"Shipping"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="swap_id",
     *          required=true,
     *
     *          @OA\Schema(type="integer"),
     *          example="1"
     *      ),
     *
     *       @OA\Parameter(
     *           in="query",
     *           name="last_name",
     *           required=true,
     *
     *           @OA\Schema(type="string"),
     *           example="Khan"
     *       ),
     *
     *       @OA\Parameter(
     *            in="query",
     *            name="phone",
     *            required=true,
     *
     *            @OA\Schema(type="string"),
     *            example="Khan"
     *        ),
     *
     *       @OA\Parameter(
     *             in="query",
     *             name="image",
     *             required=true,
     *
     *             @OA\Schema(type="file"),
     *             example="file"
     *         ),
     *
     *            @OA\Parameter(
     *              in="query",
     *              name="resale_license",
     *              required=true,
     *
     *              @OA\Schema(type="file"),
     *              example="file"
     *          ),
     *
     *         @OA\Parameter(
     *               in="query",
     *               name="photo_of_id",
     *               required=true,
     *
     *               @OA\Schema(type="file"),
     *               example="file"
     *           ),
     *              @OA\Parameter(
     *                in="query",
     *                name="photo_of_id",
     *                required=true,
     *
     *                @OA\Schema(type="file"),
     *                example="file"
     *            ),
     *          @OA\Parameter(
     *                 in="query",
     *                 name="business_name",
     *                 required=true,
     *
     *                 @OA\Schema(type="string"),
     *                 example="Business name"
     *             ),
     *          @OA\Parameter(
     *                  in="query",
     *                  name="business_address",
     *                  required=true,
     *
     *                  @OA\Schema(type="string"),
     *                  example="Business address"
     *              ),
     *          @OA\Parameter(
     *                  in="query",
     *                  name="online_store_url",
     *                  required=true,
     *
     *                  @OA\Schema(type="string"),
     *                  example="http://127.0.0.1:8000/api/documentation#/User/ad5b4db3132c00564bd7eede30c3e23a"
     *              ),
     *
     *          @OA\Parameter(
     *                   in="query",
     *                   name="ein",
     *                   required=true,
     *
     *                   @OA\Schema(type="string"),
     *                   example="ein"
     *               ),
     *          @OA\Parameter(
     *                   in="query",
     *                   name="about_me",
     *                   required=true,
     *
     *                   @OA\Schema(type="string"),
     *                   example="this is a description about me"
     *               ),
     *
     *      @OA\Parameter(
     *          in="query",
     *          name="get_all",
     *          required=false,
     *
     *          @OA\Schema(type="boolean"),
     *          example="1"
     *
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
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
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     * Store a newly created resource in storage.
     * Create Shipping info.
     *
     * @OA\Post(
     *     path="/api/shipping",
     *     tags={"Shipping"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="swap_id",
     *          required=true,
     *
     *          @OA\Schema(type="integer"),
     *          example="1"
     *      ),
     *
     *       @OA\Parameter(
     *           in="query",
     *           name="requested_address",
     *           required=true,
     *
     *           @OA\Schema(type="string"),
     *           example="Khan"
     *       ),
     *
     *       @OA\Parameter(
     *            in="query",
     *            name="requested_tracking_number",
     *            required=true,
     *
     *            @OA\Schema(type="string"),
     *            example="Khan"
     *        ),
     *
     *       @OA\Parameter(
     *             in="query",
     *             name="requested_carrier_name",
     *             required=true,
     *
     *             @OA\Schema(type="file"),
     *             example="file"
     *         ),
     *
     *            @OA\Parameter(
     *              in="query",
     *              name="requested_carrier_contact",
     *              required=true,
     *
     *              @OA\Schema(type="file"),
     *              example="file"
     *          ),
     *
     *         @OA\Parameter(
     *               in="query",
     *               name="requested_expected_delivery_date",
     *               required=true,
     *
     *               @OA\Schema(type="file"),
     *               example="file"
     *           ),
     *              @OA\Parameter(
     *                in="query",
     *                name="exchanged_address",
     *                required=true,
     *
     *                @OA\Schema(type="file"),
     *                example="file"
     *            ),
     *          @OA\Parameter(
     *                 in="query",
     *                 name="exchanged_tracking_number",
     *                 required=true,
     *
     *                 @OA\Schema(type="string"),
     *                 example="Business name"
     *             ),
     *          @OA\Parameter(
     *                  in="query",
     *                  name="exchanged_carrier_name",
     *                  required=true,
     *
     *                  @OA\Schema(type="string"),
     *                  example="Business address"
     *              ),
     *          @OA\Parameter(
     *                  in="query",
     *                  name="exchanged_carrier_contact",
     *                  required=true,
     *
     *                  @OA\Schema(type="string"),
     *                  example="http://127.0.0.1:8000/api/documentation#/User/ad5b4db3132c00564bd7eede30c3e23a"
     *              ),
     *
     *          @OA\Parameter(
     *                   in="query",
     *                   name="exchanged_expected_delivery_date",
     *                   required=true,
     *
     *                   @OA\Schema(type="string"),
     *                   example="ein"
     *               ),
     *
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="success", type="boolean", example="true"),
     *                @OA\Property(property="errors", type="json", example={"message": {"Shipment created successfully."}}),
     *           ),
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *           )
     *       )
     * )
     */

    public function store(StoreShipmentRequest $shipmentRequest)
    {
        $swap = $shipmentRequest->swap;
        $userId = auth()->id();

        if ($swap->requested_user_id == $userId) {
            $fieldPrefix = 'requested_';
        } elseif ($swap->exchanged_user_id == $userId) {
            $fieldPrefix = 'exchanged_';
        } else {
            return response()->json(['success' => false, 'data' => 'You are not authorized to create shipment for this swap']);
        }

        $shipment = Shipment::where('swap_id', $swap->id)->first();

        $data = $this->prepareShipmentData($shipmentRequest, $fieldPrefix);
        if (!$shipment) {
            Shipment::create($data);
        } else {
            $shipment->update($data);
        }
        return response()->json(['success' => true, 'data' => 'Shipment created successfully']);

    }

    private function prepareShipmentData($shipmentRequest, $prefix)
    {
        return [
            'swap_id' => $shipmentRequest->swap->id,
            $prefix . 'address' => $shipmentRequest->{$prefix . 'address'},
            $prefix . 'tracking_number' => $shipmentRequest->{$prefix . 'tracking_number'},
            $prefix . 'carrier_name' => $shipmentRequest->{$prefix . 'carrier_name'},
            $prefix . 'carrier_contact' => $shipmentRequest->{$prefix . 'carrier_contact'},
            $prefix . 'expected_delivery_date' => $shipmentRequest->{$prefix . 'expected_delivery_date'},
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update Shipping info.
     *
     * @OA\Put (
     *     path="/api/shipping",
     *     tags={"Shipping"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="swap_id",
     *          required=true,
     *
     *          @OA\Schema(type="integer"),
     *          example="1"
     *      ),
     *
     *       @OA\Parameter(
     *           in="query",
     *           name="requested_address",
     *           required=true,
     *
     *           @OA\Schema(type="string"),
     *           example="Khan"
     *       ),
     *
     *       @OA\Parameter(
     *            in="query",
     *            name="requested_tracking_number",
     *            required=true,
     *
     *            @OA\Schema(type="string"),
     *            example="Khan"
     *        ),
     *
     *       @OA\Parameter(
     *             in="query",
     *             name="requested_carrier_name",
     *             required=true,
     *
     *             @OA\Schema(type="file"),
     *             example="file"
     *         ),
     *
     *            @OA\Parameter(
     *              in="query",
     *              name="requested_carrier_contact",
     *              required=true,
     *
     *              @OA\Schema(type="file"),
     *              example="file"
     *          ),
     *
     *         @OA\Parameter(
     *               in="query",
     *               name="requested_expected_delivery_date",
     *               required=true,
     *
     *               @OA\Schema(type="file"),
     *               example="file"
     *           ),
     *              @OA\Parameter(
     *                in="query",
     *                name="exchanged_address",
     *                required=true,
     *
     *                @OA\Schema(type="file"),
     *                example="file"
     *            ),
     *          @OA\Parameter(
     *                 in="query",
     *                 name="exchanged_tracking_number",
     *                 required=true,
     *
     *                 @OA\Schema(type="string"),
     *                 example="Business name"
     *             ),
     *          @OA\Parameter(
     *                  in="query",
     *                  name="exchanged_carrier_name",
     *                  required=true,
     *
     *                  @OA\Schema(type="string"),
     *                  example="Business address"
     *              ),
     *          @OA\Parameter(
     *                  in="query",
     *                  name="exchanged_carrier_contact",
     *                  required=true,
     *
     *                  @OA\Schema(type="string"),
     *                  example="http://127.0.0.1:8000/api/documentation#/User/ad5b4db3132c00564bd7eede30c3e23a"
     *              ),
     *
     *          @OA\Parameter(
     *                   in="query",
     *                   name="exchanged_expected_delivery_date",
     *                   required=true,
     *
     *                   @OA\Schema(type="string"),
     *                   example="ein"
     *               ),
     *
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="success", type="boolean", example="true"),
     *                @OA\Property(property="errors", type="json", example={"message": {"Shipment created successfully."}}),
     *           ),
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *           )
     *       )
     * )
     */
    public function edit(string $id)
    {
        $shipment = Shipment::find($id);
        return response()->json(['success' => true, 'data' => $shipment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShipmentRequest $shipmentRequest, string $id)
    {
        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json(['success' => false, 'data' => 'Shipment not found']);
        }

        $data = $this->prepareShipmentData(
            $shipmentRequest,
            $shipment->requested_user_id == auth()->id() ? 'requested_' : 'exchanged_'
        );

        $shipment->update($data);

        return response()->json(['success' => true, 'data' => 'Shipment updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shipment = Shipment::find($id);
        $shipment->delete();

        return response()->json(['success' => true, 'data' => 'Shipment deleted successfully']);
    }
}
