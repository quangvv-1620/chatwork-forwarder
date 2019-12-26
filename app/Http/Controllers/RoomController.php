<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use Illuminate\Http\Request;

use SunAsterisk\Chatwork\Chatwork;
use SunAsterisk\Chatwork\Exceptions\APIException;
use App\Repositories\Interfaces\ChatworkRepositoryInterface as ChatworkRepository;

class RoomController extends Controller
{
    private $chatworkRepository;

    public function __construct(ChatworkRepository $chatworkRepository)
    {
        $this->chatworkRepository = $chatworkRepository;
    }

    /**
     * Return a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $bot = Bot::findOrFail($request->bot_id);
            $chatwork = Chatwork::withAPIToken($bot->bot_key);
            $rooms = $this->chatworkRepository->getRooms($chatwork);

            return $rooms;
        } catch (APIException $e) {
            return [];
        }
    }
}
