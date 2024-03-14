<?php

namespace App\Exceptions;

use App\Resource\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiException extends Exception
{
    /**
     * The status code for the exception.
     *
     * @var int
     */
    protected int $statusCode;
    private array $data;

    /**
     * Create a new ApiException instance.
     *
     * @param string $message
     * @param int    $statusCode
     * @return void
     */
    public function __construct(string $message, $data = [], $statusCode = 500,)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->data       = $data;
    }

//    public function report()
//    {
//        Mail::to(Constant::DEVELOPER_EMAIL)
//            ->send(new OrderSyncFailedMail($this->message));
//    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function render($request)
    {
        // Log the exception for debugging purposes
        Log::error($this->getMessage(), [
            'trace' => $this->getTraceAsString(),
        ]);

        return ApiResponse::make($this->data, $this->getMessage(), $this->statusCode);
    }
}
