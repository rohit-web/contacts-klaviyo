<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Http\Requests\CsvImportRequest;
use App\Jobs\KlaviyoUserAdd;
use App\User;
use App\Util\KlaviyoHelper;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ContactController extends Controller
{
    protected $klaviyoHelperObj;

    public function __construct(KlaviyoHelper $klaviyoHelperObj)
    {
        $this->klaviyoHelperObj = $klaviyoHelperObj;
    }

    public function contacts(Request $request)
    {
        if ($request->ajax()) {
            $data = Contact::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('created_by', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row->user_id == Auth::user()->id) {
                        $btn = '<a href="javascript:void(0)" class="edit btn btn-danger btn-sm">Delete</a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'created_by'])
                ->make(true);
        }

        return view('users');
    }

    public function store(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|unique:contacts|email',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),

            ));
        }

        $param['name'] = $request->get('full_name');
        $param['email'] = $request->get('email');
        $param['phone'] = $request->get('phone');
        $this->saveContact($param);

        return response()->json(['success' => 'Data is successfully added']);
    }

    private function saveContact($param)
    {
        if (!empty($param)) {
            KlaviyoUserAdd::dispatch($param, 'list/' . env('list_id') . '/members');

            $contactObj = new Contact();
            $contactObj->full_name = $param['name'];
            $contactObj->email = $param['email'];
            $contactObj->phone = $param['phone'];
            $contactObj->user_id = \Auth::user()->id;
            $contactObj->save();
            unset($contactObj);
        }

    }

    public function upload(CsvImportRequest $request)
    {
        try {
            $file = $request->file('csv_file');
            $location = 'uploads';

            // Upload file
            $file->move($location, $file->getClientOriginalName());

            // Import CSV to Database
            $filepath = public_path($location . "/" . $file->getClientOriginalName());
            $data = $this->csvToArrayAndCreateContact($filepath);
            return Redirect::back()->with('message', 'File is uploaded successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

    }

    private function csvToArrayAndCreateContact($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new \Exception("file is not exist", 500);
            return false;
        }

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            $n = 0;
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $existCount = Contact::where('email', $row[1])->count();
                    if ($existCount < 1) {
                        $param['name'] = $row[0];
                        $param['email'] = $row[1];
                        $param['phone'] = $row[2];
                        $this->saveContact($param);
                        \Log::error("Record has stored and creaed by API " . $n++);
                    }
                }

            }
            fclose($handle);
        }
    }
}
