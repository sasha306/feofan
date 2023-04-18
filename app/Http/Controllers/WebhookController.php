<?php

namespace App\Http\Controllers;

use App\Helpers\Telegram;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\User;
use RuliLG\StableDiffusion\StableDiffusion;
use RuliLG\StableDiffusion\Prompt;

class WebhookController extends Controller
{
        
    public function index( Request $request, Telegram  $telegram){
        
       $message = $request->input('message.text');
       $user_id = $request->input('message.from.id');
       $name = $request->input('message.from.first_name');
       $current_user = User::where('user_tg_id', $user_id)->first();
       $dashboard = [
            'keyboard' =>
                [
                    [
                        [
                            'text' => 'Поговорить с дедом',
                            'callback_data' => 'GPT'
                        ],
                        [
                            'text' => 'Использовать DALL·E 2 ',
                            'callback_data' => 'DALL·E 2 '
                        ],
                        [
                            'text' => 'Использовать Stable Diffusion',
                            'callback_data' => 'SDiffusion'                        ],
                    ]
                ],
            'resize_keyboard' => TRUE,
            
            ];
     
       if($current_user){
           
            $current_user->count+=1;
            $current_user ->save();
            
       }else{
            $current_user = User::create([
                'name' => $name,
                'user_tg_id' => $user_id,
                'count' => 1,
            ]);
            $current_user->save();
       }
       
        switch ($message) { 
            case "/start":            
                $start_text = "Привет, я Дед Феофан, выбери из предложенного на клавиатуре что ты хочешь сделать.";
                $telegram->sendButtons($user_id, $start_text, $dashboard);
            break;

            case "/help":
                $telegram->sendMessage($user_id, 'Тут будет список команд');
            break;

            case "/clear":
                $telegram->sendMessage($user_id, 'Очистить историю разговора');
            break;
        }
       
        if($current_user->use_neural_network){
           
            switch ($current_user->use_neural_network) { 
            case "Stable Diffusion":
                $this->SDiffusion($message, $user_id, $telegram, $current_user, $dashboard );
            break;
            
            case "chateGPT":
                $this->chatGPT($message, $user_id, $request, $telegram, $current_user, $dashboard);
            break;
            
            case "DALL·E 2":
                $this->dall_E($message, $user_id, $telegram, $current_user, $dashboard );
            break;
        }
           
       }else{
           
        switch ($message) { 
            case "Использовать Stable Diffusion":
            $telegram->sendMessage($user_id, 'Вы используете Stable Diffusion, опишите в сообщении, что нужно нарисовать. Для описания ипользуйте английский язык.'); 
            $current_user->use_neural_network = 'Stable Diffusion';
            $current_user->save();
            break;
            case "Поговорить с дедом": 
            $telegram->sendMessage($user_id, 'Вы используете chatGPT, задайте Ваш вопрос'); 
            $current_user->use_neural_network = 'chateGPT';
            $current_user->save();
            break;
            case "Использовать DALL·E 2":
            $telegram->sendMessage($user_id, 'Вы используете DALL·E 2, опишите в сообщении, что нужно нарисовать'); 
            $current_user->use_neural_network = 'DALL·E 2';
            $current_user->save();    
            break;
        }
       }
            
    }

    public function SDiffusion($message, $user_id, $telegram, $current_user, $dashboard){
        
        if($message == 'Главное меню'){
           
           $current_user->use_neural_network = '';
           $current_user->save();
          
       
           $telegram->sendButtons(
               $user_id,
               "Чем еще займемся?",
               $dashboard
               );
       }else {
       
        $button_back = [
            'keyboard' =>
                [
                    [
                        [
                            'text' => 'Главное меню',
                            'callback_data' => 'GPT'
                        ],
                        
                    ]
                ],
            'resize_keyboard' => TRUE,
            
            ]; 
          $telegram->sendButtons(
               $user_id,
               "Сейчас дед будет рисовать",
               $button_back
               );

        $result = StableDiffusion::make()
            ->withPrompt(
                Prompt::make()
                    ->with($message)
                    ->photograph()
                    ->resolution8k()
                    ->trendingOnArtStation()
                    ->highlyDetailed()
                    ->dramaticLighting()
                    ->octaneRender()
            )
            ->generate(1);
    
        $is_successful = null;
        $number_of_requests = 0;

        do {
            sleep(3);
            $freshResults = StableDiffusion::get($result->replicate_id); 
            $is_successful = $freshResults->is_successful;
            $number_of_requests++;       
        } while (!$is_successful && ($number_of_requests < 10));

    

        if ($is_successful) {
            $telegram->sendPhoto($user_id,
                                 $freshResults->output[0], 
                                 'Дед может StableDiffusion'
                        );
        } else {
            $telegram->sendMessage($user_id,
                                  'Дед не смог и устал :('
                        );
        }
       }
    }

    public function chatGPT($message, $user_id, $request, $telegram, $current_user, $dashboard){  
        
       if($message == 'Главное меню'){
           
           $current_user->use_neural_network = '';
           $current_user->save();
           
       
           $telegram->sendButtons(
               $user_id,
               "Может порисуем?",
               $dashboard
               );
       }else {
       
        $button_back = [
            'keyboard' =>
                [
                    [
                        [
                            'text' => 'Главное меню',
                            'callback_data' => 'GPT'
                        ],
                        
                    ]
                ],
            'resize_keyboard' => TRUE,
            
            ]; 
          $telegram->sendButtons(
               $user_id,
               "Дедушка старый дай время подумать",
               $button_back
               );
        
        $messages = $request->session()->get('messages', [
            ['role' => 'system', 'content' => ' Answer as concisely as possible.']
        ]);    
        $messages[] = ['role' => 'user', 'content' => $message];

        
        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages
            ]);
       
        $telegram->sendMessage(
            $user_id,
            $result->choices[0]->message->content 
            );
       }
      
    }

    public function dall_E($message, $user_id, $telegram, $current_user, $dashboard){
        
         
       if($message == 'Главное меню'){
           
           $current_user->use_neural_network = '';
           $current_user->save();
           
       
           $telegram->sendButtons(
               $user_id,
               "Чем еще займемся?",
               $dashboard
               );
       }else {
       
        $button_back = [
            'keyboard' =>
                [
                    [
                        [
                            'text' => 'Главное меню',
                            'callback_data' => 'GPT'
                        ],
                        
                    ]
                ],
            'resize_keyboard' => TRUE,
            
            ]; 
          $telegram->sendButtons(
               $user_id,
               "Сейчас нарисую",
               $button_back
               );
        
        $result = OpenAI::images()->create([
        'prompt' => $message,
        'n' => 1,
        'size' => '512x512',
        'response_format' => 'url',
    ]);

    $telegram->sendPhoto($user_id, $result["data"][0]["url"], 'Дед может DALL·E 2  ');
       }
}
}
