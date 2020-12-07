<?php

namespace App\Console\Commands;

use App\chat;
use App\Conversation;
use App\Services\Helper;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use PHPSocketIO\SocketIO;
use Workerman\Worker;

class sockets extends Command
{

    protected $signature = 'start';


    protected $description = 'web sockets service';

    protected $currentUser;

    protected $users = array();

    public function __construct()
    {
        parent::__construct();
    }

        public function handle()
    {

        $io = new SocketIO(8080);
        $io->on('connection', function($socket)use($io){
            $io->emit('connected', 'connected: true');

            $socket->on('join room', function($data)use($io, $socket){
                $currentUser = User::where('token', $data['user_token'])->first();
                if(!$currentUser){
                    echo "user not found \n";
                    return $io->emit('user not found', 'user not found');
                }
                $currentUser->socket_id = $socket->id;
                $currentUser->save();
                echo "join room \n";
                Auth::loginUsingId($currentUser->id);
//                $this->users[$currentUser->id] = $data['socket_id'];
                echo $currentUser->socket_id."     db socket id     "."current user id   ".$currentUser->id."\n";
                echo "joined \n";
//                print_r($this->users);
            });


            $socket->on('send message', function($data)use($io,$socket){
                echo "event send message \n\n";
                $sender = User::where('token', $data['sender_token'])->first();
                $receiver = User::where('id', $data['receiver_id'])->first();
                if(!$sender){
                    return $io->emit('user not found', 'user not found');
                }
                if(!$receiver){
                    return $io->emit('user not found', 'user not found or no more active');
                }
                echo $sender->id."---sender id \n";

                $conversation = Conversation::where(['sender_id'=> $sender->id, 'receiver_id' => $data['receiver_id']])->orWhere( function($u) use ($sender, $data) {
                    $u->where(['receiver_id'=> $sender->id, 'sender_id' => $data['receiver_id'] ]);
                })->first();

                if(!$conversation){
                    echo "create new conversation \n";
                    $conversation = new Conversation();
                    $conversation->sender_id = $sender->id;
                    $conversation->receiver_id = $data['receiver_id'];
                    $conversation->save();
                }
                echo $conversation->id."---conversation id \n";
                $chat = new chat();
                $chat->sender_id = $sender->id;
                $chat->receiver_id = $data['receiver_id'];
                $chat->conversation_id = $conversation->id;
                $chat->msg = $data['message'];
                $chat->save();
                echo "chat saved \n";
                $o = [
                    'message' => $data['message'],
                    'id' => $chat->id,
                    'time' => $chat->created_at,
                    'conversation_id' => $conversation->id,
                    'user_type' => 'sender',
                    'username' => $sender->first_name.' '.$sender->last_name,
                    'userpic' => asset('/project-assets/images/'.$sender->profile_image),
                ];

                echo $sender->socket_id."   db sender socket id \n";
                echo $socket->id."   socket id \n";
                $io->to((string) $sender->socket_id)->emit('new message', $o);
                $io->to((string) $receiver->socket_id)->emit('new message', $o);

//                $io->to($sender->sockect_id)->emit('new message', $o);
//                    $io->to($this->users[$sender->id])->emit('new message', $o);
//                    echo $this->users[$sender->id]. "   emit to sender id \n";
//                    if(array_key_exists($data['receiver_id'], $this->users)){
//                        echo $this->users[$data['receiver_id']]. "   emit to receiver id \n";
//                        $io->to($this->users[$data['receiver_id']])->emit('new message', $o);
//                    }
                echo $data['message']."  -----message sent \n";

            });

//            $socket->on('typing', function($data)use($io){
//                if($data['typing']){
//                    $io->to($this->users[$data['receiver_id']])->emit('typing', true);
//                }else{
//                    $io->to($this->users[$data['receiver_id']])->emit('typing', false);
//                }
//            });


        });



        Worker::runAll();

    }
}
