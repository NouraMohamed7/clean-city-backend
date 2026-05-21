<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AutoAssignmentService;
use App\Traits\ApiResponseTrait;

class AssignmentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private AutoAssignmentService $autoAssignment) {}

    public function autoAssignAll()
    {
        $count = $this->autoAssignment->assignAllPending();
        return $this->successResponse(['assigned_count' => $count], 'Auto-assignment completed');
    }
}
