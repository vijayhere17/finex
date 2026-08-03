let rid = 0;

let coin_rate = PHP2JS.data.coin_rate;
let contract_addr = PHP2JS.data.usdt_con_addr;
let contract_abi = JSON.parse(PHP2JS.data.usdt_con_abi || '[]');

let payable_coin = 0;

const deposit_addr = PHP2JS.data.to_address;

// false = proper FinexVault on-chain buy (BSC testnet / mainnet).
// true  = skip chain and create Pending for admin Approve (offline testing only).
const USE_ADMIN_APPROVE_TEMP = false;

// FinexVault (BSC) — instant on-chain buy (used only when USE_ADMIN_APPROVE_TEMP = false).
const blockchainEnabled = !!(PHP2JS.data.blockchain_enabled);
const finexVaultAddress = PHP2JS.data.finex_vault_address || '';
let finexVaultAbi = [];
try {
    finexVaultAbi = typeof PHP2JS.data.finex_vault_abi === 'string'
        ? JSON.parse(PHP2JS.data.finex_vault_abi || '[]')
        : (PHP2JS.data.finex_vault_abi || []);
} catch (e) {
    finexVaultAbi = [];
}
const bscChainId = parseInt(PHP2JS.data.bsc_chain_id || 97, 10);

jQuery(document).ready(function() {
    // Card "Activate" selects that fixed slot and starts payment / admin-approve flow.
    jQuery('.btn-activate-slot').on('click', function(e) {
        e.preventDefault();

        var stakeId = $(this).data('stakeid');
        var amount = $(this).data('amount');
        var slotNumber = $(this).data('slot');

        if (!stakeId) {
            erroralert('This slot package is not configured yet. Please contact support.');
            return false;
        }

        selectSlot(stakeId, amount, slotNumber);
        processstake();
    });
});

// Select a fixed slot package (no manual amount entry).
function selectSlot(stakeId, amount, slotNumber)
{
    $("#topup_amount").val(amount);

    $("input[name=package]").prop('checked', false);
    $("input[name=package][stakeid='" + stakeId + "']").prop('checked', true);

    if (!$("input[name=paymentmode]").is(':checked')) {
        $("#payment_alc").prop('checked', true);
    }

    $("#txt_amount").text('$' + Number(amount).toLocaleString());
    if (slotNumber) {
        $("#txt_next_slot").text('Slot ' + slotNumber);
    }

    getcalculation();
}

function getcalculation()
{
    var amount = parseFloat($("#topup_amount").val());

    if (isNaN(amount)) {
        amount = 0;
    }

    var cap = parseFloat($("input[name=package]:checked").attr('cap'));
    if (isNaN(cap)) {
        cap = 0;
    }

    $("#txt_cap").text((cap > 0 ? cap.toFixed(2) : '0') + "x");

    var apy = parseFloat(window.directRoiPercent);
    if (isNaN(apy)) {
        apy = parseFloat(window.slotMeta && window.slotMeta.direct_roi_percent);
    }
    if (isNaN(apy)) {
        apy = 0;
    }

    var payable = (amount / coin_rate).toFixed(8);
    payable_coin = payable;

    $("#txt_apy").text(apy.toFixed(0) + "%");
    $("#txt_payable").text(payable);

    if (amount > 0) {
        $("#txt_amount").text('$' + Number(amount).toLocaleString());
    }

    $("#amount_error").hide().text('');
}

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    processstake();
});

