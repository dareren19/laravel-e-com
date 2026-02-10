<?php

namespace App\Http\Controllers;


use App\Models\Order;

use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\View\View;

use Illuminate\Http\Request;
use App\Services\HitPayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;

use App\Http\Controllers\Concerns\HandlesCart;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;



class CheckoutController extends Controller
{
    use HandlesCart;
    public function __construct(private HitPayService $hitPayService ){

    }
    public function index(): RedirectResponse|View{
        $cart = session('cart', []);
        if(empty($cart)){
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        $cartItems = $this->getCartWithProducts($cart);
        $subtotal = $this->calculateSubTotal($cartItems);

        return view('Checkout', [
            "cartItems" => $cartItems,
            "subtotal" => $subtotal,
        ]);
    }
    public function process(Request $request): RedirectResponse|SymfonyResponse{
        $validate= $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            
        ]);

        $cart = session('cart', []);

        if(empty($cart)){
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        $cartItems = $this->getCartWithProducts($cart);
        $subtotal = $this->calculateSubTotal($cartItems);


        try{
            DB::beginTransaction();
            $order = Order::create([
                "order_number" => Order::generateOrderNumber(),
                "guest_name" => $validate['name'],
                "guest_email" => $validate['email'],
                "guest_phone" => $validate['phone'] ?? null,
                "shipping_address" => $validate['address'],
                "subtotal" => $subtotal,
                "total" => $subtotal,
                "currency" => 'PHP',
                "status" => "pending",
            ]);

            foreach($cartItems as $item){
                OrderItem::create([
                    "order_id" => $order->id,
                    "product_id" => $item['product']['id'],
                    "product_name" => $item['product']['name'],
                    "price" => $item['product']['price'],
                    "quantity" => $item['quantity'],
                    "subtotal" => $item['product']['price'] * $item['quantity'],
                ]);
            }
            $paymentUrl = $this->hitPayService->createPaymentRequest($order);

            if($paymentUrl){
                $order->update([
                    'hitpay_payment_request_id' => $this->hitPayService->getLastPaymentRequestId(),
                ]);
                DB::commit();
                session()->forget('cart');
                session()->save();

                return redirect()->away($paymentUrl);
            }
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to create payment, Please try again.');

        }catch(\Exception $e){
            DB::rollback();
            Log::error('Checkout error :' .$e->getMessage());
            return redirect()->back()->with('error', 'An error occured, Please try again.');
        }
    }
    public function success(Request $request) {
        $reference = $request->query('reference');
        return view("products/checkout/success", [
            "reference" => $reference,
        ]);
    }
    public function failed(Request $request) {
        $reference = $request->query('reference');
        return view("products/checkout/failed",['reference' => $reference,]);
    }
    public function webhook(Request $request): \Illuminate\Http\Response{
        $payload = $request->all();

        if(!$this->hitPayService->verifyWebhook($payload,$request->header('X-HITPAY-SIGNATURE', ""))){
            Log::warning('Invalid HitPay webhook signature');
            return response('Invalid Signature', 400);
        }
        $paymentRequestId = $payload['payment_request_id'] ?? null;
        $status = $payload['status'] ?? null;

        if(!$paymentRequestId || !$status){
            return response('Missing data', 400);
        }
        $order = Order::where('hitpay_payment_request_id', $paymentRequestId)->first();
        if(!$order){
            Log::warning('Order not found for payment request: '.$paymentRequestId);
            return response("Order not found", 400);
        }
        if($status === 'completed'){
            $order->update([
                "status" => 'paid',
                "hitpay_payment_id" => $payload['payment_id'] ?? null,
                "paid_at" => now(),
            ]);
        }elseif(in_array($status,["failed", "expired"])){
            $order->update([
                "status" => "failed",
            ]);

        }
        return response('OK', 200);

    }
}
