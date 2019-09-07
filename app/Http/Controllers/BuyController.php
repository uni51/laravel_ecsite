<?php

namespace App\Http\Controllers;

use App\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// BuyクラスとMailクラスを読み込む
use App\Mail\Buy;
use Illuminate\Support\Facades\Mail;

class BuyController extends Controller
{
    public function index()
    {
        $cartitems = CartItem::select('cart_items.*', 'items.name', 'items.amount')
            ->where('user_id', Auth::id())
            ->join('items', 'items.id','=','cart_items.item_id')
            ->get();
        $subtotal = 0;
        foreach($cartitems as $cartitem){
            $subtotal += $cartitem->amount * $cartitem->quantity;
        }
        return view('buy/index', ['cartitems' => $cartitems, 'subtotal' => $subtotal]);
    }

    public function store(Request $request)
    {
        if( $request->has('post') ){
            // MailクラスとBuyクラスを使ってメールを送信する
            Mail::to(Auth::user()->email)->send(new Buy());
            CartItem::where('user_id', Auth::id())->delete();
            return view('buy/complete');
        }
        // フォームのリクエスト情報をセッションに記録する
        $request->flash();
        return $this->index();
    }
}
