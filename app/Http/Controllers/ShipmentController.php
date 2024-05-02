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
     */

    public function store(StoreShipmentRequest $shipmentRequest)
    {
        Shipment::create([
            'swap_id' => $shipmentRequest->swap_id,
            'last_name' => $shipmentRequest->last_name,
            'phone' => $shipmentRequest->phone,
            'image' => $shipmentRequest->image->store('images'),
            'resale_license' => $shipmentRequest->resale_license->store('images'),
            'photo_of_id' => $shipmentRequest->photo_of_id->store('images'),
            'business_name' => $shipmentRequest->business_name,
            'business_address' => $shipmentRequest->business_address,
            'online_store_url' => $shipmentRequest->online_store_url,
            'ein' => $shipmentRequest->ein,
            'about_me' => $shipmentRequest->about_me,
        ]);

        return response()->json(['success' => true, 'data' => 'Shipment created successfully']);

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
        $shipment = Shipment::find($id);
        return response()->json(['success' => true, 'data' => $shipment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShipmentRequest $shipmentRequest, string $id)
    {
        $shipment = Shipment::find($id);
        $shipment->update($shipmentRequest->all());

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
