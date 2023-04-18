<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Феофан</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
<div class="container">
    <h1 class="h1">Я Феофан! Что нужно, кожаный мешок?</h1>

    <form class="p-4 flex space-x-4 justify-center items-center" action="{{route('post.store')}}" method="post">
        @csrf
        <label for="message">Спрашивай с уважением:</label>
        <input id="message" type="text" name="message" autocomplete="off" class="border rounded-md  p-2 flex-1" />
        <label for="flag">Нарисовать -1</label>
        <input id="flag" type="text" name="flag" autocomplete="off" class="border rounded-md  p-2 flex-1" />
        <a class="bg-gray-800 text-white p-2 rounded-md" href="http://localhost/feofan2/public/">Перезапуск</a>
        <button type="submit">Отправить</button>
    </form>

    @foreach($messages as $message)
    <div class="flex rounded-lg p-4 @if ($message['role'] === 'assistant') bg-green-200 flex-reverse @else bg-blue-200 @endif ">
        <div class="ml-4">
            <div class="text-lg">
                @if ($message['role'] === 'assistant')
                    <a href="#" class="font-medium text-gray-900">Великий ФЕОФАН:</a>
                @else
                    <a href="#" class="font-medium text-gray-900">жалкий человечек:</a>
                @endif
            </div>
            <div class="mt-1">
                <p class="text-gray-600">
                    {!! \Illuminate\Mail\Markdown::parse($message['content']) !!}
                </p>
            </div>
        </div>
    </div>
    @endforeach
  </div>
</div>
</div>
</body>
</html>

</div>
</div>
</body>
</html>
