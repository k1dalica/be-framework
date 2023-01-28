<?php

  function formatCompanyType ($type) {
    $types = [
      'acquirer' => 'Acquirer',
      'developer' => 'Developer',
      'enterprise_merchant' => 'Enterprise Merchant',
      'financial' => 'Financial',
      'fintech' => 'Fintech',
      'iso_msp' => 'ISO / MSP',
      'merchant' => 'Merchant',
      'msb' => 'MSB',
      'payfac' => 'Payfac',
      'processor' => 'Processor',
      'psp_ipsp' => 'PSP / IPSP',
      'tppp' => 'TPPP',
      'var' => 'VAR',
      'other' => 'Other'
    ];

    return $types[$type];
  }
?>