async function processstake()
{
    if(!$("input[name='package']").is(':checked'))
    {
        erroralert('Please activate your next eligible slot');
        return false;
    }

    var selectedState = $("input[name='package']:checked").attr('data-state');
    if (selectedState && selectedState !== 'ready')
    {
        erroralert('Only the next eligible slot can be activated');
        return false;
    }

    if(!$("input[name='paymentmode']").is(':checked'))
    {
        erroralert('Please select payment option');
        return false;
    }

    const decimal = $("input[name='paymentmode']:checked").attr('decimal');
    const payment = $("input[name='paymentmode']:checked").attr('value');

    const amount = $("#topup_amount").val();
    const slotAmount = $("input[name='package']:checked").attr('stakeamount');

    if(amount == '' || amount <= 0)
    {
        erroralert('Please select a slot to activate');
        return false;
    }

    // Amount must exactly match the selected fixed slot — no custom values.
    if(parseFloat(amount) !== parseFloat(slotAmount))
    {
        erroralert('Investment amount must match the selected slot');
        return false;
    }

    // Refresh payable from the fixed slot amount before sending chain tx.
    getcalculation();

    // Offline admin-approve only when explicitly enabled (not for normal testnet).
    if (USE_ADMIN_APPROVE_TEMP) {
        blockui();
        submitHashRequest(0, payment, 0, 'TEST-PENDING-' + Date.now(), true);
        return;
    }

    // Proper testnet/mainnet: FinexVault invest via MetaMask
    if (!blockchainEnabled || !finexVaultAddress || !finexVaultAbi.length) {
        erroralert('Smart contract vault is not configured. Set FINEX_VAULT_ADDRESS + BLOCKCHAIN_NETWORK=testnet in .env, then php artisan config:clear.');
        return false;
    }

    blockui();

    // If Laravel already has earlier slots (legacy admin path), sync vault first
    // so invest(nextSlot) does not revert with FinexVault: sequence.
    const synced = await syncChainSlotsBeforeInvest();
    if (!synced) {
        return;
    }

    await investViaFinexVault(payment, decimal, amount);
}

function syncChainSlotsBeforeInvest()
{
    return new Promise(function (resolve) {
        $.ajax({
            type: 'POST',
            url: BASEPATH + '/sync-chain-slots',
            data: { _token: token },
            dataType: 'json',
            success: function (result) {
                if (result && result.success) {
                    resolve(true);
                    return;
                }
                erroralert((result && result.error) || 'Could not sync on-chain slots before payment.');
                unblockui();
                resolve(false);
            },
            error: function () {
                erroralert('Network error while syncing on-chain slots.');
                unblockui();
                resolve(false);
            }
        });
    });
}

async function ensureBscNetwork()
{
    if (!window.ethereum) {
        throw new Error('MetaMask (or a Web3 wallet) is required.');
    }

    const hexId = '0x' + Number(bscChainId).toString(16);
    try {
        await window.ethereum.request({
            method: 'wallet_switchEthereumChain',
            params: [{ chainId: hexId }],
        });
    } catch (switchError) {
        // 4902 = chain not added
        if (switchError && (switchError.code === 4902 || switchError.code === -32603)) {
            await window.ethereum.request({
                method: 'wallet_addEthereumChain',
                params: [{
                    chainId: hexId,
                    chainName: bscChainId === 97 ? 'BSC Testnet' : 'BNB Smart Chain',
                    nativeCurrency: { name: 'BNB', symbol: 'BNB', decimals: 18 },
                    rpcUrls: [
                        bscChainId === 97
                            ? 'https://data-seed-prebsc-1-s1.binance.org:8545'
                            : 'https://bsc-dataseed1.binance.org/'
                    ],
                    blockExplorerUrls: [
                        bscChainId === 97
                            ? 'https://testnet.bscscan.com'
                            : 'https://bscscan.com'
                    ],
                }],
            });
        } else {
            throw switchError;
        }
    }
}

function formatRevertError(err)
{
    const msg = (err && (err.message || err.toString())) || 'Vault transaction failed';
    const data = (err && (err.data || (err.cause && err.cause.data))) || '';
    const dataStr = typeof data === 'string' ? data : (data && data.data) ? data.data : '';

    // Wrong function selector / empty revert — usually ABI mismatch
    if (/execution reverted:\s*0x\s*$/i.test(msg) || dataStr === '0x') {
        return msg + ' — vault rejected the call (wrong ABI/network/USDT, or slot sequence).';
    }

    // OpenZeppelin custom errors
    if (typeof dataStr === 'string' && dataStr.indexOf('0xfb8f41b2') === 0) {
        return 'USDT allowance too low for FinexVault. Approve was for the wrong token or amount. Use the vault USDT token, then retry.';
    }
    if (typeof dataStr === 'string' && dataStr.indexOf('0xe450d38c') === 0) {
        return 'Insufficient vault USDT balance. Fund the wallet with the token returned by FinexVault.usdt(), then retry.';
    }

    return msg;
}

