<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Traits\ApiResponseTrait;
use App\Services\NotificationService;
use App\Services\LoggerService;
use App\Services\PaymentService;
use App\Services\PhonePeService;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponseTrait;

    protected $user;
    protected $notificationService;
    protected $paymentService;
    protected $phonePe;

    public function __construct(NotificationService $notificationService,PaymentService $paymentService, PhonePeService $phonePe)
    {
        $this->user = Auth::guard('sanctum')->user();
        $this->notificationService = $notificationService;
        $this->paymentService = $paymentService;
        $this->phonePe = $phonePe;
    }

    public function callAction($method, $parameters)
    {
        if (str_contains(static::class, 'App\Http\Controllers\Api') && !LoggerService::isAllowed($method)) {
            throw new NotFoundHttpException('Not Found.');
        }
        return parent::callAction($method, $parameters);
    }
}
