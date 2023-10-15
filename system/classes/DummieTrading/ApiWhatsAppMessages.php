<?php

namespace DummieTrading;

class ApiWhatsAppMessages {
    const WELCOME_ARRAY = [
        "😁 Hola *{{name}}*, estamos muy felices de que te hayas unido a *DummieTrading*.\n\n👉 Aprende más en el siguiente link:\nzuum.link/BienvenidoDummieTrading",
        "🥳 Gracias por unirte a *DummieTrading*, *{{name}}*.\n\n👉 Aprende más en:\nzuum.link/BienvenidoDummieTrading",
        "😎 *{{name}}* Enhorabuena queremos darte la bienvenida en *DummieTrading*.\n\n👉 Aprende más en el siguiente link:\n zuum.link/BienvenidoDummieTrading",
        "👏🏻 Genial *{{name}}* te has unido a *DummieTrading*.\n\n👉 Aprende más en:\nzuum.link/BienvenidoDummieTrading",
        "👋🏻 Increíble *{{name}}* te has unido a *DummieTrading*.\n\n👉 Aprende más en:\nzuum.link/BienvenidoDummieTrading",
    ];

    const WELCOME_TRIAL_ARRAY = [
        "😁 Hola *{{name}}*. Gracias por unirte a *DummieTrading*, ya puedes activar tu trial de 15 días.\n\n👉 Ingresa a tu cuenta aquí \n{{extra}}.\n\n👉 Aprende más en el siguiente link:\nzuum.link/BienvenidoDummieTrading",
        "🥳 Genial *{{name}}*. Gracias por unirte a *DummieTrading*, ya puedes activar tu trial de 15 días.\n\n👉 Ingresa a tu cuenta aquí \n{{extra}}.\n\n👉 Aprende más en el siguiente link:\nzuum.link/BienvenidoDummieTrading",
        "👏🏻 ¡Super! *{{name}}*. Gracias por unirte a *DummieTrading*, ya puedes activar tu trial de 15 días.\n\n👉 Ingresa a tu cuenta aquí \n{{extra}}.\n\n👉 Aprende más en el siguiente link:\nzuum.link/BienvenidoDummieTrading",
    ];

    const NEW_USER_DEMO_ACCOUNT = [
        "😎 Hola, el usuario *{{name}}* requiere su cuenta demo.\n*DummieTrading*",
        "🥳 ¡Que tal!, nuestro usuario *{{name}}* requiere su cuenta demo.\n*DummieTrading*",
        "🥳 ¡Hey!, *{{name}}* requiere su cuenta demo.\n*DummieTrading*",
    ];

    const EXERCISE_CREDENTIALS_SETUP_ARRAY = [
        "😁 *¡Hola {{name}}!* te enviamos tus datos de acceso a *DummieTrading* en cuenta de prueba para realizar tu test:\n\nUsuario : *{{login}}* \nContraseña : *{{client_password}}*\nTrader : *{{trader}}*\nServer : *{{server}}*",
        "👏🏻 *Felicidades {{name}}!* te enviamos tus datos de acceso a *DummieTrading* para realizar test:\n\nUsuario : *{{login}}* \nContraseña : *{{client_password}}*\nTrader : *{{trader}}*\nServer : *{{server}}*",
        "👋🏻 *Felicidades {{name}}!* te enviamos tus datos de acceso a *DummieTrading* ya puedes hacer tu test:\n\nUsuario : *{{login}}* \nContraseña : *{{client_password}}*\nTrader : *{{trader}}*\nServer : *{{server}}*",
    ];

    const USER_TRADING_CREDENTIALS_SETUP_ARRAY = [
        "😁 *¡Hola {{name}}!* te enviamos tus datos de acceso a *DummieTrading* en cuenta de real:\n\nUsuario : *{{login}}* \nContraseña : *{{client_password}}*\nTrader : *{{trader}}*\nServer : *{{server}}*",
        "👏🏻 *Felicidades {{name}}!* te enviamos tus datos de acceso a *DummieTrading* de tu cuenta real:\n\nUsuario : *{{login}}* \nContraseña : *{{client_password}}*\nTrader : *{{trader}}*\nServer : *{{server}}*",
        "👋🏻 *Felicidades {{name}}!* te enviamos tus datos de acceso a *DummieTrading* de la cuenta real:\n\nUsuario : *{{login}}* \nContraseña : *{{client_password}}*\nTrader : *{{trader}}*\nServer : *{{server}}*",
    ];
    
    const USER_PENDING_ACTIVATION_ARRAY = [
        "👋🏻 *¡Hola {{name}}!* recientemente te registraste en DummieTrading y aún no has comprado tu cuenta\n¿Tienes alguna duda?",
        "👋🏻 *¡Hola {{name}}!* vi que te registraste en DummieTrading y aún no has comprado tu cuenta\n¿Tienes alguna duda?",
    ];
    
    const MONEY_ARRAY = [
        "💵 *¡Hola {{name}}!* hemos enviado *$ {{amount}} USD* a tu cuenta en DummieTrading",
        "💸 *¡Estimado {{name}}!* *$ {{amount}}* USD ya están en tu cuenta de DummieTrading",
        "😁 ¡Hemos enviado *$ {{amount}}* USD a tu cuenta de DummieTrading *{{name}}!*"
    ];
    
    const PROFITS_ARRAY = [
        "💵 *¡Hola {{name}}!* hemos enviado *$ {{amount}} USD* a tu cuenta en DummieTrading por tus profits",
        "💸 *¡Estimado {{name}}!* *$ {{amount}}* USD ya están en tu cuenta de DummieTrading por tus profits",
        "😁 ¡Hemos enviado *$ {{amount}}* USD a tu cuenta de DummieTrading por tus profits *{{name}}!*"
    ];
    
    public static function getRandomAnswer(array $answers = null)
    {
        return $answers[rand(0,sizeof($answers)-1)];
    }
    
    public static function getWelcomeMessage()
    {
        return self::getRandomAnswer(self::WELCOME_ARRAY);
    }

    public static function getWelcomeTrialMessage()
    {
        return self::getRandomAnswer(self::WELCOME_TRIAL_ARRAY);
    }
    
    public static function getAmountSendMessage()
    {
        return self::getRandomAnswer(self::MONEY_ARRAY);
    }
    
    public static function getProfitSendMessage()
    {
        return self::getRandomAnswer(self::PROFITS_ARRAY);
    }

    public static function getExerciseCredentialsMessage()
    {
        return self::getRandomAnswer(self::EXERCISE_CREDENTIALS_SETUP_ARRAY);
    }
   
    public static function getUserTradingCredentialsMessage()
    {
        return self::getRandomAnswer(self::USER_TRADING_CREDENTIALS_SETUP_ARRAY);
    }
   
    public static function getUserPendingActivationMessage()
    {
        return self::getRandomAnswer(self::USER_PENDING_ACTIVATION_ARRAY);
    }

    public static function getNewUserDemoAccountMessage()
    {
        return self::getRandomAnswer(self::NEW_USER_DEMO_ACCOUNT);
    }
}
