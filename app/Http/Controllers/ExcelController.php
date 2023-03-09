<?php


namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller

{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {
        $data = DB::table('users')->orderBy('id', 'DESC')->paginate(5);
        return view('excel.upload', compact('data'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    function importData(Request $request)
    {
        $this->validate($request, [
            'uploaded_file' => 'required|file|mimes:xls,xlsx'
        ]);
        Excel::import(new UsersImport, $request->file('uploaded_file'));

        return redirect('/')->with('success', 'All good!');
    }

    /**
     * @param $customer_data
     */
    public function ExportExcel($customer_data)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($customer_data);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Customer_ExportedData.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    /**
     *This function loads the customer data from the database then converts it
     * into an Array that will be exported to Excel
     */
    function exportData()
    {
        $data = DB::table('tbl_customer')->orderBy('CustomerID', 'DESC')->get();
        $data_array [] = array("CustomerName", "Gender", "Address", "City", "PostalCode", "Country");
        foreach ($data as $data_item) {
            $data_array[] = array(
                'CustomerName' => $data_item->CustomerName,
                'Gender' => $data_item->Gender,
                'Address' => $data_item->Address,
                'City' => $data_item->City,
                'PostalCode' => $data_item->PostalCode,
                'Country' => $data_item->Country
            );
        }
        $this->ExportExcel($data_array);
    }
}
