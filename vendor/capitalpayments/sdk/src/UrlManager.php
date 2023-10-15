<?php 

namespace CapitalPayments\Sdk;

class UrlManager {
    // account
    const GET_ENVIRONMENT = 'https://www.capitalpayments.me/app/api/getEnvironment';
    const GET_ACCOUNT = 'https://www.capitalpayments.me/app/api/getAccount';
    const LOGIN = 'https://www.capitalpayments.me/app/api/login';

    // invoices
    const CREATE_INVOICE = 'https://www.capitalpayments.me/app/api/createInvoice';
    const CREATE_INVOICES = 'https://www.capitalpayments.me/app/api/createInvoices';
    const GET_INVOICE_STATUS = 'https://www.capitalpayments.me/app/api/getInvoiceStatus';
    const CANCEL_INVOICE = 'https://www.capitalpayments.me/app/api/cancelInvoice';
    
    // payouts
    const CREATE_PAYOUT = 'https://www.capitalpayments.me/app/api/createPayout';
    const CREATE_PAYOUTS = 'https://www.capitalpayments.me/app/api/createPayouts';
    const GET_PAYOUT_STATUS = 'https://www.capitalpayments.me/app/api/getPayoutStatus';
    const CANCEL_PAYOUT = 'https://www.capitalpayments.me/app/api/cancelPayout';

    // wallet
    const GET_BALANCE = 'https://www.capitalpayments.me/app/api/getBalance';
    const GET_MAIN_WALLET = 'https://www.capitalpayments.me/app/api/getMainWallet';
    const GET_WALLETS = 'https://www.capitalpayments.me/app/api/getWallets';

    # customer 
    const GET_CUSTOMERS = 'https://www.capitalpayments.me/app/api/getCustomers';
    const GET_CUSTOMER = 'https://www.capitalpayments.me/app/api/getCustomer';
    const DELETE_CUSTOMER = 'https://www.capitalpayments.me/app/api/deleteCustomer';
    const CREATE_CUSTOMER = 'https://www.capitalpayments.me/app/api/addCustomer';

    # item
    const GET_ITEMS = 'https://www.capitalpayments.me/app/api/getItems';
    const GET_ITEM = 'https://www.capitalpayments.me/app/api/getItem';
    const DELETE_ITEM = 'https://www.capitalpayments.me/app/api/deleteItem';
    const CREATE_ITEM = 'https://www.capitalpayments.me/app/api/addItem';
}