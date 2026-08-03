<?php

/**
 * Finex on-chain payment (BSC).
 * For proper testnet testing use network=testnet (chain 97) + MockUSDT + FinexVault.
 */

$network = env('BLOCKCHAIN_NETWORK', 'testnet'); // testnet | mainnet

$networks = [
    'testnet' => [
        'chain_id' => 97,
        'rpc_url' => env('BLOCKCHAIN_RPC_URL', 'https://data-seed-prebsc-1-s1.binance.org:8545'),
        'explorer' => 'https://testnet.bscscan.com',
        // Your deployed MockUSDT on BSC Testnet (from live Finex setup)
        'usdt_address' => env('BLOCKCHAIN_USDT_ADDRESS', '0x65100813fEB38174Fd26457BbD13dc75D5E5D74c'),
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

// Minimal FinexVault ABI used by buy-bot invest()
$defaultVaultAbi = [
    [
        'inputs' => [
            ['internalType' => 'uint256', 'name' => 'slotNumber', 'type' => 'uint256'],
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
            ['internalType' => 'address', 'name' => 'user', 'type' => 'address'],
        ],
        'name' => 'currentSlot',
        'outputs' => [
            ['internalType' => 'uint256', 'name' => '', 'type' => 'uint256'],
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
    'finex_vault_address' => env('FINEX_VAULT_ADDRESS', ''),
    'finex_vault_abi' => $vaultAbi,

    // When true, status=2 with a tx hash activates even if RPC receipt check is soft-fail
    // (still requires a non-empty hash). Keep true on testnet while tooling settles.
    'trust_hash_on_status_2' => filter_var(env('BLOCKCHAIN_TRUST_HASH', true), FILTER_VALIDATE_BOOLEAN),
];
