<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationModificationRequest;
use App\Http\Services\ReservationService;
use Illuminate\Http\JsonResponse;

class ReservationModificationController extends Controller
{
    protected ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
        $this->middleware(middleware: ['auth:sanctum', 'isApproved', 'setLocale']);

    }

    /**
     * إرسال طلب تعديل
     */
    public function requestModification(ReservationModificationRequest $request, int $id): JsonResponse
    {
        $modification = $this->reservationService->requestModification($id, $request->validated());

        return response()->json([
            'message' => __('notifications.reservation_modify_request'),
            'modification' => $modification,
        ]);
    }

    /**
     * قبول طلب تعديل
     */
    public function acceptModification(int $modificationId): JsonResponse
    {
        $modification = $this->reservationService->acceptModification($modificationId);

        return response()->json([
            'message' => __('notifications.reservation_modify_accepted'),
            'modification' => $modification,
        ]);
    }

    /**
     * رفض طلب تعديل
     */
    public function rejectModification(int $modificationId): JsonResponse
    {
        $modification = $this->reservationService->rejectModification($modificationId);

        return response()->json([
            'message' => __('notifications.reservation_modify_rejected'),
            'modification' => $modification,
        ]);
    }
}
