<?php

namespace App\Http\Requests\Api\CartItem;

use App\Http\Controllers\Api\Traits\Api_Response;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Exception;

class CheckoutRequest extends FormRequest
{
    use Api_Response;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('customer')->check() && auth('customer')->user()->tokenCan('customer');
    }

    public function run()
    {
        try {
            $items = auth('customer')->user()->cart->cartItems;
            if (count($items) < 1)
                return $this->apiResponse(['success' => false], 422, 'Your cart is empty');
            foreach ($items as $item) {
                $order = new Order();
                $order->user_id = auth('customer')->id();
                $order->product_id = $item->product_id;
                $order->color_id = $item->color_id;
                $order->size_id = $item->size_id;
                $order->address_id = $this->address_id;
                $order->shippingType_id = $this->shippingType_id;
                $order->quantity = $item->quantity;
                $order->status_id = 1;
                if ($order->save()) {
                    $product = Product::find($item->product_id);
                    $product->quantity -= $item->quantity;
                    $product->save();
                    $item->delete();
                }
            }
            return $this->apiResponse(['success' => true], 200, 'checkout was successes');
        } catch (Exception $ex) {
            return $this->apiResponse(null, 500, $ex->getMessage());
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'address_id' => 'required|numeric|exists:addresses,id',
            'shippingType_id' => 'required|numeric|exists:shipping_types,id'
        ];
    }

    public function messages()
    {
        return[
          'address_id.required'=>__('messages.CartItem.checkout.address_id.required'),
          'address_id.numeric'=>__('messages.CartItem.checkout.address_id.numeric'),
          'address_id.exists'=>__('messages.CartItem.checkout.address_id.exists'),
          'shippingType_id.required'=>__('messages.CartItem.checkout.shippingType_id.required'),
          'shippingType_id.numeric'=>__('messages.CartItem.checkout.shippingType_id.numeric'),
          'shippingType_id.exists'=>__('messages.CartItem.checkout.shippingType_id.exists'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->apiResponse(null, 422, $validator->errors()));
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException($this->apiResponse(null, 401, __('messages.authorization')));
    }
}
