<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyAuthenticationOptionsAction;
use Spatie\LaravelPasskeys\Http\Controllers\AuthenticateUsingPasskeyController;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;


class PasskeyController extends Controller
{
    public function registerOptions(Request $request)
    {
        Log::info('PASSKEY: registerOptions hit', [
            'user_id' => $request->user()?->id,
            'user_email' => $request->user()?->email,
            'name' => $request->input('name'),
        ]);


        $user = $request->user();
        $action = app(GeneratePasskeyRegisterOptionsAction::class);
        $options = $action->execute($user);


        Log::info('PASSKEY: registerOptions generated', [
            'user_id' => $user?->id,
            'options_length' => strlen($options),
        ]);


        session()->put('passkey-registration-options', $options);

        return response()->json(json_decode($options, true));
    }


    public function store(Request $request)
    {
        Log::info('PASSKEY: store hit', [
            'user_id' => $request->user()?->id,
            'user_email' => $request->user()?->email,
            'request_keys' => array_keys($request->all()),
            'name' => $request->input('name'),
        ]);


        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'passkey' => ['required', 'array'],
        ]);


        $user = $request->user();
        $action = app(StorePasskeyAction::class);
        $options = session()->pull('passkey-registration-options');


        Log::info('PASSKEY: store before execute', [
            'user_id' => $user?->id,
            'has_session_options' => !empty($options),
            'passkey_keys' => array_keys($request->input('passkey', [])),
        ]);


        try {
            $action->execute(
                $user,
                json_encode($request->input('passkey')),
                $options,
                $request->getHost(),
                ['name' => $request->input('name', 'My Passkey')]
            );


            Log::info('PASSKEY: stored successfully', [
                'user_id' => $user?->id,
                'name' => $request->input('name', 'My Passkey'),
            ]);


            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('PASSKEY: store failed', [
                'user_id' => $user?->id,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);


            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        }
    }


    public function destroy(Request $request, $id)
    {
        Log::info('PASSKEY: destroy hit', [
            'user_id' => $request->user()?->id,
            'passkey_id' => $id,
        ]);


        $request->user()->passkeys()->whereKey($id)->delete();


        Log::info('PASSKEY: deleted', [
            'user_id' => $request->user()?->id,
            'passkey_id' => $id,
        ]);


        return back()->with('success', 'Passkey deleted.');
    }
}
