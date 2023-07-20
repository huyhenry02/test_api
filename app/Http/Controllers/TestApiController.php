<?php

namespace App\Http\Controllers;

use App\Models\Accompanied_service;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Detail_service;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class TestApiController extends Controller
{
    public function cau1()
    {
//        dd(111);
        $services = Detail_service::where('quantity', '>', 3)
            ->where('quantity', '<', 10)
            ->get(['booking_code', 'accompanied_service_code', 'quantity']);
        dd($services);
        return response()->json($services);

    }
    public function cau2()
    {
        Room::where('max_customer', '>', 10)
            ->update(['price' => DB::raw('price + 10000')]);
        $updated_cau2= Room::where('max_customer', '>', 10)->get(['price','code']);
        dd($updated_cau2);
        $message = utf8_encode('Cập nhật giá phòng thành công');
        return response()->json(['message' => $message]);
    }
    public function cau3()
    {
        Booking::where('status','cancel')->delete();
        return response()->json(['message' => 'Xóa đơn đặt phòng thành công']);
    }
    public function cau4()
    {
        $cau4 = Customer::where(function ($query){
            $query->where('name','like','H%')
                ->orWhere('name','like','N%')
                ->orWhere('name','like','M%');
        })
            ->whereRaw('LENGTH(name) <=20')->get();
        return response()->json($cau4);
    }
    public function cau5()
    {
        $cau5 = Customer::distinct()->pluck('name')->toArray();
        return response()->json($cau5);
    }
    public function cau6()
    {
        $cau6 = Accompanied_service::where(function ($query) {
                $query->where('unit','cans')
                      ->where('price','>','10000');
            })
            ->orWhere(function ($query) {
                $query->where('unit','bag')
                    ->where('price','<','10000');
            })->get();
        return response()->json($cau6);
    }
    public function cau7()
    {
        $cau7 = Booking::from('booking AS b')
            ->join('rooms AS r','b.room_code','=','r.code')
            ->join('customers AS c','b.cus_code','=','c.code')
            ->join('detail_services AS ds','b.code','=','ds.booking_code')
            ->join('accompanied_services AS a','ds.accompanied_service_code','=','a.code')
            ->select(
                'b.code AS booking_code',
                'b.room_code',
                'r.type AS room_type',
                'r.price AS room_price',
                'c.code AS customer_code',
                'c.name AS customer_name',
                'c.phone AS customer_phone',
                'b.booking_at',
                'b.start_time',
                'b.end_time',
                'ds.accompanied_service_code',
                'ds.quantity AS detail_service_quantity',
                'a.unit AS detail_service_unit',
                'a.price AS accompanied_service_price'
            )
            ->where(function ($query) {
                $query->whereRaw("(YEAR(b.booking_at) = '2016' OR YEAR(b.booking_at) = '2017')");
            })
            ->where('r.price', '>', '100000')
            ->get();

        foreach ($cau7 as $result) {
            echo $result->booking_code .
                ' - ' . $result->room_code .
                ' - ' . $result->room_type .
                ' - ' . $result->room_price .
                ' - ' . $result->customer_name .
                ' - ' . $result->booking_at .
                ' - ' . $result->detail_service_quantity .
                ' - ' . $result->detail_service_unit .
                ' - ' . $result->accompanied_service_price ;
        }

        return response()->json($cau7);

    }
    public function cau8()
    {
        $cau8 = Booking::from('booking AS b')
            ->join('rooms AS r', 'b.room_code', '=', 'r.code')
            ->join('customers AS c', 'b.cus_code', '=', 'c.code')
            ->leftJoin('detail_services AS ds', 'b.code', '=', 'ds.booking_code')
            ->leftJoin('accompanied_services AS a', 'ds.accompanied_service_code', '=', 'a.code')
            ->select(
                'b.code AS booking_code',
                'b.room_code',
                'r.type AS room_type',
                'r.price AS room_price',
                'c.name AS customer_name',
                'b.booking_at',
                DB::raw('(r.price * (TIME_TO_SEC(MAX(b.end_time)) - TIME_TO_SEC(MIN(b.start_time))))) AS total_price_room'),
                DB::raw('SUM(ds.quantity * a.price) AS total_use_service'),
                DB::raw('(r.price * (TIME_TO_SEC(MAX(b.end_time)) - TIME_TO_SEC(MIN(b.start_time))))) + SUM(ds.quantity * a.price) AS total')
            )
            ->groupBy('b.code', 'b.room_code', 'r.type', 'r.price', 'c.name', 'b.booking_at')
            ->get();

        foreach ($cau8 as $result) {
            echo $result->booking_code .
                ' - ' . $result->room_code .
                ' - ' . $result->room_type .
                ' - ' . $result->room_price .
                ' - ' . $result->customer_name .
                ' - ' . $result->booking_at .
                ' - ' . $result->total_price_room .
                ' - ' . $result->total_use_service .
                ' - ' . $result->total;
        }
        return response()->json($cau8);

    }
    public function cau9()
    {
        $cau9 = Customer::from('customers AS c')
            ->where('c.address', 'Ha noi')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('booking')
                    ->whereRaw('c.code = booking.cus_code');
            })
            ->select('c.code AS customer_code', 'c.name AS customer_name', 'c.address AS customer_address', 'c.phone AS customer_phone')
            ->get();

        foreach ($cau9 as $result) {
            echo $result->customer_code .
                ' - ' . $result->customer_name .
                ' - ' . $result->customer_address .
                ' - ' . $result->customer_phone ;
        }

        return response()->json($cau9);
    }
    public function cau10()
    {
        $cau10 = Room::from('booking AS b')
            ->join('rooms AS r', 'b.room_code', '=', 'r.code')
            ->select('r.code AS room_code', 'r.type AS room_type', 'r.max_customer', 'r.price', DB::raw('COUNT(*) AS numBooked'))
            ->where('b.status', 'booked')
            ->groupBy('r.code', 'r.type', 'r.max_customer', 'r.price')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($cau10 as $result) {
            echo $result->room_code .
                ' - ' . $result->room_type .
                ' - ' . $result->max_customer .
                ' - ' . $result->price .
                ' - ' . $result->numBooked;
        }

        return response()->json($cau10);

    }
}
