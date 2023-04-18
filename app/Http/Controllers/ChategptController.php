<?php

namespace App\Http\Controllers;

use App\Helpers\Telegram;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;



class ChategptController extends Controller
{
        
    public function index( $request, Telegram  $telegram){
        
       $message = $request->input('message.text');
       $user_id = $request->input('message.from.id');
       
        
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
    }
}
