<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListAssignmentsRequest;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Http\Resources\AssignmentCollection;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class AssignmentController extends Controller
{
    /** GET /courses/{course}/assignments?status=&page= */
    public function index(ListAssignmentsRequest $request, Course $course): AssignmentCollection
    {
        $assignments = $course->assignments()
            ->visibleTo($request->user())
            ->when($request->validated('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return new AssignmentCollection($assignments);
    }

    /** POST /assignments -> 201 */
public function store(StoreAssignmentRequest $request): JsonResponse
{
    $assignment = Assignment::create([
        ...$request->validated(),
        'created_by' => $request->user()->id,
    ]);

    return AssignmentResource::make($assignment)
        ->response()
        ->setStatusCode(201);
}

    /** PUT|PATCH /assignments/{assignment} -> 200 */
    public function update(UpdateAssignmentRequest $request, Assignment $assignment): AssignmentResource
    {
        $assignment->update($request->validated());

        return new AssignmentResource($assignment);
    }

    /** DELETE /assignments/{assignment} -> 204 */
    public function destroy(Assignment $assignment): Response
    {
        Gate::authorize('delete', $assignment);

        $assignment->delete();

        return response()->noContent();
    }
}