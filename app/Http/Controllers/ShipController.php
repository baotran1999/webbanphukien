<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ship;

class ShipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ships = Ship::all();
        return view('admin.ships.listShip',['ships'=>$ships]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.ships.addShip');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Form validation
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required'
        ]);
        //  Store data in database
        $ship = new Ship([
            'name' => $request->input('name'),
            'price' => $request->input('price')
        ]);
        $ship->save();
        return redirect()->route('ship.list')->with("success","Lưu thành công");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ship = Ship::find($id);
        return view('admin.ships.editShip',['ship' => $ship]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ship = Ship::find($id);
        $ship->name = $request->input('name');
        $ship->price = $request->input('price');
        $ship->save();
        return redirect()->route('ship.list')->with("success","Sửa thành công");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ship = Ship::find($id);
        $ship->delete();
        return redirect()->route('ship.list')->with("success","Xóa thành công");
    }

     /**
     * Add ship price
     *
     * @return \Illuminate\Http\Response
     */
    public function addShipPrice()
    {
        $msg = Ship::find($_GET['ship']);
        return response()->json($msg);
    }
}   