async function sendContractTx(tx, from)
{
    let gasprice = await web3.eth.getGasPrice();
    gasprice = Math.round(Number(gasprice) * 1.2);
    let gas_estimate = await tx.estimateGas({ from: from });
    gas_estimate = Math.round(Number(gas_estimate) * 1.2);
    return tx.send({
        from: from,
        gas: web3.utils.toHex(gas_estimate),
        gasPrice: web3.utils.toHex(gasprice),
    });
}

async function investViaFinexVault(payment, decimal, amount)
{
    try {
        await ensureBscNetwork();
        await connectwallet();

        const slotNumber = parseInt(
            $("input[name=package]:checked").attr('slotnumber')
            || $("input[name=package]:checked").attr('data-slot')
            || (window.slotMeta && window.slotMeta.next_slot)
            || '1',
            10
        );

        if (!slotNumber || slotNumber < 1 || slotNumber > 12) {
            erroralert('Invalid slot number for vault invest.');
            unblockui();
            return;
        }

        if (!finexVaultAddress || !web3.utils.isAddress(finexVaultAddress)) {
            erroralert('Vault address missing. Set FINEX_VAULT_ADDRESS in .env.');
            unblockui();
            return;
        }

        const vault = new web3.eth.Contract(finexVaultAbi, finexVaultAddress);

        // Always use the token the vault actually pulls (avoids approving a different MockUSDT).
        let paymentToken = contract_addr;
        try {
            const onchainUsdt = await vault.methods.usdt().call();
            if (onchainUsdt && web3.utils.isAddress(onchainUsdt)) {
                paymentToken = onchainUsdt;
                contract_addr = onchainUsdt;
            }
        } catch (usdtErr) {
            console.log('vault.usdt() failed, falling back to configured USDT', usdtErr);
        }

        if (!paymentToken || !web3.utils.isAddress(paymentToken)) {
            erroralert('USDT contract address missing. Set BLOCKCHAIN_USDT_ADDRESS to FinexVault.usdt().');
            unblockui();
            return;
        }

        // Exact on-chain slot price (18 decimals on testnet vault).
        let amountwei;
        try {
            amountwei = await vault.methods.getSlotAmount(slotNumber).call();
        } catch (priceErr) {
            console.log('getSlotAmount failed, using UI amount', priceErr);
            if (decimal == 6) {
                amountwei = web3.utils.toWei(String(amount), 'mwei');
            } else {
                amountwei = web3.utils.toWei(String(amount), 'ether');
            }
        }

        const erc20Abi = [
            {"inputs":[{"name":"account","type":"address"}],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"stateMutability":"view","type":"function"},
            {"inputs":[{"name":"owner","type":"address"},{"name":"spender","type":"address"}],"name":"allowance","outputs":[{"name":"","type":"uint256"}],"stateMutability":"view","type":"function"},
            {"inputs":[{"name":"spender","type":"address"},{"name":"amount","type":"uint256"}],"name":"approve","outputs":[{"name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"}
        ];
        const usdt = new web3.eth.Contract(erc20Abi, paymentToken);

        let balance;
        try {
            balance = await usdt.methods.balanceOf(accounts[0]).call();
        } catch (balErr) {
            erroralert(
                'Cannot read vault USDT on this network. Switch MetaMask to BSC Testnet and fund token: ' + paymentToken
            );
            unblockui();
            return;
        }
        if (BigInt(balance) < BigInt(amountwei)) {
            erroralert(
                'Insufficient vault USDT balance. Need slot amount on token ' + paymentToken +
                ' (FinexVault.usdt). Current balance is too low.'
            );
            unblockui();
            return;
        }

        let allowance = await usdt.methods.allowance(accounts[0], finexVaultAddress).call();
        if (BigInt(allowance) < BigInt(amountwei)) {
            await sendContractTx(
                usdt.methods.approve(finexVaultAddress, amountwei),
                accounts[0]
            );
        }

        let sponsor = (window.sponsorWalletAddress || PHP2JS.data.sponsor_wallet || '0x0000000000000000000000000000000000000000');
        if (!sponsor || !web3.utils.isAddress(sponsor)) {
            sponsor = '0x0000000000000000000000000000000000000000';
        }

        // New wallets must register before / as part of invest on this vault.
        try {
            await sendContractTx(vault.methods.register(sponsor), accounts[0]);
        } catch (regErr) {
            // Already registered (or register not required) — continue to invest.
            console.log('register skipped/failed', regErr);
        }

        const offchainId = rid || 0;
        // Live FinexVault selector: invest(uint8,address,uint256) — NOT uint256 slot.
        const investTx = vault.methods.invest(slotNumber, sponsor, offchainId);

        await investTx.send({
            from: accounts[0],
            gas: web3.utils.toHex(Math.round(Number(await investTx.estimateGas({ from: accounts[0] })) * 1.2)),
            gasPrice: web3.utils.toHex(Math.round(Number(await web3.eth.getGasPrice()) * 1.2)),
        }).on('transactionHash', (hash) => {
            submitHashRequest(rid, payment, 1, hash, false);
        }).on('receipt', (receipt) => {
            if (receipt.status) {
                submitHashRequest(rid, payment, 2, receipt.transactionHash, true);
            } else {
                erroralert('On-chain investment failed.');
                unblockui();
            }
        }).on('error', (error) => {
            erroralert(formatRevertError(error));
            unblockui();
        });
    } catch (err) {
        console.log(err);
        erroralert(formatRevertError(err));
        unblockui();
    }
}

/**
 * @param {boolean} finalize - when true, show result and redirect after status 0/2
 */
async function submitHashRequest(id, payment, status, hash, finalize)
{
    const stake_id = $("input[name=package]:checked").attr('stakeid');
    const amount = $("#topup_amount").val();

    var reqObj = {
        _token: token,
        id : id,
        stake_id : stake_id,
        payment : payment,
        amount : amount,
        status : status,
        hash : hash
    };

    $.ajax({
        type: 'POST',
        url: BASEPATH + '/process-submit-buy-bot',
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                if (result.id) {
                    rid = result.id;
                }

                // TEMP admin path: status 0 = Pending request created
                if (status == 0) {
                    unblockui();
                    successalert(result.message || 'Topup request submitted. Waiting for admin approval.');
                    window.location.href = BASEPATH + '/bot-request';
                    return;
                }

                if (finalize || status == 2) {
                    unblockui();
                    if (result.activated) {
                        successalert(result.message || 'Slot activated successfully on-chain!');
                        window.location.href = BASEPATH + '/dashboard';
                    } else if (result.message) {
                        // Backend may activate without returning activated:true (legacy)
                        successalert(result.message);
                        window.location.href = BASEPATH + '/bot-request';
                    } else {
                        erroralert(result.error || 'Payment confirmed but activation failed. Contact support with your tx hash.');
                    }
                }
            } else {
                if (finalize || status == 0 || status == 2) {
                    erroralert(result.error || 'Activation failed.');
                    unblockui();
                } else if (status == 1 && result.id) {
                    rid = result.id;
                } else if (status != 1) {
                    erroralert(result.error || 'Request failed.');
                    unblockui();
                }
            }
        },
        error: function() {
            if (finalize || status == 0 || status == 2) {
                erroralert('Network error while confirming activation.');
                unblockui();
            }
        }
    });
}
