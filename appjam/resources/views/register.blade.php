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
                <input type="text" id="user_name" name="name" placeholder="이름"> <br>
                <input type="email" id="user_email" name="email" placeholder="이메일"> <br>
                <input type="password" id="user_password" name="password" placeholder="비밀번호"> <br>
                <input type="password" id="user_password_confirmation" name="password_confirmation" placeholder="비밀번호 확인"> <br>
                <button type="button" class="login-btn" id="register-btn">회원가입</button>
                <a href="/login" class="register-link">이미 계정이 있으신가요? / 로그인</a>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        let login_btn = document.getElementById('register-btn');
        login_btn.addEventListener('click',function () {
            let email = document.getElementById('user_email').value;
            let name = document.getElementById('user_name').value;
            let password = document.getElementById('user_password').value;
            let password_confirmation = document.getElementById('user_password_confirmation').value;
            let data = {"email": email, "password" : password, "password_confirmation" : password_confirmation, "name" : name};
            axios.post('http://localhost:8000/api/v1/user', data)
            .then(function(response) {
                if(response.data[0] == 'success') {
                    alert('회원가입되었습니다');
                    document.location.href ='/login';
                }
            }).catch(function(error){
                let keys = [];
                let key = error.response.data.error;
                for(k in key) {
                    keys.push(k);
                }

                alert(error.response.data.error[keys[0]]);

            });
        });

    </script>
</body>
</html>