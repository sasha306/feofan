?php

namespace App\Helpers;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;



class TestCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "test";

    /**
     * @var string Command Description
     */
    protected $description = "Test Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $this->replyWithMessage(['text' => 'Hello! Welcome to our bot, Here are our available commands:']);
        
       
    }
}