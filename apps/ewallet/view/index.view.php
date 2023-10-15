<div id="app">
    <div class="row justify-content-center">
        <div class="col-11">
            <ewalletatm-viewer
                :ewallet="ewallet"
                @getewallet="getEwallet"
                ref="ewalletAtm"></ewalletatm-viewer>

            <ewalletwithdraw-viewer
                :ewallet="ewallet"
                @getewallet="getEwallet"
                ref="ewalletWithdraw"></ewalletwithdraw-viewer>

            <ewalletaddfunds-viewer
                :ewallet="ewallet"
                @getewallet="getEwallet"
                ref="ewalletAddFunds"></ewalletaddfunds-viewer>
            
            <ewalletqr-viewer
                :ewallet="ewallet"
                ref="ewalletqr"></ewalletqr-viewer>

            <ewallet-viewer
                ref="ewallet"
                @openatm="openAtm"
                @openwithdraw="openWithdraw"
                @openaddfunds="openAddFunds"
                :ewallet="ewallet" 
                @getewallet="getEwallet"
                @getewalletqr="getEwalletQr"
                ></ewallet-viewer>
        </div>
    </div>
</div>