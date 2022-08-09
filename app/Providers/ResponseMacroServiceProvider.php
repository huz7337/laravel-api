<?php


namespace App\Providers;


use Illuminate\Support\Facades\Response;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response as ResponseFactory;

class ResponseMacroServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ResponseFactory::macro('success', fn($data, $statusCode = 200) => \response()->json($data, $statusCode));

        ResponseFactory::macro(
            'error',
            function (string $message, ?MessageBag $messageBag = null, int $statusCode = 500, ?array $payload = null) {
                $response = ['message' => $message];
                if (!is_null($messageBag) && $messageBag->any()) {
                    $errors = [];
                    foreach ($messageBag->messages() as $field => $details) {
                        array_push($errors, ['field' => $field, 'details' => $details]);
                    }

                    $response['errors'] = $errors;
                }

                if (null !== $payload) {
                    $response['payload'] = $payload;
                }

                return \response()->json($response, $statusCode);
            });
        ResponseFactory::macro(
            'errorMessage',
            fn(
                string $message,
                int $statusCode = 400,
                ?array $payload = null
            ) => \response()->error($message, null, $statusCode, $payload)
        );

        ResponseFactory::macro('validationError', function (string $message, array $errors) {
            $response = [
                'message' => $message,
                'errors' => $errors
            ];
            return \response()->json($response, 422);
        });

        ResponseFactory::macro('notFound', function () {
            return \response()->error(__('error_item_not_found'), null, 404);
        });

        ResponseFactory::macro('forbidden', function () {
            return \response()->error(
                __('You do not have the necessary permissions to access this resource'),
                null,
                403
            );
        });

        ResponseFactory::macro('empty', fn() => \response('', 204));

        ResponseFactory::macro(
            'message',
            function (string $message, int $statusCode = 200) {
                if ($statusCode >= 400) {
                    return \response()->errorMessage($message, $statusCode);
                }

                return \response()->success(['message' => $message], $statusCode);
            }
        );

    }
}
