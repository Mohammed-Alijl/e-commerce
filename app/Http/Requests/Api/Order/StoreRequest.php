<?php

namespace App\Http\Requests\Api\Order;

use App\Http\Controllers\Api\Traits\Api_Response;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Exception;

class StoreRequest extends FormRequest
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
            $order = new Order();
            $order->user_id = auth('customer')->id();
            $order->product_id = $this->product_id;
            $order->color_id = $this->color_id;
            if ($this->filled('size_id'))
                $order->size_id = $this->size_id;
            $order->quantity = $this->quantity;
            $order->address_id = $this->address_id;
            $order->shippingType_id = $this->shippingType_id;
            $order->status_id = 1;
            if ($order->save()) {
                $product = Product::find($this->product_id);
                $product->quantity -= $this->quantity;
                $product->save();
                return $this->apiResponse(new OrderResource($order), 201, __('messages.order.create'));
            }
            return $this->apiResponse(null, 500, __('messages.failed'));
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
            'product_id' => 'required|numeric|exists:products,id',
            'color_id' => 'required|numeric|exists:colors,id',
            'size_id' => 'nullable|numeric|exists:sizes,id',
            'address_id' => 'required|numeric|exists:addresses,id',
            'shippingType_id' => 'required|numeric|exists:shipping_types,id',
            'quantity' => "required|numeric|min:1|max:" . Product::find($this->product_id)->quantity,
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => __('messages.order.product_id.required'),
            'product_id.numeric' => __('messages.order.product_id.numeric'),
            'product_id.exists' => __('messages.order.product_id.exists'),
            'color_id.required' => __('messages.order.color_id.required'),
            'color_id.numeric' => __('messages.order.color_id.numeric'),
            'color_id.exists' => __('messages.order.color_id.exists'),
            'size_id.numeric' => __('messages.order.size_id.numeric'),
            'size_id.exists' => __('messages.order.size_id.exists'),
            'address_id.required' => __('messages.order.address_id.required'),
            'address_id.numeric' => __('messages.order.address_id.numeric'),
            'address_id.exists' => __('messages.order.address_id.exists'),
            'shippingType_id.required' => __('messages.order.shippingType_id.required'),
            'shippingType_id.numeric' => __('messages.order.shippingType_id.numeric'),
            'shippingType_id.exists' => __('messages.order.shippingType.exists'),
            'quantity.required' => __('messages.order.quantity.required'),
            'quantity.numeric' => __('messages.order.quantity.numeric'),
            'quantity.min' => __('messages.order.quantity.min'),
            'quantity.max' => __('messages.order.quantity.max'),
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
