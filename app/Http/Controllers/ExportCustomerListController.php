<?php

namespace App\Http\Controllers;

use App\Customer;
use App\MotoMenus\ToCSV;
use Carbon\Carbon;

class ExportCustomerListController extends Controller
{
    public function index()
    {
        try {
            $customers = Customer::with('note', 'user')->filter()->canSeeAll()->orderBy('last_name')->get();

            $headers = [
                'ID',
                'First name',
                'Middle name',
                'Last name',
                'Address',
                'City',
                'Postcode',
                'State',
                'Phone',
                'Phone2',
                'Email',
                'Notes',
                'User',
            ];

            $csv = [
                $headers
            ];

            $customers->each(function ($customer) use (&$csv) {
                array_push($csv, [
                    $customer->id,
                    $customer->first_name,
                    $customer->middle_name,
                    $customer->last_name,
                    $customer->address,
                    $customer->city,
                    $customer->postcode,
                    $customer->state,
                    $customer->phone,
                    $customer->phone2,
                    $customer->email,
                    $customer->note->note ?? '',
                    $customer->user->name,
                ]);
            });

            $output = 'Customers-' . Carbon::now()->toDateTimeString() . '.csv';


            return response()->json(base64_encode((new ToCSV($csv, $output))->getCSV()), 201);
        } catch (\Exception $e) {
            return response()->json([], 422);
        }
    }
}
