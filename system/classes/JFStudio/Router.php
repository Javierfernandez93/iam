<?php

namespace JFStudio;

class Router {
    const Backoffice = 1;
    const Profile = 2;
    const Signup = 5;
    const Login = 6;
    const RecoverPassword = 7;
    const NewPassword = 8;
    const Wallet = 23;
    const Invoices = 48;
    const ProfileSetting = 54;
    const Home = 61;
    const PayPal = 64;
    const Help = 67;
    const AdminTicket = 68;
    const Payments = 73;
    const AddPayment = 74;
    const StoreCredit = 75;
    const Store = 84;
    const Test = 85;
    const Account = 86;
    const Notice = 90;
    const About = 91;
    const Full = 94;
    const StorePackage = 95;
    const AdminUsersList = 96;
    
    /* admin */
    const AdminUsers = 9;
    const AdminActivations = 10;
    const AdminAdministrators = 11;
    const AdminLogin = 13;
    const AdmiActivation = 14;
    const AdminUserEdit = 19;
    const AdminUserAdd = 20;
    const AdminAdministratorsAdd = 21;
    const AdminAdministratorsEdit = 21;
    const AdminTransactions = 24;
    const AdminDash = 26;
    const AdminDeposits = 29;
    const AdminNotices = 31;
    const AdminNoticesEdit = 32;
    const AdminNoticesAdd = 33;
    const AdminStats = 34;
    const AdminReport = 35;
    const AdminBuys = 50;
    const AdminWallet = 56;
    const AdminTools = 57;
    const AdminToolsAdd = 58;
    const AdminToolsEdit = 59;
    const AdminEmail = 62;
    const AdminGains = 89;
    const AdminLanding = 92;
    const AdminPaymentMethods = 93;
    const AdminIntent = 106;
    const AdminPackages = 107;
    const AdminProducts = 108;
    const Deposit = 109;
    const AdminEmails = 110;

    static function getName(int $route = null)
    {
        return match($route) {
            self::Backoffice => 'Backoffice',
            self::Profile => 'Perfil',
            self::Signup => 'Únete hoy mismo',
            self::Login => 'Ingresa a tu cuenta',
            self::RecoverPassword => 'Recuperar contraseña',
            self::NewPassword => 'Cambiar contraseña',
            self::Wallet => 'Cartera electrónica',
            self::AdminDash => 'Home',
            self::AdminUsers => 'Usuarios',
            self::AdminUserEdit => 'Editar usuario',
            self::AdminUserAdd => 'Añadir usuario',
            self::AdminActivations => 'Activaciones',
            self::AdminAdministrators => 'Administradores',
            self::AdminAdministratorsAdd => 'Añadir administrador',
            self::AdminAdministratorsEdit => 'Editar administrador',
            self::AdminLogin => 'Iniciar sesión admin',
            self::AdmiActivation => 'Activar en plan',
            self::AdminTransactions => 'Transacciones',
            self::AdminNotices => 'Listar noticias',
            self::AdminNoticesEdit => 'Editar noticia',
            self::AdminNoticesAdd => 'Añadir noticia',
            self::AdminDeposits => 'Ver fondeos',
            self::AdminStats => 'Estadísticas',
            self::AdminReport => 'Reporte',
            self::Invoices => 'Mis compras',
            self::AdminBuys => 'Compras',
            self::ProfileSetting => 'Ajustes de cuenta',
            self::AdminWallet => 'Ewallet',
            self::AdminTools => 'Herramientas',
            self::AdminToolsAdd => 'Añadir herramienta',
            self::AdminToolsEdit => 'Editar herramienta',
            self::Home => 'Página inicial',
            self::AdminEmail => 'Email',
            self::PayPal => 'Pago seguro con PayPal',
            self::Help => 'Ayuda',
            self::AdminTicket => 'Tickets',
            self::Payments => 'Pagos realizados a tus cuentas',
            self::AddPayment => 'Añadir pago de mensualidad',
            self::StoreCredit => 'Créditos',
            self::Store => 'Configura cuenta prueba',
            self::Test => 'Command',
            self::Account => 'Cuenta',
            self::AdminGains => 'Ganancias',
            self::Notice => 'Noticia',
            self::About => 'Nosotros',
            self::AdminLanding => 'Lista de landings',
            self::AdminPaymentMethods => 'Métodos de pago',
            self::Full => 'Mi cuenta',
            self::StorePackage => 'Productos',
            self::AdminUsersList => 'Lista usuarios',
            self::AdminIntent => 'Entrenar ChatBot',
            self::AdminPackages => 'Paquetes',
            self::AdminProducts => 'Productos',
            self::Deposit => 'Depósito',
            self::AdminEmails => 'Lista de correos',
            default => 'Sin nombre'
        };
    }
}