<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use App\Company;
use App\User;
use App\Survey;
use Common;

class CompanyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	//$companies = Company::paginate(1);
        //return view('admin.company.index', compact('companies'));
        return view('admin.company.index');
    }

    public function getUsers()
    {
        $companies = Company::all();
        return DataTables::of($companies)
			->editColumn('fiscal_year', function ($company) {				
                $fiscal_year = date("M", strtotime($company->fiscal_year)); 
                return $fiscal_year;
            })
            ->addColumn('action', function($company){
                if (User::where('company_id', '=', $company->id)->exists() || Survey::where('company_id', '=', $company->id)->exists()) {
                    $msg = "'Delete operation failed since it has associated record(s).'";
                    return '<a href="'.action('admin\CompanyController@edit', $company['id']).'"><i class="fa fa-edit"></i></a>
                            <button onclick="return alert('.$msg.');" type="button"><i class="fa fa-trash"></i></button>';
                }else{
                    $msg = "'Do you want to delete this client?'";
                    return '<a href="'.action('admin\CompanyController@edit', $company['id']).'"><i class="fa fa-edit"></i></a>
                            <form onsubmit="return confirm('.$msg.');" style="display: inline-block;" action="'.action('admin\CompanyController@destroy', $company['id']).'" method="post">
                               '.csrf_field().'
                                <input name="_method" type="hidden" value="DELETE">
                                <button type="submit"><i class="fa fa-trash"></i></button>
                            </form>';
                }
            })->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $industryTypes = Common::industryTypes();
        return view('admin.company.create', compact('industryTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'name' => 'required|string|unique:companies,name',
            'stock_code' =>  'required|string',
            'industry_type' =>  'required|string',
            'fiscal_year' =>  'required|string',
            /*'first_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'phone.*' => 'nullable|numeric',
            'title' =>  'required|string',
            'department' =>  'required|string',
        ], [
            'phone.*.numeric' => 'The phone must be a number.',*/
        ]);
        
        $input = request()->all();
        //print_r($input);exit();
        $company_data = array(
                            'name' => $input['name'], 
                            'stock_code' => $input['stock_code'], 
                            'industry_type' => $input['industry_type'], 
                            'fiscal_year' => $input['fiscal_year']
                        );
        $company = Company::create($input);

        /*$user_data = array(
                            'first_name' => $input['first_name'], 
                            'last_name' => $input['last_name'], 
                            'email' => $input['email'], 
                            'password' => bcrypt($input['password'],
                            'email' => $input['email'],
                            'phone' => json_encode(array_values(array_filter($_POST['phone']))),
                            'title' => $input['title'],
                            'department' => $input['department'],
                            'language_preference' => $input['language_preference'],
                            'company_id' => $company->id
                        );
        $user = User::create($user_data);*/
        

        return redirect('admin/companies')->with('success','Client has been created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $industryTypes = Common::industryTypes();
        return view('admin.company.edit', compact('company', 'industryTypes'));
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
        $this->validate(request(), [
            'name' => 'required|string|unique:companies,name,'.$id,
            'stock_code' =>  'required|string',
            'industry_type' =>  'required|string',
            'fiscal_year' =>  'required|string',
        ]);
        
        $input = request()->all();
        Company::find($id)->update($input);
        return redirect('admin/companies')->with('success','Client has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        return redirect('admin/companies')->with('success','Client has been deleted.');
    }
}
