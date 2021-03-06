<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\Producer;
use App\Brand;
use App\Cart;
use App\Ship;
use App\Order;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThankMail;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = DB::select(
        'select p.*,c.title cate_title,b.name brand_name,r.name producer_name from products p,categories c,brands b,producers r
         where p.category_id = c.id and p.brand_id = b.id and p.producer_id = r.id'
        );
        return view('admin.products.listProduct',['products'=>$products]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $producers = Producer::all();
        $brands = Brand::all();
        return view('admin.products.addProduct',['categories'=>$categories,'producers' => $producers,'brands' => $brands]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $products = Product::all();
        foreach($products as $row){
            if($row['sku'] === $request->input('sku')){
                return redirect()->route('product.add.form')->with('invalid','Mã sản phẩm đã tồn tại');
            }
        }
        if ($request->hasFile('image')) {
            //  Let's do everything here
            if ($request->file('image')->isValid()) {
                //
                $validated = $request->validate([
                    'title' => 'required',
                    'price' => 'required',
                    'content' => 'required',
                    'category_id' => 'required',
                    'producer_id' => 'required',
                    'brand_id' => 'required',
                    'image' => 'mimes:jpeg,png,webp|max:1014',
                    'quantity' => 'required',
                    'sku' => 'required'
                ]);
                $extension = $request->image->extension();
                $request->image->storeAs('/public/images/products', $validated['title'].".".$extension);
                $product = Product::create([
                   'title' => $validated['title'],
                   'price' => $validated['price'],
                   'category_id' => $validated['category_id'],
                   'image_path' => $validated['title'].".".$extension,
                   'producer_id' => $validated['producer_id'],
                   'description' => $validated['content'],
                   'brand_id' => $validated['brand_id'],
                   'quantity' => $validated['quantity'],
                   'sku' => $validated['sku']
                ]);
                $product->save();
                return redirect()->route('product.list')->with("success","Lưu thành công");
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        $comments = DB::table('comments')->join('customers', 'customers.id', '=', 'comments.customer_id')->where('comments.product_id','=',$id)->orderBy('comments.created_at','desc')->paginate(2,['comments.*','customers.username'],'comment');
        $replies = DB::select('select a.*,b.username customer_name from replies a,customers b where a.customer_id = b.id');
        $randomProduct = Product::inRandomOrder()->limit(3)->get();
        return view('product-detail',['product'=>$product,'randomProduct'=>$randomProduct,'comments'=>$comments,'replies'=>$replies]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = Category::all();
        $producers = Producer::all();
        $brands = Brand::all();
        $product = Product::find($id);
        return view('admin.products.editProduct',['product' => $product,'categories'=>$categories,'producers' => $producers,'brands' => $brands]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->hasFile('image')) {
            //  Let's do everything here
            if ($request->file('image')->isValid()) {
                //
                $validated = $request->validate([
                    'title' => 'required',
                    'price' => 'required',
                    'content' => 'required',
                    'category_id' => 'required',
                    'producer_id' => 'required',
                    'brand_id' => 'required',
                    'image' => 'mimes:jpeg,png,webp|max:1014',
                    'quantity' => 'required',
                    'sku' => 'required'
                ]);
                $extension = $request->image->extension();
                $request->image->storeAs('/public/images/products', $validated['title'].".".$extension);
                $product = Product::find($id);
                $product->title =$validated['title'];
                $product->price = $validated['price'];
                $product->category_id = $validated['category_id'];
                $product->image_path =  $validated['title'].".".$extension;
                $product->producer_id = $validated['producer_id'];
                $product->brand_id = $validated['brand_id'];
                $product->description = $validated['content'];
                $product->quantity = $validated['quantity'];
                $product->sku = $validated['sku'];
                $product->save();
                return redirect()->route('product.list')->with("success","Sửa thành công");
            }
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        return redirect()->route('product.list')->with("success","Xóa thành công");
    }

    /**
     * Disable status product
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disable($id)
    {
        $product = Product::find($id);
        $product->status = 0;
        $product->save();
        return redirect()->route('product.list')->with("success","Vô hiệu hóa thành công");
    }

    /**
     * Enable status product
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function enable($id)
    {
        $product = Product::find($id);
        $product->status = 1;
        $product->save();
        return redirect()->route('product.list')->with("success","Mở thành công");
    }

    /**
     * Add to cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request, $id)
    {
        if(Auth::check() || Session::get('customer') != null){
            $product = Product::find($id);
            $oldCart = Session::has('cart') ? Session::get('cart') : null;
            $cart = new Cart($oldCart);
            $cart->add($product,$product->id);
            $request->session()->put('cart',$cart);
            return redirect()->route('product.detail',['id' => $product->id])->with('success','Thêm giỏ hàng thành công');
        }else{
            return redirect()->route('login')->with("invalid","Vui lòng đăng nhập trước khi mua hàng");
        }
    }  
    
    /**
     * Show item in cart
     *
     * @return \Illuminate\Http\Response
     */
    public function getCart(){
        if(!Session::has('cart')){
            return view('cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        return view('cart', ['products' => $cart->items, 'totalPrice' => $cart->totalPrice]);
    }

    /**
     * Delete item in cart
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteItem($id){
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->deleteItem($id);
        if(count($cart->items) > 0){
            Session::put('cart',$cart);
        }else{
            Session::forget('cart');
        }
        return redirect()->route('cart');
    }

    /**
     * Checkout
     *
     * @return \Illuminate\Http\Response
     */
    public function checkout(){
        if(!Session::has('cart')){
            return view('cart');
        }
        $oldCart = Session::get('cart');
        $id = Session::get('customer')->id;
        $ships = Ship::all();
        $customer = DB::select(
            'select u.*,c.name c_name,d.name d_name,w.name w_name 
            from customers u,cities c,districts d,wards w 
            where u.city_id = c.matp and u.district_id = d.maqh and u.ward_id = w.xaid and id = ?',[$id]
        );
        $cart = new Cart($oldCart);
        return view('checkout', ['products' => $cart->items, 'totalPrice' => $cart->totalPrice, 'customer' => $customer[0], 'ships' => $ships]);
    }

        /**
     * Pay
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pay(Request $request){
        if(!Session::has('cart')){
            return view('cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        $ran_id = substr(str_shuffle("0123456789"), 0, 5);
        try {
            foreach($cart->items as $row){
                $order = new Order();
                $order->customer_id = $request->input('customer_id');
                $order->qty = $row['qty'];
                $order->price = $row['price'];
                $order->total = $row['qty'] * $row['price'];
                $order->product_id = $row['item']['id'];
                $order->order_code = $ran_id;
                $order->ship_id = $request->input('ship');
                $order->save();
                $product = Product::find($row['item']['id']);
                Product::where('id',$row['item']['id'])->update(['quantity' => $product['quantity'] - $row['qty']]);
            }
        } catch (\Exception $e) {
            return redirect()->route('checkout')->with('invalid', $e->getMessage());
        }
        Mail::to($request->input('email'))->send(new ThankMail());
        Session::forget('cart');
        return view('thank');
    }

    
    /**
     * Decrease item in cart
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function decreaseItem($id){
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->decreaseItemByOne($id);
        if(count($cart->items) > 0){
            Session::put('cart',$cart);
        }else{
            Session::forget('cart');
        }
        return redirect()->route('cart');
    }

    /**
     * Increase item in cart
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function increaseItem($id){
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->increaseItemByOne($id);
        Session::put('cart',$cart);
        return redirect()->route('cart');
    }
}
