<?php

namespace App\Http\Controllers\API;

use Auth;
use App\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Http\Controllers\Controller;
use App\Libraries\ResponseFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
    API    : Login
    url    : http://localhost/crownstack/api/login
    Method : Post
    Device Type : 1:ios;2:Android
    **/
    public function login(Request $request){

    	// return User::all();
    	
       	try {

            $validation = Validator::make($request->all(), [
                'email'         => 'required|email',
                'password'      => 'required|min:6', 
            ]);

            if ($validation->fails()) {
                return ResponseFactory::setResponse($validation->messages()->first(), true, 400);
            }
            
            $credentials = $request->only(['email', 'password']);

            $check_user = User::query()->where(['email'=> $request->email])->first();
 
            if (isset($check_user)) {
               
                if (Auth::attempt($credentials)) {
                   
                    $user = auth()->user();
                    
                    // Check if user is deactivated from admin.
                    if($user->status == 0){
                        return ResponseFactory::setResponse('Your account is currently deactivated from Admin. Please contact for support.', true, 401);
                    }

                    // Deleting all passport tokens for user
                    $user->tokens()->delete();

                    $token = $user->createToken('crownstack')->accessToken;
                    
                    // Updating provider device and location info
                    $check_user->update(['device_type' => $request->device_type, 
                                            'device_token' => $request->device_token,                                       
                                      ]);
                    $check_user['token']  = $token;
                    
                    return ResponseFactory::setResponse('Logged in successfully.', false, 200,$check_user); 
                    
                } else {
                    return ResponseFactory::setResponse('Invalid credentials. Please try again.', true, 401);                   
                }

            } else {
                return ResponseFactory::setResponse('Email ID does not exist in our records!', true, 401);
            }
        } catch (Exception $e) {

            return ResponseFactory::setResponse($e->getMessage(), true, 500);
        }
            
    }

    public function allProductList(){
        try{

           $product = Product::all();

           return ResponseFactory::setResponse('All product list.', false, 200, $product);

        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }
    }

    public function allCategoryList(){
        try{

           $category = Category::all();

           return ResponseFactory::setResponse('All category list.', false, 200, $category);

        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }
    }

    public function products(Request $request){
        try{

             $validation = Validator::make($request->all(), [
                'category_id' => 'required|numeric',
                
            ]);

            if ($validation->fails()) {
                return ResponseFactory::setResponse($validation->messages()->first(), true, 400);
            }

           $product = Product::query()->where('category_id',$request->category_id)->get();

           return ResponseFactory::setResponse('All product list.', false, 200, $product);

        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }
    }

    public function addToCart(Request $request){
        try{
            // return Auth::user();

             $validation = Validator::make($request->all(), [
                'product_id' => 'required|numeric',
                'quantity'   => 'required|numeric',
                'amount'      => 'required|numeric'
                
            ]);

            if ($validation->fails()) {
                return ResponseFactory::setResponse($validation->messages()->first(), true, 400);
            }

            $check_if_exists = Product::query()->where('id',$request->product_id)->exists();
            if( $check_if_exists !=1 ){
                return ResponseFactory::setResponse('Product id does not exists.', true, 404);
            }


            $user_id = auth()->id(); 
            $product_id = $request->product_id;

            $check = Cart::query()->where(['user_id' => $user_id, 'product_id' => $product_id ])->first();

            if(isset($check) || $check != ''){
                $quantity = $check->quantity+$request->quantity;

                $check->update([
                    'quantity' => $quantity,
                    'amount' =>$request->amount*$quantity,
                ]);

               return ResponseFactory::setResponse('Product is Updated in cart successfully.', false, 200);
            }

            $input = $request->all();
            $input['amount'] = $request->quantity*$request->amount;
            $input['user_id'] = auth()->id();
            Cart::create($input);

           return ResponseFactory::setResponse('Product is added to cart successfully.', false, 200);

        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }
    }

    public function getCart(){
        try{
            // return Auth::user();

           $cart_product = Cart::where('user_id',auth()->id())->with('product')->get();

           return ResponseFactory::setResponse('All product list.', false, 200, $cart_product);

        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }
    }

}
