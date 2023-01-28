<?php require_once('header.php'); ?>
<?php require_once('assets/formatState.php'); ?>
<?php require_once('assets/formatMoney.php'); ?>
<?php require_once('assets/formatCountry.php'); ?>

<style>
  body {
    font-family: "Montserrat", sans-serif;
  }
</style>

<div class='text-center'><img src='assets/images/plugg-co-logo.png'></div>
<h2 class="doc-title">Client Invoice</h2>

<div class="row">
  <div class="container">
    <div class="col col-8" style="width: 60%">
      <div>
        <?= $ownerCompany->CompanyName ?>
      </div>
      <div>
        <?= $ownerCompany->address ?>
      </div>
      <div>
        <?= $ownerCompany->city ?? 'N/A' ?>, <?= $ownerCompany->state ?? 'N/A' ?>
        , <?= $ownerCompany->postal ?? 'N/A' ?>
      </div>
      <div>
        <?= $ownerCompany->email ?>, <?= $ownerCompany->phone ?? 'N/A' ?>
      </div>
      <div>
        <?= $ownerCompany->website ?>
      </div>
    </div>
    <div class="col col-4 text-right" style="width: 40%">
      <div>
        Invoice date:
      </div>
      <div>
        <b><?= formatDate($invoice->createdAt) ?></b>
      </div>
      <div>
        Invoice number:
      </div>
      <div>
        <b><?= $invoice->invoiceId ?? $invoice->id ?></b>
      </div>
      <div>
        Client ID:
      </div>
      <div>
        <b><?= $clientId ?></b>
      </div>
      <div>
        Page:
      </div>
      <div>
        <b>1 of 1</b>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col col-12">
    <div>
      <?= $client->company ?>
    </div>
    <div>
      <?= $client->address ?>
    </div>
    <div>
      <?= $client->city ?>, <?= formatState($client->state) ?? 'N/A' ?>, <?= $client->postal ?? 'N/A' ?>
      , <?= formatCountry($client->country) ?? 'N/A' ?>
    </div>
  </div>
</div>

<div class="row">
  <div class="text-center">
    <h3 class="doc-title">INVOICE</h3>
  </div>
</div>

<div class="row">
  <?php
  if ($invoice->startDate || $invoice->endDate) {
    $startDate = formatDate($invoice->startDate);
    $endDate = formatDate($invoice->endDate);
    echo "<div class='text-center'>
        Date Range: <b>$startDate</b> - <b>$endDate</b>
      </div>";
  }
  ?>
</div>

<div class="row mt-15">
  <div class="container">
    <div class="col col-12">
      Account Number: <b><?= $accountNumber ?></b>
    </div>
  </div>
</div>

<div class="row mt-20">
  <div class="container">
    <div clas="col col-12">
      <table class="table mt-20" style="border: none; width:100%;">
        <tr>
          <td style="width: 25%; padding-bottom:10px;"><b>Date</b></td>
          <td style="width: 55%; padding-bottom:10px;"><b>Description</b></td>
          <td style="width: 20%; padding-bottom:10px;"><b>Amount</b></td>
        </tr>
        <?php

        if (count($debitPayments) == 0) {
          echo "<tr>
                <td colspan='4' class='text-center'>There are no payments for selected date range.</td>
              </tr>";
        } else {
          $total = 0;
          foreach ($debitPayments as $payment) {
            $amount = formatMoney($payment->debit);
            $total += $payment->debit;

            echo
            "<tr>
                <td style=\"width: 25%\">" . formatDate($payment->createdAt) . "</td>
                <td style=\"width: 55%\">" . $payment->note . "</td>
                <td style=\"width: 20%\">$amount</td>
              </tr>";
          }

          $total = formatMoney($total);
          echo
          "<tr> 
            <td rowspan='2' colspan='2' style='padding-top: 20px;'><b>TOTAL PAYABLE</b></td>
            <td rowspan='2' style='padding-top: 20px;'><b>" . $total . "</b></td>
          </tr>";
        }

        ?>
      </table>
    </div>
  </div>
</div>

<div class="row" style="margin-top:60px">
  <div class="container">
    <div class="col col-12">
      <table class="table mt-20" style="border: none; width:100%;">
        <tr>
          <td style="width: 30%;"><b><u>Payment Instructions:</u></b></td>
          <td style="width: 70%;">If necessary, please request our payment instructions from your Account Manager.</td>
        </tr>
        <tr>
          <td style="width: 30%; padding-top:30px;"><b><u>Payment Terms:</u></b></td>
          <td style="width: 70%; padding-top:30px;">Net 7</td>
        </tr>
      </table>
    </div>
  </div>
</div>