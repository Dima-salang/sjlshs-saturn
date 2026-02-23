<?php

namespace App\Http\Controllers;

use App\Http\Requests\QRCode\GenerateQRCodeRequest;
use App\Http\Resources\QRCodeResource;
use App\Services\QRService;
use Illuminate\Http\JsonResponse;

class QRCodeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected QRService $qrService
    ) {}

    /**
     * Store a newly generated QR code.
     */
    public function store(GenerateQRCodeRequest $request): JsonResponse
    {
        $qrCode = $this->qrService->generateQRCode($request->validated());

        return response()->json([
            'message' => 'QR Code generated successfully.',
            'data' => new QRCodeResource($qrCode),
        ], 201);
    }
}
