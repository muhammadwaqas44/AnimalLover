<?php

namespace App\Http\Controllers\ApiController;

use App\chat;
use App\Conversation;
use App\Http\Controllers\Controller;
use App\Services\Helper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function getConversations(Request $request)
    {

        $currentUser = User::where('token', $request->token)->first();
        if(!$currentUser){
            return Helper::jsonResponse(0, '', [], 'User not found');
        }
        $conversations = Conversation::where('sender_id', $currentUser->id)->orWhere('receiver_id', $currentUser->id)->with(['last_message.receiver','last_message.sender'])->get();
//        dd($conversations);

//        $conversation = Conversation::where(['sender_id'=> $currentUser->id])->orWhere( function($u) use ($currentUser) {
//            $u->where(['receiver_id'=> $currentUser->id ]);
//        })->first();

        if(!$conversations){
            return Helper::jsonResponse(1, '', [], 'No conversation found');
        }

        $data = [];
        foreach ($conversations as $element) {
            $data[] = $this->conversationStructure($element);
        }
        return Helper::jsonResponse(1, 'conversations',$data , '');
    }

    public function getChat(Request $request){
        $errorMessage = [
            'conversation_id.required' => 'Conversation id is required'
        ];
        $validator = Validator::make($request->all(),
            [
                'conversation_id' => 'required|numeric',
            ], $errorMessage
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }

        $chat = chat::where('conversation_id', $request->conversation_id)->with('sender', 'receiver')->orderBy('id', 'DESC')->get();
        $data = [];
        foreach ($chat as $element) {
            $data[] = $this->chatStructure($element);
        }

        return Helper::jsonResponse(1, 'chat is here',['length' => $chat->count(),'chat' => $data ] , '');
    }

    public function conversationStructure($element){
        $sender_name = '';
        $sender_id = '';
        $sender_pic = '';
        if($element->last_message->sender_id == Auth::user()->id){
            if(!empty($element->last_message->receiver->first_name)){
                $sender_name = $element->last_message->receiver->first_name;
            }
//            $sender_name = $element->last_message->receiver->first_name.' '.$element->last_message->receiver->last_name;

            $sender_id = $element->last_message->receiver_id;
            if(!empty($element->last_message->receiver->profile_image)){
                $sender_pic = asset('/project-assets/images/'.$element->last_message->receiver->profile_image);
            }

        }
        if($element->last_message->receiver_id == Auth::user()->id){
            if(!empty($element->last_message->receiver->first_name)){
                $sender_name = $element->last_message->sender->first_name;
            }
//            $sender_name = $element->last_message->sender->first_name.' '.$element->last_message->sender->last_name;

            $sender_id = $element->last_message->sender_id;
            if(!empty($element->last_message->sender->profile_image)){
                $sender_pic = asset('/project-assets/images/'.$element->last_message->sender->profile_image);
            }

        }
        return [
            'last_message' => $element->last_message->msg,
            'last_message_time' => $element->last_message->created_at,
            'conversation_id' => $element->id,
            'chat_started_at' => $element->created_at,
            'user_id' => $sender_id,
            'user_pic' => $sender_pic,
            'user_name' => $sender_name,
        ];
    }

    public function chatStructure($element){
        $name = '';
        $pic = '';
        if(!empty($element->sender->first_name)){
            if(!empty($element->sender->last_name)){
                $name = $element->sender->first_name.' '.$element->sender->last_name;
            }else{
                $name = $element->sender->first_name;
            }
        }
        if(!empty($element->sender->profile_image)){
            $pic = asset('/project-assets/images/'.$element->sender->profile_image);
        }

        if($element->sender_id == Auth::user()->id){
            return [
                'message' => $element->msg,
                'id' => $element->id,
                'time' => $element->created_at,
                'conversation_id' => $element->conversation_id,
                'user_type' => 'sender',
                'username' => $name,
                'userpic' => $pic,
            ];
        }else{
            return [
                'message' => $element->msg,
                'id' => $element->id,
                'time' => $element->created_at,
                'conversation_id' => $element->conversation_id,
                'user_type' => 'receiver',
                'username' => $name,
                'userpic' => $pic,
            ];
        }
    }
}

