<?php

namespace App\Http\Controllers;


use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\User;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;



class PostController extends Controller
{
    
    public function store( Request $request){
        
       $message = $request->input('message');
       $user_id = $request->input('flag');
       $name = $request->input('message.name');
       $current_user = User::where('user_tg_id', $user_id)->first();
     
       if($current_user){
           
            $current_user->count+=1;
            $current_user ->save();
            
       }else{
           
            $current_user = User::create([
                'name' => $name,
                'user_tg_id' => $user_id,
                'text' => $message
            ]);
            $current_user->save();
       }
       
       
        
       $messages = $request->session()->get('messages', [
        ['role' => 'system', 'content' => ' Answer as concisely as possible.']
        ]);
    
        $messages[] = ['role' => 'user', 'content' => $request->input('message')];

        $result = OpenAI::chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => $messages
        ]); 
    
    $message = $result->choices[0]->message->content;
    
    $messages[] = ['role' => 'assistant', 'content' => $message];
    
   
    

    $request->session()->put('messages', $messages);
    return redirect('/');
    }

}
