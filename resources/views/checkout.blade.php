@extends('layouts.template')

@section('title','Giỏ hàng')

@section('content')
<style>
    .row {
  display: -ms-flexbox; /* IE10 */
  display: flex;
  -ms-flex-wrap: wrap; /* IE10 */
  flex-wrap: wrap;
  margin: 0 -16px;
}

.col-25 {
  -ms-flex: 35%; /* IE10 */
  flex: 35%;
}

.col-50 {
  -ms-flex: 50%; /* IE10 */
  flex: 50%;
}

.col-75 {
  -ms-flex: 65%; /* IE10 */
  flex: 65%;
}

.col-25,
.col-50,
.col-75 {
  padding: 0 16px;
}

.input-form-pay {
  width: 100%;
  margin-bottom: 20px;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 3px;
}

label {
  margin-bottom: 10px;
  display: block;
}

.icon-container {
  margin-bottom: 20px;
  padding: 7px 0;
  font-size: 24px;
}

.btn {
  background-color: #04AA6D;
  color: white;
  padding: 12px;
  margin: 10px 0;
  border: none;
  width: 100%;
  border-radius: 3px;
  cursor: pointer;
  font-size: 17px;
}

.btn:hover {
  background-color: #45a049;
}

a {
  color: #2196F3;
}

hr {
  border: 1px solid lightgrey;
}

span.price {
  float: right;
  color: grey;
}

/* Responsive layout - when the screen is less than 800px wide, make the two columns stack on top of each other instead of next to each other (also change the direction - make the "cart" column go on top) */
@media (max-width: 800px) {
  .row {
    flex-direction: column-reverse;
  }
  .col-25 {
    margin-bottom: 20px;
  }
}
</style>
<div class="container mb-5">
    <div class="heading-link mt-3">
         <ul class="item">
              <li class="home">
                   <a href="{{ route('index') }}">Trang chủ</a>
              </li>
              <li class="icon">
                   <a style="color:black;" href={{ route('cart') }}>Giỏ hàng</a>
              </li>
              <li class="icon active">
                <a href={{ route('checkout') }}>Thanh toán</a>
           </li>
         </ul>
    </div>
    <div class="heading-lg">
         <h1>THANH TOÁN</h1>
    </div>
    <div class="row mt-4">
        <div class="col-75">
          <div class="container">
            @if(Session::has('invalid'))
              <div class="alert alert-danger alert-dismissible mt-2">
                    <a class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{Session::get('invalid')}}
              </div>
            @endif
            <form action="{{ route('pay') }}" method="POST">
              @csrf
              <input type="hidden" name="customer_id" value="{{ $customer->id }}" />
              <input type="hidden" name="_token" value="{{ csrf_token() }}" />
              <input type="hidden" class="sum" value="{{ $totalPrice }}" />
              <div class="row">
                <div class="col-50">
                  <label for="name"><i class="fa fa-user"></i>Tên</label>
                  <input type="text" id="name" name="name" class="input-form-pay" value="{{ Session::get('customer')->username }}" readonly>
                  <label for="email"><i class="fa fa-envelope"></i> Email</label>
                  <input type="text" id="email" name="email" class="input-form-pay" value="{{ Session::get('customer')->email }}" readonly>
                  <label for="city"><i class="fas fa-map-marker-alt"></i> Thành phố</label>
                  <input type="text" id="city" name="city" class="input-form-pay" value="{{ $customer->c_name }}" disabled>
                  <label for="district"><i class="fas fa-map-marker-alt"></i> Quận/huyện</label>
                  <input type="text" id="district" name="district" class="input-form-pay" value="{{ $customer->d_name }}" disabled>
                  <label for="ward"><i class="fas fa-map-marker-alt"></i> Xã/phường</label>
                  <input type="text" id="ward" name="ward" class="input-form-pay" value="{{ $customer->w_name }}" disabled>
                  <label for="ship"><i class="fas fa-shipping-fast"></i> Đơn vị giao hàng</label>
                  <select name="ship" id="ship" class="form-control">
                    @foreach ($ships as $ship)
                        <option value="{{ $ship['id'] }}">{{ $ship['name'] }} - {{ number_format($ship['price'],-3,',',',') }} VND</option>
                    @endforeach
                  </select>
                </div>
                
              </div>
              <input type="submit" value="Thanh toán" class="btn mt-4">
            </form>
          </div>
        </div>
        <div class="col-25">
          <div class="container">
            <h4>Giỏ hàng <span class="price" style="color:black"><i class="fa fa-shopping-cart"></i> <b>{{ Session::has('cart') ? Session::get('cart')->totalQty : '' }}</b></span></h4>
            @foreach ($products as $product)
                <p><a href="{{ route('product.detail',['id' => $product['item']['id']]) }}">{{ $product['item']['title'] }}</a> <span class="price">{{ number_format($product['item']['price'] * $product['qty'],-3,',',',') }} VND</span></p> 
            @endforeach
            <hr>
            <p>Total <span class="total-cart" style="color:black"><b>{{ number_format($totalPrice,-3,',',',') }} VND</b></span></p>
          </div>
        </div>
      </div>
</div>
@endsection