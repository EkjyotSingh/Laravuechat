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

            /////For pgsql
            $unreadIds = Message::select(\DB::raw('"from" as sender_id, count("from") as messages_count' ))
            ->where('to', auth()->id())
            ->where('read', false)
            ->where('reciever', auth()->id())
            ->groupBy('from')
            ->get();
            $to=Message::select(\DB::raw('"to" as sender_id, max(id) as latest_msg' ))
            ->where('from', auth()->id())
            ->where('sender', auth()->id())
            ->groupBy('to');
            $latest_ms=Message::select(\DB::raw('"from" as sender_id, max(id) as latest_msg' ))
            ->where('to', auth()->id())
            ->where('reciever', auth()->id())
            ->groupBy('from')
            ->unionall($to)
            ->get();

            ////For mysql
            //$unreadIds = Message::select(\DB::raw('`from` as sender_id, count(`from`) as messages_count' ))
            //->where('to', auth()->id())
            //->where('read', false)
            //->where('reciever', auth()->id())
            //->groupBy('from')
            //->get();
            //$to=Message::select(\DB::raw('`to` as sender_id, max(id) as latest_msg' ))
            //->where('from', auth()->id())
            //->where('sender', auth()->id())
            //->groupBy('to');
            //$latest_ms=Message::select(\DB::raw('`from` as sender_id, max(id) as latest_msg' ))
            //->where('to', auth()->id())
            //->where('reciever', auth()->id())
            //->groupBy('from')
            //->unionall($to)
            //->get();
            
        // add an unread key to each contact with the count of unread messages
        $user = $users->map(function($contact) use ($unreadIds , $latest_ms) {
            $contactUnread = $unreadIds->where('sender_id', $contact->id)->first();
            $r=$latest_ms->where('sender_id', $contact->id)->union($latest_ms->where('sender_id',auth()->user()->id)->first())->sortby('latest_msg')->last();
            $l_msg=$r ?Message::where('id',$r->latest_msg)->first() : null;
            $contact->latestMessage = $l_msg ? $l_msg->message : '';
            $contact->latestMessageId = $l_msg ? $l_msg->id : '';
            $contact->unread = $contactUnread ? $contactUnread->messages_count : 0;
            return $contact;
            
        });
        return response()->json($user,200);
    }
        return abort(404);
    }
    public function user_message($id=null,$page=0,$limit=0){
        if(!\Request::ajax()){
           return abort(404);
        }

        $user = User::findOrFail($id);
       $messages = $this-> message_by_user_id($id ,$page,$limit);
    //   Message::where('from', $id)->where('to', auth()->id())->update(['read' => true]);
    $this->message_readed($id);

        return response()->json([
            'messages'=>$messages,
            'user'=>$user,
        ]);
    }
    function message_readed($id){
        Message::where('from', $id)->where('to', auth()->id())->update(['read' => true]);
    }
    public function send_message(Request $request){
        if(!$request->ajax()){
            abort(404);
        }
       $messages = Message::create([
           'message'=>$request->message,
           'from'=>auth()->user()->id,
           'to'=>$request->user_id,
            'sender' => auth()->user()->id,
            'reciever' => $request->user_id

       ]);
        $messages->read = 0;
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
        $d=Message::where(function($query) use($id){
                            $query->where('id',$id);
                            $query->where('from', auth()->user()->id);
                            $query->Where('sender', auth()->user()->id);
                        })->orWhere(function($query) use($id){
                            $query->where('id',$id);
                            $query->where('to',auth()->user()->id);
                            $query->where('reciever',auth()->user()->id);
                        })->first();

        if($d->from == auth()->user()->id){
            if($d->reciever == 0){
                $d->delete();
            }else{
                $d->update([
                    'sender' => 0,
                ]);
            }
        }else if($d->to == auth()->user()->id){
            if($d->sender == 0){
                $d->delete();
            }else{
                $d->update([
                    'reciever' => 0,
                ]);
            }
        }else{}
                            
        return response()->json('deleted',200);
    }

    public function delete_all_message($id=null){
        $messages =  $this->message_by_user_id($id,'delete','delete');
        foreach ($messages as $value) {

                if($value->from == auth()->user()->id){
                    if($value->reciever == 0){
                        $value->delete();
                    }else{
                        $value->update([
                            'sender' => 0,
                        ]);
                    }
                }else if($value->to == auth()->user()->id){
                    if($value->sender == 0){
                        $value->delete();
                    }else{
                        $value->update([
                            'reciever' => 0,
                        ]);
                    }
                }else{}

        }
        return response()->json('all deleted',200);
    }

    public function message_by_user_id($id,$page,$limit){
        if($page == 'delete'){
        }elseif($page == 0){
            
        }else{
            $offset = (int)$page * $limit;
        }
        $messages = Message::where(function($q) use($id){
            $q->where('from',auth()->user()->id);
            $q->where('to',$id);
            $q->where('sender',auth()->user()->id);
        })->orWhere(function($q) use ($id){
            $q->where('from',$id);
            $q->where('to',auth()->user()->id);
            $q->where('reciever',auth()->user()->id);
        })->with('user');

        if($page == 'delete'){
            $message= $messages->get();
            return $message;
        }elseif($page ==0){
            $messages->orderby('id','desc')->limit($limit);
            $message= $messages->get()->sortBy('id')->values();
            return $message;
        }else{
            $messages->orderby('id','desc')->offset($offset)->limit($limit);
            $message= $messages->get();
            return $message;
        }
    }
}
