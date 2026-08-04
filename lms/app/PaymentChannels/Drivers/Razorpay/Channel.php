<?php

namespace App\PaymentChannels\Drivers\Razorpay;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;
    protected $api_key;
    protected $api_secret;


    protected array $credentialItems = [
        'api_key',
        'api_secret',
    ];


    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();
        $this->setCredentialItems($paymentChannel);
    }

    public function paymentRequest(Order $order)
    {
        $generalSettings = getGeneralSettings();
        $price = $this->makeAmountByCurrency($order->total_amount, $this->currency);

        $Html = '<form action="/payments/verify/Razorpay" method="get">
            <input type="hidden" name="order_id" value="' . $order->id . '">
            <script src="/assets/default/js/app.js"></script>
            <script src="https://checkout.razorpay.com/v1/checkout.js"
                    data-key="' . $this->api_key . '"
                    data-amount="' . (int)round($price * 100) . '"
                    data-buttontext="Pay Now"
                    data-description="Razorpay Payment"
                    data-currency="' . $this->currency . '"
                    data-image="' . $generalSettings['logo'] . '"
                    data-prefill.name="' . $order->user->full_name . '"
                    data-prefill.email="' . $order->user->email . '"
                    data-theme.color="#43d477">
            </script>
            <style>
                .razorpay-payment-button {
                    opacity: 0;
                    visibility: hidden;
                }
            </style>
            <script>
                $(document).ready(function() {
                    $(".razorpay-payment-button").trigger("click");
                })
            </script>
        </form>';

        echo $Html;
        return '';
    }

    private function makeCallbackUrl(Order $order)
    {
        return route('payment_verify', [
            'gateway' => 'Razorpay'
        ]);
    }

    public function verify(Request $request)
    {
        $input = $request->all();
        $orderId = $request->input('order_id');

        if (empty($orderId) or empty($input['razorpay_payment_id'])) {
            return null;
        }

        $order = Order::where('id', $orderId)
            ->with('user')
            ->first();

        if (empty($order)) {
            return null;
        }

        if ($order->status === Order::$paid) {
            return $order;
        }

        try {
            $api = new Api($this->api_key, $this->api_secret);
            $payment = $api->payment->fetch($input['razorpay_payment_id']);

            // Security check: Verify amount and currency match
            $price = $this->makeAmountByCurrency($order->total_amount, $this->currency);
            $orderAmountPaise = (int)round($price * 100);

            if ((int)$payment['amount'] !== $orderAmountPaise) {
                $order->update(['status' => Order::$fail]);
                return $order;
            }

            if (strtolower($payment['currency']) !== strtolower($this->currency)) {
                $order->update(['status' => Order::$fail]);
                return $order;
            }

            $status = 'failed';
            if ($payment['status'] === 'captured') {
                $status = 'captured';
            } elseif ($payment['status'] === 'authorized') {
                $response = $payment->capture(['amount' => $payment['amount']]);
                if ($response['status'] === 'captured') {
                    $status = 'captured';
                }
            }

            if ($status === 'captured') {
                $order->update(['status' => Order::$paying]);
            } else {
                $order->update(['status' => Order::$fail]);
            }

            $order->payment_method = Order::$paymentChannel;
            $order->save();

            return $order;

        } catch (\Exception $e) {
            $order->update(['status' => Order::$fail]);
            return $order;
        }
    }
}
