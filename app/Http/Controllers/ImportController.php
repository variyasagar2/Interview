<?php

namespace App\Http\Controllers;

use App\Imports\DataImport;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index(Request $request)
    {
        ini_set('memory_limit', '-1');
        $validator = Validator::make(
            [
                'excel' => $request->excel,
                'extension' => strtolower($request->excel->getClientOriginalExtension()),
            ],
            [
                'excel' => 'required',
                'extension' => 'required|in:xlsx,xls,csv',
            ]

        );
        if ($validator->fails()) {
//            dd();
            return redirect()->back()->withErrors($validator->errors());
        }
        Excel::import(new DataImport(), $request->file('excel')->store('temp'));
        dd($request->all());
    }
}
