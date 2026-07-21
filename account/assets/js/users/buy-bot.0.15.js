let rid = 0;

let coin_rate = PHP2JS.data.coin_rate;
let contract_addr = PHP2JS.data.usdt_con_addr;
let contract_abi = JSON.parse(PHP2JS.data.usdt_con_abi);

let payable_coin = 0;

const deposit_addr = PHP2JS.data.to_address;

jQuery(document).ready(function() {
    // Amounts are $50 multiples, so only whole digits are allowed in the field.
    $('#topup_amount').on('input keyup', function() {
        var clean = this.value.replace(/[^0-9]/g, '');
        if (this.value !== clean) { this.value = clean; }
        getcalculation();
    });
});

// Auto-select the package tier whose range covers the entered amount (maxamount 0 = open-ended top tier).
function autoselectpackage(amount)
{
    if (isNaN(amount) || amount <= 0) { return; }

    $("input[name=package]").each(function() {
        var min = parseFloat($(this).attr('minamount'));
        var max = parseFloat($(this).attr('maxamount'));

        if (amount >= min && (max <= 0 || amount <= max))
        {
            $(this).prop('checked', true);
        }
    });
}

function getcalculation()
{
    
    var amount = parseFloat($("#topup_amount").val());

    if (isNaN(amount)) {
        amount = 0;
    }

    
    autoselectpackage(amount);

    // Get selected package cap
    var cap = parseFloat($("input[name=package]:checked").attr('cap'));

    if (isNaN(cap)) {
        cap = 0;
    }

    // Display cap
    $("#txt_cap").text(cap.toFixed(2) + "X");

    
    var maxIncome = amount * cap;

    if (isNaN(maxIncome)) {
        maxIncome = 0;
    }

    $("#txt_max_income").text(maxIncome.toFixed(2));

   
    var apy = parseFloat(window.monthlyROI);

    if (isNaN(apy)) {
        apy = 0;
    }

    // Calculate payable USDT
    var payable = (amount / coin_rate).toFixed(8);
    payable_coin = payable;

   
    $("#txt_apy").text(apy.toFixed(2) + "%");
    $("#txt_daily_income").text(((amount * apy) / 100).toFixed(2));

    $("#txt_amount").text(amount.toFixed(4));
    $("#txt_payable").text(payable);

    validateamount();
}

function validateamount()
{
    var value = $("#topup_amount").val();
    var amount = parseFloat(value);
    var msg = '';

    if (value != '')
    {
        if (isNaN(amount) || amount < 50)
        {
            msg = 'Minimum topup amount is $50.';
        }
        else if (amount % 50 != 0)
        {
            msg = 'Amount must be a multiple of $50 (e.g. 50, 100, 150 ...).';
        }
    }

    if (msg == '')
    {
        $("#amount_error").hide().text('');
        return true;
    }

    $("#amount_error").text(msg).show();
    return false;
}

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    processstake();
});

async function processstake()
{
    if(!$("input[name='package']").is(':checked'))
    {
        erroralert('Please select stake package');
        return false;
    }

    if(!$("input[name='paymentmode']").is(':checked'))
    {
        erroralert('Please select payment option');
        return false;
    }

    // const contractaddr = $("input[name='paymentmode']:checked").attr('contract');

    const decimal = $("input[name='paymentmode']:checked").attr('decimal');
    const payment = $("input[name='paymentmode']:checked").attr('value');

    const amount = $("#topup_amount").val();

    if(amount == '')
    {
        erroralert('Please enter a topup amount');
        return false;
    }

    if(amount <= 0)
    {
        erroralert('Please enter a valid topup amount');
        return false;
    }

    if(amount < 50)
    {
        erroralert('Minimum topup amount is $50');
        return false;
    }

    if(amount % 50 != 0)
    {
        erroralert('Please enter topup amount $50 multiple');
        return false;
    }

    const pkg_min = parseFloat($("input[name='package']:checked").attr('minamount'));
    const pkg_max = parseFloat($("input[name='package']:checked").attr('maxamount'));

    if(parseFloat(amount) < pkg_min || (pkg_max > 0 && parseFloat(amount) > pkg_max))
    {
        erroralert('Entered amount does not match the selected package range');
        return false;
    }



    blockui();

    await connectwallet();

    // ---------------------------------------------------------------------------

    if(decimal == 18)
    {
        var amountwei = web3.utils.toWei(payable_coin.toString(), 'ether');
    }
    else if(decimal == 6)
    {
        var amountwei = web3.utils.toWei(amount.toString(), 'mwei');
    }

    try {
        const payContract = new web3.eth.Contract(contract_abi, contract_addr);

        let balance = await payContract.methods.balanceOf(accounts[0]).call();

        if(BigInt(balance) < BigInt(amountwei))
        {
            erroralert("Insufficient balance to perform the topup.");
            unblockui();
            return;
        }

        const tx = payContract.methods.transfer(deposit_addr, amountwei);

        let gasprice = await web3.eth.getGasPrice();
            gasprice = Math.round(gasprice * 1.2);

        let gas_estimate = await tx.estimateGas({ from: accounts[0] });
            gas_estimate = Math.round(gas_estimate * 1.2);

        await tx.send({
            from: accounts[0],
            gas: web3.utils.toHex(gas_estimate),
            gasPrice: web3.utils.toHex(gasprice),
        }).on('transactionHash', (hash) => {
            submitHashRequest(rid, payment, 1, hash);
        }).on('receipt', (receipt) => {
            if (receipt.status) {  submitHashRequest(rid, payment, 2, receipt.transactionHash); }
        }).on('error', (error) => {
            erroralert(error.message || "Transaction failed.");
            unblockui();
        });
    } catch(err) {
        console.log(err)
        erroralert(err.message || "An unexpected error occurred.");
        unblockui();
    }
}

async function submitHashRequest(id, payment, status, hash)
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
        url: BASEPATH + "/process-submit-buy-bot",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                rid = result.id;
                if(status == 2)
                {
                    unblockui();
                    successalert(result.message)
                    window.location.href = BASEPATH+'/bot-request';
                }
            } else {
                erroralert(result.error);
                unblockui();
            }
        }
    });
}
