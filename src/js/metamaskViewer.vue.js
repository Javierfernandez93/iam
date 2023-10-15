import { User } from '../../src/js/user.module.js?v=2.6.4'   

const MetamaskViewer = {
    name : 'metamask-viewer',
    data() {
        return {
            User: new User,
            accounts: null,
            chainId: null,
            status: null,
            NETWORKS : {
                ETHERUM: '0x1'
            }
        }
    },
    methods: {
        payInvoiceFromWallet() {  
            this.loading = true     

            this.User.payInvoiceFromWallet({invoice_id:this.invoice.invoice_id},(response)=>{

                this.loading = false          

                if(response.s == 1)
                {
                    this.invoice.status = response.response.status
                }
            })
        },
        async getAccounts() {
            this.accounts = await window.ethereum.request({ method: 'eth_requestAccounts' })
              .catch((err) => {
                if (err.code === 4001) {
                  console.log('Please connect to MetaMask.');
                } else {
                  console.error(err);
                }
            });
        },
        async validateMetaMask() {
            if (window.ethereum) 
            {
                window.web3 = new Web3(ethereum);

                try {
                    await ethereum.enable();

                    if(ethereum.isConnected())
                    {
                        this.status = 'Connected'
                    }
                } catch (err) {
                    this.status = 'User denied account access'
                }
            } else if (window.web3) {
                window.web3 = new Web3(web3.currentProvider)
            } else {
                this.status = 'No Metamask (or other Web3 Provider) installed'
            }
        },
        handleChainChanged(chainId) {
            this.chainId = chainId;
        }
    },
    async mounted() 
    {       
        await this.validateMetaMask()

        this.getAccounts()

        this.chainId = await window.ethereum.request({ method: 'eth_chainId' });

        
        // LISTENERS
        window.ethereum.on('chainChanged', this.handleChainChanged);

        console.log(web3.eth.sendTransaction( {
            to: '0x...',
            from: '0x...',
            value: '0x...',
            // And so on...
          },
          (error, result) => {
            if (error) {
              return console.error(error);
            }
            // Handle the result
            console.log(result);
          }
        ))
        
        console.log(this.chainId)
        console.log(this.chainId == this.NETWORKS.ETHERUM)
    },
    template : `
        current Network {{chainId}}
        <div v-if="accounts">
            <ul class="list-group">
                <li v-for="account in accounts" class="list-group-item">{{account}}</li>
            </ul>
        </div>
        
        <div>{{status}}</div>

        <button class="btn btn-primary" @click="getAccounts">getAccounts</button>
    `,
}

export { MetamaskViewer } 