import { Http } from '../../src/js/http.module.js?v=1.4.6';

class User extends Http {
    constructor() {
        super();
    }
    doLogin(data, callback) {
        return this.call('../../app/application/do_login.php', data, callback);
    }
    getBackofficeVars(data, callback) {
        return this.call('../../app/application/get_backoffice_vars.php', data, callback);
    }
    getVarsConfiguration(data, callback) {
        return this.call('../../app/application/getVarsConfiguration.php', data, callback);
    }
    deleteAccount(data, callback) {
        return this.call('../../app/application/deleteAccount.php', data, callback);
    }
    getNotifications(data, callback) {
        return this.call('../../app/application/get_notifications.php', data, callback);
    }
    getBalance(data, callback) {
        return this.call('../../app/application/get_balance.php', data, callback);
    }
    createTransactionRequirement(data, callback) {
        return this.call('../../app/application/create_transaction_requirement.php', data, callback);
    }
    getMaxTradingAccountsPerUser(data, callback) {
        return this.call('../../app/application/getMaxTradingAccountsPerUser.php', data, callback);
    }
    getZuumTools(data, callback) {
        return this.call('../../app/application/getZuumTools.php', data, callback);
    }
    signupChecker(data, callback) {
        return this.call('../../app/application/signupChecker.php', data, callback);
    }
    getTestStatus(data, callback) {
        return this.call('../../app/application/getTestStatus.php', data, callback);
    }
    getPidForExternalTool(data, callback) {
        return this.call('../../app/application/getPidForExternalTool.php', data, callback);
    }
    signupExternal(data, callback) {
        return this.call('../../app/application/signupExternal.php', data, callback);
    }
    setUserAsDisconnected(data, callback) {
        return this.call('../../app/application/setUserAsDisconnected.php', data, callback);
    }
    addTradingAccount(data, callback) {
        return this.call('../../app/application/addTradingAccount.php', data, callback);
    }
    getLastTransactionsRequirement(data, callback) {
        return this.call('../../app/application/get_last_transactions_requirement.php', data, callback);
    }
    getPayPalPaymentStatus(data, callback) {
        return this.call('../../app/application/getPayPalPaymentStatus.php', data, callback);
    }
    getCurrencies(data, callback) {
        return this.call('../../app/application/get_currencies.php', data, callback);
    }
    doWithdraw(data, callback) {
        return this.call('../../app/application/do_withdraw.php', data, callback);
    }
    editWithdrawMethod(data, callback) {
        return this.call('../../app/application/edit_withdraw_method.php', data, callback);
    }
    getWithdraws(data, callback) {
        return this.call('../../app/application/get_withdraws.php', data, callback);
    }
    getPlans(data, callback) {
        return this.call('../../app/application/get_plans.php', data, callback);
    }
    editProfile(data, callback) {
        return this.call('../../app/application/edit_profile.php', data, callback);
    }
    getCountries(data, callback) {
        return this.call('../../app/application/get_countries.php', data, callback);
    }
    enrollInCourse(data, callback) {
        return this.call('../../app/application/enrollInCourse.php', data, callback);
    }
    getAdvices(data, callback) {
        return this.call('../../app/application/getAdvices.php', data, callback);
    }
    changePassword(data, callback) {
        return this.call('../../app/application/change_password.php', data, callback);
    }
    recoverPassword(data, callback) {
        return this.call('../../app/application/recover_password.php', data, callback);
    }
    getToolsList(data, callback) {
        return this.call('../../app/application/getToolsList.php', data, callback);
    }
    getCoursesList(data, callback) {
        return this.call('../../app/application/getCoursesList.php', data, callback);
    }
    getCourse(data, callback) {
        return this.call('../../app/application/getCourse.php', data, callback);
    }
    getSessionPerCourse(data, callback) {
        return this.call('../../app/application/getSessionPerCourse.php', data, callback);
    }
    getSessionsCourse(data, callback) {
        return this.call('../../app/application/getSessionsCourse.php', data, callback);
    }
    getAuthToChangePassword(data, callback) {
        return this.call('../../app/application/get_auth_to_change_password.php', data, callback);
    }
    getLandings(data, callback) {
        return this.call('../../app/application/getLandings.php', data, callback);
    }
    saveLanding(data, callback) {
        return this.call('../../app/application/saveLanding.php', data, callback);
    }
    doSignup(data, callback) {
        return this.call('../../app/application/do_signup.php', data, callback);
    }
    getProfile(data, callback) {
        return this.call('../../app/application/get_profile.php', data, callback);
    }
    getLastReferrals(data, callback) {
        return this.call('../../app/application/get_last_referrals.php', data, callback);
    }
    getReferrals(data, callback) {
        return this.call('../../app/application/get_referrals.php', data, callback);
    }
    getCatalogBanners(data, callback) {
        return this.call('../../app/application/getCatalogBanners.php', data, callback);
    }
    getReferral(data, callback) {
        return this.call('../../app/application/get_referral.php', data, callback);
    }
    getProfits(data, callback) {
        return this.call('../../app/application/get_profits.php', data, callback);
    }
    getProfitStats(data, callback) {
        return this.call('../../app/application/get_profit_stats.php', data, callback);
    }
    getNoticesList(data, callback) {
        return this.call('../../app/application/get_notices_list.php', data, callback);
    }
    getCampaigns(data, callback) {
        return this.call('../../app/application/getCampaigns.php', data, callback);
    }
    getCampaign(data, callback) {
        return this.call('../../app/application/getCampaign.php', data, callback);
    }
    saveCampaign(data, callback) {
        return this.call('../../app/application/saveCampaign.php', data, callback, null, null, 'POST');
    }
    publishCampaign(data, callback) {
        return this.call('../../app/application/publishCampaign.php', data, callback);
    }
    unPublishCampaign(data, callback) {
        return this.call('../../app/application/unPublishCampaign.php', data, callback);
    }
    deleteCampaign(data, callback) {
        return this.call('../../app/application/deleteCampaign.php', data, callback);
    }
    saveBannerPerCampaign(data, callback) {
        return this.call('../../app/application/saveBannerPerCampaign.php', data, callback, null, null, 'POST');
    }
    getBanners(data, callback) {
        return this.call('../../app/application/getBanners.php', data, callback);
    }
    updateCampaign(data, callback) {
        return this.call('../../app/application/updateCampaign.php', data, callback);
    }
    getAllCountries(data, callback) {
        return this.call('../../app/application/getAllCountries.php', data, callback);
    }
    getAllVCards(data, callback) {
        return this.call('../../app/application/getAllVCards.php', data, callback);
    }
    publishVcard(data, callback) {
        return this.call('../../app/application/publishVcard.php', data, callback);
    }
    unPublishVcard(data, callback) {
        return this.call('../../app/application/unPublishVcard.php', data, callback);
    }
    deleteVcard(data, callback) {
        return this.call('../../app/application/deleteVcard.php', data, callback);
    }
    getAllTemplates(data, callback) {
        return this.call('../../app/application/getAllTemplates.php', data, callback);
    }
    getVcardConfiguration(data, callback) {
        return this.call('../../app/application/getVcardConfiguration.php', data, callback);
    }
    getVcard(data, callback) {
        return this.call('../../app/application/getVcard.php', data, callback);
    }
    saveVCard(data, callback) {
        return this.call('../../app/application/saveVCard.php', data, callback);
    }
    updateVCard(data, callback) {
        return this.call('../../app/application/updateVCard.php', data, callback);
    }
    getMyStorageFiles(data, callback) {
        return this.call('../../app/application/getMyStorageFiles.php', data, callback);
    }
    getStorageCapacity(data, callback) {
        return this.call('../../app/application/getStorageCapacity.php', data, callback);
    }
    getCountriesPhones(data, callback) {
        return this.call('../../app/application/getCountriesPhones.php', data, callback);
    }
    getBrokers(data, callback) {
        return this.call('../../app/application/getBrokers.php', data, callback);
    }
    uploadStorageFile(data,callback,progress){
        return this.callFile('../../app/application/upload_storage_file.php',data,callback,progress);
    } 
    // callfile
    uploadImageProfile(data, progress, callback) {
        return this.callFile('../../app/application/upload_image_profile.php', data, callback, progress);
    }
    uploadPaymentImage(data, progress, callback) {
        return this.callFile('../../app/application/uploadPaymentImage.php', data, callback, progress);
    }
    uploadImageBanner(data, progress, callback) {
        return this.callFile('../../app/application/uploadImageBanner.php', data, callback, progress);
    }
    //ewallet
    getLastTransactionsWallet(data, callback) {
        return this.call('../../app/application/getLastTransactionsWallet.php', data, callback, null, null, 'POST');
    }
    getEwalletBalance(data, callback) {
        return this.call('../../app/application/getEwalletBalance.php', data, callback, null, null, 'POST');
    }
    getLastWithdraws(data, callback) {
        return this.call('../../app/application/getLastWithdraws.php', data, callback, null, null, 'POST');
    }
    getInvoiceById(data, callback) {
        return this.call('../../app/application/getInvoiceById.php', data, callback, null, null, 'POST');
    }
    getLastAddress(data, callback) {
        return this.call('../../app/application/getLastAddress.php', data, callback);
    }
    getEwallet(data, callback) {
        return this.call('../../app/application/getEwallet.php', data, callback, null, null, 'POST');
    }
    doWithdraw(data, callback) {
        return this.call('../../app/application/doWithdraw.php', data, callback, null, null, 'POST');
    }
    sendEwalletFunds(data, callback) {
        return this.call('../../app/application/sendEwalletFunds.php', data, callback, null, null, 'POST');
    }
    withdrawFunds(data, callback) {
        return this.call('../../app/application/withdrawFunds.php', data, callback, null, null, 'POST');
    }
    getWithdrawsMethods(data, callback) {
        return this.call('../../app/application/getWithdrawsMethods.php', data, callback);
    }
    getUserCampaign(data, callback) {
        return this.call('../../app/application/getUserCampaign.php', data, callback);
    }
    editWithdrawMethod(data, callback) {
        return this.call('../../app/application/editWithdrawMethod.php', data, callback);
    }
    payInvoiceFromWallet(data, callback) {
        return this.call('../../app/application/payInvoiceFromWallet.php', data, callback);
    }
    getTransactionFee(data, callback) {
        return this.call('../../app/application/getTransactionFee.php', data, callback);
    }
    getTransactionWithdrawFee(data, callback) {
        return this.call('../../app/application/getTransactionWithdrawFee.php', data, callback);
    }
    addFunds(data, callback) {
        return this.call('../../app/application/addFunds.php', data, callback);
    }
    // cart 
    initCart(data, callback) {
        return this.call('../../app/application/initCart.php', data, callback);
    }
    getStoreItemsNetwork(data, callback) {
        return this.call('../../app/application/getStoreItemsNetwork.php', data, callback);
    }
    getStoreItemsPackage(data, callback) {
        return this.call('../../app/application/getStoreItemsPackage.php', data, callback);
    }
    getStoreItemsMarketing(data, callback) {
        return this.call('../../app/application/getStoreItemsMarketing.php', data, callback);
    }
    getPaymentMethods(data, callback) {
        return this.call('../../app/application/getPaymentMethods.php', data, callback);
    }
    addPackage(data, callback) {
        return this.call('../../app/application/addPackage.php', data, callback);
    }
    getInvoices(data, callback) {
        return this.call('../../app/application/getInvoices.php', data, callback);
    }
    selectCatalogPaymentMethodId(data, callback) {
        return this.call('../../app/application/selectCatalogPaymentMethodId.php', data, callback);
    }
    selectCatalogCurrencyId(data, callback) {
        return this.call('../../app/application/selectCatalogCurrencyId.php', data, callback);
    }
    saveBuy(data, callback) {
        return this.call('../../app/application/saveBuy.php', data, callback);
    }
    getCartResume(data, callback) {
        return this.call('../../app/application/getCartResume.php', data, callback);
    }
    deleteItem(data, callback) {
        return this.call('../../app/application/deleteItem.php', data, callback);
    }
    getCatalogTimezones(data, callback) {
        return this.call('../../app/application/getCatalogTimezones.php', data, callback);
    }
    getInvoice(data, callback) {
        return this.call('../../app/application/getInvoice.php', data, callback);
    }
    getAllFaqs(data, callback) {
        return this.call('../../app/application/getAllFaqs.php', data, callback);
    }
    getTickets(data, callback) {
        return this.call('../../app/application/getTickets.php', data, callback);
    }
    addTicket(data, callback) {
        return this.call('../../app/application/addTicket.php', data, callback);
    }
    sendTicketReply(data, callback) {
        return this.call('../../app/application/sendTicketReply.php', data, callback);
    }
    getTransactionInfo(data, callback) {
        return this.call('../../app/application/getTransactionInfo.php', data, callback, null, null, 'POST');
    }
    getTransactionInfo(data, callback) {
        return this.call('../../app/application/getTransactionInfo.php', data, callback, null, null, 'POST');
    }
    getEwalletAddressInfo(data, callback) {
        return this.call('../../app/application/getEwalletAddressInfo.php', data, callback, null, null, 'POST');
    }
    getConferences(data, callback) {
        return this.call('../../app/application/getConferences.php', data, callback, null, null, 'POST');
    }
    getLicences(data, callback) {
        return this.call('../../app/application/getLicences.php', data, callback, null, null, 'POST');
    }
    getReferralPayments(data, callback) {
        return this.call('../../app/application/getReferralPayments.php', data, callback, null, null);
    }
    approbeReferralPayment(data, callback) {
        return this.call('../../app/application/approbeReferralPayment.php', data, callback, null, null);
    }
    deleteReferralPayment(data, callback) {
        return this.call('../../app/application/deleteReferralPayment.php', data, callback, null, null);
    }
    addReferralPayment(data, callback) {
        return this.call('../../app/application/addReferralPayment.php', data, callback, null, null);
    }
    getServiceConfiguration(data, callback) {
        return this.call('../../app/application/getServiceConfiguration.php', data, callback, null, null);
    }
    getApiCredentials(data, callback) {
        return this.call('../../app/application/getApiCredentials.php', data, callback, null, null);
    }
    configureService(data, callback) {
        return this.call('../../app/application/configureService.php', data, callback, null, null);
    }
    makeApiCredentials(data, callback) {
        return this.call('../../app/application/makeApiCredentials.php', data, callback, null, null);
    }
    getProduct(data, callback) {
        return this.call('../../app/application/getProduct.php', data, callback, null, null);
    }
    addProduct(data, callback) {
        return this.call('../../app/application/addProduct.php', data, callback, null, null);
    }
    followAccount(data, callback) {
        return this.call('../../app/application/followAccount.php', data, callback, null, null);
    }
    /* chatbot */
    isUserConnectedWithDummyTrading(data, callback) {
        return this.call('../../app/application/isUserConnectedWithDummyTrading.php', data, callback, null, null);
    }
    /* chatbot */
    getIntentReply(data, callback) {
        return this.call('../../app/application/getIntentReply.php', data, callback, null, null);
    }
    getChatIaFirstMessage(data, callback) {
        return this.call('../../app/application/getChatIaFirstMessage.php', data, callback, null, null);
    }
    getCatalogMamAccount(data, callback) {
        return this.call('../../app/application/getCatalogMamAccount.php', data, callback, null, null);
    }
    telegramDispatcher(data, callback) {
        return this.call('../../app/application/telegramDispatcher.php', data, callback, null, null);
    }
    /* iam */
    getLastOrders(data, callback) {
        return this.call('../../app/application/getLastOrders.php', data, callback, null, null);
    }
    getPackages(data, callback) {
        return this.call('../../app/application/getPackages.php', data, callback, null, null);
    }
    /* suscriber */
    saveSuscriber(data, callback) {
        return this.call('../../app/application/saveSuscriber.php', data, callback, null, null);
    }
    getProfileShort(data, callback) {
        return this.call('../../app/application/getProfileShort.php', data, callback, null, null);
    }
}

export { User }