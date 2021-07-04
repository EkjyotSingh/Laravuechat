<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Events\MessageSend;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;

class MessageController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }
    public function user_list(){
        $users = User::latest()->where('id','!=',auth()->user()->id)->get();
        //dd($users);
        if(\Request::ajax()){

            $unreadIds = Message::select(\DB::raw('`from` as sender_id, count(`from`) as messages_count' ))
            ->where('to', auth()->id())
            ->where('read', false)
            ->where('type', 1)
            ->groupBy('from')
            ->get();
            $to=Message::select(\DB::raw('`to` as sender_id, max(id) as latest_msg' ))
            ->where('from', auth()->id())
            ->where('type', 0)
            ->groupBy('to');
            $latest_ms=Message::select(\DB::raw('`from` as sender_id, max(id) as latest_msg' ))
            ->where('to', auth()->id())
            ->where('type', 1)
            ->groupBy('from')
            ->unionall($to)
            ->get();
            
//dd($to->get());
        // add an unread key to each contact with the count of unread messages
        $user = $users->map(function($contact) use ($unreadIds , $latest_ms) {
            $contactUnread = $unreadIds->where('sender_id', $contact->id)->first();
            $r=$latest_ms->where('sender_id', $contact->id)->union($latest_ms->where('sender_id',auth()->user()->id)->first())->sortby('latest_msg')->last();
            $l_msg=$r ?Message::where('id',$r->latest_msg)->first() : null;
            $contact->latestMessage = $l_msg ? $l_msg->message : '';
            $contact->unread = $contactUnread ? $contactUnread->messages_count : 0;
            return $contact;
            
        });
        //dd($user);
        return response()->json($user,200);
    }
        return abort(404);
    }
    public function user_message($id=null){
        if(!\Request::ajax()){
           return abort(404);
        }

        $user = User::findOrFail($id);
       $messages = $this-> message_by_user_id($id);
       Message::where('from', $id)->where('to', auth()->id())->update(['read' => true]);

        return response()->json([
            'messages'=>$messages,
            'user'=>$user,
        ]);
    }
    public function send_message(Request $request){
        if(!$request->ajax()){
            abort(404);
        }
       $messages = Message::create([
           'message'=>$request->message,
           'from'=>auth()->user()->id,
           'to'=>$request->user_id,
           'type'=>0
       ]);
       $messages = Message::create([
        'message'=>$request->message,
        'from'=>auth()->user()->id,
        'to'=>$request->user_id,
        'type'=>1
    ]);
    try{
       broadcast(new MessageSend($messages));
       return response()->json($messages,201);
    }
    catch(RequestException $e){
echo $e;
    }
    }
    public function delete_single_message($id=null){
        if(!\Request::ajax()){
          return  abort(404);
        }
        Message::findOrFail($id)->delete();
        return response()->json('deleted',200);
    }
    public function delete_all_message($id=null){
      $messages =  $this->message_by_user_id($id);
        foreach ($messages as $value) {
          Message::findOrFail($value->id)->delete();
        }
        return response()->json('all deleted',200);
    }
    public function message_by_user_id($id){
        $messages = Message::where(function($q) use($id){
            $q->where('from',auth()->user()->id);
            $q->where('to',$id);
            $q->where('type',0);
        })->orWhere(function($q) use ($id){
            $q->where('from',$id);
            $q->where('to',auth()->user()->id);
            $q->where('type',1);
        })->with('user')->get(); 
        return $messages;
    }
}
