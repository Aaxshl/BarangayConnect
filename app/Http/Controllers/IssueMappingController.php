<?php
namespace App\Http\Controllers;
use App\Models\CitizenRequest;

class IssueMappingController extends Controller {
    public function index() {
        return view('admin.mapping.index');
    }
    public function data() {
        $issues = CitizenRequest::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotIn('status',['closed'])
            ->get(['id','request_type','description','location','latitude','longitude','status','created_at']);
        return response()->json($issues);
    }
}
