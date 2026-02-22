<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * 乘客註冊／登入／登出
 * 乘客以手機號碼為主要識別，不需填寫 email、姓名
 */
class AuthController extends Controller
{
    /**
     * 註冊頁
     */
    public function showRegisterForm()
    {
        if (Auth::check() && Auth::user()->role === 'passenger') {
            return redirect()->route('booking');
        }

        return view('passenger.register');
    }

    /**
     * 註冊（只需手機 + 密碼）
     * email 自動產生，name 自動為「乘客」
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^09\d{8}$/', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'phone.required' => '請輸入手機號碼。',
            'phone.regex' => '請輸入正確的 10 碼手機號碼（09 開頭）。',
            'phone.unique' => '此手機號碼已註冊。',
            'password.required' => '請設定密碼。',
            'password.min' => '密碼至少 6 個字元。',
            'password.confirmed' => '兩次密碼輸入不一致。',
        ]);

        $email = 'passenger_' . $validated['phone'] . '@taxi.local';

        User::create([
            'name' => '乘客-' . $validated['phone'],
            'email' => $email,
            'password' => $validated['password'],
            'phone' => $validated['phone'],
            'role' => 'passenger',
        ]);

        return redirect()->route('passenger.login')
            ->with('success', '註冊成功，請使用手機與密碼登入。');
    }

    /**
     * 登入頁
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'passenger') {
            return redirect()->route('booking');
        }

        return view('passenger.login');
    }

    /**
     * 登入（使用手機 + 密碼）
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'phone.required' => '請輸入手機號碼。',
            'password.required' => '請輸入密碼。',
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return back()->withErrors(['phone' => '手機號碼或密碼錯誤。'])->withInput($request->only('phone'));
        }

        if ($user->role !== 'passenger') {
            return back()->withErrors(['phone' => '此帳號非乘客身份，請使用司機登入。'])->withInput($request->only('phone'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('booking'));
    }

    /**
     * 登出
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('passenger.login');
    }
}
