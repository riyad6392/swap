<?php

namespace App\Http\Controllers;

use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Requests\Shipment\UpdateShipmentRequest;
use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    const PER_PAGE = 10;
    /**
     * Display a listing of the resource.
     */
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
     * Store a newly created resource in storage.
     */
    public function store(StoreShipmentRequest $shipmentRequest)
    {
        Shipment::create($shipmentRequest);

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
