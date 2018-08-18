<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class UserController extends Controller
{

    /**
     * login
     *
     * @param Request $request
     * @return \lluminate\Http\Response
     */
    public function login(Request $request) {
        $inputVal = $request->all();
        $validator = $this->validateVal($inputVal, 'login');

        if ($validator['status']) {
            if (\Auth::attempt(['email' => $inputVal['email'], 'password' => $inputVal['password']])) {
                $client = \DB::table('oauth_clients')
                    ->where('password_client', true)
                    ->first();

                $data = [
                    'grant_type' => 'password',
                    'client_id' => $client->id,
                    'client_secret' => $client->secret,
                    'username' => request('email'),
                    'password' => request('password'),
                ];

                $request = Request::create('/oauth/token', 'POST', $data);

                return app()->handle($request);
            }else {
                return response()->json([
                    'error' => '아이디와 비밀번호를 확인해주세요.'
                ],400,[],JSON_UNESCAPED_UNICODE);
            }
        }else {
            return response()->json([
                'error' => $validator['errors']
            ],400,[],JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputVal = $request->all();

        $validator = $this->validateVal($inputVal,'store');

        if ($validator['status']) {
            $inputVal['password'] = bcrypt($inputVal['password']);
            $id = User::create($inputVal)->id;
            return response()->json([
                'success',
                $id
            ],200,[],JSON_UNESCAPED_UNICODE);
        }else {
            return response()->json([
                'error' => $validator['errors']
            ],400,[],JSON_UNESCAPED_UNICODE);
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
        if(!is_numeric($id)) {
            return response()->json([
                'error' => 'invalid id number'
            ],400,[],JSON_UNESCAPED_UNICODE);
        }

        $result = User::find($id);
        if ($result) {
            return response()->json([
                'message' => $result,
            ],200,[],JSON_UNESCAPED_UNICODE);
        }else {
            return response()->json([
                'error' => 'data does not exist'
            ],400,[],JSON_UNESCAPED_UNICODE);
        }
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
        $inputVal = $request->all();

        $validator = $this->validateVal($inputVal,'update');

        if ($validator['status']) {
            $result = User::find($id);

            if ($result) {
                $result->update($inputVal);
            }else {
                return response()->json([
                    'error' => '잘못된 회원 id 입니다.'
                ],400,[],JSON_UNESCAPED_UNICODE);
            }

            return response()->json([
                'success',
                $id
            ],200,[],JSON_UNESCAPED_UNICODE);
        }else {
            return response()->json([
                'error' => $validator['errors']
            ],400,[],JSON_UNESCAPED_UNICODE);
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
        if(!is_numeric($id)) {
            return response()->json([
                'error' => '잘못된 회원 id 입니다.'
            ],400,[],JSON_UNESCAPED_UNICODE);
        }

        $result = User::find($id);
        if ($result) {
            $result->delete();
            return response()->json([
                'success'
            ],200,[],JSON_UNESCAPED_UNICODE);
        }else {
            return response()->json([
                    'error' => '존재하지 않는 회원 id 입니다.'
            ],400,[],JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * @param $data
     * @param $type
     * @return array
     */
    private function validateVal($data, $type) {
        $errors = [];

        switch ($type) {
            case 'store':
                $rules = [
                    'name' => ['required','regex:/[a-zA-Z가-힣]/','max:10'],
                    'email' => ['required','email','unique:users,email'],
                    'password' => ['required','confirmed'],
                    'password_confirmation' => ['required'],
                ];
                break;
            case 'update':
                $rules = [
                    'name' => ['regex:/[a-zA-Z가-힣]/','max:10'],
                    'email' => ['email','unique:users,email'],
                ];
                break;
            case 'login':
                $rules = [
                    'email' => ['required','email'],
                    'password' => ['required'],
                ];
                break;
        }

        $messages = [
            'name.required' => '이름을 입력해주세요.',
            'name.regex' => '이름은 영문, 한글만 가능합니다.',
            'name.max' => '이름의 최대 길이는 10글자입니다.',
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '이메일이 형식에 맞지 않습니다.',
            'email.unique' => '이미 가입된 이메일입니다.',
            'password.required' => '비밀번호를 입력해주세요',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
            'password_confirmation.required' => '비밀번호 확인란을 입력해주세요.',
        ];

        $validator = \Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            $status = false;
            for ($i = 0; $i < count($validator->errors()->keys()); $i++) {
                $errors[$validator->errors()->keys()[$i]] = $validator->errors()->get($validator->errors()->keys()[$i])[0];
            }
        } else {
            $status = true;
        }

        return ['status' => $status, 'errors' => $errors];
    }
}