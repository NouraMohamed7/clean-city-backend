<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Report;
use App\Models\Company;
use App\Models\City;
use App\Models\Category;
use App\Services\AnalyticsService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private AnalyticsService $analytics) {}

    public function dashboard()
    {
        $stats = $this->analytics->getAdminStats();
        return $this->successResponse($stats, 'Dashboard stats retrieved');
    }

    public function reports(Request $request)
    {
        $query = Report::with(['user', 'city', 'category', 'assignedCompany']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->paginate($request->per_page ?? 20);
        return $this->paginatedResponse($reports, 'All reports retrieved');
    }

    public function users(Request $request)
    {
        $query = User::with('city')->where('role', 'user');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $users = $query->latest()->paginate($request->per_page ?? 20);
        return $this->paginatedResponse($users, 'Users retrieved');
    }

    public function toggleBan(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return $this->successResponse($user, 'User status updated');
    }

    public function companies()
    {
        $companies = Company::with('city')->latest()->get();
        return $this->successResponse($companies, 'Companies retrieved');
    }

    public function storeCompany(Request $request)
    {
        $company = Company::create($request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'email' => 'required|email|unique:companies',
            'phone' => 'nullable|string',
        ]));

        return $this->successResponse($company, 'Company created', 201);
    }

    public function analytics()
    {
        return $this->successResponse([
            'trend' => $this->analytics->getReportsTrend(),
            'by_city' => $this->analytics->getReportsByCity(),
            'by_severity' => $this->analytics->getReportsBySeverity(),
        ], 'Analytics retrieved');
    }

    public function cities()
    {
        return $this->successResponse(City::all(), 'Cities retrieved');
    }

    public function categories()
    {
        return $this->successResponse(Category::all(), 'Categories retrieved');
    }
}
