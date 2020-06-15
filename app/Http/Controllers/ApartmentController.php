<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Apartment;
use App\Sponsorship;
use Carbon\Carbon;


class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apartments = Apartment::all();
        $maxPrice = Sponsorship::max('price');
        return view('guest.apartments.index', compact('apartments', 'maxPrice'));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        function distanceResults($lat1, $lon1, $latitude, $longitude, $unit)
        {
            $theta = $lon1 - $longitude;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($latitude)) + cos(deg2rad($lat1)) * cos(deg2rad($latitude)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);
            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        };

        $apartments = Apartment::where('visibility', 1)->get();
        $filteredApartments = [];
        $sponsoredApartments = [];

        $data = $request->all();
        $dataLat = floatval($data['lat']);
        $dataLng = floatval($data['lng']);

        foreach ($apartments as $apartment) {
            $apartmentLat = $apartment->lat;
            $apartmentLng = $apartment->lng;

            $result = distanceResults($apartmentLat, $apartmentLng, $dataLat, $dataLng, 'k');
            if ($result <= 20) {
                foreach ($apartment->sponsorships as $sponsorship) {
                    $now = Carbon::now();
                    $endDate = $sponsorship->pivot->end_date;
                    if ($now < $endDate && !in_array($apartment, $sponsoredApartments)) {
                        $sponsoredApartments[] = $apartment;
                    }
                }
                if (!in_array($apartment, $sponsoredApartments)) {
                    $filteredApartments[] = $apartment;
                }
            }
        }

        if (count($filteredApartments) == 0 && count($sponsoredApartments) == 0) {
            return redirect()->route('guest.apartments.index')
                ->with('failure', 'Nessun Appartamento disponibile in zona');
        }

        return view('guest.apartments.search', compact('sponsoredApartments', 'filteredApartments', 'dataLat', 'dataLng'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
