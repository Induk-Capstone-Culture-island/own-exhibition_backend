<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
       /**
 * @OA\Post(
 * path="/register",
 * summary="Sign up",
 * description="register with name, email, password, password_confirmation, tc",
 * operationId="register",
 * tags={"auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"name", "email","password", "Password_confirmation", "tc"},
 *       @OA\Property(property="name", type="string", format="name", example="Cheolsu"),
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="password", type="string", format="password", example="12345"),
 *       @OA\Property(property="Password_confirmation", type="string", format="Password_confirmation", example="12345"),
 *       @OA\Property(property="tc", type="boolean", format="tc", example="true"),
 *    ),
 * ),
 * @OA\Response(
 *    response=200,
 *    description="Email already exists",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Email already exists.")
 *        )
 *     )
 *    response=201,
 *    description="Successfull registeration",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Registeration Success.")
 *        )
 *     )
 * )
 */
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'password'=> 'required|confirmed',
            'tc'=>'required',
        ]);
        if(User::where('email',$request->email)->first()){
            return response([
                'message' => 'Email already exists',
                'status' => 'failed'
            ],200);
        }
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=> Hash::make($request->password),
            'tc'=>json_decode($request->tc),
        ]);
        $token = $user->createToken($request->email)->plainTextToken;
        return response()->json([
            'token' => $token,
            'message' => 'Registeration Success',
            'status' => 'success'
        ],201);
    }

    /**
 * @OA\Post(
 * path="/login",
 * summary="Sign in",
 * description="Login by email, password",
 * operationId="authLogin",
 * tags={"auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"email","password"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
 *    ),
 * ),
 * @OA\Response(
 *    response=401,
 *    description="The Provided Credentials are incorrect",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="The Provided Credentials are incorrect")
 *        )
 *     )
 * )
 */

    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=> 'required',
        ],
        [
            'email.required' => '이메일 입력필수',
        ]);
        $user = User::where('email',$request->email)->first();
        if ($user && Hash::check($request->password, $user->password)){
            $token = $user->createToken($request->email)->plainTextToken;
            return response([
                'token' => $token,
                'message' => 'login Success',
                'status' => 'success'
            ],200);
        }
        return response([
            'message' => 'The Provided Credentials are incorrect',
            'status' => 'failed'
        ], 401);
    }

        /**
 * @OA\Post(
 * path="/logout",
 * summary="logout",
 * description="Logout",
 * operationId="authLogin",
 * tags={"auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"email","password"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
 *    ),
 * ),
 * @OA\Response(
 *    response=401,
 *    description="The Provided Credentials are incorrect",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="The Provided Credentials are incorrect")
 *        )
 *     )
 * )
 */
    public function logout(){

        auth()->user()->currentAccesstoken()->delete();

        return response()->json([
            'message' => 'You have succesfully been logged out and your token has been removed',
            'status'=>'success'
        ], 200);
    }
    /**
 * @OA\Schema(
 *     schema="profileGet",
 * allOf={
 *    @OA\Schema(ref="#/components/schemas/User"),
 *    @OA\Schema(
 *       @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/OrderCategory")),
 *    ),
 *    @OA\Schema(
 *       @OA\Property(property="locations", type="array", @OA\Items(ref="#/components/schemas/stateCounties")),
 *    ),
 *    @OA\Schema(
 *       @OA\Property(property="avatar", type="object", ref="#/components/schemas/File"),
 *    ),
 *    @OA\Schema(
 *       @OA\Property(property="address", type="object", ref="#/components/schemas/AddressCoordinates"),
 *    )
 * }
 * )
 *
 * @OA\Get(
 * path="/userinfo",
 * summary="get my information",
 * description="Get profile short information",
 * operationId="profileShow",
 * tags={"profile"},
 * security={ {"bearer": {} }},
 * @OA\Response(
 *    response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *       @OA\Property(property="data", type="object", ref="#/components/schemas/profileGet")
 *        )
 *     ),
 * @OA\Response(
 *    response=401,
 *    description="User should be authorized to get profile information",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Not authorized"),
 *    )
 * )
 * )
 */
    public function userinfo(){ 
        #mypage
        $loggeduser = auth()->user(); #인증된 유저의 정보를 db에서 가져옴

        return response()->json([
            'user' => $loggeduser,
            'message' => 'Logged User Data',
            'status'=>'success'
        ], 200);
    }

    public function changepassword(Request $request)
    {
        $request->validate([ #클라이언트의 요청
            'password'=> 'required',
        ]);
        $user = auth()->user(); #인증된 사용자의 정보
        #$user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'message' => 'Password Changed',
            'status'=>'success'
        ], 200);
    }
    
    public function getUser($id)
    {
        $user = User::find($id);
        #dd($user);
        return response()->json([
             'info' => $user,
             'status' => 'success'
        ], 200);
        #dd와 var_dump의 차이?
        #if문으로 실패했을때 처리
    }
    
    public function Delete()
    {
        $user = auth()->user()->delete(); #인증된 사용자의 정보
        #$deleteUser->$user->delete();
        return response()->json([
            'user' => $user,
            'status' => 'success'
        ], 200);
    }

}
