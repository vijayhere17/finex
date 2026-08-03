<?php

/**
 * Finex on-chain payment (BSC).
 * For proper testnet testing use network=testnet (chain 97) + MockUSDT + FinexVault.
 *
 * Live FinexVault (BSC Testnet): 0x99a532bb04b0d3B76737Ed757476B175d3F2C066
 * That vault's payment token is returned by usdt() — currently:
 *   0x71550e7710baf92843077136e428ae912613d6bf
 * invest signature on that vault: invest(uint8 slotNumber, address sponsor, uint256 offchainId)
 */

$network = env('BLOCKCHAIN_NETWORK', 'testnet'); // testnet | mainnet

$networks = [
    'testnet' => [
        'chain_id' => 97,
        'rpc_url' => env('BLOCKCHAIN_RPC_URL', 'https://data-seed-prebsc-1-s1.binance.org:8545'),
        'explorer' => 'https://testnet.bscscan.com',
        // Must match FinexVault.usdt() on the deployed vault (not a random MockUSDT).
        'usdt_address' => env('BLOCKCHAIN_USDT_ADDRESS', '0x71550e7710baf92843077136e428ae912613d6bf'),
        'usdt_decimals' => (int) env('BLOCKCHAIN_USDT_DECIMALS', 18),
    ],
    'mainnet' => [
        'chain_id' => 56,
        'rpc_url' => env('BLOCKCHAIN_RPC_URL', 'https://bsc-dataseed1.binance.org/'),
        'explorer' => 'https://bscscan.com',
        'usdt_address' => env('BLOCKCHAIN_USDT_ADDRESS', '0x55d398326f99059fF775485246999027B3197955'),
        'usdt_decimals' => (int) env('BLOCKCHAIN_USDT_DECIMALS', 18),
    ],
];

$active = $networks[$network] ?? $networks['testnet'];

// ABI decoded from live FinexVault bytecode / openchain signatures
$defaultVaultAbi = [
    [
        'inputs' => [
            ['internalType' => 'uint8', 'name' => 'slotNumber', 'type' => 'uint8'],
            ['internalType' => 'address', 'name' => 'sponsor', 'type' => 'address'],
            ['internalType' => 'uint256', 'name' => 'offchainId', 'type' => 'uint256'],
        ],
        'name' => 'invest',
        'outputs' => [],
        'stateMutability' => 'nonpayable',
        'type' => 'function',
    ],
    [
        'inputs' => [
            ['internalType' => 'address', 'name' => 'sponsor', 'type' => 'address'],
        ],
        'name' => 'register',
        'outputs' => [],
        'stateMutability' => 'nonpayable',
        'type' => 'function',
    ],
    [
        'inputs' => [],
        'name' => 'usdt',
        'outputs' => [
            ['internalType' => 'address', 'name' => '', 'type' => 'address'],
        ],
        'stateMutability' => 'view',
        'type' => 'function',
    ],
    [
        'inputs' => [
            ['internalType' => 'uint8', 'name' => 'slotNumber', 'type' => 'uint8'],
        ],
        'name' => 'getSlotAmount',
        'outputs' => [
            ['internalType' => 'uint256', 'name' => '', 'type' => 'uint256'],
        ],
        'stateMutability' => 'view',
        'type' => 'function',
    ],
    [
        'inputs' => [],
        'name' => 'MAX_SLOT',
        'outputs' => [
            ['internalType' => 'uint256', 'name' => '', 'type' => 'uint256'],
        ],
        'stateMutability' => 'view',
        'type' => 'function',
    ],
    [
        'inputs' => [],
        'name' => 'paused',
        'outputs' => [
            ['internalType' => 'bool', 'name' => '', 'type' => 'bool'],
        ],
        'stateMutability' => 'view',
        'type' => 'function',
    ],
];

$abiFromEnv = env('FINEX_VAULT_ABI');
$vaultAbi = $defaultVaultAbi;
if (!empty($abiFromEnv)) {
    $decoded = json_decode($abiFromEnv, true);
    if (is_array($decoded) && count($decoded) > 0) {
        $vaultAbi = $decoded;
    }
}

return [
    'enabled' => filter_var(env('BLOCKCHAIN_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
    'network' => $network,
    'chain_id' => (int) ($active['chain_id']),
    'rpc_url' => $active['rpc_url'],
    'explorer' => $active['explorer'],
    'usdt_address' => $active['usdt_address'],
    'usdt_decimals' => (int) $active['usdt_decimals'],

    // Deployed FinexVault on the selected network — REQUIRED for on-chain Activate
    'finex_vault_address' => env('FINEX_VAULT_ADDRESS', '0x99a532bb04b0d3B76737Ed757476B175d3F2C066'),
    'finex_vault_abi' => $vaultAbi,

    // When true, status=2 with a tx hash activates even if RPC receipt check is soft-fail
    // (still requires a non-empty hash). Keep true on testnet while tooling settles.
    'trust_hash_on_status_2' => filter_var(env('BLOCKCHAIN_TRUST_HASH', true), FILTER_VALIDATE_BOOLEAN),
];
