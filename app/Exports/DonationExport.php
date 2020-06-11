<?php

namespace App\Exports;

use App\ProjectOrder;
use App\ProjectPayment;
use Maatwebsite\Excel\Concerns\FromCollection;

class DonationExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
       return ProjectOrder::all();
    }
}
