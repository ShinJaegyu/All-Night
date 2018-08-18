<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="wrap">        
        <div class="container">
            <div class="logo"><img src="/img/logo_img.png" alt="img"></div>
            <div class="form-wrap">
                <input type="email" id="user_email" name="email" placeholder="이메일"> <br>
                <input type="password" id="user_password" name="password" placeholder="비밀번호"> <br>
                <button type="button" id="login-btn" class="login-btn">로그인</button>
                <a href="/register" class="register-link">아직 계정이 없으신가요? / 회원가입</a>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        let login_btn = document.getElementById('login-btn');
        login_btn.addEventListener('click',function () {
            let email = document.getElementById('user_email').value;
            let password = document.getElementById('user_password').value;
            let data = {"email": email, "password" : password};
            axios.post('http://localhost:8000/api/v1/login', data)
            .then(function(response) {
                console.log(response.data);
                localStorage.setItem('access_token',response.data['access_token']);
                localStorage.setItem('refresh_token',response.data['refresh_token']);
                document.location.href="/";

            }).catch(function(error){
                console.log(error.response);
            });
        });
    </script>
</body>
</html>