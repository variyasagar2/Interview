<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\CommonFeeCollection;
use App\Models\CommonFeeCollectionHeadwise;
use App\Models\EntryMode;
use App\Models\FeeCategory;
use App\Models\FeeCollectionType;
use App\Models\FeeType;
use App\Models\FinancialTrans;
use App\Models\FinancialTransDetail;
use App\Models\Module;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DataImport implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */
    public $keys = [];
    public $module = [];

    public function __construct()
    {
        $modules = [
            ['module' => 'Academic', 'module_id' => '1'],
            ['module' => 'Academic Misc', 'module_id' => '11'],
            ['module' => 'Hostel', 'module_id' => '2'],
            ['module' => 'Hostel Misc', 'module_id' => '22'],
            ['module' => 'Transport', 'module_id' => '3'],
            ['module' => 'Transport Misc', 'module_id' => '33'],
        ];
        foreach ($modules as $m) {
            Module::firstOrCreate($m);
        }


        $this->module = $modules = [
            ['entry_mode_name' => "Due Amount", 'crdr' => 'D', 'entry_mode_id' => 1],
            ['entry_mode_name' => "Write Off Amount", 'crdr' => 'C', 'entry_mode_id' => 2],
            ['entry_mode_name' => "Scholarship Amount", 'crdr' => 'C', 'entry_mode_id' => 3],
            ['entry_mode_name' => "Reverse Concession Amount", 'crdr' => 'D', 'entry_mode_id' => 4],
            ['entry_mode_name' => "Concession Amount", 'crdr' => 'C', 'entry_mode_id' => 5],

            ['entry_mode_name' => "Paid Amount", 'crdr' => 'C', 'entry_mode_id' => 6],
            ['entry_mode_name' => "Adjusted Amount", 'crdr' => 'C', 'entry_mode_id' => 7],
            ['entry_mode_name' => "Refund Amount", 'crdr' => 'D', 'entry_mode_id' => 8],
            ['entry_mode_name' => "Fund TranCfer Amount", 'crdr' => 'C', 'entry_mode_id' => 9],
        ];
        foreach ($modules as $m) {
            EntryMode::firstOrCreate($m);
        }
    }

    public function startRow(): int
    {
        return 6;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (count($this->keys) == 0 && $row['0'] == 'Sr.') {
                $this->keys = $row->toarray();
                continue;
            } elseif (count($this->keys) != 0) {
                $rec = array_combine($this->keys, $row->toarray());
                $branchC = ['branch_name' => $rec['Faculty']];
                $branch = Branch::where($branchC)->first();
                if (empty($branch)) {
                    $branch = Branch::create($branchC);
                    $feecollectiontypes = [
                        ['collection_head' => 'Academic', 'collection_description' => 'Academic', 'branch_id' => $branch->id],
                        ['collection_head' => 'Academic Misc', 'collection_description' => 'Academic Misc', 'branch_id' => $branch->id],
                        ['collection_head' => 'Hostel', 'collection_description' => 'Hostel', 'branch_id' => $branch->id],
                        ['collection_head' => 'Hostel Misc', 'collection_description' => 'Hostel Misc', 'branch_id' => $branch->id],
                        ['collection_head' => 'Transport', 'collection_description' => 'Transport', 'branch_id' => $branch->id],
                        ['collection_head' => 'Transport Misc', 'collection_description' => 'Transport Misc', 'branch_id' => $branch->id],
                    ];
                    $feecollectiontypes = FeeCollectionType::insert($feecollectiontypes);

                }
                $feeCategory = FeeCategory::firstOrCreate(['fee_category' => $rec['Fee Category'], 'branch_id' => $branch->id,]);
                $academic = ["tuition fee", "library fee", 'exam fee'];
                $academicMice = ["ine eee", "exam paper"];
                $hostel = ["mess fee"];
                $category = strtolower($rec['Fee Head']);

                if (in_array($category, $academic)) {
                    $cond = ['branch_id' => $branch->id, 'collection_head' => 'Academic'];
                    $cond1 = ['module' => 'Academic'];
                } elseif (in_array($category, $academicMice)) {
                    $cond = ['branch_id' => $branch->id, 'collection_head' => 'Academic Misc'];
                    $cond1 = ['module' => 'Academic Misc'];
                } elseif (in_array($category, $hostel)) {
                    $cond = ['branch_id' => $branch->id, 'collection_head' => 'Hostel'];
                    $cond1 = ['module' => 'Hostel'];
                }
                $feecollectiontype = FeeCollectionType::where($cond)->first();

                $module = Module::where($cond1)->first();
                $FeeType = [
                    'branch_id' => $branch->id,
                    'fee_category_id' => $feeCategory->id,
                    'fname' => $rec['Fee Head'],
                    'fee_type_ledger' => $rec['Fee Head'],
                    'fee_collection_id' => $feecollectiontype->id,
                    'fee_head_type' => $module->module_id,

                ];
                $Feetype = FeeType::firstOrCreate($FeeType);

                foreach ($this->module as $ent) {
                    if ($rec[$ent['entry_mode_name']] != 0) {
                        if ($ent['entry_mode_id'] < 6) {
                            $finacialTran = FinancialTrans::firstOrCreate([
                                'module_id' => $module->module_id,
                                'adm_no' => $rec['Admno/UniqueId'],
                                'tran_date' => date('Y-m-d', strtotime($rec['Date'])),
                                'acad_year' => $rec['Academic Year'],
                                'entry_mode_id' =>$ent['entry_mode_id'],
                                'voucher_no' => $rec['Voucher No.'],
                                'tran_id' => $rec['Receipt No.'],
                                'branch_id' => $branch->id,
                                'type_of_consession' => 1
                            ]);
                            $finacialTranDetails = FinancialTransDetail::create([
                                'financial_trans_id' => $finacialTran->id,
                                'module_id' => $module->module_id,
                                'amount' => abs($rec[$ent['entry_mode_name']]),
                                'head_id' => $Feetype->id,
                                'crdr' => $rec[$ent['entry_mode_name']] > 0 ? "C" : 'D',
                                'branch_id' => $branch->id,
                                'head_name' => $rec['Fee Head']
                            ]);
                            $finacialTran->amount += $finacialTranDetails->amount;
                            $finacialTran->crdr = $finacialTran->amount > 0 ? "C" : 'D';
                            $finacialTran->save();
                        } else {
                            $CommonFeeCollection = CommonFeeCollection::firstOrCreate([

                                'module_id' => $module->module_id,
                                'adm_no' => $rec['Admno/UniqueId'],
                                'roll_no' => $rec['Roll No.'],
                                'branch_id' => $branch->id,
                                'tran_id' => $rec['Receipt No.'],
                                'acadamic_year' => $rec['Academic Year'],
                                'financial_year' => $rec['Academic Year'],
                                'display_receipt_no' => $rec['Receipt No.'],
                                'entry_mode_id' =>$ent['entry_mode_id'],
                                'paid_date' => date('Y-m-d', strtotime($rec['Date']))
                            ]);
                            $CommonFeeCollectionHeadwise = CommonFeeCollectionHeadwise::create([
                                'module_id' => $module->module_id,
                                'receipt_id' => $CommonFeeCollection->id,
                                'head_id' => $Feetype->id,
                                'head_name' => $rec['Fee Head'],
                                'branch_id' => $branch->id,
                                'amount' => abs($rec[$ent['entry_mode_name']]),
                            ]);
                            $CommonFeeCollection->amount += $CommonFeeCollectionHeadwise->amount;
                            $CommonFeeCollection->save();
                        }
                    }
                }

            }
        }
    }
}
