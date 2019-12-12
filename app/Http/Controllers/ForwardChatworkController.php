<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Models\Condition;
use Illuminate\Http\Request;
use App\Services\ForwardChatworkService;
use App\Repositories\Interfaces\PayloadHistoryRepositoryInterface as PayloadHistoryRepository;
use App\Repositories\Interfaces\MessageHistoryRepositoryInterface as MessageHistoryRepository;

class ForwardChatworkController extends Controller
{
    protected $payloadHistoryRepository;
    protected $messageHistoryRepository;

    public function __construct(PayloadHistoryRepository $payHisRepo, MessageHistoryRepository $mesHisRepo)
    {
        $this->payloadHistoryRepository = $payHisRepo;
        $this->messageHistoryRepository = $mesHisRepo;
    }

    /**
    * @SWG\Post(
    *   path="api/v1/webhooks/{token}",
    *   summary="Sending payload to chatwork",
    *   operationId="store",
    *   tags={"Forward Chatwork"},
    *   security={
    *       {"ApiKeyAuth": {}}
    *   },
    *   @SWG\Parameter(
    *       name="token",
    *       in="path",
    *       required=true,
    *       type="string",
    *       description="Webhook token",
    *   ),
    *   @SWG\Parameter(
    *       name="params",
    *       in="formData",
    *       required=true,
    *       description="This is params you send to webhook",
    *       type="array",
    *           @SWG\Items(
    *               @SWG\Property(
    *                   property="clientId",
    *                   type="integer",
    *                   description="passing clientId from headers", 
    *                   example=1 
    *               ),        
    *               @SWG\Property(   
    *                   property="accessToken",
    *                   type="string",
    *                   description="passing accessToken from headers", 
    *                   example="tyedgfhjgu" 
    *               )
    *           )
    *   ),
    *   @SWG\Response(response=200, description="successful operation")
    * )
    *
    */
    public function forwardMessage(Request $request, $token)
    {
        $params = json_decode(json_encode((object) $request->all()), false);
        $webhook = Webhook::enable()->where('token', $token)->first();

        if ($webhook) {
            $forwardChatworkService = new ForwardChatworkService(
                $webhook,
                $params,
                $this->payloadHistoryRepository,
                $this->messageHistoryRepository
            );

            $forwardChatworkService->call();

            return response()->json('Excuted successfully');
        }

        return response()->json('Webhook not found. Please try again');
    }
}
