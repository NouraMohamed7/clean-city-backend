<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CleanRouteService;
use App\Services\CompanyService;
use App\Services\AutoAssignmentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private CompanyService $companyService,
        private CleanRouteService $routeService,
        private AutoAssignmentService $autoAssignment
    ) {}

    public function index()
    {
        $companies = Company::with('city')->where('is_active', true)->get();
        return $this->successResponse($companies, 'Companies retrieved');
    }

    public function show(Company $company)
    {
        $company->load(['city', 'reports' => fn($q) => $q->latest()->limit(10)]);
        return $this->successResponse($company, 'Company retrieved');
    }

    public function myReports(Request $request)
    {
        $company = $request->user()->company;
        $reports = $this->companyService->getActiveReports($company);
        return $this->successResponse($reports, 'Company reports retrieved');
    }

    public function stats(Request $request, Company $company)
    {
        $stats = $this->companyService->getPerformance($company);
        return $this->successResponse($stats, 'Company stats retrieved');
    }

    public function route(Request $request, Company $company)
    {
        $route = $this->routeService->calculate($company);
        return $this->successResponse($route, 'Route calculated');
    }

    public function autoAssignAll()
    {
        $count = $this->autoAssignment->assignAllPending();
        return $this->successResponse(['assigned_count' => $count], 'Auto-assignment completed');
    }
}
