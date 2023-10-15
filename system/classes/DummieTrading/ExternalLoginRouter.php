<?php

namespace DummieTrading;

class ExternalLoginRouter {
    const ZUUM_SIGNUP_CHECKER = 'http://localhost:8888/mizuum/app/services/signupChecker.php';
    const LOGIN_FOR_TOOL = 'http://localhost:8888/mizuum/app/services/loginForTool.php';
    const ZUUM_SIGNUP = 'http://localhost:8888/mizuum/app/services/signupExternal.php';
    const API_GENERATOR = 'http://localhost:8888/mizuum/app/services/makeApiCredentials.php';
    const GET_EXTERNAL_USER_BY_EMAIL = 'http://localhost:8888/mizuum/app/services/getExternalUserByEmail.php';
}